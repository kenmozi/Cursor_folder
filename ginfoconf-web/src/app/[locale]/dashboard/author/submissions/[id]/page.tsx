'use client';

import { useEffect, useState } from 'react';
import { useLocale } from 'next-intl';
import { useSearchParams } from 'next/navigation';
import Link from 'next/link';
import { ArrowLeft, Download, FileText, CheckCircle } from 'lucide-react';
import { DashboardLayout } from '@/components/layout/DashboardLayout';
import { Badge } from '@/components/ui/Badge';
import { PageLoader } from '@/components/ui/Spinner';
import { ErrorAlert } from '@/components/ui/ErrorAlert';
import { submissionApi } from '@/lib/api';
import { formatDate, formatBytes } from '@/lib/utils';
import type { Submission } from '@/types';

const navItems = (locale: string) => [
  { label: 'My Submissions', href: `/${locale}/dashboard/author/submissions`, icon: FileText },
];

export default function SubmissionDetailPage({ params }: { params: { id: string } }) {
  const locale = useLocale();
  const searchParams = useSearchParams();
  const justSubmitted = searchParams.get('submitted') === '1';
  const [submission, setSubmission] = useState<Submission | null>(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState('');

  useEffect(() => {
    submissionApi.get(Number(params.id))
      .then(setSubmission)
      .catch(() => setError('Failed to load submission.'))
      .finally(() => setLoading(false));
  }, [params.id]);

  const handleDownload = async (fileId: number) => {
    if (!submission) return;
    const { url } = await submissionApi.getDownloadUrl(submission.id, fileId);
    window.open(url, '_blank');
  };

  return (
    <DashboardLayout title="Submission Detail" navItems={navItems(locale)}>
      <div className="p-6 space-y-5 max-w-3xl">
        <div className="flex items-center gap-3">
          <Link href={`/${locale}/dashboard/author/submissions`} className="btn-ghost btn-sm">
            <ArrowLeft className="h-4 w-4" />
            Back
          </Link>
        </div>

        {justSubmitted && (
          <div className="card p-5 bg-green-50 border-green-200 flex items-center gap-3">
            <CheckCircle className="h-6 w-6 text-green-600 shrink-0" />
            <div>
              <p className="font-semibold text-green-800">Paper submitted successfully!</p>
              <p className="text-sm text-green-600">You will receive a confirmation email shortly.</p>
            </div>
          </div>
        )}

        {loading ? (
          <PageLoader />
        ) : error ? (
          <ErrorAlert message={error} />
        ) : submission ? (
          <>
            {/* Header */}
            <div className="card p-6">
              <div className="flex items-start justify-between gap-4">
                <div>
                  <Badge status={submission.status} className="mb-2" />
                  <h1 className="text-xl font-bold text-slate-900">{submission.title}</h1>
                  <p className="text-sm text-slate-500 mt-1">#{submission.id} · {submission.conference?.slug}</p>
                </div>
              </div>
            </div>

            {/* Details */}
            <div className="card divide-y divide-slate-100">
              <div className="p-5">
                <p className="text-xs font-semibold uppercase tracking-wide text-slate-400 mb-2">Abstract</p>
                <p className="text-sm text-slate-700 leading-relaxed whitespace-pre-wrap">{submission.abstract}</p>
              </div>

              {submission.keywords && submission.keywords.length > 0 && (
                <div className="p-5">
                  <p className="text-xs font-semibold uppercase tracking-wide text-slate-400 mb-2">Keywords</p>
                  <div className="flex flex-wrap gap-2">
                    {submission.keywords.map((kw, i) => (
                      <span key={i} className="badge bg-slate-100 text-slate-600">{kw}</span>
                    ))}
                  </div>
                </div>
              )}

              {submission.authors && submission.authors.length > 0 && (
                <div className="p-5">
                  <p className="text-xs font-semibold uppercase tracking-wide text-slate-400 mb-2">Authors</p>
                  <div className="space-y-1.5">
                    {submission.authors.map((a, i) => (
                      <div key={i} className="flex items-center gap-2 text-sm text-slate-700">
                        <span className="font-medium">{a.name}</span>
                        {a.affiliation && <span className="text-slate-400">· {a.affiliation}</span>}
                        {a.is_corresponding && (
                          <span className="badge bg-brand-50 text-brand-600 text-xs">Corresponding</span>
                        )}
                      </div>
                    ))}
                  </div>
                </div>
              )}

              {/* Files */}
              {submission.files && submission.files.length > 0 && (
                <div className="p-5">
                  <p className="text-xs font-semibold uppercase tracking-wide text-slate-400 mb-3">Files</p>
                  <div className="space-y-2">
                    {submission.files.map((f) => (
                      <div key={f.id} className="flex items-center justify-between rounded-lg border border-slate-100 bg-slate-50/50 px-4 py-2.5">
                        <div className="flex items-center gap-2.5">
                          <FileText className="h-4 w-4 text-brand-500 shrink-0" />
                          <div>
                            <p className="text-sm font-medium text-slate-700">{f.original_name}</p>
                            <p className="text-xs text-slate-400">{formatBytes(f.size_bytes)} · v{f.version} · {formatDate(f.uploaded_at, 'PP')}</p>
                          </div>
                        </div>
                        <button
                          onClick={() => handleDownload(f.id)}
                          className="btn-ghost btn-sm"
                          title="Download"
                        >
                          <Download className="h-4 w-4" />
                        </button>
                      </div>
                    ))}
                  </div>
                </div>
              )}

              {/* Timeline */}
              <div className="p-5 flex justify-between text-xs text-slate-400">
                <span>Created: {formatDate(submission.created_at, 'PPP')}</span>
                {submission.submitted_at && (
                  <span>Submitted: {formatDate(submission.submitted_at, 'PPP')}</span>
                )}
              </div>
            </div>
          </>
        ) : null}
      </div>
    </DashboardLayout>
  );
}
