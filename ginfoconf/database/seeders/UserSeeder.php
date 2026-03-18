<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Super admin
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

        // Demo conference admin
        User::updateOrCreate(
            ['email' => 'admin@ginfoconf.test'],
            [
                'name'        => 'Conference Chair',
                'password'    => Hash::make('password'),
                'affiliation' => 'University of Demo',
                'country'     => 'FR',
                'locale'      => 'en',
            ]
        );

        // Demo reviewer
        User::updateOrCreate(
            ['email' => 'reviewer@ginfoconf.test'],
            [
                'name'        => 'Reviewer One',
                'password'    => Hash::make('password'),
                'affiliation' => 'CNRS',
                'country'     => 'FR',
                'locale'      => 'fr',
            ]
        );

        // Demo author
        User::updateOrCreate(
            ['email' => 'author@ginfoconf.test'],
            [
                'name'        => 'Author Example',
                'password'    => Hash::make('password'),
                'affiliation' => 'Tokyo University',
                'country'     => 'JP',
                'locale'      => 'ja',
            ]
        );
    }
}
