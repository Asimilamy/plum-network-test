<?php

namespace App\Providers;

use Illuminate\Contracts\View\View;
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
    }

    /**
     * Expose session state and named routes the Vue pages need on every render.
     */
    protected function shareVuePageData(): void
    {
        view()->composer(['app', 'welcome'], function (View $view): void {
            $view->with('shared', [
                'csrfToken' => csrf_token(),
                'errors' => session('errors')?->getBag('default')->toArray() ?? [],
                'old' => session()->getOldInput(),
                'status' => session('status'),
                'user' => Auth::user()?->only(['id', 'name', 'email']),
                'routes' => [
                    'home' => URL::route('home'),
                    'login' => URL::route('login'),
                    'register' => URL::route('register'),
                    'passwordRequest' => URL::route('password.request'),
                    'passwordEmail' => URL::route('password.email'),
                    'dashboard' => URL::route('dashboard'),
                    'logout' => URL::route('logout'),
                ],
            ]);
        });
    }
}
