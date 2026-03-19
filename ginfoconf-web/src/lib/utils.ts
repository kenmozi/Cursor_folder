import { clsx, type ClassValue } from 'clsx';
import { twMerge } from 'tailwind-merge';
import { format, parseISO, isValid } from 'date-fns';

export function cn(...inputs: ClassValue[]) {
  return twMerge(clsx(inputs));
}

export function formatDate(dateStr?: string | null, fmt = 'PPP'): string {
  if (!dateStr) return '—';
  try {
    const d = parseISO(dateStr);
    return isValid(d) ? format(d, fmt) : '—';
  } catch {
    return '—';
  }
}

export function formatBytes(bytes: number): string {
  if (bytes < 1024) return `${bytes} B`;
  if (bytes < 1024 * 1024) return `${(bytes / 1024).toFixed(1)} KB`;
  return `${(bytes / 1024 / 1024).toFixed(1)} MB`;
}

export const STATUS_LABELS: Record<string, string> = {
  draft: 'Draft',
  submitted: 'Submitted',
  under_review: 'Under Review',
  accepted: 'Accepted',
  rejected: 'Rejected',
  revision_required: 'Revision Required',
  camera_ready: 'Camera Ready',
  withdrawn: 'Withdrawn',
};

export const STATUS_COLORS: Record<string, string> = {
  draft: 'bg-slate-100 text-slate-700',
  submitted: 'bg-blue-100 text-blue-700',
  under_review: 'bg-yellow-100 text-yellow-700',
  accepted: 'bg-green-100 text-green-700',
  rejected: 'bg-red-100 text-red-700',
  revision_required: 'bg-orange-100 text-orange-700',
  camera_ready: 'bg-emerald-100 text-emerald-700',
  withdrawn: 'bg-slate-100 text-slate-400',
  // Assignment
  pending: 'bg-blue-100 text-blue-700',
  declined: 'bg-red-100 text-red-700',
  completed: 'bg-green-100 text-green-700',
  expired: 'bg-slate-100 text-slate-400',
  // Conference
  active: 'bg-green-100 text-green-700',
  open: 'bg-blue-100 text-blue-700',
  closed: 'bg-slate-100 text-slate-500',
  archived: 'bg-slate-100 text-slate-400',
};

export const RECOMMENDATION_LABELS: Record<string, string> = {
  strong_accept: 'Strong Accept',
  accept: 'Accept',
  weak_accept: 'Weak Accept',
  borderline: 'Borderline',
  weak_reject: 'Weak Reject',
  reject: 'Reject',
  strong_reject: 'Strong Reject',
};

export function getErrorMessage(error: unknown): string {
  if (error instanceof Error) return error.message;
  if (typeof error === 'object' && error !== null) {
    const e = error as Record<string, unknown>;
    if (e.response && typeof e.response === 'object') {
      const res = e.response as Record<string, unknown>;
      const data = res.data as Record<string, unknown> | undefined;
      if (data?.message && typeof data.message === 'string') return data.message;
      if (data?.errors && typeof data.errors === 'object') {
        const msgs = Object.values(data.errors).flat();
        return msgs[0] as string;
      }
    }
  }
  return 'An unexpected error occurred.';
}
