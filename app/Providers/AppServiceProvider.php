<?php

namespace App\Providers;

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
                Storage::disk('local')
            );
        });

        $this->app->singleton(Slot::class, function () {
            return new Slot(
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
