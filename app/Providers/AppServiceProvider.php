<?php

namespace App\Providers;

use App\Models\LocallyFundedProject;
use App\Observers\LocallyFundedProjectObserver;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
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
        LocallyFundedProject::observe(LocallyFundedProjectObserver::class);

        RateLimiter::for('web-traffic', function (Request $request) {
            return Limit::perMinute($request->user() ? 900 : 180)
                ->by($request->ip());
        });

        RateLimiter::for('register', function (Request $request) {
            return [
                Limit::perMinute(5)->by('register-ip:'.$request->ip()),
                Limit::perHour(25)->by('register-ip-hour:'.$request->ip()),
            ];
        });

        RateLimiter::for('verification-resend', function (Request $request) {
            return Limit::perMinute(6)->by('verification-resend:'.$request->ip());
        });

        RateLimiter::for('password-reset', function (Request $request) {
            $email = strtolower(trim((string) $request->input('email', '')));
            $key = $email !== '' ? $email : $request->ip();

            return [
                Limit::perMinute(3)->by('password-reset:'.$key.'|'.$request->ip()),
                Limit::perHour(12)->by('password-reset-hour:'.$request->ip()),
            ];
        });

        RateLimiter::for('otp-verify', function (Request $request) {
            $email = strtolower(trim((string) $request->session()->get('otp_email', '')));
            $key = $email !== '' ? $email : $request->ip();

            return [
                Limit::perMinute(10)->by('otp-verify:'.$key.'|'.$request->ip()),
                Limit::perHour(60)->by('otp-verify-hour:'.$request->ip()),
            ];
        });

        RateLimiter::for('notifications', function (Request $request) {
            $userKey = $request->user()?->idno ?: $request->ip();

            return Limit::perMinute(120)->by('notifications:'.$userKey);
        });

        RateLimiter::for('pagasa-time', function (Request $request) {
            $userKey = $request->user()?->idno ?: $request->ip();

            return Limit::perMinute(120)->by('pagasa-time:'.$userKey);
        });
    }
}
