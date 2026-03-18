'use client';

import { useEffect, useState } from 'react';
import { useLocale } from 'next-intl';
import Link from 'next/link';
import { FileText, Plus } from 'lucide-react';
import { DashboardLayout } from '@/components/layout/DashboardLayout';
import { Badge } from '@/components/ui/Badge';
import { PageLoader } from '@/components/ui/Spinner';
import { ErrorAlert } from '@/components/ui/ErrorAlert';
import { EmptyState } from '@/components/ui/EmptyState';
import { submissionApi } from '@/lib/api';
import { formatDate } from '@/lib/utils';
import type { Submission } from '@/types';

const navItems = (locale: string) => [
  { label: 'My Submissions', href: `/${locale}/dashboard/author/submissions`, icon: FileText },
];

export default function SubmissionsPage() {
  const locale = useLocale();
  const [submissions, setSubmissions] = useState<Submission[]>([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState('');

  useEffect(() => {
    submissionApi.list()
      .then((res) => setSubmissions(res.data))
      .catch(() => setError('Failed to load submissions.'))
      .finally(() => setLoading(false));
  }, []);

  return (
    <DashboardLayout title="My Submissions" navItems={navItems(locale)}>
      <div className="p-6 space-y-5">
        <div className="flex items-center justify-between">
          <h2 className="text-xl font-bold text-slate-900">All Submissions</h2>
          <Link href={`/${locale}/dashboard/author/submissions/new`} className="btn-primary">
            <Plus className="h-4 w-4" />
            New Submission
          </Link>
        </div>

        <div className="card">
          {loading ? (
            <PageLoader />
          ) : error ? (
            <ErrorAlert message={error} className="m-5" />
          ) : submissions.length === 0 ? (
            <EmptyState
              icon={FileText}
              title="No submissions yet"
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
                    <th>#</th>
                    <th>Title</th>
                    <th>Conference</th>
                    <th>Status</th>
                    <th>Submitted</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  {submissions.map((s) => (
                    <tr key={s.id}>
                      <td className="text-slate-400 text-xs">#{s.id}</td>
                      <td className="font-medium text-slate-800 max-w-[260px] truncate">
                        <Link href={`/${locale}/dashboard/author/submissions/${s.id}`} className="hover:text-brand-600">
                          {s.title}
                        </Link>
                      </td>
                      <td className="text-slate-500 text-xs">{s.conference?.slug ?? '—'}</td>
                      <td><Badge status={s.status} /></td>
                      <td className="text-slate-400 text-xs">{s.submitted_at ? formatDate(s.submitted_at, 'PP') : 'Draft'}</td>
                      <td>
                        <Link href={`/${locale}/dashboard/author/submissions/${s.id}`} className="text-xs text-brand-600 hover:underline font-medium">
                          Open
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
