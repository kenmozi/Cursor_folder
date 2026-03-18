'use client';

import { useEffect, useState } from 'react';
import { useLocale } from 'next-intl';
import { useForm } from 'react-hook-form';
import { Calendar, Plus, Trash2 } from 'lucide-react';
import { AdminLayout } from '@/components/admin/AdminLayout';
import { FormField } from '@/components/ui/FormField';
import { PageLoader } from '@/components/ui/Spinner';
import { ErrorAlert } from '@/components/ui/ErrorAlert';
import { Spinner } from '@/components/ui/Spinner';
import { adminConferenceApi } from '@/lib/api';
import { getErrorMessage, formatDate } from '@/lib/utils';
import type { ConferenceAdmin } from '@/types';

interface DateForm {
  submission_open: string;
  submission_close: string;
  review_open: string;
  review_close: string;
  notification_date: string;
  camera_ready_date: string;
}

export default function DatesPage({ params }: { params: { id: string } }) {
  const locale = useLocale();
  const slug = params.id;
  const [conference, setConference] = useState<ConferenceAdmin | null>(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState('');
  const [success, setSuccess] = useState('');

  const { register, handleSubmit, reset, formState: { isSubmitting } } = useForm<DateForm>();

  useEffect(() => {
    adminConferenceApi.get(slug).then((c) => {
      setConference(c);
      reset({
        submission_open: c.submission_open?.substring(0, 10) ?? '',
        submission_close: c.submission_close?.substring(0, 10) ?? '',
        review_open: c.review_open?.substring(0, 10) ?? '',
        review_close: c.review_close?.substring(0, 10) ?? '',
        notification_date: c.notification_date?.substring(0, 10) ?? '',
        camera_ready_date: c.camera_ready_date?.substring(0, 10) ?? '',
      });
    }).finally(() => setLoading(false));
  }, [slug]);

  const onSave = async (data: DateForm) => {
    try {
      setError('');
      setSuccess('');
      await adminConferenceApi.update(slug, data);
      setSuccess('Dates updated successfully.');
    } catch (e) {
      setError(getErrorMessage(e));
    }
  };

  return (
    <AdminLayout title="Important Dates" conferenceSlug={slug}>
      <div className="p-6 max-w-2xl space-y-5">
        <h2 className="text-xl font-bold text-slate-900">Important Dates</h2>

        {error && <ErrorAlert message={error} />}
        {success && (
          <div className="rounded-lg bg-green-50 border border-green-200 p-3 text-sm text-green-700">{success}</div>
        )}

        {loading ? (
          <PageLoader />
        ) : (
          <form onSubmit={handleSubmit(onSave)} className="card p-6 space-y-4">
            <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <FormField label="Submission Opens" type="date" {...register('submission_open')} />
              <FormField label="Submission Closes" type="date" {...register('submission_close')} />
              <FormField label="Review Opens" type="date" {...register('review_open')} />
              <FormField label="Review Closes" type="date" {...register('review_close')} />
              <FormField label="Author Notification" type="date" {...register('notification_date')} />
              <FormField label="Camera-Ready Deadline" type="date" {...register('camera_ready_date')} />
            </div>
            <button type="submit" disabled={isSubmitting} className="btn-primary">
              {isSubmitting ? <Spinner className="h-4 w-4 text-white" /> : <>
                <Calendar className="h-4 w-4" />
                Save Dates
              </>}
            </button>
          </form>
        )}
      </div>
    </AdminLayout>
  );
}
