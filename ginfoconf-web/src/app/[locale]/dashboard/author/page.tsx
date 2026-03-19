'use client';

import { useEffect, useState } from 'react';
import { useLocale, useTranslations } from 'next-intl';
import Link from 'next/link';
import { FileText, Plus, Clock, CheckCircle, AlertCircle } from 'lucide-react';
import { DashboardLayout } from '@/components/layout/DashboardLayout';
import { StatCard } from '@/components/ui/StatCard';
import { Badge } from '@/components/ui/Badge';
import { PageLoader } from '@/components/ui/Spinner';
import { ErrorAlert } from '@/components/ui/ErrorAlert';
import { EmptyState } from '@/components/ui/EmptyState';
import { submissionApi } from '@/lib/api';
import { formatDate } from '@/lib/utils';
import { useAuthStore } from '@/store/auth';
import type { Submission } from '@/types';

function useAuthorNavItems(locale: string) {
  const t = useTranslations('admin');
  return [
    { label: 'My Submissions', href: `/${locale}/dashboard/author/submissions`, icon: FileText },
  ];
}

export default function AuthorDashboard() {
  const locale = useLocale();
  const t = useTranslations('dashboard');
  const { user } = useAuthStore();
  const navItems = useAuthorNavItems(locale);
  const [submissions, setSubmissions] = useState<Submission[]>([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState('');

  useEffect(() => {
    submissionApi.list({ page: 1 })
      .then((res) => setSubmissions(res.data))
      .catch(() => setError('Failed to load submissions.'))
      .finally(() => setLoading(false));
  }, []);

  const stats = {
    total: submissions.length,
    submitted: submissions.filter((s) => s.status !== 'draft').length,
    accepted: submissions.filter((s) => s.status === 'accepted').length,
    revisions: submissions.filter((s) => s.status === 'revision_required').length,
  };

  return (
    <DashboardLayout title={t('author_title')} navItems={navItems}>
      <div className="p-6 space-y-6">
        <div className="flex items-center justify-between">
          <div>
            <h2 className="text-xl font-bold text-slate-900">{t('welcome', { name: user?.name ?? '' })}</h2>
            <p className="text-sm text-slate-500 mt-0.5">Manage your conference submissions.</p>
          </div>
          <Link href={`/${locale}/dashboard/author/submissions/new`} className="btn-primary">
            <Plus className="h-4 w-4" />
            {t('new_submission')}
          </Link>
        </div>

        {/* Stats */}
        <div className="grid grid-cols-2 lg:grid-cols-4 gap-4">
          <StatCard label="Total Submissions" value={stats.total} icon={FileText} color="indigo" />
          <StatCard label="Submitted" value={stats.submitted} icon={Clock} color="blue" />
          <StatCard label="Accepted" value={stats.accepted} icon={CheckCircle} color="green" />
          <StatCard label="Awaiting Revision" value={stats.revisions} icon={AlertCircle} color="yellow" />
        </div>

        {/* Recent submissions */}
        <div className="card">
          <div className="flex items-center justify-between p-5 border-b border-slate-100">
            <h3 className="font-semibold text-slate-800">Recent Submissions</h3>
            <Link href={`/${locale}/dashboard/author/submissions`} className="text-sm text-brand-600 hover:underline">
              View all
            </Link>
          </div>
          {loading ? (
            <PageLoader />
          ) : error ? (
            <ErrorAlert message={error} className="m-5" />
          ) : submissions.length === 0 ? (
            <EmptyState
              icon={FileText}
              title="No submissions yet"
              description="Start by creating your first paper submission."
              action={
                <Link href={`/${locale}/dashboard/author/submissions/new`} className="btn-primary">
                  <Plus className="h-4 w-4" />
                  New Submission
                </Link>
              }
            />
          ) : (
            <div className="table-wrapper rounded-none border-none">
              <table className="data-table">
                <thead>
                  <tr>
                    <th>Title</th>
                    <th>Conference</th>
                    <th>Status</th>
                    <th>Submitted</th>
                    <th />
                  </tr>
                </thead>
                <tbody>
                  {submissions.slice(0, 5).map((s) => (
                    <tr key={s.id}>
                      <td className="font-medium text-slate-800 max-w-xs truncate">{s.title}</td>
                      <td className="text-slate-500">{s.conference?.slug ?? '—'}</td>
                      <td><Badge status={s.status} /></td>
                      <td className="text-slate-400">{s.submitted_at ? formatDate(s.submitted_at, 'PP') : '—'}</td>
                      <td>
                        <Link href={`/${locale}/dashboard/author/submissions/${s.id}`} className="text-xs text-brand-600 hover:underline font-medium">
                          View
                        </Link>
                      </td>
                    </tr>
                  ))}
                </tbody>
              </table>
            </div>
          )}
        </div>
      </div>
    </DashboardLayout>
  );
}
