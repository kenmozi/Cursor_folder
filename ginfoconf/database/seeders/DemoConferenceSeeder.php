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

class DemoConferenceSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('email', 'admin@ginfoconf.test')->firstOrFail();

        $conference = Conference::updateOrCreate(
            ['slug' => 'ginfo-2025'],
            [
                'status'            => 'open',
                'blind_mode'        => 'double',
                'submission_open'   => now()->subDays(5),
                'submission_close'  => now()->addDays(30),
                'review_open'       => now()->addDays(35),
                'review_close'      => now()->addDays(60),
                'notification_date' => now()->addDays(70),
                'camera_ready_date' => now()->addDays(90),
                'timezone'          => 'Europe/Paris',
                'location'          => 'Ouagadougou, Burkina Faso',
                'website_url'       => 'https://ginfo2025.example.com',
            ]
        );

        // Translations
        ConferenceTranslation::updateOrCreate(
            ['conference_id' => $conference->id, 'locale' => 'en'],
            [
                'title'       => 'GINFO 2025 — International Conference on Computer Science and Information Technology',
                'subtitle'    => '10th International Edition',
                'description' => 'GINFO 2025 is a premier international forum for researchers and practitioners to present and discuss the most recent innovations, trends, results, experiences, and concerns in all aspects of computer science and information technology.',
                'cfp_text'    => "We invite researchers, academics, and practitioners to submit original research papers on topics including but not limited to:\n\n- Artificial Intelligence & Machine Learning\n- Data Science & Big Data Analytics\n- Cybersecurity & Privacy\n- Cloud Computing & Distributed Systems\n- Software Engineering & DevOps\n- Internet of Things & Embedded Systems\n- Computer Networks & Communications\n- Human-Computer Interaction\n\nAll papers must be submitted in PDF format and must not exceed 10 pages in IEEE format. Submissions must be original and not currently under review elsewhere.",
            ]
        );

        ConferenceTranslation::updateOrCreate(
            ['conference_id' => $conference->id, 'locale' => 'fr'],
            [
                'title'       => 'GINFO 2025 — Conférence Internationale en Informatique et Technologie de l\'Information',
                'subtitle'    => '10ème Édition Internationale',
                'description' => 'GINFO 2025 est un forum international de premier plan permettant aux chercheurs et praticiens de présenter et discuter des dernières innovations, tendances et résultats dans tous les aspects de l\'informatique.',
                'cfp_text'    => "Nous invitons les chercheurs, académiciens et praticiens à soumettre des articles de recherche originaux. Tous les articles doivent être soumis en format PDF et ne doivent pas dépasser 10 pages au format IEEE.",
            ]
        );

        // Assign admin role
        ConferenceRole::firstOrCreate(
            ['conference_id' => $conference->id, 'user_id' => $admin->id, 'role' => 'admin'],
            ['assigned_by' => $admin->id]
        );

        // Assign reviewer role to demo reviewer
        $reviewer = User::where('email', 'reviewer@ginfoconf.test')->first();
        if ($reviewer) {
            ConferenceRole::firstOrCreate(
                ['conference_id' => $conference->id, 'user_id' => $reviewer->id, 'role' => 'reviewer'],
                ['assigned_by' => $admin->id]
            );
        }

        // Tracks & Topics
        $tracks = [
            [
                'slug' => 'ai-ml',
                'en'   => ['name' => 'AI & Machine Learning', 'description' => 'Artificial intelligence, deep learning, NLP, and related topics.'],
                'fr'   => ['name' => 'IA & Apprentissage Automatique'],
                'topics' => [
                    ['en' => 'Deep Learning', 'fr' => 'Apprentissage Profond'],
                    ['en' => 'Natural Language Processing', 'fr' => 'Traitement du Langage Naturel'],
                    ['en' => 'Computer Vision', 'fr' => 'Vision par Ordinateur'],
                    ['en' => 'Reinforcement Learning', 'fr' => 'Apprentissage par Renforcement'],
                ],
            ],
            [
                'slug' => 'security',
                'en'   => ['name' => 'Cybersecurity & Privacy', 'description' => 'Network security, cryptography, data privacy, and threat detection.'],
                'fr'   => ['name' => 'Cybersécurité & Confidentialité'],
                'topics' => [
                    ['en' => 'Network Security', 'fr' => 'Sécurité Réseau'],
                    ['en' => 'Cryptography', 'fr' => 'Cryptographie'],
                    ['en' => 'Privacy & GDPR', 'fr' => 'Vie Privée & RGPD'],
                ],
            ],
            [
                'slug' => 'data-science',
                'en'   => ['name' => 'Data Science & Analytics', 'description' => 'Big data, data mining, visualization, and analytics platforms.'],
                'fr'   => ['name' => 'Science des Données & Analytique'],
                'topics' => [
                    ['en' => 'Big Data Platforms', 'fr' => 'Plateformes Big Data'],
                    ['en' => 'Data Visualization', 'fr' => 'Visualisation des Données'],
                    ['en' => 'Knowledge Graphs', 'fr' => 'Graphes de Connaissances'],
                ],
            ],
        ];

        foreach ($tracks as $trackData) {
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
                $topic = Topic::updateOrCreate(
                    ['track_id' => $track->id, 'slug' => \Illuminate\Support\Str::slug($topicData['en'])],
                    []
                );

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

        $this->command->info("Demo conference 'ginfo-2025' seeded successfully.");
    }
}
