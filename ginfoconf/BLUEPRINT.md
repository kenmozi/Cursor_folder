# ginfoconf — Architecture & Design Blueprint
### Production-Grade Multilingual Conference Submission & Review Platform

> Lead Engineer Edition — March 2026

---

## 1. Product Vision Summary

ginfoconf is a modern, multilingual, multi-conference platform that replaces EasyChair with a premium product-grade experience for authors, reviewers, and conference chairs alike.

**Where EasyChair fails:**
- 2000s UI with no responsive design
- Per-action friction for every user type
- No real multilingual support
- Manual, opaque review assignment
- No SSG public pages — conferences are walled behind login
- No branding per conference

**ginfoconf's differentiators:**
- Every conference gets a beautiful, branded public landing page with SSR/SSG (SEO-first)
- Zero-friction submission in under 5 minutes for authors
- Invitation-driven reviewer onboarding with a single-click accept flow
- Real multilingual architecture (DB-level translations, not workarounds)
- Admin dashboard that feels like Notion meets Notion Calendar
- Role-scoped access — no accidental data leakage
- S3-ready file storage from day one
- Built for AI augmentation: similarity detection, conflict of interest detection, review scoring insights

**Core value proposition per role:**
| Role | Key Value |
|---|---|
| Author | Submit fast, track progress clearly, no confusing forms |
| Reviewer | Clean queue, focused review form, mobile-friendly |
| Chair | Full control over every workflow stage, one dashboard |
| Super Admin | Multi-conference oversight, user governance |
| Visitor | Beautiful, fast public conference page, no login needed |

---

## 2. User Roles and Permissions Matrix

### Role Definitions

| Role | Scope | Description |
|---|---|---|
| `super_admin` | Platform | Manages all conferences, users, billing, system config |
| `conference_admin` | Conference | Creates/edits conference, manages all workflows |
| `pc_member` | Conference | Program committee — sees all submissions in assigned tracks, can bid on papers |
| `reviewer` | Conference | Invited to review specific assigned submissions only |
| `author` | Conference | Submits and tracks own papers |
| `visitor` | Public | Reads public conference pages |

### Permissions Matrix

| Permission | super_admin | conf_admin | pc_member | reviewer | author | visitor |
|---|:---:|:---:|:---:|:---:|:---:|:---:|
| Create conference | ✓ | — | — | — | — | — |
| Edit any conference | ✓ | own | — | — | — | — |
| View all submissions | ✓ | own conf | track | assigned | own | — |
| Assign reviewers | ✓ | ✓ | — | — | — | — |
| Submit paper | — | — | — | — | ✓ | — |
| Submit review | — | — | ✓ (if assigned) | ✓ | — | — |
| Accept/reject paper | ✓ | ✓ | — | — | — | — |
| View review comments | ✓ | ✓ | ✓ (blind) | own | after decision | — |
| Manage users | ✓ | own conf | — | — | — | — |
| View conference public page | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ |
| Upload logo/cover | ✓ | ✓ | — | — | — | — |
| View reviewer identities | ✓ | ✓ | — | — | — | — |

### Notes on blinding:
- Default: **double-blind** (authors anonymous to reviewers, reviewers anonymous to authors)
- Configurable per conference: single-blind, open review
- PC members see reviewer identities, authors do not

---

## 3. Major User Journeys

### A. Author Journey

```
1. Discovers conference via Google (SSG public page)
2. Clicks "Submit a Paper"
3. Creates account (or logs in) — one-form registration
4. Lands on submission wizard:
   a. Paper details (title, abstract, topics, keywords)
   b. Co-authors (add by email lookup or manual entry)
   c. File upload (PDF manuscript)
   d. Review & save as draft
5. Submits before deadline — status: "Under Review"
6. Receives email: "Your submission is under review"
7. Tracks status on /dashboard/submissions
8. Receives decision notification
9. If revision required: uploads new version
10. If accepted: uploads camera-ready version
```

### B. Reviewer Journey

```
1. Receives email invitation from chair
2. Clicks single-link → lands on /invitation/[token]
3. Accepts or declines (with reason) — no account needed to decline
4. If accepted: creates account (pre-filled with invitation email)
5. Lands on reviewer dashboard — sees assigned papers
6. Clicks paper → reads abstract (or full manuscript per config)
7. Fills review form: criteria scores + comments + recommendation
8. Saves draft, returns later, submits when ready
9. Receives confirmation email
```

### C. Conference Admin Journey

```
1. Super admin or self-creates account
2. Creates conference → slug, basic info, dates
3. Uploads logo + cover image
4. Fills CFP (rich text), topics, important dates
5. Publishes conference → public page live
6. Submission period: monitors incoming papers
7. Closes submissions → assigns reviewers (manual or one-click auto-suggest)
8. Monitors review completion per paper
9. Views review summaries → makes accept/reject decisions
10. Sends notification emails → authors notified
11. Collects camera-ready files
```

### D. Public Visitor Journey

```
1. Lands on /conference/[slug] — no login needed
2. Reads hero, overview, call for papers
3. Checks important dates, topics, committee
4. Switches language (FR/EN/JA)
5. Clicks "Submit a Paper" or "Register for Conference"
```

---

## 4. Information Architecture

### URL Structure

```
# Platform-level (public)
/                              → Platform home (list featured conferences)
/conferences                   → All active conferences
/about                         → About ginfoconf
/login  /register              → Auth pages

# Conference public pages (NO login required)
/conference/[slug]             → Conference landing page
/conference/[slug]/cfp         → Call for papers (full content)
/conference/[slug]/dates       → Important dates
/conference/[slug]/committee   → Committee members
/conference/[slug]/topics      → Topics / tracks
/conference/[slug]/venue       → Venue & location
/conference/[slug]/faq         → FAQ
/conference/[slug]/contact     → Contact

# Conference submission (login required)
/conference/[slug]/submit      → Submission wizard
/conference/[slug]/submit/[id] → Edit/continue draft

# Author dashboard (login required)
/dashboard                     → Author home
/dashboard/submissions         → All submissions
/dashboard/submissions/[id]    → Single submission detail

# Reviewer dashboard (login required)
/reviewer                      → Reviewer home
/reviewer/assignments          → All assigned papers
/reviewer/assignments/[id]     → Read paper + review form

# PC Member dashboard (login required)
/pc/[conferenceSlug]           → PC overview
/pc/[conferenceSlug]/submissions → All submissions in track
/pc/[conferenceSlug]/reviews   → Review overview

# Conference admin (login required)
/admin/conferences             → List managed conferences
/admin/conferences/new         → Create conference wizard
/admin/conferences/[slug]      → Conference overview
/admin/conferences/[slug]/settings     → Conference settings
/admin/conferences/[slug]/content      → CFP, dates, topics (multilingual editor)
/admin/conferences/[slug]/media        → Logo, cover, program
/admin/conferences/[slug]/committee    → Manage committee
/admin/conferences/[slug]/reviewers    → Manage reviewers + invite
/admin/conferences/[slug]/submissions  → All submissions
/admin/conferences/[slug]/assignments  → Review assignments
/admin/conferences/[slug]/decisions    → Accept/reject decisions
/admin/conferences/[slug]/notifications → Email history

# Super admin
/superadmin/conferences        → All conferences
/superadmin/users              → All users
/superadmin/system             → System config

# Invitation flow (no login needed)
/invitation/[token]            → Accept/decline reviewer invitation
```

---

## 5. Functional Modules

### Module 1: Conference Management
- Create/edit conference (basic info, slug, status, timezone)
- Upload logo, cover image, program PDF
- Set dates: submission open/close, review open/close, notification date, camera-ready deadline
- Configure review workflow (blind level, reviews per paper, due date)
- Publish/unpublish conference
- Archive conference

### Module 2: Content Management (Multilingual)
- Rich-text CFP content (per locale)
- Important dates (label + date, ordered list)
- Topics / tracks (per locale)
- FAQ (Q&A pairs, per locale)
- Committee members (photo, name, affiliation, role)
- Venue information (per locale)
- Contact info

### Module 3: Submission Workflow
- Multi-step submission wizard
- Draft save at each step
- File upload (PDF, with version tracking)
- Co-author management (add by email or manually)
- Topic/track selection
- Status machine: `draft → submitted → under_review → accepted | rejected | revision_required → camera_ready`
- Withdrawal
- Camera-ready upload

