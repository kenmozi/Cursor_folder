'use client';

import { useEffect, useState } from 'react';
import { useLocale } from 'next-intl';
import { useSearchParams } from 'next/navigation';
import { FileText, ArrowLeft } from 'lucide-react';
import Link from 'next/link';
import { DashboardLayout } from '@/components/layout/DashboardLayout';
import { SubmissionWizard } from '@/components/submission/SubmissionWizard';
import { PageLoader } from '@/components/ui/Spinner';
import { ErrorAlert } from '@/components/ui/ErrorAlert';
import { publicApi } from '@/lib/api';
import type { ConferencePublic } from '@/types';

const navItems = (locale: string) => [
  { label: 'My Submissions', href: `/${locale}/dashboard/author/submissions`, icon: FileText },
];

export default function NewSubmissionPage() {
  const locale = useLocale();
  const searchParams = useSearchParams();
  const conferenceSlug = searchParams.get('conference') ?? '';

  const [conference, setConference] = useState<ConferencePublic | null>(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState('');

  useEffect(() => {
    if (!conferenceSlug) {
      setError('No conference selected. Please go back and choose a conference.');
      setLoading(false);
      return;
    }
    publicApi.getConference(conferenceSlug)
      .then(setConference)
      .catch(() => setError('Conference not found.'))
      .finally(() => setLoading(false));
  }, [conferenceSlug]);

  return (
    <DashboardLayout title="New Submission" navItems={navItems(locale)}>
      <div className="p-6">
        <div className="mb-6 flex items-center gap-3">
          <Link href={`/${locale}/dashboard/author/submissions`} className="btn-ghost btn-sm">
            <ArrowLeft className="h-4 w-4" />
            Back
          </Link>
          <div>
            <h2 className="text-xl font-bold text-slate-900">New Submission</h2>
            {conference && (
              <p className="text-sm text-slate-500 mt-0.5">{conference.title ?? conference.slug}</p>
            )}
          </div>
        </div>

        {loading ? (
          <PageLoader />
        ) : error ? (
          <ErrorAlert message={error} />
        ) : conference ? (
          <SubmissionWizard conference={conference} />
        ) : null}
      </div>
    </DashboardLayout>
  );
}
