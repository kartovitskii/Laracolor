<?php

namespace Kartovitskii\Laracolor;

use Illuminate\Support\ServiceProvider;

class ColorServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/laracolor.php',
            'laracolor'
        );

        $this->app->singleton('laracolor', function ($app) {
            return new ColorGenerator($app['config']->get('laracolor'));
        });
    }

    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/../config/laracolor.php' => config_path('laracolor.php'),
        ], 'laracolor-config');
    }
}