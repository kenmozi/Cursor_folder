'use client';

import { useEffect, useState } from 'react';
import { useLocale } from 'next-intl';
import { useForm } from 'react-hook-form';
import { UserCheck, Plus, Trash2 } from 'lucide-react';
import { AdminLayout } from '@/components/admin/AdminLayout';
import { FormField } from '@/components/ui/FormField';
import { PageLoader } from '@/components/ui/Spinner';
import { EmptyState } from '@/components/ui/EmptyState';
import { ErrorAlert } from '@/components/ui/ErrorAlert';
import { Spinner } from '@/components/ui/Spinner';
import { apiClient } from '@/lib/api';
import { getErrorMessage } from '@/lib/utils';
import type { CommitteeMember } from '@/types';

interface MemberForm {
  name: string;
  role: string;
  affiliation: string;
  country: string;
  email: string;
  is_public: boolean;
}

export default function CommitteePage({ params }: { params: { id: string } }) {
  const locale = useLocale();
  const slug = params.id;
  const [members, setMembers] = useState<CommitteeMember[]>([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState('');

  const { register, handleSubmit, reset, formState: { isSubmitting } } = useForm<MemberForm>({
    defaultValues: { is_public: true },
  });

  const loadMembers = () => {
    apiClient.get(`/admin/conferences/${slug}/committee`).then((r) => setMembers(r.data.data ?? [])).finally(() => setLoading(false));
  };

  useEffect(() => { loadMembers(); }, [slug]);

  const onAdd = async (data: MemberForm) => {
    try {
      setError('');
      await apiClient.post(`/admin/conferences/${slug}/committee`, data);
      reset({ is_public: true });
      loadMembers();
    } catch (e) {
      setError(getErrorMessage(e));
    }
  };

  const handleDelete = async (id: number) => {
    try {
      await apiClient.delete(`/admin/conferences/${slug}/committee/${id}`);
      loadMembers();
    } catch (e) {
      setError(getErrorMessage(e));
    }
  };

  return (
    <AdminLayout title="Committee" conferenceSlug={slug}>
      <div className="p-6 space-y-6 max-w-3xl">
        <h2 className="text-xl font-bold text-slate-900">Program Committee</h2>

        {error && <ErrorAlert message={error} />}

        {/* Add member form */}
        <div className="card p-5 space-y-4">
          <h3 className="font-semibold text-slate-800">Add Committee Member</h3>
          <form onSubmit={handleSubmit(onAdd)} className="space-y-3">
            <div className="grid grid-cols-2 gap-3">
              <FormField label="Name" required placeholder="Prof. Jane Doe" {...register('name', { required: true })} />
              <FormField label="Role" placeholder="Program Chair, Reviewer..." {...register('role')} />
              <FormField label="Affiliation" placeholder="University of Paris" {...register('affiliation')} />
              <FormField label="Country" placeholder="FR" {...register('country')} />
              <FormField label="Email" type="email" placeholder="jane@univ.fr" {...register('email')} />
            </div>
            <label className="flex items-center gap-2 cursor-pointer text-sm">
              <input type="checkbox" {...register('is_public')} className="rounded" />
              Show on public conference page
            </label>
            <button type="submit" disabled={isSubmitting} className="btn-primary">
              {isSubmitting ? <Spinner className="h-4 w-4 text-white" /> : <><Plus className="h-4 w-4" /> Add Member</>}
            </button>
          </form>
        </div>

        {/* Members list */}
        <div className="card">
          {loading ? (
            <PageLoader />
          ) : members.length === 0 ? (
            <EmptyState icon={UserCheck} title="No committee members yet" />
          ) : (
            <div className="divide-y divide-slate-100">
              {members.map((m) => (
                <div key={m.id} className="p-4 flex items-center justify-between gap-3">
                  <div className="flex items-center gap-3">
                    <div className="h-9 w-9 rounded-full bg-brand-100 flex items-center justify-center text-brand-700 font-semibold text-sm shrink-0">
                      {m.name[0]}
                    </div>
                    <div>
                      <p className="font-medium text-slate-800">{m.name}</p>
                      <p className="text-xs text-slate-400">
                        {[m.role, m.affiliation, m.country].filter(Boolean).join(' · ')}
                      </p>
                    </div>
                  </div>
                  <div className="flex items-center gap-2">
                    {!m.is_public && (
                      <span className="badge bg-slate-100 text-slate-500 text-xs">Hidden</span>
                    )}
                    <button
                      onClick={() => handleDelete(m.id)}
                      className="btn-ghost btn-sm p-1 text-red-400 hover:text-red-600"
                    >
                      <Trash2 className="h-4 w-4" />
                    </button>
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
