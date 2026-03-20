<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // ── Super admin ────────────────────────────────────────────────────────
        User::updateOrCreate(
            ['email' => 'superadmin@ginfoconf.test'],
            [
                'name'           => 'Super Admin',
                'password'       => Hash::make('password'),
                'affiliation'    => 'Platform',
                'country'        => 'US',
                'locale'         => 'en',
                'is_super_admin' => true,
            ]
        );

        // ── Conference chairs / admins ──────────────────────────────────────────
        $chairs = [
            ['email' => 'admin@ginfoconf.test',    'name' => 'Jean-Pierre Ouédraogo', 'affiliation' => 'Université Joseph Ki-Zerbo',    'country' => 'BF', 'locale' => 'fr'],
            ['email' => 'chair2@ginfoconf.test',   'name' => 'Amina Diallo',          'affiliation' => 'Université Cheikh Anta Diop',   'country' => 'SN', 'locale' => 'fr'],
            ['email' => 'chair3@ginfoconf.test',   'name' => 'David Osei',            'affiliation' => 'University of Ghana',           'country' => 'GH', 'locale' => 'en'],
            ['email' => 'chair4@ginfoconf.test',   'name' => 'Fatima Al-Hassan',      'affiliation' => 'Cairo University',              'country' => 'EG', 'locale' => 'en'],
            ['email' => 'chair5@ginfoconf.test',   'name' => 'Marco Rossi',           'affiliation' => 'Politecnico di Milano',         'country' => 'IT', 'locale' => 'en'],
        ];

        foreach ($chairs as $data) {
            User::updateOrCreate(
                ['email' => $data['email']],
                array_merge($data, ['password' => Hash::make('password')])
            );
        }

        // ── Reviewers ──────────────────────────────────────────────────────────
        $reviewers = [
            ['email' => 'reviewer@ginfoconf.test',   'name' => 'Sophie Martin',       'affiliation' => 'CNRS',                          'country' => 'FR', 'locale' => 'fr'],
            ['email' => 'reviewer2@ginfoconf.test',  'name' => 'Kwame Asante',        'affiliation' => 'KNUST',                         'country' => 'GH', 'locale' => 'en'],
            ['email' => 'reviewer3@ginfoconf.test',  'name' => 'Li Wei',              'affiliation' => 'Tsinghua University',           'country' => 'CN', 'locale' => 'en'],
            ['email' => 'reviewer4@ginfoconf.test',  'name' => 'Olga Petrov',         'affiliation' => 'Moscow State University',       'country' => 'RU', 'locale' => 'en'],
            ['email' => 'reviewer5@ginfoconf.test',  'name' => 'Ahmed Ben Salem',     'affiliation' => 'INSAT Tunis',                   'country' => 'TN', 'locale' => 'fr'],
            ['email' => 'reviewer6@ginfoconf.test',  'name' => 'Priya Nair',          'affiliation' => 'IIT Bombay',                    'country' => 'IN', 'locale' => 'en'],
            ['email' => 'reviewer7@ginfoconf.test',  'name' => 'Carlos Mendez',       'affiliation' => 'Universidad Autónoma de Madrid','country' => 'ES', 'locale' => 'en'],
            ['email' => 'reviewer8@ginfoconf.test',  'name' => 'Yuki Tanaka',         'affiliation' => 'University of Tokyo',           'country' => 'JP', 'locale' => 'en'],
            ['email' => 'reviewer9@ginfoconf.test',  'name' => 'Fatou Ndiaye',        'affiliation' => 'Université de Dakar',           'country' => 'SN', 'locale' => 'fr'],
            ['email' => 'reviewer10@ginfoconf.test', 'name' => 'Brian Omondi',        'affiliation' => 'University of Nairobi',         'country' => 'KE', 'locale' => 'en'],
        ];

        foreach ($reviewers as $data) {
            User::updateOrCreate(
                ['email' => $data['email']],
                array_merge($data, ['password' => Hash::make('password')])
            );
        }

        // ── Authors ────────────────────────────────────────────────────────────
        $authors = [
            ['email' => 'author@ginfoconf.test',    'name' => 'Hiroshi Yamamoto',    'affiliation' => 'Tokyo University',              'country' => 'JP', 'locale' => 'en'],
            ['email' => 'author2@ginfoconf.test',   'name' => 'Nadia Boudraa',       'affiliation' => 'Université d\'Alger',           'country' => 'DZ', 'locale' => 'fr'],
            ['email' => 'author3@ginfoconf.test',   'name' => 'Samuel Otieno',       'affiliation' => 'Makerere University',           'country' => 'UG', 'locale' => 'en'],
            ['email' => 'author4@ginfoconf.test',   'name' => 'Elena Kovacs',        'affiliation' => 'Budapest University of Tech',   'country' => 'HU', 'locale' => 'en'],
            ['email' => 'author5@ginfoconf.test',   'name' => 'Moussa Coulibaly',    'affiliation' => 'Université de Bamako',          'country' => 'ML', 'locale' => 'fr'],
            ['email' => 'author6@ginfoconf.test',   'name' => 'Ana Lima',            'affiliation' => 'Universidade de São Paulo',     'country' => 'BR', 'locale' => 'en'],
            ['email' => 'author7@ginfoconf.test',   'name' => 'Tariq Al-Rashidi',    'affiliation' => 'King Abdulaziz University',     'country' => 'SA', 'locale' => 'en'],
            ['email' => 'author8@ginfoconf.test',   'name' => 'Grace Mensah',        'affiliation' => 'University of Cape Coast',      'country' => 'GH', 'locale' => 'en'],
            ['email' => 'author9@ginfoconf.test',   'name' => 'Luc Tremblay',        'affiliation' => 'Université de Montréal',        'country' => 'CA', 'locale' => 'fr'],
            ['email' => 'author10@ginfoconf.test',  'name' => 'Zanele Dlamini',      'affiliation' => 'University of Witwatersrand',   'country' => 'ZA', 'locale' => 'en'],
            ['email' => 'author11@ginfoconf.test',  'name' => 'Ravi Sharma',         'affiliation' => 'Delhi Technological University', 'country' => 'IN', 'locale' => 'en'],
            ['email' => 'author12@ginfoconf.test',  'name' => 'Marie-Claire Dupont', 'affiliation' => 'École Polytechnique',           'country' => 'FR', 'locale' => 'fr'],
            ['email' => 'author13@ginfoconf.test',  'name' => 'Emeka Okafor',        'affiliation' => 'University of Lagos',           'country' => 'NG', 'locale' => 'en'],
            ['email' => 'author14@ginfoconf.test',  'name' => 'Sun Li',              'affiliation' => 'Peking University',             'country' => 'CN', 'locale' => 'en'],
            ['email' => 'author15@ginfoconf.test',  'name' => 'Ines Ferreira',       'affiliation' => 'Universidade de Lisboa',        'country' => 'PT', 'locale' => 'en'],
        ];

        foreach ($authors as $data) {
            User::updateOrCreate(
                ['email' => $data['email']],
                array_merge($data, ['password' => Hash::make('password')])
            );
        }

        $this->command->info('Users seeded: 1 super admin, 5 chairs, 10 reviewers, 15 authors.');
    }
}