### Module 4: Reviewer Management
- Invite reviewer by email (with token-based invitation link)
- Accept/decline flow
- Invitation expiry
- Conflict of interest declaration
- Availability / bidding (post-MVP)

### Module 5: Review Assignment
- Manual assignment by chair (drag-and-drop or table UI)
- Auto-suggest based on topic match (post-MVP algorithm)
- Assignment status tracking per paper
- Configurable: min/max reviewers per paper

### Module 6: Review Workflow
- Configurable review form (criteria, scores, recommendation)
- Double/single/open blind modes
- Draft save for reviews
- Submit review (locked after submission unless chair resets)
- Meta-review by PC chair (post-MVP)

### Module 7: Decision Management
- Per-paper decision (accept / reject / revision_required)
- Decision notes (internal)
- Batch decision via table
- Notification trigger on decision
- Camera-ready collection after acceptance

### Module 8: Notifications
- Transactional emails:
  - Submission received
  - Reviewer invitation
  - Review due reminder
  - Decision notification
  - Camera-ready request
- In-app notification feed (post-MVP)
- Email templates (per locale, per event)

### Module 9: Public Pages
- SSR/SSG rendered conference pages
- Language switcher
- Hero banner with conference branding
- SEO meta tags per conference
- Social share cards (OG)

### Module 10: User & Auth Management
- Registration + email verification
- Profile: name, affiliation, country, bio, photo, locale preference
- Password reset
- OAuth (post-MVP: Google, ORCID)

---

## 6. Proposed Database Schema

**Decision: PostgreSQL** — chosen over MySQL for:
- Native JSONB for review criteria options
- Better full-text search (`tsvector`)
- Superior constraints and enum types
- Array columns for keywords

---

### Core Tables

#### `users`
```sql
id              BIGSERIAL PRIMARY KEY
email           VARCHAR(255) UNIQUE NOT NULL
password        VARCHAR(255)
name            VARCHAR(255) NOT NULL
affiliation     VARCHAR(500)
country         CHAR(2)              -- ISO 3166-1 alpha-2
bio             TEXT
photo_path      VARCHAR(500)
locale          VARCHAR(10) DEFAULT 'en'
email_verified_at TIMESTAMPTZ
remember_token  VARCHAR(100)
created_at      TIMESTAMPTZ
updated_at      TIMESTAMPTZ

INDEX: idx_users_email
```

#### `conferences`
```sql
id              BIGSERIAL PRIMARY KEY
slug            VARCHAR(100) UNIQUE NOT NULL
owner_id        BIGINT REFERENCES users(id)
status          conference_status DEFAULT 'draft'  -- ENUM: draft|active|archived
timezone        VARCHAR(50) DEFAULT 'UTC'
submission_open  TIMESTAMPTZ
submission_close TIMESTAMPTZ
review_open      TIMESTAMPTZ
review_close     TIMESTAMPTZ
notification_date TIMESTAMPTZ
camera_ready_deadline TIMESTAMPTZ
max_pages        SMALLINT
min_reviewers    SMALLINT DEFAULT 2
max_reviewers    SMALLINT DEFAULT 3
blind_mode       blind_mode DEFAULT 'double'  -- ENUM: open|single|double
review_form_id   BIGINT REFERENCES review_forms(id)
allow_latex      BOOLEAN DEFAULT FALSE
allow_supplementary BOOLEAN DEFAULT TRUE
created_at       TIMESTAMPTZ
updated_at       TIMESTAMPTZ

INDEX: idx_conferences_slug
INDEX: idx_conferences_status
INDEX: idx_conferences_owner
```

#### `conference_translations`
```sql
id              BIGSERIAL PRIMARY KEY
conference_id   BIGINT REFERENCES conferences(id) ON DELETE CASCADE
locale          VARCHAR(10) NOT NULL
title           VARCHAR(500) NOT NULL
subtitle        VARCHAR(500)
description     TEXT
cfp_text        TEXT         -- rich text HTML / markdown
venue_text      TEXT
contact_text    TEXT
created_at      TIMESTAMPTZ
updated_at      TIMESTAMPTZ

UNIQUE: (conference_id, locale)
INDEX: idx_conf_translations_locale
```

#### `conference_media`
```sql
id              BIGSERIAL PRIMARY KEY
conference_id   BIGINT REFERENCES conferences(id) ON DELETE CASCADE
type            media_type NOT NULL  -- ENUM: logo|cover|program|gallery
disk            VARCHAR(20) DEFAULT 'local'
path            VARCHAR(1000) NOT NULL
original_name   VARCHAR(255)
mime_type       VARCHAR(100)
size_bytes      BIGINT
created_at      TIMESTAMPTZ
```

#### `conference_dates`
```sql
id              BIGSERIAL PRIMARY KEY
conference_id   BIGINT REFERENCES conferences(id) ON DELETE CASCADE
date            DATE NOT NULL
sort_order      SMALLINT DEFAULT 0
created_at      TIMESTAMPTZ

INDEX: idx_conf_dates_conference
```

#### `conference_date_translations`
```sql
id              BIGSERIAL PRIMARY KEY
conf_date_id    BIGINT REFERENCES conference_dates(id) ON DELETE CASCADE
locale          VARCHAR(10) NOT NULL
label           VARCHAR(300) NOT NULL  -- e.g. "Paper submission deadline"

UNIQUE: (conf_date_id, locale)
```

#### `tracks`
```sql
id              BIGSERIAL PRIMARY KEY
conference_id   BIGINT REFERENCES conferences(id) ON DELETE CASCADE
slug            VARCHAR(100)
sort_order      SMALLINT DEFAULT 0
created_at      TIMESTAMPTZ

UNIQUE: (conference_id, slug)
```

#### `track_translations`
```sql
id              BIGSERIAL PRIMARY KEY
track_id        BIGINT REFERENCES tracks(id) ON DELETE CASCADE
locale          VARCHAR(10) NOT NULL
name            VARCHAR(300) NOT NULL
description     TEXT

UNIQUE: (track_id, locale)
```

#### `topics`
```sql
id              BIGSERIAL PRIMARY KEY
conference_id   BIGINT REFERENCES conferences(id) ON DELETE CASCADE
track_id        BIGINT REFERENCES tracks(id) ON DELETE SET NULL
sort_order      SMALLINT DEFAULT 0
```

#### `topic_translations`
```sql
id              BIGSERIAL PRIMARY KEY
topic_id        BIGINT REFERENCES topics(id) ON DELETE CASCADE
locale          VARCHAR(10) NOT NULL
name            VARCHAR(300) NOT NULL

UNIQUE: (topic_id, locale)
```

#### `committee_members`
```sql
id              BIGSERIAL PRIMARY KEY
conference_id   BIGINT REFERENCES conferences(id) ON DELETE CASCADE
user_id         BIGINT REFERENCES users(id) ON DELETE SET NULL
name            VARCHAR(255) NOT NULL  -- denormalized for external members
email           VARCHAR(255)
affiliation     VARCHAR(500)
country         CHAR(2)
photo_path      VARCHAR(500)
role            committee_role NOT NULL  -- ENUM: chair|co_chair|pc_member|organizing|technical|sponsor
sort_order      SMALLINT DEFAULT 0
created_at      TIMESTAMPTZ
```

#### `conference_roles`
```sql
id              BIGSERIAL PRIMARY KEY
conference_id   BIGINT REFERENCES conferences(id) ON DELETE CASCADE
user_id         BIGINT REFERENCES users(id) ON DELETE CASCADE
role            conf_role NOT NULL  -- ENUM: admin|pc_member|reviewer
assigned_by     BIGINT REFERENCES users(id)
created_at      TIMESTAMPTZ

UNIQUE: (conference_id, user_id, role)
INDEX: idx_conf_roles_user
INDEX: idx_conf_roles_conference
```

