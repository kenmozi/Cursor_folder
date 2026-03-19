'use client';

import { useLocale } from 'next-intl';
import { useRouter } from 'next/navigation';
import { useForm } from 'react-hook-form';
import { zodResolver } from '@hookform/resolvers/zod';
import { z } from 'zod';
import { useState } from 'react';
import { ArrowLeft } from 'lucide-react';
import Link from 'next/link';
import { AdminLayout } from '@/components/admin/AdminLayout';
import { FormField, TextAreaField, SelectField } from '@/components/ui/FormField';
import { ErrorAlert } from '@/components/ui/ErrorAlert';
import { Spinner } from '@/components/ui/Spinner';
import { adminConferenceApi } from '@/lib/api';
import { getErrorMessage } from '@/lib/utils';

const schema = z.object({
  title: z.string().min(5, 'Title required'),
  subtitle: z.string().optional(),
  description: z.string().optional(),
  slug: z.string().min(3).regex(/^[a-z0-9-]+$/, 'Slug must be lowercase letters, numbers, and hyphens'),
  blind_mode: z.enum(['none', 'single', 'double']),
  timezone: z.string().min(1, 'Timezone required'),
  location: z.string().optional(),
  website_url: z.string().url().optional().or(z.literal('')),
  submission_open: z.string().optional(),
  submission_close: z.string().optional(),
  review_open: z.string().optional(),
  review_close: z.string().optional(),
  notification_date: z.string().optional(),
  camera_ready_date: z.string().optional(),
});

type FormData = z.infer<typeof schema>;

export default function NewConferencePage() {
  const locale = useLocale();
  const router = useRouter();
  const [error, setError] = useState('');

  const {
    register,
    handleSubmit,
    formState: { errors, isSubmitting },
    watch,
    setValue,
  } = useForm<FormData>({
    resolver: zodResolver(schema),
    defaultValues: { blind_mode: 'double', timezone: 'UTC' },
  });

  // Auto-generate slug from title
  const title = watch('title');

  const onSubmit = async (data: FormData) => {
    try {
      setError('');
      const conf = await adminConferenceApi.create(data);
      router.push(`/${locale}/dashboard/admin/conferences/${conf.slug}`);
    } catch (e) {
      setError(getErrorMessage(e));
    }
  };

  return (
    <AdminLayout title="New Conference">
      <div className="p-6 max-w-2xl space-y-5">
        <div className="flex items-center gap-3">
          <Link href={`/${locale}/dashboard/admin/conferences`} className="btn-ghost btn-sm">
            <ArrowLeft className="h-4 w-4" />
            Back
          </Link>
          <h2 className="text-xl font-bold text-slate-900">New Conference</h2>
        </div>

        {error && <ErrorAlert message={error} />}

        <form onSubmit={handleSubmit(onSubmit)} className="space-y-6">
          {/* Basic info */}
          <div className="card p-6 space-y-4">
            <h3 className="font-semibold text-slate-700">Basic Information</h3>
            <FormField
              label="Conference Title"
              required
              placeholder="International Conference on Computer Science 2025"
              {...register('title')}
              error={errors.title?.message}
            />
            <FormField
              label="URL Slug"
              required
              placeholder="iccs-2025"
              hint="Used in URLs — lowercase, hyphens only. e.g. iccs-2025"
              {...register('slug')}
              error={errors.slug?.message}
            />
            <FormField label="Subtitle" placeholder="5th International Edition" {...register('subtitle')} />
            <TextAreaField
              label="Description"
              rows={4}
              placeholder="Brief conference description..."
              {...register('description')}
            />
          </div>

          {/* Settings */}
          <div className="card p-6 space-y-4">
            <h3 className="font-semibold text-slate-700">Settings</h3>
            <div className="grid grid-cols-2 gap-4">
              <SelectField
                label="Blind Mode"
                required
                options={[
                  { value: 'double', label: 'Double Blind' },
                  { value: 'single', label: 'Single Blind' },
                  { value: 'none', label: 'No Blind' },
                ]}
                {...register('blind_mode')}
                error={errors.blind_mode?.message}
              />
              <FormField label="Timezone" required placeholder="UTC" {...register('timezone')} error={errors.timezone?.message} />
            </div>
            <div className="grid grid-cols-2 gap-4">
              <FormField label="Location" placeholder="Paris, France" {...register('location')} />
              <FormField label="Website URL" type="url" placeholder="https://conf.example.com" {...register('website_url')} error={errors.website_url?.message} />
            </div>
          </div>

          {/* Dates */}
          <div className="card p-6 space-y-4">
            <h3 className="font-semibold text-slate-700">Key Dates</h3>
            <div className="grid grid-cols-2 gap-4">
              <FormField label="Submission Opens" type="date" {...register('submission_open')} />
              <FormField label="Submission Closes" type="date" {...register('submission_close')} />
              <FormField label="Review Opens" type="date" {...register('review_open')} />
              <FormField label="Review Closes" type="date" {...register('review_close')} />
              <FormField label="Notification Date" type="date" {...register('notification_date')} />
              <FormField label="Camera-Ready Date" type="date" {...register('camera_ready_date')} />
            </div>
          </div>

          <button type="submit" disabled={isSubmitting} className="btn-primary btn-lg">
            {isSubmitting ? <Spinner className="h-4 w-4 text-white" /> : 'Create Conference'}
          </button>
        </form>
      </div>
    </AdminLayout>
  );
}
