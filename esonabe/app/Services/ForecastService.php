<?php

namespace App\Services;

use App\Models\Meter;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class ForecastService
{
    /**
     * Generate a consumption forecast for the given number of days.
     * Uses exponential smoothing (Holt-Winters simplified) on historical data.
     */
    public function forecast(Meter $meter, int $days = 7): array
    {
        $records = $meter->consumptionRecords()
            ->orderBy('recorded_date')
            ->where('recorded_date', '>=', Carbon::now()->subDays(90))
            ->get();

        if ($records->isEmpty()) {
            return $this->emptyForecast($days);
        }

        $values = $records->pluck('kwh_consumed')->map(fn($v) => (float) $v)->toArray();

        // Exponential smoothing with trend
        $alpha   = 0.3; // smoothing factor
        $beta    = 0.1; // trend factor
        $level   = $values[0];
        $trend   = count($values) > 1 ? ($values[1] - $values[0]) : 0;

        foreach ($values as $v) {
            $prevLevel = $level;
            $level     = $alpha * $v + (1 - $alpha) * ($level + $trend);
            $trend     = $beta * ($level - $prevLevel) + (1 - $beta) * $trend;
        }

        // Weekly seasonality: calculate average ratio per weekday
        $weekdayTotals = array_fill(0, 7, ['sum' => 0, 'count' => 0]);
        foreach ($records as $rec) {
            $dow = (int) $rec->recorded_date->format('N') - 1; // 0=Mon ... 6=Sun
            $weekdayTotals[$dow]['sum']   += (float) $rec->kwh_consumed;
            $weekdayTotals[$dow]['count'] += 1;
        }

        $avgOverall   = count($values) > 0 ? array_sum($values) / count($values) : 1;
        $seasonalIdx  = [];
        for ($d = 0; $d < 7; $d++) {
            $cnt = $weekdayTotals[$d]['count'];
            $seasonalIdx[$d] = $cnt > 0
                ? ($weekdayTotals[$d]['sum'] / $cnt) / max($avgOverall, 0.001)
                : 1.0;
        }

        $result = [];
        for ($i = 1; $i <= $days; $i++) {
            $forecastDate = Carbon::now()->addDays($i);
            $dow          = (int) $forecastDate->format('N') - 1;
            $rawForecast  = max(0, ($level + $i * $trend) * $seasonalIdx[$dow]);

            $result[] = [
                'date'       => $forecastDate->format('Y-m-d'),
                'kwh'        => round($rawForecast, 3),
                'confidence' => $this->confidence($i, count($values)),
            ];
        }

        return $result;
    }

    /**
     * Calculate consumption statistics for a meter.
     */
    public function stats(Meter $meter): array
    {
        $records30 = $meter->consumptionRecords()
            ->where('recorded_date', '>=', Carbon::now()->subDays(30))
            ->orderBy('recorded_date')
            ->get();

        $records90 = $meter->consumptionRecords()
            ->where('recorded_date', '>=', Carbon::now()->subDays(90))
            ->orderBy('recorded_date')
            ->get();

        $values30 = $records30->pluck('kwh_consumed')->map(fn($v) => (float) $v)->toArray();
        $values90 = $records90->pluck('kwh_consumed')->map(fn($v) => (float) $v)->toArray();

        $avg30 = count($values30) > 0 ? array_sum($values30) / count($values30) : 0;
        $avg90 = count($values90) > 0 ? array_sum($values90) / count($values90) : 0;

        // Trend: compare last 15 days vs previous 15 days
        $last15  = $records30->where('recorded_date', '>=', Carbon::now()->subDays(15));
        $prev15  = $records30->where('recorded_date', '<', Carbon::now()->subDays(15));

        $avgLast = $last15->count() > 0
            ? $last15->avg('kwh_consumed')
            : $avg30;
        $avgPrev = $prev15->count() > 0
            ? $prev15->avg('kwh_consumed')
            : $avg30;

        $trendPct = $avgPrev > 0
            ? round((($avgLast - $avgPrev) / $avgPrev) * 100, 1)
            : 0;

        $peak   = count($values30) > 0 ? max($values30) : 0;
        $total30 = array_sum($values30);

        // Projected monthly cost (131 CFA/kWh)
        $projectedMonthlyCfa = $avg30 * 30 * 131;

        // AI insight
        $aiInsight = $this->generateInsight($avg30, $avg90, $trendPct, $peak);

        return [
            'avg_daily_kwh_30'       => round($avg30, 3),
            'avg_daily_kwh_90'       => round($avg90, 3),
            'total_kwh_30'           => round($total30, 3),
            'peak_daily_kwh'         => round($peak, 3),
            'trend_pct'              => $trendPct,
            'projected_monthly_cfa'  => round($projectedMonthlyCfa, 2),
            'ai_insight'             => $aiInsight,
            'data_points_30'         => count($values30),
            'data_points_90'         => count($values90),
        ];
    }

    private function confidence(int $horizon, int $dataPoints): string
    {
        if ($dataPoints < 7) return 'low';
        if ($horizon <= 3 && $dataPoints >= 30) return 'high';
        if ($horizon <= 7 && $dataPoints >= 14) return 'medium';
        return 'low';
    }

    private function generateInsight(float $avg30, float $avg90, float $trendPct, float $peak): string
    {
        $locale = app()->getLocale();
        $isFr   = $locale === 'fr';

        if ($avg30 === 0.0) {
            return $isFr
                ? "Données insuffisantes pour générer une analyse."
                : "Insufficient data to generate an analysis.";
        }

        $parts = [];

        // Trend commentary
        if ($trendPct > 15) {
            $parts[] = $isFr
                ? "⚠️ Votre consommation a augmenté de {$trendPct}% ces 15 derniers jours."
                : "⚠️ Your consumption increased by {$trendPct}% over the last 15 days.";
        } elseif ($trendPct < -15) {
            $abs     = abs($trendPct);
            $parts[] = $isFr
                ? "✅ Votre consommation a diminué de {$abs}% ces 15 derniers jours. Continuez!"
                : "✅ Your consumption decreased by {$abs}% over the last 15 days. Keep it up!";
        } else {
            $parts[] = $isFr
                ? "📊 Votre consommation est stable ({$trendPct}% de variation)."
                : "📊 Your consumption is stable ({$trendPct}% variation).";
        }

        // Peak commentary
        if ($peak > $avg30 * 2) {
            $parts[] = $isFr
                ? "🔍 Un pic de consommation de " . round($peak, 1) . " kWh/j a été détecté. Vérifiez vos appareils énergivores."
                : "🔍 A peak consumption of " . round($peak, 1) . " kWh/day was detected. Check your high-consumption appliances.";
        }

        // Compare to 90-day average
        if ($avg90 > 0 && $avg30 > $avg90 * 1.1) {
            $pct     = round((($avg30 - $avg90) / $avg90) * 100, 1);
            $parts[] = $isFr
                ? "📈 Votre consommation mensuelle est {$pct}% supérieure à votre moyenne trimestrielle."
                : "📈 Your monthly consumption is {$pct}% above your quarterly average.";
        }

        // Cost awareness
        $projMonthly = round($avg30 * 30 * 131);
        $parts[] = $isFr
            ? "💡 Coût mensuel projeté: " . number_format($projMonthly, 0, ',', ' ') . " CFA basé sur votre tendance actuelle."
            : "💡 Projected monthly cost: " . number_format($projMonthly, 0, ',', ' ') . " CFA based on your current trend.";

        return implode(' ', $parts);
    }

    private function emptyForecast(int $days): array
    {
        $result = [];
        for ($i = 1; $i <= $days; $i++) {
            $result[] = [
                'date'       => Carbon::now()->addDays($i)->format('Y-m-d'),
                'kwh'        => 0,
                'confidence' => 'none',
            ];
        }
        return $result;
    }
}