#### `submissions`
```sql
id              BIGSERIAL PRIMARY KEY
conference_id   BIGINT REFERENCES conferences(id) ON DELETE CASCADE
track_id        BIGINT REFERENCES tracks(id) ON DELETE SET NULL
submitter_id    BIGINT REFERENCES users(id)
title           VARCHAR(500) NOT NULL
abstract        TEXT NOT NULL
keywords        TEXT[]               -- PostgreSQL array
status          submission_status DEFAULT 'draft'
  -- ENUM: draft|submitted|under_review|accepted|rejected|revision_required|withdrawn|camera_ready
decision_note   TEXT                 -- internal chair note
submitted_at    TIMESTAMPTZ
decided_at      TIMESTAMPTZ
decided_by      BIGINT REFERENCES users(id)
created_at      TIMESTAMPTZ
updated_at      TIMESTAMPTZ

INDEX: idx_submissions_conference
INDEX: idx_submissions_submitter
INDEX: idx_submissions_status
INDEX: idx_submissions_track
-- Full-text search
INDEX: idx_submissions_fts USING GIN(to_tsvector('english', title || ' ' || abstract))
```

#### `submission_authors`
```sql
id              BIGSERIAL PRIMARY KEY
submission_id   BIGINT REFERENCES submissions(id) ON DELETE CASCADE
user_id         BIGINT REFERENCES users(id) ON DELETE SET NULL
name            VARCHAR(255) NOT NULL
email           VARCHAR(255) NOT NULL
affiliation     VARCHAR(500)
country         CHAR(2)
is_corresponding BOOLEAN DEFAULT FALSE
is_presenter    BOOLEAN DEFAULT FALSE
sort_order      SMALLINT DEFAULT 0

INDEX: idx_sub_authors_submission
INDEX: idx_sub_authors_user
```

#### `submission_topics`
```sql
submission_id   BIGINT REFERENCES submissions(id) ON DELETE CASCADE
topic_id        BIGINT REFERENCES topics(id) ON DELETE CASCADE

PRIMARY KEY: (submission_id, topic_id)
```

#### `submission_files`
```sql
id              BIGSERIAL PRIMARY KEY
submission_id   BIGINT REFERENCES submissions(id) ON DELETE CASCADE
uploaded_by     BIGINT REFERENCES users(id)
version         SMALLINT DEFAULT 1
type            file_type NOT NULL  -- ENUM: manuscript|supplementary|camera_ready
disk            VARCHAR(20) DEFAULT 'local'
path            VARCHAR(1000) NOT NULL
original_name   VARCHAR(255)
mime_type       VARCHAR(100)
size_bytes      BIGINT
is_active       BOOLEAN DEFAULT TRUE  -- only one active manuscript at a time
uploaded_at     TIMESTAMPTZ

INDEX: idx_sub_files_submission
```

#### `review_forms`
```sql
id              BIGSERIAL PRIMARY KEY
conference_id   BIGINT REFERENCES conferences(id) ON DELETE CASCADE
name            VARCHAR(255) NOT NULL
is_active       BOOLEAN DEFAULT TRUE
created_at      TIMESTAMPTZ
```

#### `review_criteria`
```sql
id              BIGSERIAL PRIMARY KEY
form_id         BIGINT REFERENCES review_forms(id) ON DELETE CASCADE
type            criterion_type NOT NULL  -- ENUM: score|text|recommendation|boolean
min_score       NUMERIC(4,1)
max_score       NUMERIC(4,1)
step            NUMERIC(4,2) DEFAULT 1
is_required     BOOLEAN DEFAULT TRUE
weight          NUMERIC(5,2) DEFAULT 1.0
sort_order      SMALLINT DEFAULT 0
```

#### `review_criteria_translations`
```sql
id              BIGSERIAL PRIMARY KEY
criterion_id    BIGINT REFERENCES review_criteria(id) ON DELETE CASCADE
locale          VARCHAR(10) NOT NULL
label           VARCHAR(300) NOT NULL
description     TEXT
-- For score type: human labels per value stored as JSONB
score_labels    JSONB   -- e.g. {"1": "Poor", "3": "Average", "5": "Excellent"}

UNIQUE: (criterion_id, locale)
```

#### `reviewer_invitations`
```sql
id              BIGSERIAL PRIMARY KEY
conference_id   BIGINT REFERENCES conferences(id) ON DELETE CASCADE
inviter_id      BIGINT REFERENCES users(id)
user_id         BIGINT REFERENCES users(id) ON DELETE SET NULL  -- null if not yet registered
email           VARCHAR(255) NOT NULL
token           VARCHAR(64) UNIQUE NOT NULL
status          invitation_status DEFAULT 'pending'  -- ENUM: pending|accepted|declined|expired
message         TEXT
expires_at      TIMESTAMPTZ
responded_at    TIMESTAMPTZ
created_at      TIMESTAMPTZ

INDEX: idx_invitations_token
INDEX: idx_invitations_email
INDEX: idx_invitations_conference
```

#### `review_assignments`
```sql
id              BIGSERIAL PRIMARY KEY
submission_id   BIGINT REFERENCES submissions(id) ON DELETE CASCADE
reviewer_id     BIGINT REFERENCES users(id) ON DELETE CASCADE
assigned_by     BIGINT REFERENCES users(id)
status          assignment_status DEFAULT 'pending'
  -- ENUM: pending|accepted|declined|in_progress|completed|reassigned
due_date        DATE
assigned_at     TIMESTAMPTZ
responded_at    TIMESTAMPTZ
completed_at    TIMESTAMPTZ

UNIQUE: (submission_id, reviewer_id)
INDEX: idx_assignments_reviewer
INDEX: idx_assignments_submission
INDEX: idx_assignments_status
```

#### `reviews`
```sql
id              BIGSERIAL PRIMARY KEY
assignment_id   BIGINT UNIQUE REFERENCES review_assignments(id) ON DELETE CASCADE
submission_id   BIGINT REFERENCES submissions(id) ON DELETE CASCADE
reviewer_id     BIGINT REFERENCES users(id)
status          review_status DEFAULT 'draft'  -- ENUM: draft|submitted
overall_score   NUMERIC(5,2)    -- computed from weighted criteria
recommendation  recommendation_type  -- ENUM: strong_accept|accept|weak_accept|borderline|weak_reject|reject|strong_reject
submitted_at    TIMESTAMPTZ
created_at      TIMESTAMPTZ
updated_at      TIMESTAMPTZ

INDEX: idx_reviews_submission
INDEX: idx_reviews_reviewer
```

#### `review_scores`
```sql
id              BIGSERIAL PRIMARY KEY
review_id       BIGINT REFERENCES reviews(id) ON DELETE CASCADE
criterion_id    BIGINT REFERENCES review_criteria(id)
score_value     NUMERIC(5,2)
text_value      TEXT

UNIQUE: (review_id, criterion_id)
```

#### `review_comments`
```sql
id              BIGSERIAL PRIMARY KEY
review_id       BIGINT REFERENCES reviews(id) ON DELETE CASCADE
visibility      comment_visibility NOT NULL  -- ENUM: to_authors|to_chair_only
content         TEXT NOT NULL
created_at      TIMESTAMPTZ
updated_at      TIMESTAMPTZ
```

#### `notifications`
```sql
id              BIGSERIAL PRIMARY KEY
user_id         BIGINT REFERENCES users(id) ON DELETE CASCADE
conference_id   BIGINT REFERENCES conferences(id) ON DELETE SET NULL
type            VARCHAR(100) NOT NULL  -- e.g. 'submission.received', 'review.invitation'
data            JSONB         -- flexible payload
channel         notification_channel DEFAULT 'email'  -- ENUM: email|in_app
status          notification_status DEFAULT 'pending'  -- ENUM: pending|sent|failed
sent_at         TIMESTAMPTZ
created_at      TIMESTAMPTZ

INDEX: idx_notifications_user
INDEX: idx_notifications_conference
INDEX: idx_notifications_status
```

#### `faq_items`
```sql
id              BIGSERIAL PRIMARY KEY
conference_id   BIGINT REFERENCES conferences(id) ON DELETE CASCADE
sort_order      SMALLINT DEFAULT 0
is_published    BOOLEAN DEFAULT TRUE
```

#### `faq_item_translations`
```sql
id              BIGSERIAL PRIMARY KEY
faq_id          BIGINT REFERENCES faq_items(id) ON DELETE CASCADE
locale          VARCHAR(10) NOT NULL
question        TEXT NOT NULL
answer          TEXT NOT NULL

UNIQUE: (faq_id, locale)
```

---

### PostgreSQL Enum Types

