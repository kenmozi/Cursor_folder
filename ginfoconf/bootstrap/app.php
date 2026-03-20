<?php

use App\Http\Middleware\SetLocale;
use App\Policies\ConferencePolicy;
use App\Policies\SubmissionPolicy;
use App\Policies\ReviewPolicy;
use App\Models\Conference;
use App\Models\Submission;
use App\Models\Review;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Global API middleware
        $middleware->api(append: [
            SetLocale::class,
        ]);

        // CORS — handled via config/cors.php (allow frontend origin)

        // Named middleware aliases
        $middleware->alias([
            'super_admin' => \App\Http\Middleware\EnsureSuperAdmin::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Return JSON errors for API routes
        $exceptions->shouldRenderJsonWhen(function (Request $request) {
            return $request->is('api/*') || $request->expectsJson();
        });
    })
    ->booted(function () {
        // Register policies
        Gate::policy(Conference::class, ConferencePolicy::class);
        Gate::policy(Submission::class, SubmissionPolicy::class);
        Gate::policy(Review::class, ReviewPolicy::class);
    })
    ->create();
