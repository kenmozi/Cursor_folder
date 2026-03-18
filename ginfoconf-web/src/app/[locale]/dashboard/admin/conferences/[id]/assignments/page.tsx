'use client';

import { useEffect, useState } from 'react';
import { useLocale } from 'next-intl';
import { useSearchParams } from 'next/navigation';
import { GitBranch, Plus, Trash2 } from 'lucide-react';
import { AdminLayout } from '@/components/admin/AdminLayout';
import { Badge } from '@/components/ui/Badge';
import { PageLoader } from '@/components/ui/Spinner';
import { EmptyState } from '@/components/ui/EmptyState';
import { ErrorAlert } from '@/components/ui/ErrorAlert';
import { Spinner } from '@/components/ui/Spinner';
import { adminConferenceApi } from '@/lib/api';
import { formatDate, getErrorMessage } from '@/lib/utils';
import type { ReviewAssignment, User } from '@/types';

export default function AssignmentsPage({ params }: { params: { id: string } }) {
  const locale = useLocale();
  const slug = params.id;
  const searchParams = useSearchParams();
  const preselectedSubmission = searchParams.get('submission');

  const [assignments, setAssignments] = useState<ReviewAssignment[]>([]);
  const [reviewers, setReviewers] = useState<User[]>([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState('');

  // Assignment form
  const [submissionId, setSubmissionId] = useState(preselectedSubmission ?? '');
  const [reviewerId, setReviewerId] = useState('');
  const [dueDate, setDueDate] = useState('');
  const [assigning, setAssigning] = useState(false);

  const loadData = () => {
    Promise.all([
      adminConferenceApi.listAssignments(slug),
      adminConferenceApi.listReviewers(slug),
    ]).then(([aRes, rRes]) => {
      setAssignments(aRes.data);
      setReviewers(rRes.data ?? []);
    }).finally(() => setLoading(false));
  };

  useEffect(() => { loadData(); }, [slug]);

  const handleAssign = async () => {
    if (!submissionId || !reviewerId) return;
    try {
      setAssigning(true);
      setError('');
      await adminConferenceApi.assign(slug, Number(submissionId), {
        reviewer_id: Number(reviewerId),
        due_date: dueDate || undefined,
      });
      loadData();
      setReviewerId('');
    } catch (e) {
      setError(getErrorMessage(e));
    } finally {
      setAssigning(false);
    }
  };

  const handleUnassign = async (submissionIdNum: number, assignmentId: number) => {
    try {
      await adminConferenceApi.unassign(slug, submissionIdNum, assignmentId);
      loadData();
    } catch (e) {
      setError(getErrorMessage(e));
    }
  };

  return (
    <AdminLayout title="Assignments" conferenceSlug={slug}>
      <div className="p-6 space-y-6">
        <h2 className="text-xl font-bold text-slate-900">Reviewer Assignments</h2>

        {error && <ErrorAlert message={error} />}

        {/* Assign form */}
        <div className="card p-5 space-y-4">
          <h3 className="font-semibold text-slate-800">Assign Reviewer to Paper</h3>
          <div className="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div>
              <label className="label">Submission ID</label>
              <input
                type="number"
                className="input"
                placeholder="123"
                value={submissionId}
                onChange={(e) => setSubmissionId(e.target.value)}
              />
            </div>
            <div>
              <label className="label">Reviewer</label>
              <select className="input" value={reviewerId} onChange={(e) => setReviewerId(e.target.value)}>
                <option value="">Select reviewer...</option>
                {reviewers.map((r) => (
                  <option key={r.id} value={r.id}>{r.name} ({r.email})</option>
                ))}
              </select>
            </div>
            <div>
              <label className="label">Due Date (optional)</label>
              <input type="date" className="input" value={dueDate} onChange={(e) => setDueDate(e.target.value)} />
            </div>
          </div>
          <button onClick={handleAssign} disabled={assigning || !submissionId || !reviewerId} className="btn-primary">
            {assigning ? <Spinner className="h-4 w-4 text-white" /> : <><Plus className="h-4 w-4" /> Assign</>}
          </button>
        </div>

        {/* Assignments table */}
        <div className="card">
          {loading ? (
            <PageLoader />
          ) : assignments.length === 0 ? (
            <EmptyState icon={GitBranch} title="No assignments yet" />
          ) : (
            <div className="table-wrapper rounded-none border-none">
              <table className="data-table">
                <thead>
                  <tr>
                    <th>Paper</th>
                    <th>Reviewer</th>
                    <th>Status</th>
                    <th>Due</th>
                    <th>Review</th>
                    <th />
                  </tr>
                </thead>
                <tbody>
                  {assignments.map((a) => (
                    <tr key={a.id}>
                      <td className="text-slate-700 max-w-[200px] truncate text-sm">
                        {a.submission?.title ?? `#${a.submission?.id}`}
                      </td>
                      <td className="text-slate-600 text-sm">{a.reviewer?.name ?? '—'}</td>
                      <td><Badge status={a.status} /></td>
                      <td className="text-slate-400 text-xs">{a.due_date ? formatDate(a.due_date, 'PP') : '—'}</td>
                      <td>
                        {a.review ? (
                          <Badge status={a.review.status} />
                        ) : (
                          <span className="text-xs text-slate-400">Pending</span>
                        )}
                      </td>
                      <td>
                        <button
                          onClick={() => handleUnassign(a.submission?.id ?? 0, a.id)}
                          className="btn-ghost btn-sm p-1 text-red-400 hover:text-red-600"
                          title="Unassign"
                        >
                          <Trash2 className="h-3.5 w-3.5" />
                        </button>
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