```sql
CREATE TYPE conference_status   AS ENUM ('draft', 'active', 'archived');
CREATE TYPE blind_mode          AS ENUM ('open', 'single', 'double');
CREATE TYPE media_type          AS ENUM ('logo', 'cover', 'program', 'gallery');
CREATE TYPE committee_role      AS ENUM ('chair', 'co_chair', 'pc_member', 'organizing', 'technical', 'sponsor');
CREATE TYPE conf_role           AS ENUM ('admin', 'pc_member', 'reviewer');
CREATE TYPE submission_status   AS ENUM ('draft', 'submitted', 'under_review', 'accepted', 'rejected', 'revision_required', 'withdrawn', 'camera_ready');
CREATE TYPE file_type           AS ENUM ('manuscript', 'supplementary', 'camera_ready');
CREATE TYPE criterion_type      AS ENUM ('score', 'text', 'recommendation', 'boolean');
CREATE TYPE invitation_status   AS ENUM ('pending', 'accepted', 'declined', 'expired');
CREATE TYPE assignment_status   AS ENUM ('pending', 'accepted', 'declined', 'in_progress', 'completed', 'reassigned');
CREATE TYPE review_status       AS ENUM ('draft', 'submitted');
CREATE TYPE recommendation_type AS ENUM ('strong_accept', 'accept', 'weak_accept', 'borderline', 'weak_reject', 'reject', 'strong_reject');
CREATE TYPE comment_visibility  AS ENUM ('to_authors', 'to_chair_only');
CREATE TYPE notification_channel AS ENUM ('email', 'in_app');
CREATE TYPE notification_status AS ENUM ('pending', 'sent', 'failed');
```

---

## 7. Core Domain Model and Relationships

```
User ──────────────────────────────────────────────────────────────┐
 │                                                                 │
 ├── owns ──► Conference ──── has many ──► Translations           │
 │             │                           (per locale)           │
 │             ├── has many ──► Tracks ──► Track Translations      │
 │             ├── has many ──► Topics ──► Topic Translations      │
 │             ├── has many ──► ConferenceDates                   │
 │             ├── has many ──► CommitteeMembers                  │
 │             ├── has many ──► ConferenceMedia                   │
 │             ├── has many ──► ConferenceRoles ──► User          │
 │             ├── has one  ──► ReviewForm ──► Criteria           │
 │             └── has many ──► ReviewerInvitations               │
 │                                                                 │
 ├── submits ──► Submission                                        │
 │               ├── belongs to Conference                        │
 │               ├── belongs to Track                             │
 │               ├── has many ──► SubmissionAuthors               │
 │               ├── has many ──► SubmissionFiles                 │
 │               ├── belongs to many Topics                       │
 │               └── has many ──► ReviewAssignments               │
 │                               └── has one ──► Review           │
 │                                              ├── ReviewScores  │
 │                                              └── ReviewComments│
 │                                                                 │
 └── (reviewer) ──► ReviewAssignment ──────────────────────────────┘
```

**Key Relationships Summary:**
- A `Conference` has many `Submissions`
- A `Submission` has many `ReviewAssignments` (one per reviewer)
- A `ReviewAssignment` has exactly one `Review`
- A `Review` has many `ReviewScores` (one per criterion)
- A `User` can have multiple `ConferenceRoles` across different conferences
- `Translations` are always separate tables (never inline JSONB) for queryability

---

## 8. Backend Architecture in Laravel

### Decision: Laravel 13 + Laravel Sanctum + PostgreSQL

```
app/
├── Console/Commands/
│   ├── ExpireInvitations.php      ← scheduled daily
│   └── SendReviewReminders.php    ← scheduled daily
│
├── Enums/
│   ├── ConferenceStatus.php
│   ├── SubmissionStatus.php
│   ├── AssignmentStatus.php
│   ├── ReviewRecommendation.php
│   └── BlindMode.php
│
├── Events/
│   ├── SubmissionReceived.php
│   ├── ReviewerInvited.php
│   ├── ReviewerAccepted.php
│   ├── DecisionMade.php
│   └── CameraReadyRequested.php
│
├── Exceptions/
│   ├── ConferenceClosedException.php
│   ├── SubmissionLockedException.php
│   └── InvitationExpiredException.php
│
├── Http/
│   ├── Controllers/
│   │   └── Api/
│   │       ├── Auth/
│   │       │   ├── LoginController.php
│   │       │   ├── RegisterController.php
│   │       │   ├── LogoutController.php
│   │       │   └── PasswordResetController.php
│   │       ├── Public/
│   │       │   ├── ConferenceController.php   ← SSR data
│   │       │   ├── ConferenceDateController.php
│   │       │   ├── CommitteeController.php
│   │       │   └── FaqController.php
│   │       ├── Author/
│   │       │   ├── SubmissionController.php
│   │       │   ├── SubmissionFileController.php
│   │       │   └── CoAuthorController.php
│   │       ├── Reviewer/
│   │       │   ├── InvitationController.php
│   │       │   ├── AssignmentController.php
│   │       │   └── ReviewController.php
│   │       ├── Admin/
│   │       │   ├── ConferenceController.php
│   │       │   ├── ConferenceContentController.php
│   │       │   ├── ConferenceMediaController.php
│   │       │   ├── CommitteeController.php
│   │       │   ├── ReviewerController.php
│   │       │   ├── SubmissionController.php
│   │       │   ├── AssignmentController.php
│   │       │   ├── DecisionController.php
│   │       │   └── ReviewFormController.php
│   │       └── SuperAdmin/
│   │           ├── ConferenceController.php
│   │           └── UserController.php
│   │
│   ├── Middleware/
│   │   ├── ConferenceAccess.php       ← resolves conference from slug
│   │   ├── EnsureConferenceAdmin.php
│   │   ├── EnsureReviewer.php
│   │   └── SetLocale.php
│   │
│   └── Requests/
│       ├── StoreConferenceRequest.php
│       ├── StoreSubmissionRequest.php
│       ├── StoreReviewRequest.php
│       └── UpdateConferenceContentRequest.php
│
├── Models/
│   ├── User.php
│   ├── Conference.php
│   ├── ConferenceTranslation.php
│   ├── ConferenceMedia.php
│   ├── ConferenceDate.php
│   ├── Track.php
│   ├── Topic.php
│   ├── CommitteeMember.php
│   ├── ConferenceRole.php
│   ├── Submission.php
│   ├── SubmissionAuthor.php
│   ├── SubmissionFile.php
│   ├── ReviewForm.php
│   ├── ReviewCriterion.php
│   ├── ReviewerInvitation.php
│   ├── ReviewAssignment.php
│   ├── Review.php
│   ├── ReviewScore.php
│   ├── ReviewComment.php
│   ├── FaqItem.php
│   └── Notification.php
│
├── Policies/
│   ├── ConferencePolicy.php
│   ├── SubmissionPolicy.php
│   ├── ReviewPolicy.php
│   └── ReviewAssignmentPolicy.php
│
├── Services/
│   ├── ConferenceService.php      ← create, update, publish conference
│   ├── SubmissionService.php      ← submit, withdraw, transition status
│   ├── ReviewService.php          ← save draft, submit review
│   ├── AssignmentService.php      ← assign reviewers, auto-suggest
│   ├── InvitationService.php      ← send invitations, handle token
│   ├── DecisionService.php        ← accept/reject, trigger notifications
│   ├── FileService.php            ← upload, validate, generate URLs
│   ├── NotificationService.php    ← queue email + in-app notifications
│   └── TranslationService.php     ← upsert translations for any model
│
└── Resources/  (API Resources = JSON transformers)
    ├── ConferenceResource.php
    ├── ConferencePublicResource.php  ← locale-aware, no sensitive data
    ├── SubmissionResource.php
    ├── ReviewResource.php
    └── UserResource.php
```

### Service Layer Pattern

```php
// Example: SubmissionService.php
class SubmissionService
{
    public function store(User $user, Conference $conference, array $data): Submission
    {
        $this->assertSubmissionOpen($conference);
        return DB::transaction(function () use ($user, $conference, $data) {
            $submission = $conference->submissions()->create([
                'submitter_id' => $user->id,
                'title'        => $data['title'],
                'abstract'     => $data['abstract'],
                'keywords'     => $data['keywords'],
                'track_id'     => $data['track_id'] ?? null,
                'status'       => SubmissionStatus::Draft,
            ]);
            $this->syncAuthors($submission, $data['authors']);
            $this->syncTopics($submission, $data['topic_ids'] ?? []);
            return $submission;
        });
    }

    public function submit(Submission $submission): Submission
    {
        $this->assertSubmissionOpen($submission->conference);
        $this->assertManuscriptUploaded($submission);
        $submission->update([
            'status'       => SubmissionStatus::Submitted,
            'submitted_at' => now(),
        ]);
        SubmissionReceived::dispatch($submission);
        return $submission;
    }
}
```

