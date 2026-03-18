'use client';

import { useEffect, useState } from 'react';
import { useLocale } from 'next-intl';
import Link from 'next/link';
import { Globe, Plus } from 'lucide-react';
import { AdminLayout } from '@/components/admin/AdminLayout';
import { StatCard } from '@/components/ui/StatCard';
import { Badge } from '@/components/ui/Badge';
import { PageLoader } from '@/components/ui/Spinner';
import { adminConferenceApi } from '@/lib/api';
import type { ConferenceAdmin } from '@/types';

export default function AdminDashboard() {
  const locale = useLocale();
  const [conferences, setConferences] = useState<ConferenceAdmin[]>([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    adminConferenceApi.list()
      .then((res) => setConferences(res.data))
      .finally(() => setLoading(false));
  }, []);

  const open = conferences.filter((c) => c.status === 'open').length;
  const active = conferences.filter((c) => ['open', 'active'].includes(c.status)).length;

  return (
    <AdminLayout title="Admin Dashboard">
      <div className="p-6 space-y-6">
        <div className="flex items-center justify-between">
          <h2 className="text-xl font-bold text-slate-900">Overview</h2>
          <Link href={`/${locale}/dashboard/admin/conferences/new`} className="btn-primary">
            <Plus className="h-4 w-4" />
            New Conference
          </Link>
        </div>

        <div className="grid grid-cols-1 sm:grid-cols-3 gap-4">
          <StatCard label="Total Conferences" value={conferences.length} icon={Globe} color="indigo" />
          <StatCard label="Open for Submissions" value={open} icon={Globe} color="green" />
          <StatCard label="Active / In Progress" value={active} icon={Globe} color="blue" />
        </div>

        <div className="card">
          <div className="flex items-center justify-between p-5 border-b border-slate-100">
            <h3 className="font-semibold text-slate-800">My Conferences</h3>
            <Link href={`/${locale}/dashboard/admin/conferences`} className="text-sm text-brand-600 hover:underline">
              View all
            </Link>
          </div>
          {loading ? (
            <PageLoader />
          ) : (
            <div className="table-wrapper rounded-none border-none">
              <table className="data-table">
                <thead>
                  <tr>
                    <th>Conference</th>
                    <th>Status</th>
                    <th>Submission Deadline</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  {conferences.map((c) => (
                    <tr key={c.slug}>
                      <td className="font-medium text-slate-800">
                        <Link href={`/${locale}/dashboard/admin/conferences/${c.slug}`} className="hover:text-brand-600">
                          {c.title ?? c.slug}
                        </Link>
                      </td>
                      <td><Badge status={c.status} /></td>
                      <td className="text-slate-500 text-sm">
                        {c.submission_close ? new Date(c.submission_close).toLocaleDateString() : '—'}
                      </td>
                      <td>
                        <Link href={`/${locale}/dashboard/admin/conferences/${c.slug}`} className="text-xs text-brand-600 hover:underline font-medium">
                          Manage
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
    </AdminLayout>
  );
}
