<?php

namespace Database\Seeders;

use App\Models\Conference;
use App\Models\Review;
use App\Models\ReviewAssignment;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Database\Seeder;

class ReviewsSeeder extends Seeder
{
    public function run(): void
    {
        // Each entry: submission title (partial), conference, reviewer emails, and assignment/review data
        $assignments = [

            // ── AfricAI 2025 — "Swahili Sentiment" (under_review) ─────────────────
            [
                'conference'  => 'africai-2025',
                'title_frag'  => 'Swahili Sentiment',
                'chair'       => 'chair2@ginfoconf.test',
                'reviewers'   => [
                    [
                        'email'  => 'reviewer@ginfoconf.test',
                        'status' => 'in_progress',
                        'due'    => now()->addDays(20),
                        'review' => null,
                    ],
                    [
                        'email'  => 'reviewer9@ginfoconf.test',
                        'status' => 'accepted',
                        'due'    => now()->addDays(20),
                        'review' => null,
                    ],
                ],
            ],

            // ── AfricAI 2025 — "Bias in Facial Recognition" (accepted) ────────────
            [
                'conference'  => 'africai-2025',
                'title_frag'  => 'Bias in Facial Recognition',
                'chair'       => 'chair2@ginfoconf.test',
                'reviewers'   => [
                    [
                        'email'  => 'reviewer@ginfoconf.test',
                        'status' => 'completed',
                        'due'    => now()->subDays(3),
                        'review' => [
                            'status'              => 'submitted',
                            'overall_score'       => 8,
                            'recommendation'      => 'strong_accept',
                            'comments_to_authors' => "Excellent and timely study. The dataset is large and diverse, methodology is sound, and the debiasing framework is a genuine contribution. Minor concerns:\n1. The paper could better position itself against the recent ACM FAccT 2024 work by Buolamwini et al.\n2. Figure 3 is hard to read in greyscale — please use patterns or higher-contrast colours.\n3. The appendix section on data collection ethics should be moved to the main body.",
                            'comments_to_chair'   => 'Strong accept. This addresses a critical gap and the dataset alone is a valuable community resource. I recommend acceptance with minor revisions.',
                            'submitted_at'        => now()->subDays(4),
                        ],
                    ],
                    [
                        'email'  => 'reviewer2@ginfoconf.test',
                        'status' => 'completed',
                        'due'    => now()->subDays(3),
                        'review' => [
                            'status'              => 'submitted',
                            'overall_score'       => 7,
                            'recommendation'      => 'accept',
                            'comments_to_authors' => "Good paper on an important topic. The experimental section is thorough and the intersectional analysis adds depth. Suggestions:\n1. The literature review omits several key papers from 2023-2024 on African face datasets.\n2. The statistical significance of the improvements should be reported (confidence intervals or p-values).\n3. Limitations section should discuss API versioning — the tested APIs may have changed since evaluation.",
                            'comments_to_chair'   => 'Accept with minor revisions. The contribution is solid and relevant to the venue.',
                            'submitted_at'        => now()->subDays(2),
                        ],
                    ],
                ],
            ],

            // ── SecureNet 2025 — "GNN-Based Intrusion Detection" (accepted) ────────
            [
                'conference'  => 'securenet-2025',
                'title_frag'  => 'GNN-Based Intrusion Detection',
                'chair'       => 'chair3@ginfoconf.test',
                'reviewers'   => [
                    [
                        'email'  => 'reviewer3@ginfoconf.test',
                        'status' => 'completed',
                        'due'    => now()->subDays(5),
                        'review' => [
                            'status'              => 'submitted',
                            'overall_score'       => 9,
                            'recommendation'      => 'strong_accept',
                            'comments_to_authors' => "This is an impressive piece of work. The graph construction from flow-level features is elegant and the ablation study is thorough. Minor points:\n1. Please clarify how you handle flows with very short durations (< 5 packets) in the graph construction.\n2. The privacy implications of collecting flow-level features should be briefly discussed.\n3. Add runtime complexity analysis comparing to CNN and LSTM baselines.",
                            'comments_to_chair'   => 'Top-quality submission. Should be a strong accept.',
                            'submitted_at'        => now()->subDays(6),
                        ],
                    ],
                    [
                        'email'  => 'reviewer7@ginfoconf.test',
                        'status' => 'completed',
                        'due'    => now()->subDays(5),
                        'review' => [
                            'status'              => 'submitted',
                            'overall_score'       => 7,
                            'recommendation'      => 'accept',
                            'comments_to_authors' => "Solid contribution to the IDS literature. The GNN formulation is well-motivated. Concerns:\n1. The evaluation dataset (CIC-IDS-2017) is dated — please include at least one evaluation on a more recent dataset.\n2. The threshold for edge creation in the graph seems sensitive — provide a sensitivity analysis.\n3. Comparison to LUCID (Donadel et al., 2023) is missing.",
                            'comments_to_chair'   => 'Accept, though authors should address the dataset recency concern in the camera-ready.',
                            'submitted_at'        => now()->subDays(5),
                        ],
                    ],
                ],
            ],

            // ── SecureNet 2025 — "Lattice-Based Key Encapsulation" (under_review) ──
            [
                'conference'  => 'securenet-2025',
                'title_frag'  => 'Lattice-Based Key Encapsulation',
                'chair'       => 'chair3@ginfoconf.test',
                'reviewers'   => [
                    [
                        'email'  => 'reviewer5@ginfoconf.test',
                        'status' => 'in_progress',
                        'due'    => now()->addDays(15),
                        'review' => [
                            'status'              => 'draft',
                            'overall_score'       => 7,
                            'recommendation'      => 'weak_accept',
                            'comments_to_authors' => "Initial notes — work in progress review.\n\nThe optimisation work appears sound but I need to verify the claimed speedup numbers independently. The paper is clearly written.",
                            'comments_to_chair'   => null,
                            'submitted_at'        => null,
                        ],
                    ],
                    [
                        'email'  => 'reviewer10@ginfoconf.test',
                        'status' => 'accepted',
                        'due'    => now()->addDays(15),
                        'review' => null,
                    ],
                ],
            ],

            // ── SecureNet 2025 — "Privilege Escalation" (revision_required) ────────
            [
                'conference'  => 'securenet-2025',
                'title_frag'  => 'Privilege Escalation Vulnerabilities',
                'chair'       => 'chair3@ginfoconf.test',
                'reviewers'   => [
                    [
                        'email'  => 'reviewer3@ginfoconf.test',
                        'status' => 'completed',
                        'due'    => now()->subDays(4),
                        'review' => [
                            'status'              => 'submitted',
                            'overall_score'       => 5,
                            'recommendation'      => 'borderline',
                            'comments_to_authors' => "The tool idea is interesting but the evaluation has significant weaknesses:\n1. The ground truth for the 312 vulnerabilities is unclear — how were they confirmed as true positives?\n2. The 3.2% FPR seems low but is evaluated only on apps downloaded within a short window. Need a longitudinal study.\n3. The hypergraph model is not sufficiently explained — the formal definition in Section 4 has notational inconsistencies.\n4. Comparison to FlowDroid and Amandroid is needed.\n\nRevisions required before acceptance.",
                            'comments_to_chair'   => 'Revision required. The core idea is sound but evaluation needs significant improvement.',
                            'submitted_at'        => now()->subDays(5),
                        ],
                    ],
                    [
                        'email'  => 'reviewer7@ginfoconf.test',
                        'status' => 'completed',
                        'due'    => now()->subDays(4),
                        'review' => [
                            'status'              => 'submitted',
                            'overall_score'       => 4,
                            'recommendation'      => 'weak_reject',
                            'comments_to_authors' => "While the problem is relevant, the paper oversells its contributions. The hypergraph model is presented as novel but similar approaches exist (e.g., HyperDroid, 2022). The false-positive evaluation is unconvincing. Major revision needed.",
                            'comments_to_chair'   => 'Weak reject / major revision. The novelty claims need to be tempered.',
                            'submitted_at'        => now()->subDays(3),
                        ],
                    ],
                ],
            ],

            // ── SecureNet 2025 — "Zero-Trust" (rejected) ──────────────────────────
            [
                'conference'  => 'securenet-2025',
                'title_frag'  => 'Zero-Trust Architecture',
                'chair'       => 'chair3@ginfoconf.test',
                'reviewers'   => [
                    [
                        'email'  => 'reviewer5@ginfoconf.test',
                        'status' => 'completed',
                        'due'    => now()->subDays(6),
                        'review' => [
                            'status'              => 'submitted',
                            'overall_score'       => 3,
                            'recommendation'      => 'reject',
                            'comments_to_authors' => "The paper is primarily a description of a deployment and lacks scientific novelty. The proposed framework does not extend beyond well-known industry best practices (NIST SP 800-207). There is no formal evaluation and no comparison to existing frameworks. The case study lacks quantitative metrics.",
                            'comments_to_chair'   => 'Reject. Better suited to a practitioner track or industry paper venue.',
                            'submitted_at'        => now()->subDays(7),
                        ],
                    ],
                    [
                        'email'  => 'reviewer10@ginfoconf.test',
                        'status' => 'completed',
                        'due'    => now()->subDays(6),
                        'review' => [
                            'status'              => 'submitted',
                            'overall_score'       => 3,
                            'recommendation'      => 'reject',
                            'comments_to_authors' => "The work has merit as an experience report but does not meet the research bar for SecureNet. Novelty is insufficient. Suggest resubmitting to a systems/deployment track.",
                            'comments_to_chair'   => 'Reject.',
                            'submitted_at'        => now()->subDays(6),
                        ],
                    ],
                ],
            ],

            // ── DataMine 2024 — "CHUI-Stream" (camera_ready) ─────────────────────
            [
                'conference'  => 'datamine-2024',
                'title_frag'  => 'Closed High-Utility Itemset',
                'chair'       => 'chair4@ginfoconf.test',
                'reviewers'   => [
                    [
                        'email'  => 'reviewer6@ginfoconf.test',
                        'status' => 'completed',
                        'due'    => now()->subDays(120),
                        'review' => [
                            'status'              => 'submitted',
                            'overall_score'       => 8,
                            'recommendation'      => 'accept',
                            'comments_to_authors' => "Strong theoretical contribution. The Count-Min Sketch integration is well-motivated. The experimental results on real datasets are convincing. Minor: please add the proof of Lemma 3 (currently deferred to a non-existent appendix).",
                            'comments_to_chair'   => 'Accept.',
                            'submitted_at'        => now()->subDays(125),
                        ],
                    ],
                    [
                        'email'  => 'reviewer3@ginfoconf.test',
                        'status' => 'completed',
                        'due'    => now()->subDays(120),
                        'review' => [
                            'status'              => 'submitted',
                            'overall_score'       => 7,
                            'recommendation'      => 'accept',
                            'comments_to_authors' => "Good paper. The comparison to exact methods is fair and the memory savings are significant. I would appreciate more detail on the threshold selection heuristic.",
                            'comments_to_chair'   => 'Accept with minor revisions.',
                            'submitted_at'        => now()->subDays(122),
                        ],
                    ],
                ],
            ],

            // ── IoTConnect 2025 — "Digital Twin Textile" (under_review) ───────────
            [
                'conference'  => 'iotconnect-2025',
                'title_frag'  => 'Digital Twin-Driven Predictive',
                'chair'       => 'chair3@ginfoconf.test',
                'reviewers'   => [
                    [
                        'email'  => 'reviewer10@ginfoconf.test',
                        'status' => 'in_progress',
                        'due'    => now()->addDays(18),
                        'review' => null,
                    ],
                    [
                        'email'  => 'reviewer7@ginfoconf.test',
                        'status' => 'pending',
                        'due'    => now()->addDays(18),
                        'review' => null,
                    ],
                ],
            ],

            // ── GreenComp 2024 — "Energy JS Frameworks" (camera_ready) ────────────
            [
                'conference'  => 'greencomp-2024',
                'title_frag'  => 'Energy Consumption Profiling',
                'chair'       => 'chair5@ginfoconf.test',
                'reviewers'   => [
                    [
                        'email'  => 'reviewer4@ginfoconf.test',
                        'status' => 'completed',
                        'due'    => now()->subDays(205),
                        'review' => [
                            'status'              => 'submitted',
                            'overall_score'       => 9,
                            'recommendation'      => 'strong_accept',
                            'comments_to_authors' => "Excellent empirical study with clear practical implications. The methodology is rigorous and reproducible. The energy measurement setup is well described. Minor: include watt-hour numbers alongside percentages in the main results table for absolute comparison.",
                            'comments_to_chair'   => 'Strong accept. One of the best papers I have reviewed this cycle.',
                            'submitted_at'        => now()->subDays(210),
                        ],
                    ],
                    [
                        'email'  => 'reviewer8@ginfoconf.test',
                        'status' => 'completed',
                        'due'    => now()->subDays(205),
                        'review' => [
                            'status'              => 'submitted',
                            'overall_score'       => 8,
                            'recommendation'      => 'accept',
                            'comments_to_authors' => "Well-executed benchmarking study. The threat to validity section is thorough. Consider adding a discussion of how server-side rendering (SSR) modes affect the findings, since many production deployments use SSR.",
                            'comments_to_chair'   => 'Accept.',
                            'submitted_at'        => now()->subDays(207),
                        ],
                    ],
                ],
            ],
        ];

        $count = 0;
        foreach ($assignments as $entry) {
            $conference = Conference::where('slug', $entry['conference'])->first();
            if (!$conference) continue;

            $submission = Submission::where('conference_id', $conference->id)
                ->where('title', 'LIKE', '%' . $entry['title_frag'] . '%')
                ->first();
            if (!$submission) {
                $this->command->warn("  Submission '{$entry['title_frag']}' not found, skipping.");
                continue;
            }

            $chair = User::where('email', $entry['chair'])->firstOrFail();

            foreach ($entry['reviewers'] as $rvData) {
                $reviewer = User::where('email', $rvData['email'])->first();
                if (!$reviewer) continue;

                $assignment = ReviewAssignment::firstOrCreate(
                    ['submission_id' => $submission->id, 'reviewer_id' => $reviewer->id],
                    [
                        'assigned_by'  => $chair->id,
                        'status'       => $rvData['status'],
                        'due_date'     => $rvData['due'],
                        'assigned_at'  => now()->subDays(rand(1, 5)),
                        'responded_at' => in_array($rvData['status'], ['in_progress', 'accepted', 'completed', 'declined'])
                            ? now()->subDays(rand(1, 3)) : null,
                        'completed_at' => $rvData['status'] === 'completed' ? now()->subDays(1) : null,
                    ]
                );

                if (!empty($rvData['review'])) {
                    $rv = $rvData['review'];
                    Review::updateOrCreate(
                        ['assignment_id' => $assignment->id],
                        [
                            'submission_id'       => $submission->id,
                            'reviewer_id'         => $reviewer->id,
                            'status'              => $rv['status'],
                            'overall_score'       => $rv['overall_score'],
                            'recommendation'      => $rv['recommendation'],
                            'comments_to_authors' => $rv['comments_to_authors'],
                            'comments_to_chair'   => $rv['comments_to_chair'] ?? null,
                            'submitted_at'        => $rv['submitted_at'] ?? null,
                        ]
                    );
                }

                $count++;
            }
        }

        $this->command->info("{$count} review assignments (and associated reviews) seeded.");
    }
}