### Queue Strategy
- All notification emails → `notifications` queue
- File processing (thumbnail, PDF validation) → `files` queue
- Scheduled commands via `schedule:run` (cron): expire invitations, send reminders

---

## 9. API Design (REST Endpoints)

Base URL: `/api/v1`

### Authentication

```
POST   /auth/register
POST   /auth/login
POST   /auth/logout
POST   /auth/refresh
GET    /auth/me
POST   /auth/password/forgot
POST   /auth/password/reset
```

### Public Conference Endpoints (no auth required)

```
GET    /conferences                         → list active conferences (paginated)
GET    /conferences/{slug}                  → full conference public data
GET    /conferences/{slug}/dates            → important dates
GET    /conferences/{slug}/committee        → committee members
GET    /conferences/{slug}/topics           → topics + tracks
GET    /conferences/{slug}/faq              → FAQ items
```

### Author Endpoints (auth required)

```
GET    /submissions                         → author's own submissions
POST   /submissions                         → create draft
GET    /submissions/{id}                    → single submission
PUT    /submissions/{id}                    → update draft
POST   /submissions/{id}/submit             → transition to submitted
POST   /submissions/{id}/withdraw           → withdraw
GET    /submissions/{id}/files              → list files
POST   /submissions/{id}/files              → upload file (multipart)
DELETE /submissions/{id}/files/{fileId}     → remove file
GET    /submissions/{id}/files/{fileId}/download → signed download URL
GET    /submissions/{id}/reviews            → view review summaries (after decision)
```

### Reviewer Endpoints (auth required, reviewer role in conference)

```
GET    /reviewer/assignments                → all assigned papers
GET    /reviewer/assignments/{id}           → single assignment + paper details
POST   /reviewer/assignments/{id}/respond   → accept or decline { action: "accept"|"decline", reason }
GET    /reviewer/assignments/{id}/review    → get review (draft)
PUT    /reviewer/assignments/{id}/review    → save draft review
POST   /reviewer/assignments/{id}/review/submit → finalize review
GET    /reviewer/assignments/{id}/file      → download manuscript (signed URL)
```

### Invitation Endpoint (no auth required)

```
GET    /invitations/{token}                 → get invitation details
POST   /invitations/{token}/respond         → accept or decline
```

### Conference Admin Endpoints (auth required, admin role in conference)

```
# Conference management
GET    /admin/conferences                        → list managed conferences
POST   /admin/conferences                        → create conference
GET    /admin/conferences/{slug}                 → conference detail
PUT    /admin/conferences/{slug}                 → update conference settings
PATCH  /admin/conferences/{slug}/status          → publish/archive { status }
DELETE /admin/conferences/{slug}                 → delete (if no submissions)

# Content (multilingual)
GET    /admin/conferences/{slug}/content/{locale}     → get content for locale
PUT    /admin/conferences/{slug}/content/{locale}     → upsert content for locale
POST   /admin/conferences/{slug}/dates                → add important date
PUT    /admin/conferences/{slug}/dates/{id}           → update date
DELETE /admin/conferences/{slug}/dates/{id}
POST   /admin/conferences/{slug}/topics               → add topic
PUT    /admin/conferences/{slug}/topics/{id}          → update topic
DELETE /admin/conferences/{slug}/topics/{id}
POST   /admin/conferences/{slug}/faq                  → add FAQ item
PUT    /admin/conferences/{slug}/faq/{id}
DELETE /admin/conferences/{slug}/faq/{id}

# Media
POST   /admin/conferences/{slug}/media               → upload logo/cover (multipart)
DELETE /admin/conferences/{slug}/media/{id}

# Committee
GET    /admin/conferences/{slug}/committee
POST   /admin/conferences/{slug}/committee
PUT    /admin/conferences/{slug}/committee/{id}
DELETE /admin/conferences/{slug}/committee/{id}

# Reviewers
GET    /admin/conferences/{slug}/reviewers           → list reviewers
POST   /admin/conferences/{slug}/reviewers/invite    → send invitation email
DELETE /admin/conferences/{slug}/reviewers/{userId}

# Submissions
GET    /admin/conferences/{slug}/submissions         → paginated, filterable
GET    /admin/conferences/{slug}/submissions/{id}    → full detail + reviews
PATCH  /admin/conferences/{slug}/submissions/{id}/decision  → accept/reject

# Assignments
GET    /admin/conferences/{slug}/assignments         → assignment overview
POST   /admin/conferences/{slug}/assignments         → create assignment { submission_id, reviewer_id }
DELETE /admin/conferences/{slug}/assignments/{id}    → remove assignment

# Review form
GET    /admin/conferences/{slug}/review-form
PUT    /admin/conferences/{slug}/review-form
POST   /admin/conferences/{slug}/review-form/criteria
PUT    /admin/conferences/{slug}/review-form/criteria/{id}
DELETE /admin/conferences/{slug}/review-form/criteria/{id}
```

### Super Admin Endpoints

```
GET    /superadmin/conferences              → all conferences (any status)
PATCH  /superadmin/conferences/{id}/status
GET    /superadmin/users                    → all users (paginated + search)
PATCH  /superadmin/users/{id}/suspend
GET    /superadmin/stats                    → platform-level stats
```

### API Response Conventions

```json
// Success
{
  "data": { ... },
  "meta": { "pagination": { "page": 1, "per_page": 20, "total": 150 } }
}

// Error
{
  "message": "The given data was invalid.",
  "errors": { "title": ["The title field is required."] }
}
```

---

## 10. Frontend Architecture in Next.js

### Decision: Next.js 14+ App Router + next-intl + TanStack Query + Zustand

### Route Groups

```
app/
├── [locale]/                              ← i18n root param
│   ├── layout.tsx                         ← root layout, providers
│   ├── page.tsx                           ← platform home (featured conferences)
│   ├── conferences/
│   │   └── page.tsx                       ← browse all conferences
│   │
│   ├── conference/[slug]/                 ← PUBLIC conference pages (Server Components)
│   │   ├── layout.tsx                     ← conference shell (logo, nav, lang switcher)
│   │   ├── page.tsx                       ← conference home / hero
│   │   ├── cfp/page.tsx
│   │   ├── dates/page.tsx
│   │   ├── committee/page.tsx
│   │   ├── topics/page.tsx
│   │   ├── venue/page.tsx
│   │   ├── faq/page.tsx
│   │   └── contact/page.tsx
│   │
│   ├── (auth)/                            ← auth routes (no sidebar)
│   │   ├── login/page.tsx
│   │   ├── register/page.tsx
│   │   ├── forgot-password/page.tsx
│   │   └── reset-password/page.tsx
│   │
│   ├── invitation/[token]/                ← invitation accept/decline (no auth needed)
│   │   └── page.tsx
│   │
│   ├── (dashboard)/                       ← AUTHOR dashboard
│   │   ├── layout.tsx                     ← sidebar: My Submissions, Profile
│   │   ├── dashboard/page.tsx
│   │   ├── submissions/
│   │   │   ├── page.tsx                   ← list all submissions
│   │   │   ├── new/page.tsx               ← submission wizard
│   │   │   └── [id]/
│   │   │       ├── page.tsx               ← submission detail
│   │   │       └── edit/page.tsx          ← edit draft
│   │   └── profile/page.tsx
│   │
│   ├── (reviewer)/                        ← REVIEWER dashboard
│   │   ├── layout.tsx
│   │   ├── reviewer/page.tsx              ← overview
│   │   └── reviewer/assignments/
│   │       ├── page.tsx                   ← list assignments
│   │       └── [id]/page.tsx              ← review form
│   │
│   ├── (admin)/                           ← CONFERENCE ADMIN
│   │   ├── layout.tsx                     ← admin sidebar
│   │   └── admin/
│   │       ├── page.tsx                   ← conference list
│   │       ├── conferences/new/page.tsx
│   │       └── conferences/[slug]/
│   │           ├── page.tsx               ← overview dashboard
│   │           ├── settings/page.tsx
│   │           ├── content/page.tsx       ← multilingual content editor
│   │           ├── media/page.tsx
│   │           ├── committee/page.tsx
│   │           ├── reviewers/page.tsx
│   │           ├── submissions/
│   │           │   ├── page.tsx
│   │           │   └── [id]/page.tsx
│   │           ├── assignments/page.tsx
│   │           ├── decisions/page.tsx
│   │           └── review-form/page.tsx
│   │
│   └── (superadmin)/
│       ├── layout.tsx
│       └── superadmin/
│           ├── conferences/page.tsx
│           └── users/page.tsx
```

