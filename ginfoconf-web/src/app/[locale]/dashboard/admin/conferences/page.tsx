'use client';

import { useEffect, useState } from 'react';
import { useLocale } from 'next-intl';
import Link from 'next/link';
import { Globe, Plus } from 'lucide-react';
import { AdminLayout } from '@/components/admin/AdminLayout';
import { Badge } from '@/components/ui/Badge';
import { PageLoader } from '@/components/ui/Spinner';
import { EmptyState } from '@/components/ui/EmptyState';
import { adminConferenceApi } from '@/lib/api';
import { formatDate } from '@/lib/utils';
import type { ConferenceAdmin } from '@/types';

export default function AdminConferencesPage() {
  const locale = useLocale();
  const [conferences, setConferences] = useState<ConferenceAdmin[]>([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    adminConferenceApi.list().then((res) => setConferences(res.data)).finally(() => setLoading(false));
  }, []);

  return (
    <AdminLayout title="Conferences">
      <div className="p-6 space-y-5">
        <div className="flex items-center justify-between">
          <h2 className="text-xl font-bold text-slate-900">Conferences</h2>
          <Link href={`/${locale}/dashboard/admin/conferences/new`} className="btn-primary">
            <Plus className="h-4 w-4" />
            New Conference
          </Link>
        </div>

        <div className="card">
          {loading ? (
            <PageLoader />
          ) : conferences.length === 0 ? (
            <EmptyState
              icon={Globe}
              title="No conferences yet"
              action={<Link href={`/${locale}/dashboard/admin/conferences/new`} className="btn-primary"><Plus className="h-4 w-4" /> New Conference</Link>}
            />
          ) : (
            <div className="table-wrapper rounded-none border-none">
              <table className="data-table">
                <thead>
                  <tr>
                    <th>Conference</th>
                    <th>Status</th>
                    <th>Blind Mode</th>
                    <th>Submission Window</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  {conferences.map((c) => (
                    <tr key={c.slug}>
                      <td>
                        <div>
                          <p className="font-medium text-slate-800">{c.title ?? c.slug}</p>
                          <p className="text-xs text-slate-400">{c.slug}</p>
                        </div>
                      </td>
                      <td><Badge status={c.status} /></td>
                      <td className="text-slate-500 text-sm capitalize">{c.blind_mode}</td>
                      <td className="text-slate-500 text-xs">
                        {formatDate(c.submission_open, 'PP')} → {formatDate(c.submission_close, 'PP')}
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
