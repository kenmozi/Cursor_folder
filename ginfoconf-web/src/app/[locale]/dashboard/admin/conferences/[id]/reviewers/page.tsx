'use client';

import { useEffect, useState } from 'react';
import { useLocale } from 'next-intl';
import { useForm } from 'react-hook-form';
import { Mail, Users, X } from 'lucide-react';
import { AdminLayout } from '@/components/admin/AdminLayout';
import { FormField, TextAreaField } from '@/components/ui/FormField';
import { Badge } from '@/components/ui/Badge';
import { PageLoader } from '@/components/ui/Spinner';
import { EmptyState } from '@/components/ui/EmptyState';
import { ErrorAlert } from '@/components/ui/ErrorAlert';
import { Spinner } from '@/components/ui/Spinner';
import { adminConferenceApi } from '@/lib/api';
import { formatDate, getErrorMessage } from '@/lib/utils';
import type { ReviewerInvitation } from '@/types';

interface InviteForm {
  email: string;
  message: string;
}

export default function ReviewersPage({ params }: { params: { id: string } }) {
  const locale = useLocale();
  const slug = params.id;
  const [invitations, setInvitations] = useState<ReviewerInvitation[]>([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState('');
  const [success, setSuccess] = useState('');

  const { register, handleSubmit, reset, formState: { isSubmitting, errors } } = useForm<InviteForm>();

  const loadInvitations = () => {
    adminConferenceApi.listInvitations(slug)
      .then((res) => setInvitations(res.data))
      .finally(() => setLoading(false));
  };

  useEffect(() => { loadInvitations(); }, [slug]);

  const onInvite = async (data: InviteForm) => {
    try {
      setError('');
      setSuccess('');
      await adminConferenceApi.invite(slug, data);
      reset();
      setSuccess(`Invitation sent to ${data.email}`);
      loadInvitations();
    } catch (e) {
      setError(getErrorMessage(e));
    }
  };

  const handleCancel = async (invitationId: number) => {
    try {
      await adminConferenceApi.cancelInvitation(slug, invitationId);
      loadInvitations();
    } catch (e) {
      setError(getErrorMessage(e));
    }
  };

  return (
    <AdminLayout title="Reviewers" conferenceSlug={slug}>
      <div className="p-6 space-y-6 max-w-3xl">
        <h2 className="text-xl font-bold text-slate-900">Reviewer Invitations</h2>

        {/* Invite form */}
        <div className="card p-6 space-y-4">
          <h3 className="font-semibold text-slate-800">Invite a Reviewer</h3>
          {error && <ErrorAlert message={error} />}
          {success && <div className="rounded-lg bg-green-50 border border-green-200 p-3 text-sm text-green-700">{success}</div>}

          <form onSubmit={handleSubmit(onInvite)} className="space-y-4">
            <FormField
              label="Reviewer Email"
              type="email"
              required
              placeholder="colleague@university.edu"
              {...register('email', { required: 'Email required' })}
              error={errors.email?.message}
            />
            <TextAreaField
              label="Personal Message (optional)"
              rows={3}
              placeholder="We would be honored to have your expertise..."
              {...register('message')}
            />
            <button type="submit" disabled={isSubmitting} className="btn-primary">
              {isSubmitting ? <Spinner className="h-4 w-4 text-white" /> : <><Mail className="h-4 w-4" /> Send Invitation</>}
            </button>
          </form>
        </div>

        {/* Invitations list */}
        <div className="card">
          <div className="p-5 border-b border-slate-100">
            <h3 className="font-semibold text-slate-800">Sent Invitations</h3>
          </div>
          {loading ? (
            <PageLoader />
          ) : invitations.length === 0 ? (
            <EmptyState icon={Users} title="No invitations sent yet" />
          ) : (
            <div className="divide-y divide-slate-100">
              {invitations.map((inv) => (
                <div key={inv.id} className="p-4 flex items-center justify-between gap-3">
                  <div>
                    <p className="font-medium text-slate-800">{inv.email}</p>
                    <p className="text-xs text-slate-400 mt-0.5">
                      Sent {formatDate(inv.created_at, 'PP')}
                      {inv.expires_at && ` · Expires ${formatDate(inv.expires_at, 'PP')}`}
                    </p>
                  </div>
                  <div className="flex items-center gap-2">
                    <Badge status={inv.status} />
                    {inv.status === 'pending' && (
                      <button
                        onClick={() => handleCancel(inv.id)}
                        className="btn-ghost btn-sm p-1 text-red-500 hover:text-red-700"
                        title="Cancel invitation"
                      >
                        <X className="h-4 w-4" />
                      </button>
                    )}
                  </div>
                </div>
              ))}
            </div>
          )}
        </div>
      </div>
    </AdminLayout>
  );
}