### Component Architecture

```typescript
// Server Component (public conference page — SEO-first)
// app/[locale]/conference/[slug]/page.tsx
export default async function ConferencePage({ params }) {
  const conference = await fetchConference(params.slug, params.locale);
  // Rendered on server — no JS bundle for content
  return <ConferenceHero conference={conference} />;
}

export async function generateStaticParams() {
  const conferences = await fetchActiveConferenceSlugs();
  return conferences.flatMap(slug =>
    ['en', 'fr', 'ja'].map(locale => ({ locale, slug }))
  );
}

// Client Component (submission wizard)
'use client';
export function SubmissionWizard({ conferenceSlug }: Props) {
  const [step, setStep] = useState(1);
  const form = useForm<SubmissionSchema>({ resolver: zodResolver(schema) });
  const mutation = useSubmitDraftMutation(conferenceSlug);
  // ...
}
```

### State Management Strategy

| State Type | Tool |
|---|---|
| Server state (API data) | TanStack Query (React Query) |
| Form state | React Hook Form + Zod |
| Global UI state (sidebar, modals) | Zustand |
| URL state (filters, pagination) | Next.js `useSearchParams` |
| Auth session | Next.js middleware + server session cookie |

---

## 11. Internationalization Strategy

### Decision: Two-layer i18n

**Layer 1: UI strings (Next.js frontend)**
- Library: `next-intl`
- Files: `locales/en/common.json`, `locales/fr/common.json`, `locales/ja/common.json`
- Locale negotiation: URL prefix `/{locale}/...`, default `en`
- Fallback: always `en`

```
locales/
├── en/
│   ├── common.json      ← buttons, labels, navigation
│   ├── submission.json  ← submission form strings
│   ├── review.json      ← review form strings
│   └── email.json       ← email template strings
├── fr/
│   └── ...
└── ja/
    └── ...
```

**Layer 2: Conference content (database)**
- `*_translations` tables for all user-facing conference content
- API returns locale-specific translation based on `Accept-Language` header or explicit `?locale=fr` param
- Fallback chain: requested locale → `en` → first available locale
- Laravel Resource picks correct translation:

```php
// ConferencePublicResource.php
public function toArray(Request $request): array
{
    $locale = $request->get('locale', app()->getLocale());
    $translation = $this->translations
        ->firstWhere('locale', $locale)
        ?? $this->translations->firstWhere('locale', 'en')
        ?? $this->translations->first();

    return [
        'id'          => $this->id,
        'slug'        => $this->slug,
        'title'       => $translation?->title,
        'description' => $translation?->description,
        'cfp_text'    => $translation?->cfp_text,
        // ...
    ];
}
```

**Admin content editor:**
- Tab per locale in content editor
- Visual indicator for missing translations
- One-click "copy from EN" to seed other locales

**Adding a new language:**
1. Add JSON files in `locales/` (frontend)
2. Add migration entry in `locales` config table (post-MVP)
3. No code change required in backend — translation tables are language-agnostic

---

## 12. File Upload and Media Strategy

### Decision: Laravel Filesystem Abstraction (local → S3 path)

**Configuration:**
```env
FILESYSTEM_DISK=local          # switch to 's3' in production
AWS_BUCKET=ginfoconf-files
```

**File categories and rules:**

| Type | Max Size | Accepted MIME | Storage Path |
|---|---|---|---|
| Manuscript (PDF) | 50 MB | `application/pdf` | `submissions/{submission_id}/v{version}/manuscript.pdf` |
| Supplementary | 100 MB | PDF, ZIP, images | `submissions/{submission_id}/supplementary/{uuid}.ext` |
| Camera-ready | 50 MB | `application/pdf` | `submissions/{submission_id}/camera_ready/final.pdf` |
| Conference logo | 5 MB | JPEG, PNG, SVG, WEBP | `conferences/{slug}/logo.{ext}` |
| Conference cover | 10 MB | JPEG, PNG, WEBP | `conferences/{slug}/cover.{ext}` |
| Committee photo | 2 MB | JPEG, PNG, WEBP | `conferences/{slug}/committee/{uuid}.{ext}` |

**Upload flow (manuscript):**
1. Client: multipart POST to `/api/v1/submissions/{id}/files`
2. Laravel: `FileService::storeManuscript()` validates MIME, size, PDF integrity
3. Stored with `Storage::disk('local')->putFileAs(...)`
4. `submission_files` record created with metadata
5. Previous active manuscript marked `is_active = false`

**Download (signed URL pattern):**
```php
// FileService::signedDownloadUrl()
// Local: Laravel signed route with expiry
return URL::temporarySignedRoute('files.download', now()->addMinutes(30), [
    'file' => $file->id,
]);
// S3: Storage::temporaryUrl($path, now()->addMinutes(30))
```

**S3 migration path:**
- Change `FILESYSTEM_DISK=s3` in `.env`
- Run artisan command to migrate existing local files to S3
- No application code changes needed — Storage facade is disk-agnostic

---

## 13. Security and Authorization Strategy

### Authentication
- **Laravel Sanctum** — cookie-based SPA auth for Next.js (same domain via proxy) or token-based for mobile/API
- HTTPS enforced everywhere
- Email verification required before submission
- Rate limiting: 5 login attempts / minute per IP

### Authorization Architecture
- Laravel **Policies** for every major resource
- Conference-scoped roles via `conference_roles` table
- Middleware resolves conference from slug and injects into request lifecycle

```php
// ConferencePolicy.php
public function administerSubmissions(User $user, Conference $conference): bool
{
    return $user->hasRoleInConference('admin', $conference)
        || $user->isSuperAdmin();
}

// ReviewPolicy.php — double-blind enforcement
public function viewReviewerIdentity(User $user, Review $review): bool
{
    // Authors can NEVER see reviewer identity
    if ($user->isAuthorOf($review->submission)) return false;
    // Chairs and PC members can
    return $user->hasRoleInConference(['admin', 'pc_member'], $review->submission->conference);
}
```

### Data Isolation Rules
- All queries are scoped to conference: `Submission::where('conference_id', $conference->id)`
- No cross-conference data leakage possible by design
- Reviewers only see submissions assigned to them (never all submissions)
- Authors see their own submissions only

### File Security
- Manuscripts never served via public URL — always signed temporary URLs
- File paths include UUID segments (not guessable)
- MIME type validated server-side (not just extension)
- Max file size enforced at PHP level AND nginx level

### Input Validation
- All inputs go through Laravel Form Requests with Zod-equivalent validation
- Rich text (CFP content) sanitized with `HTMLPurifier` before storage
- No raw SQL — Eloquent ORM only

### API Security
- CORS strictly configured to Next.js frontend origin
- CSRF tokens for state-changing requests
- API rate limiting per user: 60 requests/minute general, 5/minute for file uploads
- Invitation tokens: cryptographically random 64-char hex, single-use, expire in 14 days

---

## 14. UI/UX Design System Direction

### Visual Style

**Design language:** Clean-slate modern — inspired by Linear.app, Vercel dashboard, and Notion — not Bootstrap forms.

**Color Palette:**
```
Primary:    #2563EB (blue-600)   → actions, links, focus
Success:    #16A34A (green-600)  → accepted, confirmed
Warning:    #D97706 (amber-600)  → revision required, deadlines near
Danger:     #DC2626 (red-600)    → rejected, overdue
Neutral:    #111827 (gray-900) to #F9FAFB (gray-50)
Background: #FFFFFF + #F8FAFC (light) / #0F172A (dark, post-MVP)
```

**Typography:**
- Font: `Inter` (system-level, no custom loading needed)
- Heading scale: 32/24/20/16/14px
- Body: 14px (dashboard), 16px (public pages)
- Monospace: `JetBrains Mono` for paper IDs, tokens

