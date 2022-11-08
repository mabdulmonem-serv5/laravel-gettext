<?php

namespace Anubixo\LaravelGettext\Config;

use Anubixo\LaravelGettext\Adapters\LaravelAdapter;
use Anubixo\LaravelGettext\Config\Models\Config as ConfigModel;
use Anubixo\LaravelGettext\Exceptions\RequiredConfigurationKeyException;
use Exception;
use Illuminate\Support\Facades\Config;
use Anubixo\LaravelGettext\Storages\SessionStorage;

class ConfigManager
{
    /**
     * Config model
     *
     * @var ConfigModel
     */
    protected ConfigModel $config;

    /**
     * Package configuration route (published)
     */
    const DEFAULT_PACKAGE_CONFIG = 'laravel-gettext';

    /**
     * @param array|null $config
     * @throws RequiredConfigurationKeyException
     */
    public function __construct(array $config = null)
    {
        if ($config) {
            $this->config = $this->generateFromArray($config);
        } else {
            // In Laravel 5.3 we need empty config model
            $this->config = new ConfigModel;
        }
    }

    /**
     * Get new instance of the ConfigManager
     *
     * @param null $config
     * @return static
     * @throws RequiredConfigurationKeyException
     */
    public static function create($config = null): static
    {
        if (is_null($config)) {
            // Default package configuration file (published)
            $config = Config::get(static::DEFAULT_PACKAGE_CONFIG);
        }

        return new static($config);
    }

    /**
     * Get the config model
     *
     * @return ConfigModel
     */
    public function get(): ConfigModel
    {
        return $this->config;
    }

    /**
     * Creates a configuration container and checks the required fields
     *
     * @param array $config
     * @return ConfigModel
     * @throws RequiredConfigurationKeyException
     * @throws Exception
     */
    protected function generateFromArray(array $config): ConfigModel
    {
        $requiredKeys = [
            'locale',
            'fallback-locale',
            'encoding'
        ];

        foreach ($requiredKeys as $key) {
            if (!array_key_exists($key, $config)) {
                throw new RequiredConfigurationKeyException(
                    sprintf('Unconfigured required value: %s', $key)
                );
            }
        }

        $container = new ConfigModel();

        $id = $config['session-identifier'] ?? 'laravel-gettext-locale';

        $adapter = $config['adapter'] ?? LaravelAdapter::class;

        $storage = $config['storage'] ?? SessionStorage::class;

        $container->setLocale($config['locale'])
            ->setSessionIdentifier($id)
            ->setEncoding($config['encoding'])
            ->setCategories(array_get($config, 'categories', ['LC_ALL']))
            ->setFallbackLocale($config['fallback-locale'])
            ->setSupportedLocales($config['supported-locales'])
            ->setDomain($config['domain'])
            ->setTranslationsPath($config['translations-path'])
            ->setProject($config['project'])
            ->setTranslator($config['translator'])
            ->setSourcePaths($config['source-paths'])
            ->setSyncLaravel($config['sync-laravel'])
            ->setAdapter($adapter)
            ->setStorage($storage);

        if (array_key_exists('relative-path', $config)) {
            $container->setRelativePath($config['relative-path']);
        }

        if (array_key_exists("custom-locale", $config)) {
            $container->setCustomLocale($config['custom-locale']);
        }

        if (array_key_exists("keywords-list", $config)) {
            $container->setKeywordsList($config['keywords-list']);
        }

        if (array_key_exists("handler", $config)) {
            $container->setHandler($config['handler']);
        }

        return $container;
    }
}
