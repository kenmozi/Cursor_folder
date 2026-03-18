'use client';

import { useEffect, useState } from 'react';
import { useLocale } from 'next-intl';
import { useRouter } from 'next/navigation';
import Link from 'next/link';
import { useForm } from 'react-hook-form';
import { zodResolver } from '@hookform/resolvers/zod';
import { z } from 'zod';
import { ArrowLeft, ClipboardList, ThumbsUp, ThumbsDown, Check } from 'lucide-react';
import { DashboardLayout } from '@/components/layout/DashboardLayout';
import { Badge } from '@/components/ui/Badge';
import { FormField, TextAreaField, SelectField } from '@/components/ui/FormField';
import { PageLoader } from '@/components/ui/Spinner';
import { ErrorAlert } from '@/components/ui/ErrorAlert';
import { Spinner } from '@/components/ui/Spinner';
import { reviewApi, submissionApi } from '@/lib/api';
import { formatDate, getErrorMessage, RECOMMENDATION_LABELS } from '@/lib/utils';
import type { ReviewAssignment } from '@/types';

const reviewSchema = z.object({
  overall_score: z.number().min(1).max(10),
  recommendation: z.string().min(1, 'Select a recommendation'),
  comments_to_authors: z.string().min(20, 'Provide at least 20 characters of feedback'),
  comments_to_chair: z.string().optional(),
});

type ReviewForm = z.infer<typeof reviewSchema>;

const navItems = (locale: string) => [
  { label: 'My Assignments', href: `/${locale}/dashboard/reviewer`, icon: ClipboardList },
];