**Spacing:** 4px base grid (Tailwind default)

### Layout Principles

**Public conference pages:**
- Full-width hero with conference cover image
- Max-width 1100px content container, centered
- Sticky header with conference nav + language switcher
- Generous whitespace between sections
- Section anchors: `#cfp`, `#dates`, `#committee`, `#topics`, `#venue`

**Dashboard (author/reviewer/admin):**
- 260px fixed left sidebar (collapsible on mobile)
- Top bar: breadcrumb + user menu
- Content: max-width 1280px, padded
- Card-based layout for KPIs, table for data lists

### Core Reusable Components (Tailwind + Radix UI primitives)

```
components/ui/
├── Button.tsx            ← variants: primary, secondary, ghost, danger
├── Badge.tsx             ← status badges with semantic colors
├── Card.tsx              ← card container with optional header/footer
├── DataTable.tsx         ← sortable, filterable, paginated table
├── Dialog.tsx            ← modal wrapper
├── Dropdown.tsx          ← context menus, select menus
├── FileUpload.tsx        ← drag-drop zone with progress
├── FormField.tsx         ← label + input + error message wrapper
├── RichTextEditor.tsx    ← Tiptap-based editor for CFP content
├── StatusBadge.tsx       ← submission/review status badges
├── LocaleSwitcher.tsx    ← language switcher dropdown
├── DatePicker.tsx        ← date + time picker with timezone
├── Avatar.tsx            ← user avatar with fallback initials
├── Breadcrumb.tsx
├── Tabs.tsx
└── EmptyState.tsx        ← for empty lists with CTA
```

### Author Submission UX

**Submission wizard — 4 steps, progress indicator at top:**

```
Step 1 — Paper Details
  [Title]
  [Abstract]  — character counter
  [Keywords]  — tag input

Step 2 — Authors
  [Your name / affiliation]  — pre-filled from profile
  [+ Add co-author]  — search by email (existing users) or manual entry
  [drag to reorder]

Step 3 — Topics & Track
  [Track dropdown]  — if conference has tracks
  [Topics]  — checkbox list (max 3)

Step 4 — File Upload
  [Drag-drop PDF zone]
  [+ Add supplementary file]
  [Review & Submit]
```

Key UX decisions:
- Auto-save draft after each step (debounced 1s)
- "Save for later" clearly visible — no anxiety about losing work
- Submit button disabled until manuscript uploaded
- Deadline countdown shown in submission form header

### Reviewer Dashboard UX

```
Overview:
  [3 Assigned] [1 Completed] [2 Pending] — KPI cards

Paper queue (table):
  Paper ID | Title | Track | Due Date | Status | Action
  --------|-------|-------|----------|--------|-------
  #042    | Neural... | AI  | Jan 30   | ●Draft | Continue →

Review form layout:
  Left panel (60%):        Right panel (40%):
  ─────────────────        ──────────────────
  Paper title              Your Review
  Abstract                 Criterion 1: [score slider]
  [Download PDF button]    Criterion 2: [score slider]
                           Criterion 3: [score slider]
                           ─────────────────────────
                           Comments to authors:
                           [textarea]
                           ─────────────────────────
                           Comments to chair (private):
                           [textarea]
                           ─────────────────────────
                           Recommendation: [dropdown]
                           ─────────────────────────
                           [Save Draft]  [Submit Review]
```

---

## 15. Recommended Folder Structure

### Backend (Laravel — `ginfoconf/`)

```
ginfoconf/
├── app/
│   ├── Console/Commands/
│   │   ├── ExpireInvitations.php
│   │   └── SendReviewReminders.php
│   ├── Enums/
│   │   ├── BlindMode.php
│   │   ├── ConferenceStatus.php
│   │   ├── SubmissionStatus.php
│   │   ├── AssignmentStatus.php
│   │   └── ReviewRecommendation.php
│   ├── Events/
│   │   ├── SubmissionReceived.php
│   │   ├── ReviewerInvited.php
│   │   ├── ReviewerAccepted.php
│   │   └── DecisionMade.php
│   ├── Exceptions/
│   │   ├── ConferenceClosedException.php
│   │   └── InvitationExpiredException.php
│   ├── Http/
│   │   ├── Controllers/Api/
│   │   │   ├── Auth/
│   │   │   ├── Public/
│   │   │   ├── Author/
│   │   │   ├── Reviewer/
│   │   │   ├── Admin/
│   │   │   └── SuperAdmin/
│   │   ├── Middleware/
│   │   └── Requests/
│   ├── Models/
│   ├── Policies/
│   ├── Resources/           ← API Resources (JSON transformers)
│   └── Services/
├── database/
│   ├── migrations/
│   └── seeders/
│       ├── DatabaseSeeder.php
│       └── DemoConferenceSeeder.php
├── routes/
│   ├── api.php              ← all API routes
│   └── web.php              ← only health check + invitation token route
├── storage/
│   └── app/
│       ├── submissions/     ← manuscript files
│       └── conferences/     ← logos, covers
└── tests/
    ├── Feature/
    │   ├── Auth/
    │   ├── Submission/
    │   └── Review/
    └── Unit/
        └── Services/
```

### Frontend (Next.js — `ginfoconf-frontend/`)

```
ginfoconf-frontend/
├── app/
│   └── [locale]/
│       ├── layout.tsx
│       ├── page.tsx
│       ├── conference/[slug]/
│       ├── (auth)/
│       ├── (dashboard)/
│       ├── (reviewer)/
│       ├── (admin)/
│       └── (superadmin)/
├── components/
│   ├── ui/                   ← primitives (Button, Badge, Dialog...)
│   ├── conference/           ← ConferenceHero, ConferenceNav, DatesList...
│   ├── submission/           ← SubmissionWizard, SubmissionCard, FileUpload...
│   ├── review/               ← ReviewForm, CriterionScore, AssignmentTable...
│   └── admin/                ← AdminSidebar, ContentEditor, ReviewerManager...
├── lib/
│   ├── api/
│   │   ├── client.ts         ← axios instance with interceptors
│   │   ├── conferences.ts    ← typed API functions
│   │   ├── submissions.ts
│   │   └── reviews.ts
│   ├── hooks/
│   │   ├── useConference.ts
│   │   ├── useSubmissions.ts
│   │   └── useAuth.ts
│   ├── stores/
│   │   ├── authStore.ts      ← Zustand
│   │   └── uiStore.ts
│   └── utils/
│       ├── dates.ts
│       └── fileValidation.ts
├── locales/
│   ├── en/
│   │   ├── common.json
│   │   ├── submission.json
│   │   └── review.json
│   ├── fr/
│   └── ja/
├── middleware.ts              ← next-intl locale routing
├── next.config.ts
└── tailwind.config.ts
```

---

## 16. MVP Scope

**Goal:** Working platform that a real conference can use end-to-end.

### MVP includes:

✅ User registration + email verification
✅ Conference creation by super admin or self-service
✅ Conference public page (hero, CFP, dates, topics, committee)
✅ Multilingual content (EN + FR baseline)
✅ Logo and cover image upload
✅ Paper submission wizard (title, abstract, authors, PDF upload)
✅ Submission draft save
✅ Reviewer invitation via email + token link
✅ Review assignment (manual by chair)
✅ Review form (configurable criteria + recommendation)
✅ Draft save for reviews
✅ Accept/reject decision by chair
✅ Email notifications (submission, invitation, decision)
✅ Author notification of decision
✅ Camera-ready file upload
✅ Double-blind mode
✅ Basic admin dashboard (submissions table, review overview)

### MVP excludes (→ Post-MVP):

❌ Japanese translation
❌ Review bidding / conflict of interest
❌ Auto-suggest reviewer assignment
❌ In-app notification feed
❌ Meta-review by PC chair
❌ OAuth (Google, ORCID)
❌ Program committee bidding
❌ Camera-ready formatting check
❌ AI-powered features
❌ Tracks (conferences with single topic area only)
❌ Dark mode

---

## 17. Post-MVP Roadmap

### Phase 2 — Review Excellence
- PC member bidding system (interest + conflicts of interest)
- Auto-assign based on topic match + bid scores
- Meta-review workflow for PC chairs
- Review reminder system (automated emails)
- Review analytics per paper (score distribution)

