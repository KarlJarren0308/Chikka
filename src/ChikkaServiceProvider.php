<?php

namespace KarlMacz\Chikka;

use Illuminate\Support\ServiceProvider;

class ChikkaServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        require __DIR__ . '/Http/routes.php';

        $this->mergeConfigFrom(__DIR__.'/config/chikka.php', 'chikka');

        $this->publishes([
            __DIR__  . '/migrations/2017_04_08_000000_create_chikka_incoming_sms_table.php' => base_path('database/migrations/2017_04_08_000000_create_chikka_incoming_sms_table.php')
        ]);

        $this->publishes([
            __DIR__  . '/migrations/2017_04_08_000001_create_chikka_outgoing_sms_table.php' => base_path('database/migrations/2017_04_08_000001_create_chikka_outgoing_sms_table.php')
        ]);
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        config([
            'config/chikka.php'
        ]);
    }
}
