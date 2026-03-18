'use client';

import { useEffect, useState } from 'react';
import { useLocale } from 'next-intl';
import Link from 'next/link';
import { ExternalLink, Image, Calendar, Users, FileText, GitBranch, UserCheck } from 'lucide-react';
import { AdminLayout } from '@/components/admin/AdminLayout';
import { Badge } from '@/components/ui/Badge';
import { PageLoader } from '@/components/ui/Spinner';
import { adminConferenceApi } from '@/lib/api';
import { formatDate } from '@/lib/utils';
import type { ConferenceAdmin } from '@/types';

const QUICK_LINKS = (locale: string, slug: string) => [
  { label: 'Branding', href: `/${locale}/dashboard/admin/conferences/${slug}/branding`, icon: Image, desc: 'Upload logo and cover image' },
  { label: 'Dates', href: `/${locale}/dashboard/admin/conferences/${slug}/dates`, icon: Calendar, desc: 'Manage important dates' },
  { label: 'Committee', href: `/${locale}/dashboard/admin/conferences/${slug}/committee`, icon: UserCheck, desc: 'Manage program committee' },
  { label: 'Reviewers', href: `/${locale}/dashboard/admin/conferences/${slug}/reviewers`, icon: Users, desc: 'Invite and manage reviewers' },
  { label: 'Submissions', href: `/${locale}/dashboard/admin/conferences/${slug}/submissions`, icon: FileText, desc: 'View all submissions' },
  { label: 'Assignments', href: `/${locale}/dashboard/admin/conferences/${slug}/assignments`, icon: GitBranch, desc: 'Assign reviewers to papers' },
];

export default function AdminConferenceOverviewPage({ params }: { params: { id: string } }) {
  const locale = useLocale();
  const slug = params.id;
  const [conference, setConference] = useState<ConferenceAdmin | null>(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    adminConferenceApi.get(slug).then(setConference).finally(() => setLoading(false));
  }, [slug]);

  if (loading) return <AdminLayout title="Conference" conferenceSlug={slug}><PageLoader /></AdminLayout>;
  if (!conference) return <AdminLayout title="Not Found" conferenceSlug={slug}><p className="p-6 text-slate-500">Conference not found.</p></AdminLayout>;

  return (
    <AdminLayout title={conference.title ?? slug} conferenceSlug={slug}>
      <div className="p-6 space-y-6">
        {/* Header */}
        <div className="card p-6">
          <div className="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
            <div>
              <div className="flex items-center gap-3 mb-2">
                <Badge status={conference.status} />
                {conference.is_submission_open && (
                  <span className="badge bg-green-100 text-green-700">Accepting Submissions</span>
                )}
              </div>
              <h1 className="text-2xl font-bold text-slate-900">{conference.title ?? slug}</h1>
              {conference.subtitle && <p className="text-slate-500 mt-0.5">{conference.subtitle}</p>}
              <p className="text-xs text-slate-400 mt-2">/{slug} · {conference.blind_mode} blind · {conference.timezone}</p>
            </div>
            <div className="flex gap-2 shrink-0">
              <Link href={`/${locale}/conference/${slug}`} target="_blank" className="btn-secondary btn-sm">
                <ExternalLink className="h-3.5 w-3.5" />
                View Public Page
              </Link>
            </div>
          </div>
        </div>

        {/* Key dates */}
        <div className="card p-5">
          <h3 className="font-semibold text-slate-800 mb-4">Key Dates</h3>
          <div className="grid grid-cols-2 md:grid-cols-3 gap-4 text-sm">
            {[
              { label: 'Submission Open', date: conference.submission_open },
              { label: 'Submission Close', date: conference.submission_close },
              { label: 'Review Open', date: conference.review_open },
              { label: 'Review Close', date: conference.review_close },
              { label: 'Notification', date: conference.notification_date },
              { label: 'Camera Ready', date: conference.camera_ready_date },
            ].map((d) => (
              <div key={d.label}>
                <p className="text-xs text-slate-400 mb-0.5">{d.label}</p>
                <p className="font-medium text-slate-700">{formatDate(d.date, 'PP')}</p>
              </div>
            ))}
          </div>
        </div>

        {/* Quick links */}
        <div>
          <h3 className="font-semibold text-slate-700 mb-3">Manage</h3>
          <div className="grid grid-cols-2 md:grid-cols-3 gap-3">
            {QUICK_LINKS(locale, slug).map((link) => (
              <Link key={link.href} href={link.href} className="card-hover p-4 group flex items-center gap-3">
                <div className="flex h-10 w-10 items-center justify-center rounded-lg bg-brand-50 text-brand-600 group-hover:bg-brand-600 group-hover:text-white transition-colors shrink-0">
                  <link.icon className="h-5 w-5" />
                </div>
                <div>
                  <p className="font-medium text-slate-800 text-sm">{link.label}</p>
                  <p className="text-xs text-slate-400">{link.desc}</p>
                </div>
              </Link>
            ))}
          </div>
        </div>
      </div>
    </AdminLayout>
  );
}
