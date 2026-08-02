<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $this->configureRateLimiting();
    }

    protected function configureRateLimiting(): void
    {
        // Login: 5 attempts per email+ip per minute
        RateLimiter::for('login', function (Request $request) {
            return [
                Limit::perMinute(5)->by($request->string('email')->lower()->append('|'.$request->ip())->toString())
                    ->response(fn () => back()->withErrors(['email' => 'Terlalu banyak percobaan. Coba lagi nanti.'])
                        ->withInput(['email' => $request->input('email')])),
            ];
        });

        // Register: 3 attempts per ip per hour
        RateLimiter::for('register', function (Request $request) {
            return Limit::perHour(3)->by($request->ip())
                ->response(fn () => back()->withErrors(['email' => 'Terlalu banyak registrasi dari IP ini. Coba lagi nanti.']));
        });

        // Contact form: 5 messages per ip per hour
        RateLimiter::for('contact', function (Request $request) {
            return Limit::perHour(5)->by($request->ip())
                ->response(fn () => back()->withErrors(['message' => 'Terlalu banyak pesan. Coba lagi nanti.']));
        });

        // Generic web throttle: 60 req/min/ip
        RateLimiter::for('web', function (Request $request) {
            return Limit::perMinute(60)->by($request->ip());
        });
    }
}