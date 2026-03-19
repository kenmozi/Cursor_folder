// ─── Auth ─────────────────────────────────────────────────────────────────────

export interface User {
  id: number;
  name: string;
  email: string;
  affiliation?: string;
  country?: string;
  bio?: string;
  locale: string;
  is_super_admin: boolean;
  created_at: string;
}

export interface AuthTokenResponse {
  user: User;
  token: string;
}

// ─── Conference ───────────────────────────────────────────────────────────────

export type ConferenceStatus = 'draft' | 'active' | 'open' | 'closed' | 'archived';
export type BlindMode = 'none' | 'single' | 'double';

export interface ConferenceTranslation {
  locale: string;
  title: string;
  subtitle?: string;
  description?: string;
  cfp_text?: string;
}

export interface ConferenceDate {
  id: number;
  key: string;
  date: string;
  label?: string;         // localized
  translations?: { locale: string; label: string }[];
}

export interface ConferenceMedia {
  logo?: string;
  cover?: string;
}

export interface Track {
  id: number;
  slug: string;
  name: string;           // localized
  description?: string;
  topics?: Topic[];
}

export interface Topic {
  id: number;
  name: string;           // localized
  slug: string;
}

export interface CommitteeMember {
  id: number;
  name: string;
  role?: string;
  affiliation?: string;
  country?: string;
  email?: string;
  is_public: boolean;
}

export interface ConferencePublic {
  slug: string;
  status: ConferenceStatus;
  blind_mode: BlindMode;
  timezone: string;
  location?: string;
  website_url?: string;
  submission_open?: string;
  submission_close?: string;
  notification_date?: string;
  camera_ready_date?: string;
  is_submission_open: boolean;
  // Localized fields
  title?: string;
  subtitle?: string;
  description?: string;
  cfp_text?: string;
  // Nested
  logo?: string;
  cover?: string;
  dates?: ConferenceDate[];
  tracks?: Track[];
  committee_members?: CommitteeMember[];
  translations?: ConferenceTranslation[];
}

export interface ConferenceAdmin extends ConferencePublic {
  id: number;
  review_open?: string;
  review_close?: string;
  created_at: string;
  media?: Record<string, string>;
  translations?: ConferenceTranslation[];
}

// ─── Submission ───────────────────────────────────────────────────────────────

export type SubmissionStatus =
  | 'draft'
  | 'submitted'
  | 'under_review'
  | 'accepted'
  | 'rejected'
  | 'revision_required'
  | 'camera_ready'
  | 'withdrawn';

export interface SubmissionAuthor {
  id?: number;
  name: string;
  email: string;
  affiliation?: string;
  is_corresponding: boolean;
  sort_order: number;
}

export interface SubmissionFile {
  id: number;
  type: string;
  version: number;
  original_name: string;
  size_bytes: number;
  is_active: boolean;
  uploaded_at: string;
}

export interface Submission {
  id: number;
  status: SubmissionStatus;
  title: string;
  abstract: string;
  keywords: string[];
  submitted_at?: string;
  created_at: string;
  conference?: { slug: string; blind_mode: BlindMode };
  track?: { id: number; slug: string };
  topics?: { id: number; name: string }[];
  authors?: SubmissionAuthor[];
  files?: SubmissionFile[];
  assignments?: ReviewAssignment[];
}

// ─── Review ───────────────────────────────────────────────────────────────────

export type AssignmentStatus = 'pending' | 'accepted' | 'declined' | 'completed' | 'expired';
export type ReviewRecommendation = 'strong_accept' | 'accept' | 'weak_accept' | 'borderline' | 'weak_reject' | 'reject' | 'strong_reject';

export interface Review {
  id: number;
  status: 'draft' | 'submitted';
  overall_score: number;
  recommendation: ReviewRecommendation;
  comments_to_authors?: string;
  comments_to_chair?: string;
  submitted_at?: string;
  assignment_id: number;
  created_at: string;
}

export interface ReviewAssignment {
  id: number;
  status: AssignmentStatus;
  due_date?: string;
  responded_at?: string;
  completed_at?: string;
  created_at: string;
  submission?: Submission;
  reviewer?: Pick<User, 'id' | 'name' | 'email'>;
  review?: Review;
}

export interface ReviewerInvitation {
  id: number;
  email: string;
  status: 'pending' | 'accepted' | 'declined' | 'expired' | 'cancelled';
  is_expired: boolean;
  message?: string;
  expires_at: string;
  responded_at?: string;
  created_at: string;
  inviter?: Pick<User, 'id' | 'name' | 'email'>;
}

// ─── Pagination ───────────────────────────────────────────────────────────────

export interface Paginated<T> {
  data: T[];
  meta: {
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
  };
  links: {
    first?: string;
    last?: string;
    prev?: string | null;
    next?: string | null;
  };
}
