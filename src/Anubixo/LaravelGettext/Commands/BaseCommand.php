<?php

namespace Anubixo\LaravelGettext\Commands;

use Anubixo\LaravelGettext\Exceptions\RequiredConfigurationFileException;
use Anubixo\LaravelGettext\Exceptions\RequiredConfigurationKeyException;
use Illuminate\Console\Command;
use Anubixo\LaravelGettext\FileSystem;
use Anubixo\LaravelGettext\Config\ConfigManager;

class BaseCommand extends Command
{
    /**
     * Filesystem helper
     *
     * @var FileSystem
     */
    protected FileSystem $fileSystem;

    /**
     * Package configuration data
     *
     * @var array
     */
    protected array $configuration;

    /**
     * Prepares the package environment for gettext commands
     *
     * @return void
     * @throws RequiredConfigurationKeyException
     */
    protected function prepare(): void
    {
        $configManager = ConfigManager::create();
        
        $this->fileSystem = new FileSystem(
            $configManager->get(),
            app_path(),
            storage_path()
        );

        $this->configuration = $configManager->get();
    }
}