### Phase 3 — Author Experience
- Co-author account invitation (co-authors receive email to claim submission)
- Rebuttal/response period (author responds to reviews)
- Revision round workflow (submit v2 manuscript)
- Submission search by keyword (full-text PostgreSQL)

### Phase 4 — Multilingual Expansion
- Japanese (JA) full translation
- RTL language support (Arabic)
- Admin UI for managing supported locales
- Machine-translation seed via DeepL API

### Phase 5 — AI Features
- Semantic similarity check between submissions
- Reviewer-paper matching via embedding similarity
- Review quality score (detects low-effort reviews)
- AI-generated review summary for chair
- Automated conflict of interest detection (shared institution/co-authorship)

### Phase 6 — Platform
- Multi-organization accounts (university / society manages multiple conferences)
- Billing / subscription tiers per conference
- White-label support (custom domain per conference)
- Program builder (schedule accepted papers into sessions)
- Proceedings export (PDF book of proceedings)

---

## 18. Implementation Milestones in Recommended Order

### Milestone 1 — Foundation (2 weeks)
1. Set up Laravel 13 + PostgreSQL + Redis
2. Database migrations (all core tables)
3. Laravel Sanctum auth (register, login, email verification)
4. Set up Next.js 14 + next-intl + Tailwind + TanStack Query
5. Locale routing middleware
6. CI pipeline (PHPUnit + Playwright smoke tests)

### Milestone 2 — Conference Public Pages (1.5 weeks)
1. `ConferenceService` — create/update conference + translations
2. Conference admin CRUD API endpoints
3. Conference media upload (logo, cover)
4. Next.js SSG conference public pages
5. Language switcher on public pages
6. Conference admin content editor (multilingual tabs)

### Milestone 3 — Submission Workflow (2 weeks)
1. `SubmissionService` — create, draft, submit, withdraw
2. File upload service + signed download URLs
3. Submission API endpoints
4. Author submission wizard (Next.js, 4 steps)
5. Auto-save draft implementation
6. Co-author management UI
7. Author dashboard (submissions list + status)

### Milestone 4 — Reviewer Onboarding (1.5 weeks)
1. `InvitationService` — generate token, send email
2. `ReviewerInvitation` flow (token landing page, accept/decline)
3. `AssignmentService` — manual assignment by chair
4. Reviewer dashboard (assignment list)
5. Admin reviewer management UI

### Milestone 5 — Review Workflow (2 weeks)
1. `ReviewForm` configurator (admin)
2. Review API (save draft, submit)
3. Reviewer review form UI (split-panel)
4. Double-blind enforcement at API level
5. Review completion tracking for chair

### Milestone 6 — Decisions & Notifications (1 week)
1. `DecisionService` — accept/reject/revision
2. Notification queue (Laravel events + mailables)
3. Decision UI for chair (batch table)
4. Author decision notification email
5. Camera-ready upload flow

### Milestone 7 — Polish & QA (1.5 weeks)
1. Mobile responsiveness audit
2. French translation completeness
3. Email template design (HTML emails)
4. End-to-end Playwright tests (happy paths for each role)
5. Security audit (auth, file access, blind enforcement)
6. Performance: SSG conference pages, API query optimization

**Total MVP timeline: ~12 weeks (3 months with a 2-person team)**

---

## Top 10 Engineering Decisions

| # | Decision | Rationale |
|---|---|---|
| 1 | **PostgreSQL over MySQL** | JSONB for flexible criterion options, native array type for keywords, tsvector full-text search, superior enum support |
| 2 | **`_translations` tables, not JSONB columns** | Translation rows are individually queryable/filterable; JSONB would require app-layer filtering on every query |
| 3 | **Laravel Sanctum over JWT** | Cookie-based auth with Next.js works seamlessly; SPA auth is built for this exact pattern; no token refresh complexity |
| 4 | **Next.js App Router with Server Components for public pages** | Conference pages must be SEO-indexed; SSG/ISR ensures Google sees full content; Client Components only where interactivity needed |
| 5 | **Service layer over fat Controllers** | Business logic in `SubmissionService`, `ReviewService` etc. — controllers stay thin; services are testable, reusable |
| 6 | **Filesystem abstraction from day 1** | `Storage::disk()` means switching local → S3 is one config change; signed URLs work identically on both |
| 7 | **Conference-scoped role table over Spatie permissions** | A user is a reviewer in Conference A and an author in Conference B — conference-scoped roles in `conference_roles` is cleaner than Spatie's string-based roles for multi-tenancy |
| 8 | **Token-based reviewer invitation (no account required to decline)** | Eliminates friction for reviewers; they can accept/decline with one click from email; account creation only required on accept |
| 9 | **Double-blind at API Policy level, not just UI** | Enforced in `ReviewPolicy` and API Resources — removing the UI wouldn't expose reviewer identity |
| 10 | **`submission_status` state machine, not boolean flags** | Single canonical status column prevents inconsistent states (e.g., `is_accepted=true AND is_rejected=true`); transitions validated in `SubmissionService` |

---

## Top 10 Product Risks

| # | Risk | Likelihood | Mitigation |
|---|---|---|---|
| 1 | **Reviewers not completing reviews on time** | High | Automated reminder emails (7d, 3d, 1d before due); clear deadline in dashboard |
| 2 | **Double-blind broken by PDF metadata** | Medium | Warn authors during upload: "Ensure your PDF contains no author metadata"; post-MVP: automated PDF metadata stripping |
| 3 | **Reviewer invitation emails go to spam** | Medium | Verified sending domain (SPF/DKIM/DMARC from day 1); plain-text email option |
| 4 | **Authors submit wrong file versions** | Medium | Clear version history in UI; "current active version" always shown; easy re-upload |
| 5 | **Conference deadline confusion across timezones** | High | Always show deadline in user's local timezone + UTC; countdown timer |
| 6 | **Small conference chairs overwhelmed by manual assignment** | Medium | Even without auto-assign, provide topic-match hints and a clear assignment matrix UI |
| 7 | **Platform cold-start (no conferences = no value)** | High | Super admin can create demo conferences; invite early adopter conferences with white-glove setup |
| 8 | **Performance degradation with large conferences (1000+ submissions)** | Low initially | Proper DB indexes from day 1; paginated APIs; SSG for public pages; addressed before scale issues emerge |
| 9 | **Multilingual content always incomplete (untranslated sections)** | Medium | UI shows completeness indicator per locale; fallback to EN always shown rather than broken UI |
| 10 | **Chair loses control of review timeline** | Medium | Admin dashboard shows "X of Y reviews complete" per paper; send batch reminders from admin UI |

---

## Top 10 Future AI Extensions

The architecture is AI-ready because: all content is in structured PostgreSQL, manuscripts are stored in addressable paths, and the Service layer can be extended with AI calls without touching controllers.

| # | AI Feature | Data it uses | Where to hook |
|---|---|---|---|
| 1 | **Reviewer-paper semantic matching** | Submission abstracts + reviewer past publications / interests | `AssignmentService::suggestReviewers()` → embedding similarity via pgvector |
| 2 | **Conflict of interest detection** | Co-author lists, affiliations, past submissions | `AssignmentService` → flag potential conflicts before assignment |
| 3 | **Review quality scoring** | Submitted reviews (text length, score distribution, specificity) | Post-review-submit hook → `ReviewQualityService` |
| 4 | **AI-generated chair summary per paper** | All reviews for a submission | `DecisionService::generateSummary()` → LLM prompt via Claude API |
| 5 **Submission similarity / plagiarism detection** | All PDF manuscripts in conference | Background job on submission → extract text, compare embeddings |
| 6 | **Abstract quality feedback** | Submission abstract | Live feedback during submission wizard (Client Component) → `/api/ai/abstract-feedback` |
| 7 | **Smart keyword suggestion** | Abstract text | Submission wizard Step 1 → suggest keywords via NLP |
| 8 | **Reviewer recommendation engine** | Acceptance history, review scores, topics | Surface "best reviewers for this paper" in assignment UI |
| 9 | **Acceptance prediction** | Historical acceptance data, review scores | Chair dashboard → "predicted acceptance probability" badge (advisory only) |
| 10 | **Auto-translated CFP content** | English CFP → other locales | Admin content editor → "Auto-translate from EN" button → DeepL/Claude API |

---

*Blueprint version 1.0 — ginfoconf — March 2026*
*Ready for implementation milestone 1.*
