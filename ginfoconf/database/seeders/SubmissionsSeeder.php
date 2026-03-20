<?php

namespace Database\Seeders;

use App\Models\Conference;
use App\Models\Submission;
use App\Models\SubmissionAuthor;
use App\Models\Track;
use App\Models\User;
use Illuminate\Database\Seeder;

class SubmissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Spread submissions across active/archived conferences
        $submissions = [

            // ── AfricAI 2025 ──────────────────────────────────────────────────────
            [
                'conference' => 'africai-2025',
                'track'      => 'ml-africa',
                'submitter'  => 'author@ginfoconf.test',
                'title'      => 'Swahili Sentiment Analysis Using Low-Resource Transfer Learning',
                'abstract'   => 'This paper presents a transfer learning approach for sentiment analysis in Swahili, a morphologically rich language with limited annotated resources. We fine-tune multilingual transformer models on a newly collected Swahili social media dataset and compare against baseline approaches. Our model achieves 87.3% accuracy, outperforming previous methods by 9 points.',
                'keywords'   => ['NLP', 'Swahili', 'Transfer Learning', 'Sentiment Analysis', 'Low-Resource'],
                'status'     => 'under_review',
                'submitted_at' => now()->subDays(8),
                'co_authors' => ['author2@ginfoconf.test'],
            ],
            [
                'conference' => 'africai-2025',
                'track'      => 'ml-africa',
                'submitter'  => 'author3@ginfoconf.test',
                'title'      => 'Crop Disease Detection in Uganda Using Lightweight CNNs on Edge Devices',
                'abstract'   => 'We propose a lightweight convolutional neural network architecture optimized for deployment on low-cost Android smartphones to detect common crop diseases in Ugandan smallholder farms. Our model achieves 94% accuracy on a dataset of 12,000 images while running inference in under 200ms on a $50 device.',
                'keywords'   => ['Computer Vision', 'Agriculture', 'Edge AI', 'CNN', 'Mobile Deployment'],
                'status'     => 'submitted',
                'submitted_at' => now()->subDays(3),
                'co_authors' => [],
            ],
            [
                'conference' => 'africai-2025',
                'track'      => 'ai-ethics',
                'submitter'  => 'author10@ginfoconf.test',
                'title'      => 'Bias in Facial Recognition Systems Across Sub-Saharan African Demographics',
                'abstract'   => 'This study evaluates five commercial facial recognition APIs on a dataset of 10,000 images sourced from diverse Sub-Saharan African populations. We document significant performance disparities across gender and ethnic groups and propose a debiasing framework based on balanced training and adversarial regularization.',
                'keywords'   => ['Fairness', 'Facial Recognition', 'Bias', 'Africa', 'AI Ethics'],
                'status'     => 'accepted',
                'submitted_at' => now()->subDays(12),
                'decided_at' => now()->subDays(1),
                'co_authors' => ['author8@ginfoconf.test'],
            ],

            // ── SecureNet 2025 ────────────────────────────────────────────────────
            [
                'conference' => 'securenet-2025',
                'track'      => 'network-sec',
                'submitter'  => 'author4@ginfoconf.test',
                'title'      => 'GNN-Based Intrusion Detection for Encrypted Traffic Classification',
                'abstract'   => 'We propose a graph neural network approach to classify malicious traffic in encrypted network flows without payload inspection. By modelling network flows as graphs, our method captures structural patterns that evade classical signature-based detection, achieving 98.1% F1-score on the CIC-IDS-2017 benchmark.',
                'keywords'   => ['Graph Neural Networks', 'IDS', 'Encrypted Traffic', 'Network Security'],
                'status'     => 'accepted',
                'submitted_at' => now()->subDays(28),
                'decided_at' => now()->subDays(3),
                'co_authors' => ['author11@ginfoconf.test'],
            ],
            [
                'conference' => 'securenet-2025',
                'track'      => 'crypto',
                'submitter'  => 'author12@ginfoconf.test',
                'title'      => 'Efficient Lattice-Based Key Encapsulation for Constrained IoT Devices',
                'abstract'   => 'We present an optimised implementation of CRYSTALS-Kyber tailored for ARM Cortex-M4 microcontrollers. Through custom assembly routines and memory layout optimisations, we reduce encapsulation time by 34% and memory footprint by 28% compared to the reference implementation, enabling post-quantum security on Class 1 IoT devices.',
                'keywords'   => ['Post-Quantum Cryptography', 'Kyber', 'IoT', 'ARM Cortex-M4', 'NIST PQC'],
                'status'     => 'under_review',
                'submitted_at' => now()->subDays(26),
                'co_authors' => ['author9@ginfoconf.test'],
            ],
            [
                'conference' => 'securenet-2025',
                'track'      => 'mobile-sec',
                'submitter'  => 'author7@ginfoconf.test',
                'title'      => 'Automated Detection of Privilege Escalation Vulnerabilities in Android Apps',
                'abstract'   => 'We introduce AndroidScan, a static analysis tool that automatically identifies privilege escalation vulnerabilities in Android applications by modelling inter-component communication as a directed hypergraph. Evaluated on 5,000 apps from Google Play, AndroidScan found 312 previously unreported vulnerabilities with a false-positive rate of 3.2%.',
                'keywords'   => ['Android Security', 'Static Analysis', 'Privilege Escalation', 'Mobile Malware'],
                'status'     => 'revision_required',
                'submitted_at' => now()->subDays(27),
                'decided_at' => now()->subDays(2),
                'co_authors' => [],
            ],
            [
                'conference' => 'securenet-2025',
                'track'      => 'network-sec',
                'submitter'  => 'author14@ginfoconf.test',
                'title'      => 'Zero-Trust Architecture for Multi-Cloud Environments: A Practical Framework',
                'abstract'   => 'This paper proposes a zero-trust security framework for organisations operating across multiple cloud providers. We define policies for identity verification, micro-segmentation, and continuous monitoring, and demonstrate the framework\'s effectiveness through a case study at a large financial institution.',
                'keywords'   => ['Zero Trust', 'Multi-Cloud', 'Identity Management', 'Micro-Segmentation'],
                'status'     => 'rejected',
                'submitted_at' => now()->subDays(28),
                'decided_at' => now()->subDays(4),
                'co_authors' => [],
            ],

            // ── DataMine 2024 (archived) ──────────────────────────────────────────
            [
                'conference' => 'datamine-2024',
                'track'      => 'pattern-mining',
                'submitter'  => 'author5@ginfoconf.test',
                'title'      => 'Closed High-Utility Itemset Mining on Data Streams with Approximate Counting',
                'abstract'   => 'We propose CHUI-Stream, an algorithm for mining closed high-utility itemsets from transactional data streams using approximate counting via Count-Min Sketch. Our method reduces memory consumption by up to 60% compared to exact methods while guaranteeing bounded error on utility estimates.',
                'keywords'   => ['Itemset Mining', 'Data Streams', 'Count-Min Sketch', 'High Utility'],
                'status'     => 'camera_ready',
                'submitted_at' => now()->subDays(170),
                'decided_at' => now()->subDays(95),
                'co_authors' => ['author6@ginfoconf.test'],
            ],
            [
                'conference' => 'datamine-2024',
                'track'      => 'anomaly-det',
                'submitter'  => 'author15@ginfoconf.test',
                'title'      => 'Contrastive Self-Supervised Learning for Anomaly Detection in Time Series',
                'abstract'   => 'We present TS-ContrastAD, a self-supervised framework for time series anomaly detection using contrastive learning. By learning representations that maximise similarity between augmented views of normal segments and minimise it for anomalous windows, our model achieves state-of-the-art results on five public benchmarks without labelled data.',
                'keywords'   => ['Time Series', 'Anomaly Detection', 'Contrastive Learning', 'Self-Supervised'],
                'status'     => 'accepted',
                'submitted_at' => now()->subDays(165),
                'decided_at' => now()->subDays(98),
                'co_authors' => [],
            ],

            // ── WebTech 2025 ──────────────────────────────────────────────────────
            [
                'conference' => 'webtech-2025',
                'track'      => 'frontend',
                'submitter'  => 'author6@ginfoconf.test',
                'title'      => 'Reactive Islands: Selective Hydration Patterns for Server-Rendered Web Apps',
                'abstract'   => 'This paper proposes a component-level hydration model called Reactive Islands for server-rendered JavaScript applications. We implement the model as a compile-time transformation pass for React and demonstrate a 45% reduction in Time-to-Interactive and 30% reduction in main thread blocking time on a production e-commerce benchmark.',
                'keywords'   => ['Web Performance', 'Hydration', 'React', 'SSR', 'Core Web Vitals'],
                'status'     => 'submitted',
                'submitted_at' => now()->subDays(5),
                'co_authors' => ['author9@ginfoconf.test'],
            ],
            [
                'conference' => 'webtech-2025',
                'track'      => 'backend-api',
                'submitter'  => 'author11@ginfoconf.test',
                'title'      => 'GraphQL Federation at Scale: Lessons from a 200-Service Supergraph',
                'abstract'   => 'We document the architecture, operational challenges, and solutions for a GraphQL federation deployment spanning 200 microservices in a large-scale SaaS platform. We describe schema governance, query planning optimisations, and a novel subgraph canary testing framework that reduced production schema-breaking changes by 78%.',
                'keywords'   => ['GraphQL', 'Federation', 'Microservices', 'API Design', 'Schema Governance'],
                'status'     => 'under_review',
                'submitted_at' => now()->subDays(14),
                'co_authors' => [],
            ],

            // ── BioInfo 2025 ──────────────────────────────────────────────────────
            [
                'conference' => 'bioinfo-2025',
                'track'      => 'genomics',
                'submitter'  => 'author13@ginfoconf.test',
                'title'      => 'Long-Read Assembly of the Nigerian Sickle Cell Disease Variant Landscape',
                'abstract'   => 'We present a whole-genome sequencing study of 500 Nigerian individuals with sickle cell disease using Oxford Nanopore long-read technology. Our assembly pipeline reveals 23 novel structural variants in the HBB locus and surrounding regulatory regions that are absent from existing African reference panels, with potential implications for disease severity prediction.',
                'keywords'   => ['Long-Read Sequencing', 'Sickle Cell', 'Structural Variants', 'Nanopore', 'African Genomics'],
                'status'     => 'submitted',
                'submitted_at' => now()->subDays(7),
                'co_authors' => ['author10@ginfoconf.test'],
            ],
            [
                'conference' => 'bioinfo-2025',
                'track'      => 'ml-bio',
                'submitter'  => 'author4@ginfoconf.test',
                'title'      => 'Graph Transformers for Protein–Protein Interaction Prediction',
                'abstract'   => 'We propose ProteinGT, a graph transformer architecture that jointly models amino acid sequence and 3D structural information for protein–protein interaction prediction. ProteinGT outperforms AlphaFold-Multimer on six benchmarks and runs 4× faster, making it practical for large interactome-scale analyses.',
                'keywords'   => ['Graph Transformers', 'Protein Interactions', 'Structural Biology', 'Deep Learning'],
                'status'     => 'under_review',
                'submitted_at' => now()->subDays(18),
                'co_authors' => ['author11@ginfoconf.test', 'author15@ginfoconf.test'],
            ],

            // ── IoTConnect 2025 ───────────────────────────────────────────────────
            [
                'conference' => 'iotconnect-2025',
                'track'      => 'smart-cities',
                'submitter'  => 'author2@ginfoconf.test',
                'title'      => 'Solar-Powered LoRaWAN Soil Moisture Sensors for Smallholder Irrigation in the Sahel',
                'abstract'   => 'We deploy a low-power IoT network of 120 solar-powered soil moisture sensors across five villages in Burkina Faso using LoRaWAN. Our system enables data-driven irrigation scheduling that reduced water consumption by 35% while increasing crop yields by 22% over a full growing season.',
                'keywords'   => ['LoRaWAN', 'Smart Agriculture', 'LPWAN', 'Soil Moisture', 'Precision Irrigation'],
                'status'     => 'submitted',
                'submitted_at' => now()->subDays(4),
                'co_authors' => ['author3@ginfoconf.test', 'author5@ginfoconf.test'],
            ],
            [
                'conference' => 'iotconnect-2025',
                'track'      => 'iiot',
                'submitter'  => 'author7@ginfoconf.test',
                'title'      => 'Digital Twin-Driven Predictive Maintenance for Textile Machinery in Ghana',
                'abstract'   => 'This paper describes the design and deployment of a digital twin system for predictive maintenance of industrial looms in a Ghanaian textile factory. Using vibration, temperature, and current sensors alongside an LSTM-based anomaly detector, we achieved a 67% reduction in unplanned downtime over six months.',
                'keywords'   => ['Digital Twins', 'Predictive Maintenance', 'IIoT', 'LSTM', 'Industry 4.0'],
                'status'     => 'under_review',
                'submitted_at' => now()->subDays(20),
                'co_authors' => [],
            ],

            // ── MobileApp 2025 ────────────────────────────────────────────────────
            [
                'conference' => 'mobileapp-2025',
                'track'      => 'mobile-dev',
                'submitter'  => 'author5@ginfoconf.test',
                'title'      => 'Offline-First Mobile Banking for Rural West Africa: Architecture and Evaluation',
                'abstract'   => 'We present the design and field evaluation of an offline-first mobile banking application built with Flutter and CRDTs (Conflict-free Replicated Data Types) for use in areas with intermittent connectivity in rural Mali. After six months of deployment to 1,200 users, the app handled 98.3% of transactions successfully despite an average offline duration of 4.2 hours per day.',
                'keywords'   => ['Offline-First', 'Flutter', 'CRDTs', 'FinTech', 'Mobile Banking', 'Rural Connectivity'],
                'status'     => 'under_review',
                'submitted_at' => now()->subDays(6),
                'co_authors' => ['author2@ginfoconf.test'],
            ],
            [
                'conference' => 'mobileapp-2025',
                'track'      => 'mobile-ux',
                'submitter'  => 'author8@ginfoconf.test',
                'title'      => 'Touch Target Size and Spacing Guidelines for Users with Motor Impairments on Mobile',
                'abstract'   => 'Through a controlled study with 60 participants with varying degrees of motor impairment, we derive empirical guidelines for touch target sizing and spacing on mobile interfaces. Our results challenge existing guidelines (WCAG 2.5.5) and suggest that targets should be at least 56×56dp with 12dp spacing for users with mild-to-moderate motor difficulties.',
                'keywords'   => ['Mobile Accessibility', 'Touch Targets', 'Motor Impairments', 'UX Guidelines', 'WCAG'],
                'status'     => 'draft',
                'submitted_at' => null,
                'co_authors' => [],
            ],
            [
                'conference' => 'mobileapp-2025',
                'track'      => 'mobile-dev',
                'submitter'  => 'author13@ginfoconf.test',
                'title'      => 'Cross-Platform mHealth App for Malaria Diagnosis Using Smartphone Microscopy',
                'abstract'   => 'We present MalariaScan, a cross-platform mobile health application that enables community health workers to diagnose malaria from smartphone-captured blood smear images. Our on-device deep learning model runs in 1.3 seconds on mid-range devices and achieves WHO-required 95% sensitivity and 90% specificity thresholds.',
                'keywords'   => ['mHealth', 'Malaria Diagnosis', 'On-Device AI', 'React Native', 'Community Health'],
                'status'     => 'submitted',
                'submitted_at' => now()->subDays(2),
                'co_authors' => ['author6@ginfoconf.test', 'author3@ginfoconf.test'],
            ],

            // ── GreenComp 2024 (archived) ─────────────────────────────────────────
            [
                'conference' => 'greencomp-2024',
                'track'      => 'green-sw',
                'submitter'  => 'author15@ginfoconf.test',
                'title'      => 'Energy Consumption Profiling of Popular JavaScript Frameworks',
                'abstract'   => 'We measure the energy consumption of five popular JavaScript frameworks (React, Angular, Vue, Svelte, SolidJS) across 15 representative benchmark tasks on three hardware configurations. Svelte and SolidJS consume on average 41% less energy than React for equivalent workloads, with implications for sustainable web development practices.',
                'keywords'   => ['Green Computing', 'JavaScript', 'Energy Profiling', 'Web Frameworks', 'Sustainability'],
                'status'     => 'camera_ready',
                'submitted_at' => now()->subDays(265),
                'decided_at' => now()->subDays(188),
                'co_authors' => ['author12@ginfoconf.test'],
            ],
        ];

        foreach ($submissions as $data) {
            $conference = Conference::where('slug', $data['conference'])->first();
            if (!$conference) {
                $this->command->warn("  Conference '{$data['conference']}' not found, skipping submission.");
                continue;
            }

            $track = Track::where('conference_id', $conference->id)
                ->where('slug', $data['track'])
                ->first();

            $submitter = User::where('email', $data['submitter'])->firstOrFail();

            $submission = Submission::updateOrCreate(
                ['conference_id' => $conference->id, 'title' => $data['title']],
                [
                    'track_id'      => $track?->id,
                    'submitter_id'  => $submitter->id,
                    'abstract'      => $data['abstract'],
                    'keywords'      => $data['keywords'],
                    'status'        => $data['status'],
                    'submitted_at'  => $data['submitted_at'] ?? null,
                    'decided_at'    => $data['decided_at'] ?? null,
                ]
            );

            // Primary author
            SubmissionAuthor::firstOrCreate(
                ['submission_id' => $submission->id, 'user_id' => $submitter->id],
                [
                    'name'        => $submitter->name,
                    'email'       => $submitter->email,
                    'affiliation' => $submitter->affiliation,
                    'country'     => $submitter->country,
                    'is_corresponding' => true,
                    'sort_order'  => 1,
                ]
            );

            // Co-authors
            $order = 2;
            foreach ($data['co_authors'] as $coEmail) {
                $coUser = User::where('email', $coEmail)->first();
                if ($coUser) {
                    SubmissionAuthor::firstOrCreate(
                        ['submission_id' => $submission->id, 'user_id' => $coUser->id],
                        [
                            'name'        => $coUser->name,
                            'email'       => $coUser->email,
                            'affiliation' => $coUser->affiliation,
                            'country'     => $coUser->country,
                            'is_corresponding' => false,
                            'sort_order'  => $order++,
                        ]
                    );
                }
            }
        }

        $this->command->info(count($submissions) . ' submissions seeded.');
    }
}
