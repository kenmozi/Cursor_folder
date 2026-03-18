'use client';

import { useState, useCallback } from 'react';
import { useLocale, useTranslations } from 'next-intl';
import { useRouter } from 'next/navigation';
import { useForm, useFieldArray } from 'react-hook-form';
import { zodResolver } from '@hookform/resolvers/zod';
import { z } from 'zod';
import { Check, ChevronRight, Plus, Trash2, Upload } from 'lucide-react';
import { submissionApi } from '@/lib/api';
import { FormField, TextAreaField } from '@/components/ui/FormField';
import { ErrorAlert } from '@/components/ui/ErrorAlert';
import { FileUploadZone } from '@/components/submission/FileUploadZone';
import { Spinner } from '@/components/ui/Spinner';
import { Badge } from '@/components/ui/Badge';
import { cn, getErrorMessage, formatDate, formatBytes } from '@/lib/utils';
import type { ConferencePublic, Submission } from '@/types';

// ── Schemas ────────────────────────────────────────────────────────────────

const metadataSchema = z.object({
  title: z.string().min(5, 'Title must be at least 5 characters'),
  abstract: z.string().min(100, 'Abstract must be at least 100 characters'),
  keywords: z.string().min(2, 'Enter at least one keyword'),
  track_id: z.string().optional(),
});

const authorsSchema = z.object({
  authors: z.array(z.object({
    name: z.string().min(2, 'Name required'),
    email: z.string().email('Valid email required'),
    affiliation: z.string().optional(),
    is_corresponding: z.boolean().default(false),
  })).min(1, 'At least one author is required'),
});

type MetadataForm = z.infer<typeof metadataSchema>;
type AuthorsForm = z.infer<typeof authorsSchema>;

const STEPS = ['step_metadata', 'step_authors', 'step_files', 'step_review'] as const;

interface SubmissionWizardProps {
  conference: ConferencePublic;
  initialSubmission?: Submission;
}

