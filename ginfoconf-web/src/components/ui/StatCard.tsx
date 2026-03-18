import { cn } from '@/lib/utils';
import type { LucideIcon } from 'lucide-react';

interface StatCardProps {
  label: string;
  value: string | number;
  icon?: LucideIcon;
  trend?: string;
  color?: 'indigo' | 'green' | 'yellow' | 'red' | 'blue';
  className?: string;
}

const colorMap = {
  indigo: 'bg-brand-50 text-brand-600',
  green: 'bg-green-50 text-green-600',
  yellow: 'bg-yellow-50 text-yellow-600',
  red: 'bg-red-50 text-red-600',
  blue: 'bg-blue-50 text-blue-600',
};

export function StatCard({ label, value, icon: Icon, trend, color = 'indigo', className }: StatCardProps) {
  return (
    <div className={cn('card p-5', className)}>
      <div className="flex items-start justify-between">
        <div>
          <p className="text-sm text-slate-500">{label}</p>
          <p className="mt-1.5 text-3xl font-bold text-slate-900">{value}</p>
          {trend && <p className="mt-1 text-xs text-slate-500">{trend}</p>}
        </div>
        {Icon && (
          <div className={cn('rounded-xl p-3', colorMap[color])}>
            <Icon className="h-6 w-6" />
          </div>
        )}
      </div>
    </div>
  );
}
