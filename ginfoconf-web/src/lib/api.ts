import axios, { AxiosError, AxiosInstance } from 'axios';
import type {
  AuthTokenResponse,
  ConferenceAdmin,
  ConferencePublic,
  Paginated,
  Review,
  ReviewAssignment,
  ReviewerInvitation,
  Submission,
  User,
} from '@/types';

const BASE_URL = process.env.NEXT_PUBLIC_API_URL ?? 'http://localhost:8000/api/v1';

// ── Axios instance ─────────────────────────────────────────────────────────

export const apiClient: AxiosInstance = axios.create({
  baseURL: BASE_URL,
  headers: { 'Content-Type': 'application/json', Accept: 'application/json' },
});

// Attach token + locale on every request
apiClient.interceptors.request.use((config) => {
  if (typeof window !== 'undefined') {
    const token = localStorage.getItem('ginfoconf_token');
    if (token) config.headers.Authorization = `Bearer ${token}`;

    const locale = localStorage.getItem('ginfoconf_locale') ?? 'en';
    config.params = { ...config.params, locale };
  }
  return config;
});

// Global 401 handling — clear session
apiClient.interceptors.response.use(
  (res) => res,
  (error: AxiosError) => {
    if (error.response?.status === 401 && typeof window !== 'undefined') {
      localStorage.removeItem('ginfoconf_token');
      localStorage.removeItem('ginfoconf_user');
      window.location.href = '/login';
    }
    return Promise.reject(error);
  }
);

// ── Auth ───────────────────────────────────────────────────────────────────

export const authApi = {
  register: (data: { name: string; email: string; password: string; affiliation?: string; country?: string }) =>
    apiClient.post<AuthTokenResponse>('/auth/register', data).then((r) => r.data),

  login: (data: { email: string; password: string }) =>
    apiClient.post<AuthTokenResponse>('/auth/login', data).then((r) => r.data),

  logout: () => apiClient.post('/auth/logout'),

  me: () => apiClient.get<User>('/auth/me').then((r) => r.data),
};

// ── Public Conferences ─────────────────────────────────────────────────────

export const publicApi = {
  listConferences: (params?: { page?: number }) =>
    apiClient.get<Paginated<ConferencePublic>>('/conferences', { params }).then((r) => r.data),

  getConference: (slug: string) =>
    apiClient.get<ConferencePublic>(`/conferences/${slug}`).then((r) => r.data),

  getInvitation: (token: string) =>
    apiClient.get(`/invitations/${token}`).then((r) => r.data),

  acceptInvitation: (token: string) =>
    apiClient.post(`/invitations/${token}/accept`).then((r) => r.data),

  declineInvitation: (token: string, reason?: string) =>
    apiClient.post(`/invitations/${token}/decline`, { reason }).then((r) => r.data),
};

// ── Author ─────────────────────────────────────────────────────────────────

export const submissionApi = {
  list: (params?: { page?: number }) =>
    apiClient.get<Paginated<Submission>>('/submissions', { params }).then((r) => r.data),

  get: (id: number) =>
    apiClient.get<Submission>(`/submissions/${id}`).then((r) => r.data),

  create: (data: object) =>
    apiClient.post<Submission>('/submissions', data).then((r) => r.data),

  update: (id: number, data: object) =>
    apiClient.put<Submission>(`/submissions/${id}`, data).then((r) => r.data),

  submit: (id: number) =>
    apiClient.post<Submission>(`/submissions/${id}/submit`).then((r) => r.data),

  withdraw: (id: number) =>
    apiClient.post<Submission>(`/submissions/${id}/withdraw`).then((r) => r.data),

  uploadManuscript: (id: number, file: File, onProgress?: (pct: number) => void) => {
    const form = new FormData();
    form.append('file', file);
    return apiClient
      .post(`/submissions/${id}/files/manuscript`, form, {
        headers: { 'Content-Type': 'multipart/form-data' },
        onUploadProgress: (e) => {
          if (onProgress && e.total) onProgress(Math.round((e.loaded / e.total) * 100));
        },
      })
      .then((r) => r.data);
  },

  uploadSupplementary: (id: number, file: File, onProgress?: (pct: number) => void) => {
    const form = new FormData();
    form.append('file', file);
    return apiClient
      .post(`/submissions/${id}/files/supplementary`, form, {
        headers: { 'Content-Type': 'multipart/form-data' },
        onUploadProgress: (e) => {
          if (onProgress && e.total) onProgress(Math.round((e.loaded / e.total) * 100));
        },
      })
      .then((r) => r.data);
  },

  getDownloadUrl: (submissionId: number, fileId: number) =>
    apiClient.get<{ url: string; expires_in: number }>(`/submissions/${submissionId}/files/${fileId}/download`).then((r) => r.data),

  deleteFile: (submissionId: number, fileId: number) =>
    apiClient.delete(`/submissions/${submissionId}/files/${fileId}`),
};

