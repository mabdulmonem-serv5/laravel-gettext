<?php

namespace Anubixo\LaravelGettext;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\ServiceProvider;
use Anubixo\LaravelGettext\Adapters\AdapterInterface;
use Anubixo\LaravelGettext\Config\ConfigManager;
use Anubixo\LaravelGettext\Config\Models\Config;

/**
 * Main service provider
 *
 * Class LaravelGettextServiceProvider
 * @package Anubixo\LaravelGettext
 *
 */
class LaravelGettextServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected bool $defer = false;

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/../../config/config.php' => config_path('laravel-gettext.php')
        ], 'config');

    }

    /**
     * Register the service provider.
     *
     * @return void
     * @throws Exceptions\RequiredConfigurationKeyException
     */
    public function register(): void
    {
        $configuration = ConfigManager::create();

        $this->app->bind(
            AdapterInterface::class,
            $configuration->get()->getAdapter()
        );

        $this->app->singleton(Config::class, function($app) use ($configuration){
            return $configuration->get();
        });

        // Main class register
        $this->app->singleton(LaravelGettext::class, function (Application $app) use ($configuration) {

            $fileSystem = new FileSystem($configuration->get(), app_path(), storage_path());
            $storage = $app->make($configuration->get()->getStorage());

            if ('symfony' == $configuration->get()->getHandler()) {
                // symfony translator implementation
                $translator = new Translators\Symfony(
                    $configuration->get(),
                    $this->app->make(AdapterInterface::class),
                    $fileSystem,
                    $storage
                );
            } else {
                // GNU/Gettext php extension
                $translator = new Translators\Gettext(
                    $configuration->get(),
                    $this->app->make(AdapterInterface::class),
                    $fileSystem,
                    $storage
                );
            }

            return new LaravelGettext($translator);

        });
        $this->app->alias(LaravelGettext::class, 'laravel-gettext');

        // Alias
        $this->app->booting(function () {
            $aliasLoader = AliasLoader::getInstance();
            $aliasLoader->alias('LaravelGettext', \Anubixo\LaravelGettext\Facades\LaravelGettext::class);
        });

        $this->registerCommands();
    }

    /**
     * Register commands
     */
    protected function registerCommands()
    {
        // Package commands
        $this->app->bind('Anubixo::gettext.create', function ($app) {
            return new Commands\GettextCreate();
        });

        $this->app->bind('Anubixo::gettext.update', function ($app) {
            return new Commands\GettextUpdate();
        });

        $this->commands([
            'Anubixo::gettext.create',
            'Anubixo::gettext.update',
        ]);
    }

    /**
     * Get the services
     *
     * @return array
     */
    public function provides(): array
    {
        return [
            'laravel-gettext'
        ];
    }
}