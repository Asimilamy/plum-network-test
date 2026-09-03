<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\DevCommands;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->shareVuePageData();
        $this->registerLocalMailServer();
    }

    /**
     * Run a local SMTP catch-all alongside the other `php artisan dev` processes.
     *
     * Mail is delivered to http://localhost:8025 instead of leaving the machine.
     */
    protected function registerLocalMailServer(): void
    {
        if (! $this->app->environment('local')) {
            return;
        }

        $binary = collect(['mailpit', 'mailhog'])
            ->first(fn (string $candidate): bool => is_executable(trim((string) shell_exec("command -v {$candidate} 2>/dev/null"))));

        if ($binary === null) {
            return;
        }

        DevCommands::register($binary, 'mail')->color('magenta');
    }

    /**
     * Expose the authenticated user and the page/API endpoints the Vue pages need.
     */
    protected function shareVuePageData(): void
    {
        view()->composer('app', function (View $view): void {
            /** @var User */
            $user = Auth::user();

            $view->with('shared', [
                'appName' => config('app.name'),
                'csrfToken' => csrf_token(),
                'user' => $user === null ? null : [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role->value,
                    'roleLabel' => $user->role->label(),
                    'isSuperAdmin' => $user->isSuperAdmin(),
                ],
                'routes' => [
                    'home' => URL::route('home'),
                    'login' => URL::route('login'),
                    'register' => URL::route('register'),
                    'passwordRequest' => URL::route('password.request'),
                    'dashboard' => URL::route('dashboard'),
                    'users' => URL::route('users.index'),
                ],
                'api' => [
                    'login' => URL::route('api.login'),
                    'register' => URL::route('api.register'),
                    'passwordEmail' => URL::route('api.password.email'),
                    'passwordUpdate' => URL::route('api.password.update'),
                    'logout' => URL::route('api.logout'),
                    'usersIndex' => URL::route('api.users.index'),
                    'usersStore' => URL::route('api.users.store'),
                ],
            ]);
        });
    }
}
