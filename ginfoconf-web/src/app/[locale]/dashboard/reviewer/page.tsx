'use client';

import { useEffect, useState } from 'react';
import { useLocale } from 'next-intl';
import Link from 'next/link';
import { ClipboardList, Clock, CheckCircle, AlertCircle } from 'lucide-react';
import { DashboardLayout } from '@/components/layout/DashboardLayout';
import { StatCard } from '@/components/ui/StatCard';
import { Badge } from '@/components/ui/Badge';
import { PageLoader } from '@/components/ui/Spinner';
import { EmptyState } from '@/components/ui/EmptyState';
import { reviewApi } from '@/lib/api';
import { formatDate } from '@/lib/utils';
import { useAuthStore } from '@/store/auth';
import type { ReviewAssignment } from '@/types';

const navItems = (locale: string) => [
  { label: 'My Assignments', href: `/${locale}/dashboard/reviewer`, icon: ClipboardList },
];

export default function ReviewerDashboard() {
  const locale = useLocale();
  const { user } = useAuthStore();
  const [assignments, setAssignments] = useState<ReviewAssignment[]>([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    reviewApi.listAssignments()
      .then((res) => setAssignments(res.data))
      .finally(() => setLoading(false));
  }, []);

  const pending = assignments.filter((a) => a.status === 'pending');
  const accepted = assignments.filter((a) => a.status === 'accepted');
  const completed = assignments.filter((a) => a.status === 'completed');

  const isDueSoon = (a: ReviewAssignment) => {
    if (!a.due_date) return false;
    const days = (new Date(a.due_date).getTime() - Date.now()) / (1000 * 60 * 60 * 24);
    return days < 5 && days > 0;
  };

  return (
    <DashboardLayout title="Reviewer Dashboard" navItems={navItems(locale)}>
      <div className="p-6 space-y-6">
        <div>
          <h2 className="text-xl font-bold text-slate-900">Welcome back, {user?.name}</h2>
          <p className="text-sm text-slate-500 mt-0.5">Your current review assignments.</p>
        </div>

        {/* Stats */}
        <div className="grid grid-cols-3 gap-4">
          <StatCard label="Pending Response" value={pending.length} icon={Clock} color="yellow" />
          <StatCard label="In Progress" value={accepted.length} icon={ClipboardList} color="blue" />
          <StatCard label="Completed" value={completed.length} icon={CheckCircle} color="green" />
        </div>

        {/* Assignments list */}
        <div className="card">
          <div className="p-5 border-b border-slate-100">
            <h3 className="font-semibold text-slate-800">All Assignments</h3>
          </div>

          {loading ? (
            <PageLoader />
          ) : assignments.length === 0 ? (
            <EmptyState
              icon={ClipboardList}
              title="No assignments yet"
              description="You will see your review assignments here once a program chair assigns papers to you."
            />
          ) : (
            <div className="divide-y divide-slate-100">
              {assignments.map((a) => (
                <div key={a.id} className="p-5 flex items-center justify-between gap-4">
                  <div className="flex-1 min-w-0">
                    <p className="font-medium text-slate-800 truncate">
                      {a.submission?.title ?? `Submission #${a.submission?.id}`}
                    </p>
                    <p className="text-xs text-slate-400 mt-0.5">
                      {a.submission?.conference?.slug ?? ''}
                      {a.due_date && ` · Due ${formatDate(a.due_date, 'PP')}`}
                    </p>
                  </div>
                  <div className="flex items-center gap-3 shrink-0">
                    {isDueSoon(a) && (
                      <span className="flex items-center gap-1 text-xs font-medium text-orange-600">
                        <AlertCircle className="h-3.5 w-3.5" />
                        Due soon
                      </span>
                    )}
                    <Badge status={a.status} />
                    <Link href={`/${locale}/dashboard/reviewer/assignments/${a.id}`} className="btn-primary btn-sm">
                      {a.review?.status === 'submitted' ? 'View Review' : a.status === 'accepted' ? 'Write Review' : 'Respond'}
                    </Link>
                  </div>
                </div>
              ))}
            </div>
          )}
        </div>
      </div>
    </DashboardLayout>
  );
}
