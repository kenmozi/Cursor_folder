'use client';

import { useEffect, useState } from 'react';
import { useLocale } from 'next-intl';
import Link from 'next/link';
import { FileText } from 'lucide-react';
import { AdminLayout } from '@/components/admin/AdminLayout';
import { Badge } from '@/components/ui/Badge';
import { PageLoader } from '@/components/ui/Spinner';
import { EmptyState } from '@/components/ui/EmptyState';
import { ErrorAlert } from '@/components/ui/ErrorAlert';
import { adminConferenceApi } from '@/lib/api';
import { formatDate, getErrorMessage } from '@/lib/utils';
import type { Submission } from '@/types';

export default function AdminSubmissionsPage({ params }: { params: { id: string } }) {
  const locale = useLocale();
  const slug = params.id;
  const [submissions, setSubmissions] = useState<Submission[]>([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState('');
  const [statusFilter, setStatusFilter] = useState('');

  const loadSubmissions = () => {
    adminConferenceApi.listSubmissions(slug, statusFilter ? { status: statusFilter } : {})
      .then((res) => setSubmissions(res.data))
      .catch((e) => setError(getErrorMessage(e)))
      .finally(() => setLoading(false));
  };

  useEffect(() => { loadSubmissions(); }, [slug, statusFilter]);

  const statuses = ['', 'draft', 'submitted', 'under_review', 'accepted', 'rejected', 'revision_required'];

  return (
    <AdminLayout title="Submissions" conferenceSlug={slug}>
      <div className="p-6 space-y-5">
        <div className="flex items-center justify-between flex-wrap gap-3">
          <h2 className="text-xl font-bold text-slate-900">Submissions</h2>
          {/* Status filter */}
          <select
            value={statusFilter}
            onChange={(e) => setStatusFilter(e.target.value)}
            className="input w-auto"
          >
            <option value="">All Statuses</option>
            {statuses.slice(1).map((s) => (
              <option key={s} value={s}>{s.replace('_', ' ')}</option>
            ))}
          </select>
        </div>

        {error && <ErrorAlert message={error} />}

        <div className="card">
          {loading ? (
            <PageLoader />
          ) : submissions.length === 0 ? (
            <EmptyState icon={FileText} title="No submissions found" />
          ) : (
            <div className="table-wrapper rounded-none border-none">
              <table className="data-table">
                <thead>
                  <tr>
                    <th>#</th>
                    <th>Title</th>
                    <th>Submitter</th>
                    <th>Status</th>
                    <th>Submitted</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  {submissions.map((s) => (
                    <tr key={s.id}>
                      <td className="text-slate-400 text-xs">#{s.id}</td>
                      <td>
                        <p className="font-medium text-slate-800 max-w-[240px] truncate">{s.title}</p>
                      </td>
                      <td className="text-slate-500 text-sm">{s.authors?.[0]?.name ?? '—'}</td>
                      <td><Badge status={s.status} /></td>
                      <td className="text-slate-400 text-xs">{s.submitted_at ? formatDate(s.submitted_at, 'PP') : '—'}</td>
                      <td>
                        <div className="flex gap-2">
                          <Link
                            href={`/${locale}/dashboard/admin/conferences/${slug}/assignments?submission=${s.id}`}
                            className="text-xs text-brand-600 hover:underline font-medium"
                          >
                            Assign
                          </Link>
                        </div>
                      </td>
                    </tr>
                  ))}
                </tbody>
              </table>
            </div>
          )}
        </div>
      </div>
    </AdminLayout>
  );
}
