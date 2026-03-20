<?php

namespace Database\Seeders;

use App\Models\Conference;
use App\Models\ConferenceRole;
use App\Models\ConferenceTranslation;
use App\Models\Track;
use App\Models\TrackTranslation;
use App\Models\Topic;
use App\Models\TopicTranslation;
use App\Models\User;
use Illuminate\Database\Seeder;

class ConferencesSeeder extends Seeder
{
    public function run(): void
    {
        $chairs = [
            'admin@ginfoconf.test',
            'chair2@ginfoconf.test',
            'chair3@ginfoconf.test',
            'chair4@ginfoconf.test',
            'chair5@ginfoconf.test',
        ];

        $chairUsers = collect($chairs)->map(fn($e) => User::where('email', $e)->firstOrFail());

        $conferences = [
            // ── 1. GINFO 2025 (already seeded by DemoConferenceSeeder — skip) ──────

            // ── 2. AfricAI 2025 ────────────────────────────────────────────────────
            [
                'slug'       => 'africai-2025',
                'chair'      => $chairUsers[1],
                'status'     => 'active',
                'blind_mode' => 'double',
                'acronym'    => 'AfricAI',
                'edition'    => '3rd',
                'timezone'   => 'Africa/Nairobi',
                'location'   => 'Nairobi, Kenya',
                'website_url'=> 'https://africai2025.example.com',
                'sub_open'   => now()->subDays(10),
                'sub_close'  => now()->addDays(20),
                'rev_open'   => now()->addDays(25),
                'rev_close'  => now()->addDays(50),
                'notif'      => now()->addDays(60),
                'camera'     => now()->addDays(80),
                'translations' => [
                    'en' => [
                        'title'       => 'AfricAI 2025 — African Conference on Artificial Intelligence',
                        'subtitle'    => '3rd Edition',
                        'description' => 'AfricAI 2025 brings together AI researchers and practitioners across the African continent to share advances in machine learning, computer vision, NLP, and AI ethics with a focus on African contexts and challenges.',
                        'cfp_text'    => "We welcome original submissions on:\n\n- Machine Learning for Low-Resource Languages\n- AI for Healthcare in Africa\n- Agricultural AI & Food Security\n- Ethics and Fairness in AI\n- Federated Learning & Privacy\n- AI in Education\n\nPapers should be 6-8 pages in ACM format.",
                    ],
                    'fr' => [
                        'title'       => 'AfricAI 2025 — Conférence Africaine sur l\'Intelligence Artificielle',
                        'subtitle'    => '3ème Édition',
                        'description' => 'AfricAI 2025 réunit des chercheurs et praticiens en IA du continent africain.',
                        'cfp_text'    => "Nous accueillons des soumissions originales sur l'IA appliquée aux défis africains.",
                    ],
                ],
                'tracks' => [
                    ['slug' => 'ml-africa',    'en' => ['name' => 'ML for Africa',     'description' => 'Machine learning tailored to African contexts.'],
                                               'fr' => ['name' => 'ML pour l\'Afrique'],
                     'topics' => [['en' => 'Low-resource NLP', 'fr' => 'TAL faible ressource'], ['en' => 'Agricultural AI'], ['en' => 'Healthcare AI']]],
                    ['slug' => 'ai-ethics',    'en' => ['name' => 'AI Ethics & Policy', 'description' => 'Fairness, accountability, and transparency in AI.'],
                     'topics' => [['en' => 'Algorithmic Fairness'], ['en' => 'Data Governance'], ['en' => 'AI Regulation']]],
                ],
                'reviewers' => ['reviewer@ginfoconf.test', 'reviewer2@ginfoconf.test', 'reviewer9@ginfoconf.test'],
            ],

            // ── 3. SecureNet 2025 ──────────────────────────────────────────────────
            [
                'slug'       => 'securenet-2025',
                'chair'      => $chairUsers[2],
                'status'     => 'active',
                'blind_mode' => 'single',
                'acronym'    => 'SecureNet',
                'edition'    => '7th',
                'timezone'   => 'Africa/Accra',
                'location'   => 'Accra, Ghana',
                'website_url'=> 'https://securenet2025.example.com',
                'sub_open'   => now()->subDays(30),
                'sub_close'  => now()->subDays(5),
                'rev_open'   => now()->subDays(3),
                'rev_close'  => now()->addDays(25),
                'notif'      => now()->addDays(35),
                'camera'     => now()->addDays(55),
                'translations' => [
                    'en' => [
                        'title'       => 'SecureNet 2025 — International Symposium on Cybersecurity',
                        'subtitle'    => '7th International Symposium',
                        'description' => 'SecureNet 2025 is a leading forum for cybersecurity research covering network security, cryptography, privacy, and threat intelligence, with emphasis on emerging threats in developing economies.',
                        'cfp_text'    => "Call for papers on:\n\n- Network Intrusion Detection\n- Cryptographic Protocols\n- Mobile Security\n- IoT Security\n- Digital Forensics\n- Cyber Threat Intelligence\n\nMax 12 pages, IEEE double-column format.",
                    ],
                ],
                'tracks' => [
                    ['slug' => 'network-sec',  'en' => ['name' => 'Network Security',    'description' => 'Firewalls, intrusion detection, and network defence.'],
                     'topics' => [['en' => 'Intrusion Detection'], ['en' => 'DDoS Mitigation'], ['en' => 'Zero Trust']]],
                    ['slug' => 'crypto',       'en' => ['name' => 'Cryptography',         'description' => 'Classical and post-quantum cryptographic schemes.'],
                     'topics' => [['en' => 'Post-Quantum Crypto'], ['en' => 'Homomorphic Encryption'], ['en' => 'Digital Signatures']]],
                    ['slug' => 'mobile-sec',   'en' => ['name' => 'Mobile & IoT Security','description' => 'Security of mobile devices and embedded systems.'],
                     'topics' => [['en' => 'Android Security'], ['en' => 'IoT Firmware Analysis'], ['en' => 'Bluetooth Attacks']]],
                ],
                'reviewers' => ['reviewer3@ginfoconf.test', 'reviewer5@ginfoconf.test', 'reviewer7@ginfoconf.test', 'reviewer10@ginfoconf.test'],
            ],

            // ── 4. CloudSys 2026 (draft) ───────────────────────────────────────────
            [
                'slug'       => 'cloudsys-2026',
                'chair'      => $chairUsers[4],
                'status'     => 'draft',
                'blind_mode' => 'double',
                'acronym'    => 'CloudSys',
                'edition'    => '2nd',
                'timezone'   => 'Europe/Rome',
                'location'   => 'Milan, Italy',
                'website_url'=> 'https://cloudsys2026.example.com',
                'sub_open'   => now()->addDays(30),
                'sub_close'  => now()->addDays(90),
                'rev_open'   => now()->addDays(95),
                'rev_close'  => now()->addDays(130),
                'notif'      => now()->addDays(140),
                'camera'     => now()->addDays(160),
                'translations' => [
                    'en' => [
                        'title'       => 'CloudSys 2026 — International Conference on Cloud Computing and Distributed Systems',
                        'subtitle'    => '2nd Edition',
                        'description' => 'CloudSys 2026 explores the frontiers of cloud computing, serverless architectures, distributed data processing, and edge computing.',
                        'cfp_text'    => "Topics of interest include:\n\n- Serverless & FaaS Platforms\n- Kubernetes and Container Orchestration\n- Edge-Cloud Continuum\n- Cloud Storage and Databases\n- SLA and QoS Management\n\nSubmit up to 10 pages in Springer LNCS format.",
                    ],
                ],
                'tracks' => [
                    ['slug' => 'serverless',   'en' => ['name' => 'Serverless Computing', 'description' => 'Functions-as-a-Service and event-driven architectures.'],
                     'topics' => [['en' => 'FaaS Performance'], ['en' => 'Cold Start Optimization'], ['en' => 'Workflow Orchestration']]],
                    ['slug' => 'edge-cloud',   'en' => ['name' => 'Edge & Fog Computing',  'description' => 'Computation at the network edge.'],
                     'topics' => [['en' => 'Edge AI'], ['en' => 'Latency Optimization'], ['en' => 'Offloading Strategies']]],
                ],
                'reviewers' => ['reviewer4@ginfoconf.test', 'reviewer8@ginfoconf.test'],
            ],

            // ── 5. DataMine 2024 (archived) ────────────────────────────────────────
            [
                'slug'       => 'datamine-2024',
                'chair'      => $chairUsers[3],
                'status'     => 'archived',
                'blind_mode' => 'double',
                'acronym'    => 'DataMine',
                'edition'    => '5th',
                'timezone'   => 'Africa/Cairo',
                'location'   => 'Cairo, Egypt',
                'website_url'=> 'https://datamine2024.example.com',
                'sub_open'   => now()->subDays(200),
                'sub_close'  => now()->subDays(150),
                'rev_open'   => now()->subDays(145),
                'rev_close'  => now()->subDays(110),
                'notif'      => now()->subDays(100),
                'camera'     => now()->subDays(80),
                'translations' => [
                    'en' => [
                        'title'       => 'DataMine 2024 — International Conference on Data Mining and Knowledge Discovery',
                        'subtitle'    => '5th Edition',
                        'description' => 'DataMine 2024 covered advances in data mining, knowledge extraction, and analytics across large-scale datasets.',
                        'cfp_text'    => "Past topics included pattern mining, anomaly detection, graph mining, and stream processing.",
                    ],
                    'fr' => [
                        'title'       => 'DataMine 2024 — Conférence Internationale en Fouille de Données',
                        'subtitle'    => '5ème Édition',
                        'description' => 'DataMine 2024 a couvert les avancées en fouille de données et découverte de connaissances.',
                        'cfp_text'    => 'Sujets passés : fouille de motifs, détection d\'anomalies, fouille de graphes.',
                    ],
                ],
                'tracks' => [
                    ['slug' => 'pattern-mining','en' => ['name' => 'Pattern Mining',       'description' => 'Frequent patterns, association rules, sequential patterns.'],
                     'topics' => [['en' => 'Association Rules'], ['en' => 'Sequential Patterns'], ['en' => 'Subgraph Mining']]],
                    ['slug' => 'anomaly-det',   'en' => ['name' => 'Anomaly Detection',    'description' => 'Outlier and novelty detection in data streams.'],
                     'topics' => [['en' => 'Streaming Anomalies'], ['en' => 'Network Anomalies'], ['en' => 'Time Series Anomalies']]],
                ],
                'reviewers' => ['reviewer6@ginfoconf.test', 'reviewer3@ginfoconf.test'],
            ],

            // ── 6. WebTech 2025 ────────────────────────────────────────────────────
            [
                'slug'       => 'webtech-2025',
                'chair'      => $chairUsers[1],
                'status'     => 'active',
                'blind_mode' => 'open',
                'acronym'    => 'WebTech',
                'edition'    => '12th',
                'timezone'   => 'Africa/Dakar',
                'location'   => 'Dakar, Senegal',
                'website_url'=> 'https://webtech2025.example.com',
                'sub_open'   => now()->subDays(15),
                'sub_close'  => now()->addDays(15),
                'rev_open'   => now()->addDays(20),
                'rev_close'  => now()->addDays(45),
                'notif'      => now()->addDays(55),
                'camera'     => now()->addDays(75),
                'translations' => [
                    'en' => [
                        'title'       => 'WebTech 2025 — International Conference on Web Technologies and Applications',
                        'subtitle'    => '12th Edition',
                        'description' => 'WebTech 2025 covers all aspects of web technologies, from frontend frameworks to backend scalability, semantic web, and web security.',
                        'cfp_text'    => "Topics include:\n\n- Progressive Web Apps\n- WebAssembly\n- Semantic Web & Linked Data\n- REST and GraphQL APIs\n- Web Accessibility\n- Browser Security\n\nPapers up to 8 pages in ACM format.",
                    ],
                    'fr' => [
                        'title'       => 'WebTech 2025 — Conférence Internationale sur les Technologies Web',
                        'subtitle'    => '12ème Édition',
                        'description' => 'WebTech 2025 couvre tous les aspects des technologies web modernes.',
                        'cfp_text'    => "Sujets : PWA, WebAssembly, Web sémantique, API REST/GraphQL, sécurité web.",
                    ],
                ],
                'tracks' => [
                    ['slug' => 'frontend',     'en' => ['name' => 'Frontend Engineering', 'description' => 'Modern JS frameworks, performance, and accessibility.'],
                     'topics' => [['en' => 'React & Vue'], ['en' => 'Web Performance'], ['en' => 'Accessibility (a11y)']]],
                    ['slug' => 'backend-api',  'en' => ['name' => 'Backend & APIs',        'description' => 'Server-side architecture, REST, GraphQL, microservices.'],
                     'topics' => [['en' => 'GraphQL Design'], ['en' => 'Microservices'], ['en' => 'API Security']]],
                ],
                'reviewers' => ['reviewer9@ginfoconf.test', 'reviewer5@ginfoconf.test', 'reviewer2@ginfoconf.test'],
            ],

            // ── 7. BioInfo 2025 ────────────────────────────────────────────────────
            [
                'slug'       => 'bioinfo-2025',
                'chair'      => $chairUsers[3],
                'status'     => 'active',
                'blind_mode' => 'double',
                'acronym'    => 'BioInfo',
                'edition'    => '8th',
                'timezone'   => 'Africa/Cairo',
                'location'   => 'Alexandria, Egypt',
                'website_url'=> 'https://bioinfo2025.example.com',
                'sub_open'   => now()->subDays(20),
                'sub_close'  => now()->addDays(10),
                'rev_open'   => now()->addDays(15),
                'rev_close'  => now()->addDays(40),
                'notif'      => now()->addDays(50),
                'camera'     => now()->addDays(70),
                'translations' => [
                    'en' => [
                        'title'       => 'BioInfo 2025 — International Conference on Bioinformatics and Computational Biology',
                        'subtitle'    => '8th Edition',
                        'description' => 'BioInfo 2025 presents cutting-edge research at the intersection of computer science and biology, covering genomics, proteomics, and AI-driven drug discovery.',
                        'cfp_text'    => "Topics include:\n\n- Genome Sequence Analysis\n- Protein Structure Prediction\n- Drug Discovery with ML\n- Single-Cell Analysis\n- Systems Biology\n- Clinical Bioinformatics\n\nSubmit 8-12 pages in IEEE format.",
                    ],
                ],
                'tracks' => [
                    ['slug' => 'genomics',     'en' => ['name' => 'Genomics & Sequencing', 'description' => 'Genome assembly, variant calling, and comparative genomics.'],
                     'topics' => [['en' => 'Variant Calling'], ['en' => 'Metagenomics'], ['en' => 'RNA-seq Analysis']]],
                    ['slug' => 'ml-bio',       'en' => ['name' => 'ML in Biology',          'description' => 'Machine learning applied to biological data.'],
                     'topics' => [['en' => 'AlphaFold & Derivatives'], ['en' => 'Drug-Target Interaction'], ['en' => 'Multi-omics Integration']]],
                ],
                'reviewers' => ['reviewer6@ginfoconf.test', 'reviewer8@ginfoconf.test', 'reviewer4@ginfoconf.test'],
            ],

            // ── 8. EduTech 2026 (draft) ────────────────────────────────────────────
            [
                'slug'       => 'edutech-2026',
                'chair'      => $chairUsers[0],
                'status'     => 'draft',
                'blind_mode' => 'single',
                'acronym'    => 'EduTech',
                'edition'    => '1st',
                'timezone'   => 'Africa/Abidjan',
                'location'   => 'Abidjan, Côte d\'Ivoire',
                'website_url'=> null,
                'sub_open'   => now()->addDays(60),
                'sub_close'  => now()->addDays(120),
                'rev_open'   => now()->addDays(125),
                'rev_close'  => now()->addDays(155),
                'notif'      => now()->addDays(165),
                'camera'     => now()->addDays(185),
                'translations' => [
                    'en' => [
                        'title'       => 'EduTech 2026 — International Conference on Educational Technology and E-Learning',
                        'subtitle'    => '1st Edition',
                        'description' => 'EduTech 2026 is a new conference focusing on the use of technology in education, covering e-learning platforms, adaptive learning, and AI tutoring systems.',
                        'cfp_text'    => "Topics:\n\n- Adaptive Learning Systems\n- AI Tutoring and Feedback\n- MOOCs and Online Education\n- Gamification in Education\n- Learning Analytics\n\nUp to 8 pages, Springer LNCS.",
                    ],
                    'fr' => [
                        'title'       => 'EduTech 2026 — Conférence Internationale sur la Technologie Éducative',
                        'subtitle'    => '1ère Édition',
                        'description' => 'EduTech 2026 se concentre sur l\'usage des technologies dans l\'éducation en Afrique.',
                        'cfp_text'    => "Sujets : apprentissage adaptatif, IA pour l'éducation, MOOCs, analytique d'apprentissage.",
                    ],
                ],
                'tracks' => [
                    ['slug' => 'adaptive-learning', 'en' => ['name' => 'Adaptive Learning',   'description' => 'Personalized and adaptive educational systems.'],
                     'topics' => [['en' => 'Learner Modelling'], ['en' => 'Intelligent Tutoring'], ['en' => 'Recommendation Systems']]],
                ],
                'reviewers' => ['reviewer1@ginfoconf.test'],
            ],

            // ── 9. IoTConnect 2025 ─────────────────────────────────────────────────
            [
                'slug'       => 'iotconnect-2025',
                'chair'      => $chairUsers[2],
                'status'     => 'active',
                'blind_mode' => 'double',
                'acronym'    => 'IoTConnect',
                'edition'    => '4th',
                'timezone'   => 'Africa/Accra',
                'location'   => 'Kumasi, Ghana',
                'website_url'=> 'https://iotconnect2025.example.com',
                'sub_open'   => now()->subDays(25),
                'sub_close'  => now()->addDays(5),
                'rev_open'   => now()->addDays(10),
                'rev_close'  => now()->addDays(40),
                'notif'      => now()->addDays(50),
                'camera'     => now()->addDays(70),
                'translations' => [
                    'en' => [
                        'title'       => 'IoTConnect 2025 — International Conference on Internet of Things and Connected Systems',
                        'subtitle'    => '4th Edition',
                        'description' => 'IoTConnect 2025 explores the design, deployment, and security of IoT ecosystems, from smart cities and agriculture to industrial IoT.',
                        'cfp_text'    => "Topics:\n\n- Smart Cities & Infrastructure\n- Industrial IoT (IIoT)\n- IoT Protocols & Standards\n- Low-Power Wide-Area Networks\n- Digital Twins\n- IoT Security & Privacy\n\nPapers up to 10 pages in IEEE format.",
                    ],
                ],
                'tracks' => [
                    ['slug' => 'smart-cities', 'en' => ['name' => 'Smart Cities',           'description' => 'IoT applications for urban management and services.'],
                     'topics' => [['en' => 'Smart Grids'], ['en' => 'Traffic Management'], ['en' => 'Waste Management IoT']]],
                    ['slug' => 'iiot',         'en' => ['name' => 'Industrial IoT',          'description' => 'IoT in manufacturing, logistics, and industry 4.0.'],
                     'topics' => [['en' => 'Predictive Maintenance'], ['en' => 'Digital Twins'], ['en' => 'SCADA & ICS Security']]],
                ],
                'reviewers' => ['reviewer10@ginfoconf.test', 'reviewer7@ginfoconf.test', 'reviewer1@ginfoconf.test'],
            ],

            // ── 10. GreenComp 2024 (archived) ──────────────────────────────────────
            [
                'slug'       => 'greencomp-2024',
                'chair'      => $chairUsers[4],
                'status'     => 'archived',
                'blind_mode' => 'double',
                'acronym'    => 'GreenComp',
                'edition'    => '3rd',
                'timezone'   => 'Europe/Rome',
                'location'   => 'Rome, Italy',
                'website_url'=> 'https://greencomp2024.example.com',
                'sub_open'   => now()->subDays(300),
                'sub_close'  => now()->subDays(240),
                'rev_open'   => now()->subDays(235),
                'rev_close'  => now()->subDays(200),
                'notif'      => now()->subDays(190),
                'camera'     => now()->subDays(160),
                'translations' => [
                    'en' => [
                        'title'       => 'GreenComp 2024 — International Symposium on Green and Sustainable Computing',
                        'subtitle'    => '3rd Edition',
                        'description' => 'GreenComp 2024 addressed the environmental impact of computing, including energy-efficient algorithms, green data centres, and sustainable software engineering.',
                        'cfp_text'    => "Past topics: energy-efficient algorithms, green data centres, carbon-aware computing, sustainable software.",
                    ],
                ],
                'tracks' => [
                    ['slug' => 'green-dc',     'en' => ['name' => 'Green Data Centres',     'description' => 'Energy efficiency in large-scale computing infrastructure.'],
                     'topics' => [['en' => 'Cooling Efficiency'], ['en' => 'Renewable Energy in DCs'], ['en' => 'Carbon-aware Scheduling']]],
                    ['slug' => 'green-sw',     'en' => ['name' => 'Sustainable Software',   'description' => 'Energy-aware software design and measurement.'],
                     'topics' => [['en' => 'Energy Profiling'], ['en' => 'Green AI'], ['en' => 'Sustainable DevOps']]],
                ],
                'reviewers' => ['reviewer4@ginfoconf.test', 'reviewer8@ginfoconf.test'],
            ],

            // ── 11. MobileApp 2025 ─────────────────────────────────────────────────
            [
                'slug'       => 'mobileapp-2025',
                'chair'      => $chairUsers[0],
                'status'     => 'active',
                'blind_mode' => 'single',
                'acronym'    => 'MobileApp',
                'edition'    => '6th',
                'timezone'   => 'Africa/Ouagadougou',
                'location'   => 'Ouagadougou, Burkina Faso',
                'website_url'=> 'https://mobileapp2025.example.com',
                'sub_open'   => now()->subDays(8),
                'sub_close'  => now()->addDays(22),
                'rev_open'   => now()->addDays(28),
                'rev_close'  => now()->addDays(55),
                'notif'      => now()->addDays(65),
                'camera'     => now()->addDays(85),
                'translations' => [
                    'en' => [
                        'title'       => 'MobileApp 2025 — International Conference on Mobile Applications and Services',
                        'subtitle'    => '6th Edition',
                        'description' => 'MobileApp 2025 covers mobile application development, cross-platform frameworks, mobile UX, and mobile health and finance applications in emerging markets.',
                        'cfp_text'    => "Topics:\n\n- Cross-Platform Development (Flutter, React Native)\n- Mobile Health (mHealth)\n- Mobile Payment & FinTech\n- Offline-First Architecture\n- Mobile Accessibility\n- 5G Applications\n\nUp to 8 pages, ACM format.",
                    ],
                    'fr' => [
                        'title'       => 'MobileApp 2025 — Conférence Internationale sur les Applications Mobiles',
                        'subtitle'    => '6ème Édition',
                        'description' => 'MobileApp 2025 couvre le développement mobile, la santé mobile et les services financiers mobiles.',
                        'cfp_text'    => "Sujets : Flutter/React Native, mSanté, paiement mobile, architecture offline-first.",
                    ],
                ],
                'tracks' => [
                    ['slug' => 'mobile-dev',   'en' => ['name' => 'Mobile Development',     'description' => 'Frameworks, performance, and testing for mobile apps.'],
                     'topics' => [['en' => 'Flutter & Dart'], ['en' => 'React Native'], ['en' => 'PWA vs Native']]],
                    ['slug' => 'mobile-ux',    'en' => ['name' => 'Mobile UX & Design',      'description' => 'User experience, accessibility, and interface design for mobile.'],
                     'topics' => [['en' => 'Touch Interaction'], ['en' => 'Mobile Accessibility'], ['en' => 'Dark Mode UX']]],
                ],
                'reviewers' => ['reviewer@ginfoconf.test', 'reviewer9@ginfoconf.test', 'reviewer2@ginfoconf.test'],
            ],
        ];

        foreach ($conferences as $conf) {
            $chair = $conf['chair'];

            $conference = Conference::updateOrCreate(
                ['slug' => $conf['slug']],
                [
                    'owner_id'          => $chair->id,
                    'acronym'           => $conf['acronym'] ?? null,
                    'edition'           => $conf['edition'] ?? null,
                    'status'            => $conf['status'],
                    'blind_mode'        => $conf['blind_mode'],
                    'timezone'          => $conf['timezone'],
                    'min_reviewers'     => 2,
                    'max_reviewers'     => 3,
                    'max_pages'         => 12,
                    'submission_open'   => $conf['sub_open'],
                    'submission_close'  => $conf['sub_close'],
                    'review_open'       => $conf['rev_open'],
                    'review_close'      => $conf['rev_close'],
                    'notification_date' => $conf['notif'],
                    'camera_ready_date' => $conf['camera'],
                ]
            );

            // Translations
            foreach ($conf['translations'] as $locale => $trans) {
                ConferenceTranslation::updateOrCreate(
                    ['conference_id' => $conference->id, 'locale' => $locale],
                    $trans
                );
            }

            // Chair role
            ConferenceRole::firstOrCreate(
                ['conference_id' => $conference->id, 'user_id' => $chair->id, 'role' => 'admin'],
                ['assigned_by' => $chair->id]
            );

            // Reviewer roles
            foreach ($conf['reviewers'] as $reviewerEmail) {
                $reviewer = User::where('email', $reviewerEmail)->first();
                if ($reviewer) {
                    ConferenceRole::firstOrCreate(
                        ['conference_id' => $conference->id, 'user_id' => $reviewer->id, 'role' => 'reviewer'],
                        ['assigned_by' => $chair->id]
                    );
                }
            }

            // Tracks & Topics
            foreach ($conf['tracks'] as $trackData) {
                $track = Track::updateOrCreate(
                    ['conference_id' => $conference->id, 'slug' => $trackData['slug']],
                    []
                );

                TrackTranslation::updateOrCreate(
                    ['track_id' => $track->id, 'locale' => 'en'],
                    $trackData['en']
                );

                if (isset($trackData['fr'])) {
                    TrackTranslation::updateOrCreate(
                        ['track_id' => $track->id, 'locale' => 'fr'],
                        $trackData['fr']
                    );
                }

                foreach ($trackData['topics'] as $topicData) {
                    $existingTranslation = TopicTranslation::where('locale', 'en')
                        ->where('name', $topicData['en'])
                        ->whereHas('topic', fn($q) => $q->where('track_id', $track->id))
                        ->first();

                    if ($existingTranslation) {
                        $topic = $existingTranslation->topic;
                    } else {
                        $topic = Topic::create([
                            'conference_id' => $conference->id,
                            'track_id'      => $track->id,
                        ]);
                    }

                    TopicTranslation::updateOrCreate(
                        ['topic_id' => $topic->id, 'locale' => 'en'],
                        ['name' => $topicData['en']]
                    );
                    if (isset($topicData['fr'])) {
                        TopicTranslation::updateOrCreate(
                            ['topic_id' => $topic->id, 'locale' => 'fr'],
                            ['name' => $topicData['fr']]
                        );
                    }
                }
            }

            $this->command->info("  Conference '{$conf['slug']}' seeded.");
        }

        $this->command->info('10 conferences seeded successfully.');
    }
}
