<?php

namespace App\Providers;

use App\Seagon\Middleware\Theory;
use App\Seagon\Quotation;
use App\Seagon\Slot;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider;
use LINE\LINEBot;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->alias('line-bot', LINEBot::class);

        $this->app->singleton(Quotation::class, function () {
            return new Quotation(
                $this->app->make(LINEBot::class),
                Storage::disk('local'),
                $this->app->make(Slot::class)
            );
        });

        $this->app->singleton(Slot::class, function () {
            return new Slot(
                Storage::disk('local')
            );
        });

        $this->app->singleton(Theory::class, function () {
            return new Theory(
                $this->app->make(LINEBot::class),
                Storage::disk('local')
            );
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