export default function AssignmentPage({ params }: { params: { id: string } }) {
  const locale = useLocale();
  const router = useRouter();
  const [assignment, setAssignment] = useState<ReviewAssignment | null>(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState('');
  const [responding, setResponding] = useState(false);
  const [saving, setSaving] = useState(false);

  useEffect(() => {
    reviewApi.getAssignment(Number(params.id))
      .then(setAssignment)
      .catch(() => setError('Assignment not found.'))
      .finally(() => setLoading(false));
  }, [params.id]);

  const { register, handleSubmit, watch, setValue, formState: { errors } } = useForm<ReviewForm>({
    resolver: zodResolver(reviewSchema),
    defaultValues: {
      overall_score: assignment?.review?.overall_score ?? 5,
      recommendation: assignment?.review?.recommendation ?? '',
      comments_to_authors: assignment?.review?.comments_to_authors ?? '',
      comments_to_chair: assignment?.review?.comments_to_chair ?? '',
    },
  });

  const score = watch('overall_score');

  const handleRespond = async (action: 'accept' | 'decline') => {
    if (!assignment) return;
    try {
      setResponding(true);
      setError('');
      const updated = await reviewApi.respond(assignment.id, action);
      setAssignment(updated);
    } catch (e) {
      setError(getErrorMessage(e));
    } finally {
      setResponding(false);
    }
  };

  const onSave = async (data: ReviewForm, submit = false) => {
    if (!assignment) return;
    try {
      setSaving(true);
      setError('');
      await reviewApi.saveReview(assignment.id, data, submit);
      if (submit) {
        router.push(`/${locale}/dashboard/reviewer`);
      } else {
        const updated = await reviewApi.getAssignment(assignment.id);
        setAssignment(updated);
      }
    } catch (e) {
      setError(getErrorMessage(e));
    } finally {
      setSaving(false);
    }
  };

  const recommendationOptions = Object.entries(RECOMMENDATION_LABELS).map(([value, label]) => ({
    value,
    label,
  }));

  return (
    <DashboardLayout title="Assignment" navItems={navItems(locale)}>
      <div className="p-6 max-w-3xl space-y-5">
        <div className="flex items-center gap-3">
          <Link href={`/${locale}/dashboard/reviewer`} className="btn-ghost btn-sm">
            <ArrowLeft className="h-4 w-4" />
            Back
          </Link>
        </div>

        {error && <ErrorAlert message={error} />}

        {loading ? (
          <PageLoader />
        ) : !assignment ? null : (
          <>
            {/* Paper info */}
            <div className="card p-5">
              <div className="flex items-start justify-between gap-3 mb-3">
                <h2 className="font-bold text-slate-900 text-lg">
                  {assignment.submission?.title ?? 'Paper'}
                </h2>
                <Badge status={assignment.status} />
              </div>
              <p className="text-sm text-slate-500">
                Conference: <strong>{assignment.submission?.conference?.slug}</strong>
                {assignment.due_date && (
                  <> · Due: <strong>{formatDate(assignment.due_date, 'PPP')}</strong></>
                )}
              </p>
              {assignment.submission?.abstract && (
                <div className="mt-4">
                  <p className="text-xs font-semibold uppercase tracking-wide text-slate-400 mb-1">Abstract</p>
                  <p className="text-sm text-slate-600 leading-relaxed whitespace-pre-wrap line-clamp-6">
                    {assignment.submission.abstract}
                  </p>
                </div>
              )}
            </div>

            {/* Accept / Decline */}
            {assignment.status === 'pending' && (
              <div className="card p-5">
                <h3 className="font-semibold text-slate-800 mb-3">Do you accept this review assignment?</h3>
                <div className="flex gap-3">
                  <button
                    onClick={() => handleRespond('accept')}
                    disabled={responding}
                    className="btn-primary flex-1 justify-center"
                  >
                    {responding ? <Spinner className="h-4 w-4 text-white" /> : <><ThumbsUp className="h-4 w-4" /> Accept</>}
                  </button>
                  <button
                    onClick={() => handleRespond('decline')}
                    disabled={responding}
                    className="btn-secondary flex-1 justify-center"
                  >
                    <ThumbsDown className="h-4 w-4" />
                    Decline
                  </button>
                </div>
              </div>
            )}

            {/* Review form */}
            {(assignment.status === 'accepted' || assignment.review) && (
              <form className="card p-6 space-y-5">
                <h3 className="font-bold text-slate-800 text-base">
                  {assignment.review?.status === 'submitted' ? 'Submitted Review' : 'Write Your Review'}
                </h3>

                {assignment.review?.status === 'submitted' && (
                  <div className="flex items-center gap-2 rounded-lg bg-green-50 border border-green-200 px-4 py-2.5 text-sm text-green-700">
                    <Check className="h-4 w-4 shrink-0" />
                    Review submitted successfully.
                  </div>
                )}

                {/* Score slider */}
                <div>
                  <label className="label">Overall Score: <span className="text-brand-600 font-bold">{score}</span> / 10</label>
                  <input
                    type="range"
                    min={1}
                    max={10}
                    step={1}
                    disabled={assignment.review?.status === 'submitted'}
                    {...register('overall_score', { valueAsNumber: true })}
                    className="w-full accent-brand-600 disabled:opacity-50"
                  />
                  <div className="flex justify-between text-xs text-slate-400 mt-1">
                    <span>1 – Strong Reject</span>
                    <span>5 – Borderline</span>
                    <span>10 – Strong Accept</span>
                  </div>
                </div>

                <SelectField
                  label="Recommendation"
                  required
                  options={recommendationOptions}
                  placeholder="Select recommendation..."
                  disabled={assignment.review?.status === 'submitted'}
                  {...register('recommendation')}
                  error={errors.recommendation?.message}
                />

                <TextAreaField
                  label="Comments to Authors"
                  required
                  rows={6}
                  disabled={assignment.review?.status === 'submitted'}
                  hint="Your detailed review comments. These will be shared with the authors."
                  {...register('comments_to_authors')}
                  error={errors.comments_to_authors?.message}
                />

                <TextAreaField
                  label="Confidential Comments to Chairs"
                  rows={3}
                  disabled={assignment.review?.status === 'submitted'}
                  hint="Optional. Not shared with authors."
                  {...register('comments_to_chair')}
                />

                {assignment.review?.status !== 'submitted' && (
                  <div className="flex justify-between gap-3 pt-2">
                    <button
                      type="button"
                      onClick={handleSubmit((d) => onSave(d, false))}
                      disabled={saving}
                      className="btn-secondary"
                    >
                      {saving ? <Spinner className="h-4 w-4" /> : 'Save Draft'}
                    </button>
                    <button
                      type="button"
                      onClick={handleSubmit((d) => onSave(d, true))}
                      disabled={saving}
                      className="btn-primary btn-lg"
                    >
                      {saving ? <Spinner className="h-4 w-4 text-white" /> : <>Submit Review <Check className="h-4 w-4" /></>}
                    </button>
                  </div>
                )}
              </form>
            )}
          </>
        )}
      </div>
    </DashboardLayout>
  );
}
