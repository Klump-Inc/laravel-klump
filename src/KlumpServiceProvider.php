<?php

namespace Klump\LaravelKlump;

use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;
use Klump\LaravelKlump\Http\Livewire\KlumpCheckout;
use Klump\LaravelKlump\Services\KlumpService;

class KlumpServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'klump');
        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');

        $this->publishes([
            __DIR__.'/../config/klump.php' => config_path('klump.php'),
            __DIR__.'/../resources/views' => resource_path('views/vendor/klump'),
        ], 'klump');

        Livewire::component('klump-checkout', KlumpCheckout::class);
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/klump.php', 'klump');

        $this->app->singleton('klump', function ($app) {
            return new KlumpService();
        });
    }
}