// ── Reviewer ───────────────────────────────────────────────────────────────

export const reviewApi = {
  listAssignments: (params?: { page?: number }) =>
    apiClient.get<Paginated<ReviewAssignment>>('/reviewer/assignments', { params }).then((r) => r.data),

  getAssignment: (id: number) =>
    apiClient.get<ReviewAssignment>(`/reviewer/assignments/${id}`).then((r) => r.data),

  respond: (id: number, action: 'accept' | 'decline', reason?: string) =>
    apiClient.post<ReviewAssignment>(`/reviewer/assignments/${id}/respond`, { action, reason }).then((r) => r.data),

  saveReview: (assignmentId: number, data: object, submit = false) =>
    apiClient
      .post<Review>(`/reviewer/assignments/${assignmentId}/review`, { ...data, submit })
      .then((r) => r.data),

  getReview: (assignmentId: number) =>
    apiClient.get<Review>(`/reviewer/assignments/${assignmentId}/review`).then((r) => r.data),
};

// ── Admin ──────────────────────────────────────────────────────────────────

export const adminConferenceApi = {
  list: (params?: { page?: number }) =>
    apiClient.get<Paginated<ConferenceAdmin>>('/admin/conferences', { params }).then((r) => r.data),

  get: (slug: string) =>
    apiClient.get<ConferenceAdmin>(`/admin/conferences/${slug}`).then((r) => r.data),

  create: (data: object) =>
    apiClient.post<ConferenceAdmin>('/admin/conferences', data).then((r) => r.data),

  update: (slug: string, data: object) =>
    apiClient.put<ConferenceAdmin>(`/admin/conferences/${slug}`, data).then((r) => r.data),

  // Content
  upsertContent: (slug: string, locale: string, data: object) =>
    apiClient.put(`/admin/conferences/${slug}/content/${locale}`, data).then((r) => r.data),

  // Media
  uploadMedia: (slug: string, type: 'logo' | 'cover', file: File, onProgress?: (pct: number) => void) => {
    const form = new FormData();
    form.append('type', type);
    form.append('file', file);
    return apiClient
      .post(`/admin/conferences/${slug}/media`, form, {
        headers: { 'Content-Type': 'multipart/form-data' },
        onUploadProgress: (e) => {
          if (onProgress && e.total) onProgress(Math.round((e.loaded / e.total) * 100));
        },
      })
      .then((r) => r.data);
  },

  deleteMedia: (slug: string, type: string) =>
    apiClient.delete(`/admin/conferences/${slug}/media/${type}`),

  // Reviewers
  listReviewers: (slug: string) =>
    apiClient.get(`/admin/conferences/${slug}/reviewers`).then((r) => r.data),

  listInvitations: (slug: string) =>
    apiClient.get<Paginated<ReviewerInvitation>>(`/admin/conferences/${slug}/invitations`).then((r) => r.data),

  invite: (slug: string, data: { email: string; message?: string }) =>
    apiClient.post<ReviewerInvitation>(`/admin/conferences/${slug}/invitations`, data).then((r) => r.data),

  cancelInvitation: (slug: string, invitationId: number) =>
    apiClient.delete(`/admin/conferences/${slug}/invitations/${invitationId}`),

  // Submissions
  listSubmissions: (slug: string, params?: { page?: number; status?: string }) =>
    apiClient.get<Paginated<Submission>>(`/admin/conferences/${slug}/submissions`, { params }).then((r) => r.data),

  getSubmission: (slug: string, id: number) =>
    apiClient.get<Submission>(`/admin/conferences/${slug}/submissions/${id}`).then((r) => r.data),

  recordDecision: (slug: string, id: number, data: { status: string; notify?: boolean }) =>
    apiClient.post<Submission>(`/admin/conferences/${slug}/submissions/${id}/decision`, data).then((r) => r.data),

  // Assignments
  listAssignments: (slug: string, params?: { submission_id?: number }) =>
    apiClient.get<Paginated<ReviewAssignment>>(`/admin/conferences/${slug}/assignments`, { params }).then((r) => r.data),

  assign: (slug: string, submissionId: number, data: { reviewer_id: number; due_date?: string }) =>
    apiClient.post<ReviewAssignment>(`/admin/conferences/${slug}/submissions/${submissionId}/assignments`, data).then((r) => r.data),

  unassign: (slug: string, submissionId: number, assignmentId: number) =>
    apiClient.delete(`/admin/conferences/${slug}/submissions/${submissionId}/assignments/${assignmentId}`),
};