export function SubmissionWizard({ conference, initialSubmission }: SubmissionWizardProps) {
  const t = useTranslations('submission');
  const locale = useLocale();
  const router = useRouter();

  const [step, setStep] = useState(0);
  const [submission, setSubmission] = useState<Submission | null>(initialSubmission ?? null);
  const [error, setError] = useState('');
  const [saving, setSaving] = useState(false);
  const [uploadProgress, setUploadProgress] = useState(0);
  const [uploading, setUploading] = useState(false);
  const [submitting, setSubmitting] = useState(false);

  // ── Step 1: Metadata ────────────────────────────────────────────────────

  const metaForm = useForm<MetadataForm>({
    resolver: zodResolver(metadataSchema),
    defaultValues: {
      title: submission?.title ?? '',
      abstract: submission?.abstract ?? '',
      keywords: submission?.keywords?.join(', ') ?? '',
    },
  });

  const saveMetadata = async (data: MetadataForm) => {
    try {
      setSaving(true);
      setError('');
      const payload = {
        ...data,
        conference_slug: conference.slug,
        keywords: data.keywords.split(',').map((k) => k.trim()).filter(Boolean),
      };

      const saved = submission
        ? await submissionApi.update(submission.id, payload)
        : await submissionApi.create(payload);

      setSubmission(saved);
      setStep(1);
    } catch (e) {
      setError(getErrorMessage(e));
    } finally {
      setSaving(false);
    }
  };

  // ── Step 2: Authors ─────────────────────────────────────────────────────

  const authorsForm = useForm<AuthorsForm>({
    resolver: zodResolver(authorsSchema),
    defaultValues: {
      authors: submission?.authors?.length
        ? submission.authors.map((a) => ({
            name: a.name,
            email: a.email,
            affiliation: a.affiliation ?? '',
            is_corresponding: a.is_corresponding,
          }))
        : [{ name: '', email: '', affiliation: '', is_corresponding: true }],
    },
  });

  const { fields, append, remove } = useFieldArray({
    control: authorsForm.control,
    name: 'authors',
  });

  const saveAuthors = async (data: AuthorsForm) => {
    if (!submission) return;
    try {
      setSaving(true);
      setError('');
      const saved = await submissionApi.update(submission.id, { authors: data.authors });
      setSubmission(saved);
      setStep(2);
    } catch (e) {
      setError(getErrorMessage(e));
    } finally {
      setSaving(false);
    }
  };

  // ── Step 3: File Upload ──────────────────────────────────────────────────

  const handleManuscriptUpload = async (file: File) => {
    if (!submission) return;
    try {
      setUploading(true);
      setError('');
      const saved = await submissionApi.uploadManuscript(submission.id, file, setUploadProgress);
      // Refresh submission to show new file
      const refreshed = await submissionApi.get(submission.id);
      setSubmission(refreshed);
    } catch (e) {
      setError(getErrorMessage(e));
    } finally {
      setUploading(false);
      setUploadProgress(0);
    }
  };

  // ── Step 4: Review & Submit ──────────────────────────────────────────────

  const handleSubmit = async () => {
    if (!submission) return;
    try {
      setSubmitting(true);
      setError('');
      const submitted = await submissionApi.submit(submission.id);
      router.push(`/${locale}/dashboard/author/submissions/${submitted.id}?submitted=1`);
    } catch (e) {
      setError(getErrorMessage(e));
    } finally {
      setSubmitting(false);
    }
  };

  const activeManuscript = submission?.files?.find((f) => f.type === 'manuscript' && f.is_active);

  // ── Render ───────────────────────────────────────────────────────────────

  return (
    <div className="max-w-2xl mx-auto">
      {/* Stepper */}
      <nav className="flex items-center mb-8 gap-0">
        {STEPS.map((s, i) => (
          <div key={s} className="flex items-center flex-1 min-w-0">
            <button
              onClick={() => i < step && setStep(i)}
              className={cn(
                'flex items-center gap-2 text-sm font-medium transition-colors',
                i === step ? 'text-brand-700' : i < step ? 'text-green-600 cursor-pointer' : 'text-slate-400 cursor-default'
              )}
            >
              <span className={cn(
                'flex h-7 w-7 shrink-0 items-center justify-center rounded-full border-2 text-xs font-bold',
                i === step ? 'border-brand-600 bg-brand-600 text-white' :
                i < step ? 'border-green-500 bg-green-50 text-green-600' :
                'border-slate-200 bg-white text-slate-400'
              )}>
                {i < step ? <Check className="h-3.5 w-3.5" /> : i + 1}
              </span>
              <span className="hidden sm:block">{t(s)}</span>
            </button>
            {i < STEPS.length - 1 && (
              <div className={cn('flex-1 h-0.5 mx-2', i < step ? 'bg-green-300' : 'bg-slate-200')} />
            )}
          </div>
        ))}
      </nav>

      {error && <ErrorAlert message={error} className="mb-5" onRetry={() => setError('')} />}

      {/* ── Step 0: Metadata ── */}
      {step === 0 && (
        <form onSubmit={metaForm.handleSubmit(saveMetadata)} className="card p-6 space-y-5">
          <h2 className="font-bold text-slate-800 text-lg">{t('step_metadata')}</h2>
          <FormField
            label={t('title')}
            required
            {...metaForm.register('title')}
            error={metaForm.formState.errors.title?.message}
          />
          <TextAreaField
            label={t('abstract')}
            required
            rows={6}
            {...metaForm.register('abstract')}
            error={metaForm.formState.errors.abstract?.message}
            hint="Minimum 100 characters. For double-blind conferences, ensure no author-identifying information is present."
          />
          <FormField
            label={t('keywords')}
            required
            placeholder="machine learning, deep learning, NLP"
            hint={t('keywords_hint')}
            {...metaForm.register('keywords')}
            error={metaForm.formState.errors.keywords?.message}
          />
          <div className="flex justify-end gap-3 pt-2">
            <button type="button" onClick={() => metaForm.handleSubmit(async (d) => { await saveMetadata(d); setStep(0); })()} className="btn-secondary btn-sm">
              {t('save_draft')}
            </button>
            <button type="submit" disabled={saving} className="btn-primary">
              {saving ? <Spinner className="h-4 w-4 text-white" /> : <>{t('next')} <ChevronRight className="h-4 w-4" /></>}
            </button>
          </div>
        </form>
      )}

      {/* ── Step 1: Authors ── */}
      {step === 1 && (
        <form onSubmit={authorsForm.handleSubmit(saveAuthors)} className="card p-6 space-y-5">
          <h2 className="font-bold text-slate-800 text-lg">{t('step_authors')}</h2>
          <div className="space-y-4">
            {fields.map((field, i) => (
              <div key={field.id} className="rounded-xl border border-slate-200 p-4 space-y-3">
                <div className="flex items-center justify-between">
                  <h4 className="text-sm font-semibold text-slate-700">
                    {i === 0 ? 'Submitting Author' : `Author ${i + 1}`}
                  </h4>
                  {i > 0 && (
                    <button type="button" onClick={() => remove(i)} className="btn-ghost btn-sm text-red-500 p-1">
                      <Trash2 className="h-4 w-4" />
                    </button>
                  )}
                </div>
                <div className="grid grid-cols-2 gap-3">
                  <FormField
                    label="Name"
                    required
                    {...authorsForm.register(`authors.${i}.name`)}
                    error={authorsForm.formState.errors.authors?.[i]?.name?.message}
                  />
                  <FormField
                    label="Email"
                    type="email"
                    required
                    {...authorsForm.register(`authors.${i}.email`)}
                    error={authorsForm.formState.errors.authors?.[i]?.email?.message}
                  />
                </div>
                <FormField
                  label="Affiliation"
                  placeholder="University / Institution"
                  {...authorsForm.register(`authors.${i}.affiliation`)}
                />
                <label className="flex items-center gap-2 cursor-pointer">
                  <input type="checkbox" className="rounded" {...authorsForm.register(`authors.${i}.is_corresponding`)} />
                  <span className="text-sm text-slate-600">Corresponding author</span>
                </label>
              </div>
            ))}
          </div>
          <button
            type="button"
            onClick={() => append({ name: '', email: '', affiliation: '', is_corresponding: false })}
            className="btn-secondary btn-sm w-full justify-center"
          >
            <Plus className="h-4 w-4" />
            {t('add_author')}
          </button>

          <div className="flex justify-between gap-3 pt-2">
            <button type="button" onClick={() => setStep(0)} className="btn-secondary">{t('back')}</button>
            <button type="submit" disabled={saving} className="btn-primary">
              {saving ? <Spinner className="h-4 w-4 text-white" /> : <>{t('next')} <ChevronRight className="h-4 w-4" /></>}
            </button>
          </div>
        </form>
      )}

      {/* ── Step 2: Files ── */}
      {step === 2 && (
        <div className="card p-6 space-y-5">
          <h2 className="font-bold text-slate-800 text-lg">{t('step_files')}</h2>

          {/* Current manuscript */}
          {activeManuscript ? (
            <div className="rounded-xl border border-green-200 bg-green-50 p-4 flex items-center gap-3">
              <Check className="h-5 w-5 text-green-600 shrink-0" />
              <div className="flex-1 min-w-0">
                <p className="text-sm font-medium text-green-800 truncate">{activeManuscript.original_name}</p>
                <p className="text-xs text-green-600">{formatBytes(activeManuscript.size_bytes)} · v{activeManuscript.version}</p>
              </div>
              <span className="text-xs text-green-600">Replace:</span>
            </div>
          ) : null}

          <FileUploadZone
            label={t('manuscript')}
            accept={{ 'application/pdf': ['.pdf'] }}
            maxSize={50 * 1024 * 1024}
            progress={uploadProgress}
            uploading={uploading}
            onFile={handleManuscriptUpload}
            hint="PDF only, max 50 MB. Ensure author information is removed for double-blind review."
          />

          <div className="flex justify-between gap-3 pt-2">
            <button type="button" onClick={() => setStep(1)} className="btn-secondary">{t('back')}</button>
            <button
              type="button"
              onClick={() => setStep(3)}
              disabled={!activeManuscript}
              className="btn-primary"
            >
              {t('next')} <ChevronRight className="h-4 w-4" />
            </button>
          </div>
        </div>
      )}

      {/* ── Step 3: Review & Submit ── */}
      {step === 3 && submission && (
        <div className="card p-6 space-y-6">
          <h2 className="font-bold text-slate-800 text-lg">{t('step_review')}</h2>

          <div className="rounded-xl border border-slate-100 divide-y divide-slate-100">
            <div className="p-4">
              <p className="text-xs font-semibold uppercase tracking-wide text-slate-400 mb-1">Title</p>
              <p className="text-slate-800 font-medium">{submission.title}</p>
            </div>
            <div className="p-4">
              <p className="text-xs font-semibold uppercase tracking-wide text-slate-400 mb-1">Abstract</p>
              <p className="text-sm text-slate-600 line-clamp-4">{submission.abstract}</p>
            </div>
            <div className="p-4">
              <p className="text-xs font-semibold uppercase tracking-wide text-slate-400 mb-1">Authors</p>
              <p className="text-sm text-slate-600">
                {submission.authors?.map((a) => a.name).join(', ')}
              </p>
            </div>
            <div className="p-4">
              <p className="text-xs font-semibold uppercase tracking-wide text-slate-400 mb-1">Manuscript</p>
              {activeManuscript ? (
                <p className="text-sm text-slate-600">{activeManuscript.original_name} ({formatBytes(activeManuscript.size_bytes)})</p>
              ) : (
                <p className="text-sm text-red-500">No manuscript uploaded</p>
              )}
            </div>
          </div>

          <div className="rounded-xl bg-amber-50 border border-amber-200 p-4 text-sm text-amber-700">
            <strong>Please review carefully.</strong> Once submitted, your paper cannot be edited. You may withdraw and resubmit during the submission window.
          </div>

          <div className="flex justify-between gap-3">
            <button type="button" onClick={() => setStep(2)} className="btn-secondary">{t('back')}</button>
            <button
              type="button"
              onClick={handleSubmit}
              disabled={submitting || !activeManuscript}
              className="btn-primary btn-lg"
            >
              {submitting ? <Spinner className="h-4 w-4 text-white" /> : t('submit_btn')}
            </button>
          </div>
        </div>
      )}
    </div>
  );
}
