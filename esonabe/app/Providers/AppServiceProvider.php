<?php

namespace App\Providers;

use App\Models\Contract;
use App\Policies\ContractPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Gate::policy(Contract::class, ContractPolicy::class);

        // Fallback: let users manage their own contracts' meters
        Gate::define('view-meter', function ($user, $meter) {
            return $user->id === $meter->contract->user_id;
        });
    }
}
