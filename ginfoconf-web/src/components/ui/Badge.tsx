import { cn, STATUS_COLORS, STATUS_LABELS } from '@/lib/utils';

interface BadgeProps {
  status: string;
  label?: string;
  className?: string;
}

export function Badge({ status, label, className }: BadgeProps) {
  const colorClass = STATUS_COLORS[status] ?? 'bg-slate-100 text-slate-600';
  return (
    <span className={cn('badge', colorClass, className)}>
      {label ?? STATUS_LABELS[status] ?? status}
    </span>
  );
}
