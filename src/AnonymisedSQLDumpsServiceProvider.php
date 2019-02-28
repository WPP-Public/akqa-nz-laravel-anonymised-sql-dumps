<?php

namespace Heyday\AnonymisedSQLDumps;

use Illuminate\Support\ServiceProvider;

class AnonymisedSQLDumpsServiceProvider extends ServiceProvider
{

    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/anonymised-sql-dumps.php' => config_path('anonymised-sql-dumps.php'),
            ], 'config');
        }

        $this->app->bind('command.snapshot:create', Create::class);
        $this->commands([
            'command.snapshot:create'
        ]);
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/anonymised-sql-dumps.php', 'anonymised-sql-dumps');
    }

}