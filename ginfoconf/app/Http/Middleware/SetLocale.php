<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    private const SUPPORTED = ['en', 'fr', 'ja'];

    public function handle(Request $request, Closure $next): Response
    {
        // Priority: ?locale= query param → Accept-Language header → 'en'
        $locale = $request->query('locale')
            ?? $this->parseAcceptLanguage($request)
            ?? 'en';

        if (!in_array($locale, self::SUPPORTED)) {
            $locale = 'en';
        }

        app()->setLocale($locale);

        return $next($request);
    }

    private function parseAcceptLanguage(Request $request): ?string
    {
        $header = $request->header('Accept-Language', '');

        foreach (explode(',', $header) as $part) {
            $lang = substr(strtolower(trim($part)), 0, 2);
            if (in_array($lang, self::SUPPORTED)) {
                return $lang;
            }
        }

        return null;
    }
}
