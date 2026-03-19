import { AlertCircle } from 'lucide-react';
import { cn } from '@/lib/utils';

interface ErrorAlertProps {
  message: string;
  className?: string;
  onRetry?: () => void;
}

export function ErrorAlert({ message, className, onRetry }: ErrorAlertProps) {
  return (
    <div className={cn('rounded-lg bg-red-50 border border-red-200 p-4', className)}>
      <div className="flex items-start gap-3">
        <AlertCircle className="h-5 w-5 text-red-500 mt-0.5 shrink-0" />
        <div className="flex-1">
          <p className="text-sm text-red-700">{message}</p>
          {onRetry && (
            <button onClick={onRetry} className="mt-2 text-xs font-medium text-red-700 underline hover:no-underline">
              Try again
            </button>
          )}
        </div>
      </div>
    </div>
  );
}
