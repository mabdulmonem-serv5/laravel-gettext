<?php

namespace Anubixo\LaravelGettext\Config\Models;

use Exception;

class Config
{
    /**
     * Session identifier
     *
     * @var string
     */
    protected string $sessionIdentifier;

    /**
     * Charset encoding for files
     *
     * @var string
     */
    protected string $encoding;

    /**
     * Full ISO Locale (en_EN)
     *
     * @var string
     */
    protected string $locale;

    /**
     * Locale categories
     *
     * @type array
     */
    protected array $categories;

    /**
     * Fallback locale
     *
     * @var string
     */
    protected string $fallbackLocale;

    /**
     * Supported locales
     *
     * @var array
     */
    protected array $supportedLocales;

    /**
     * Gettext domain
     *
     * @var string
     */
    protected string $domain;

    /**
     * Path to translation files
     *
     * @var string
     */
    protected string $translationsPath;

    /**
     * Project identifier
     *
     * @var string
     */
    protected string $project;

    /**
     * Translator contact data
     *
     * @var string
     */
    protected string $translator;

    /**
     * Source paths
     *
     * @var array
     */
    protected array $sourcePaths;

    /**
     * Sync with laravel locale
     *
     * @type Boolean
     */
    protected bool $syncLaravel;

    /**
     * The adapter class used to sync with laravel locale
     *
     * @var string
     */
    protected string $adapter;


    /**
     * The storage class used store the current locale information
     *
     * @var string
     */
    protected string $storage;

    /**
     * Custom locale name
     * Used when needed locales are unavailable
     *
     * @type Boolean
     */
    protected bool $customLocale;

    /**
     * Default relative path
     *
     * @type string
     */
    protected string $relativePath;

    /**
     * Poedit keywords list
     *
     * @type array
     */
    protected array $keywordsList;

    /**
     * Core translation handler
     */
    protected mixed $handler;

    public function __construct()
    {
        $this->encoding         = 'UTF-8';
        $this->supportedLocales = [];
        $this->sourcePaths      = [];
        $this->customLocale     = false;
        $this->relativePath     = "../../../../../app";
    }

    public function getRelativePath(): string
    {
        return $this->relativePath;
    }

    public function setRelativePath($path): void
    {
        $this->relativePath = $path;
    }

    /**
     * @return string
     */
    public function getSessionIdentifier(): string
    {
        return $this->sessionIdentifier;
    }

    /**
     * @param string $sessionIdentifier
     *
     * @return $this
     */
    public function setSessionIdentifier(string $sessionIdentifier): static
    {
        $this->sessionIdentifier = $sessionIdentifier;

        return $this;
    }

    /**
     * @return string
     */
    public function getEncoding(): string
    {
        return $this->encoding;
    }

    /**
     * @param string $encoding
     *
     * @return $this
     */
    public function setEncoding(string $encoding): static
    {
        $this->encoding = $encoding;

        return $this;
    }

    /**
     * @return string
     */
    public function getLocale(): string
    {
        return $this->locale;
    }

    /**
     * @param string $locale
     *
     * @return $this
     */
    public function setLocale(string $locale): static
    {
        $this->locale = $locale;

        return $this;
    }

    /**
     * Gets categories
     *
     * @return array
     */
    public function getCategories(): array
    {
        return $this->categories;
    }

    /**
     * Sets categories
     *
     * @param array $categories
     *
     * @return self
     */
    public function setCategories(array $categories): static
    {
        $this->categories = $categories;

        return $this;
    }

    /**
     * @return string
     */
    public function getFallbackLocale(): string
    {
        return $this->fallbackLocale;
    }

    /**
     * @param string $fallbackLocale
     *
     * @return $this
     */
    public function setFallbackLocale(string $fallbackLocale): static
    {
        $this->fallbackLocale = $fallbackLocale;

        return $this;
    }

    /**
     * @return array
     */
    public function getSupportedLocales(): array
    {
        return $this->supportedLocales;
    }

    /**
     * @param array $supportedLocales
     *
     * @return $this
     */
    public function setSupportedLocales(array $supportedLocales): static
    {
        $this->supportedLocales = $supportedLocales;

        return $this;
    }

    /**
     * @return string
     */
    public function getDomain(): string
    {
        return $this->domain;
    }

    /**
     * @param string $domain
     *
     * @return $this
     */
    public function setDomain(string $domain): static
    {
        $this->domain = $domain;

        return $this;
    }

    /**
     * @return string
     */
    public function getTranslationsPath(): string
    {
        return $this->translationsPath;
    }

    /**
     * @param string $translationsPath
     *
     * @return $this
     */
    public function setTranslationsPath(string $translationsPath): static
    {
        $this->translationsPath = $translationsPath;

        return $this;
    }

    /**
     * @return string
     */
    public function getProject(): string
    {
        return $this->project;
    }

    /**
     * @param string $project
     *
     * @return $this
     */
    public function setProject(string $project): static
    {
        $this->project = $project;

        return $this;
    }

    /**
     * @return string
     */
    public function getTranslator(): string
    {
        return $this->translator;
    }

    /**
     * @param string $translator
     *
     * @return $this
     */
    public function setTranslator(string $translator): static
    {
        $this->translator = $translator;

        return $this;
    }

    /**
     * @return array
     */
    public function getSourcePaths(): array
    {
        return $this->sourcePaths;
    }

    /**
     * @param array $sourcePaths
     *
     * @return $this
     */
    public function setSourcePaths(array $sourcePaths): static
    {
        $this->sourcePaths = $sourcePaths;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isSyncLaravel(): bool
    {
        return $this->syncLaravel;
    }

    /**
     * Gets the Sync with laravel locale.
     *
     * @return bool
     */
    public function getSyncLaravel(): bool
    {
        return $this->syncLaravel;
    }

    /**
     * @param boolean $syncLaravel
     *
     * @return $this
     */
    public function setSyncLaravel(bool $syncLaravel): static
    {
        $this->syncLaravel = $syncLaravel;

        return $this;
    }

    /**
     * Gets the adapter class.
     *
     * @return string
     */
    public function getAdapter(): string
    {
        return $this->adapter;
    }

    /**
     * @param string $adapter
     *
     * @return $this
     */
    public function setAdapter(string $adapter): static
    {
        $this->adapter = $adapter;

        return $this;
    }

    /**
     * Getter for storage
     *
     * @return string
     */
    public function getStorage(): string
    {
        return $this->storage;
    }

    /**
     * @param string $storage
     *
     * @return $this
     */
    public function setStorage(string $storage): static
    {
        $this->storage = $storage;

        return $this;
    }



    /**
     * Return an array with all domain names
     *
     * @return array
     */
    public function getAllDomains(): array
    {
        $domains = [$this->domain]; // add the default domain

        foreach ($this->sourcePaths as $domain => $paths) {
            if (is_array($paths)) {
                $domains[] = $domain;
            }
        }

        return array_unique($domains);
    }

    /**
     * Return all routes for a single domain
     *
     * @param $domain
     *
     * @return array
     */
    public function getSourcesFromDomain($domain): array
    {
        // grab any paths wrapped in $domain
        $explicitPaths = array_key_exists($domain, $this->sourcePaths)
            ? $this->sourcePaths[$domain]
            : [];

        // if we're not including the default domain, return what we have so far
        if ($this->domain != $domain) {
            return $explicitPaths;
        }

        // otherwise, grab all the default domain paths
        // and merge them with paths wrapped in $domain
        return array_reduce(
            $this->sourcePaths,
            function ($carry, $path) {
                if (!is_array($path)) {
                    $carry[] = $path;
                }

                return $carry;
            },
            $explicitPaths
        );
    }

    /**
     * Gets C locale setting.
     *
     * @return boolean
     */
    public function getCustomLocale(): bool
    {
        return $this->customLocale;
    }

    /**
     * Sets if will use C locale structure.
     *
     * @param $customLocale
     * @return self
     */
    public function setCustomLocale($customLocale): static
    {
        $this->customLocale = $customLocale;

        return $this;
    }

    /**
     * Gets the Poedit keywords list.
     *
     * @return array|string[]
     */
    public function getKeywordsList(): array
    {
        return !empty($this->keywordsList)
            ? $this->keywordsList
            : ['_'];
    }

    /**
     * Sets the Poedit keywords list.
     *
     * @param mixed $keywordsList the keywords list
     *
     * @return self
     */
    public function setKeywordsList(mixed $keywordsList): static
    {
        $this->keywordsList = $keywordsList;

        return $this;
    }

    /**
     * Sets the handler type. Also check for valid handler name
     *
     * @param $handler
     *
     * @return $this
     *
     * @throws Exception
     */
    public function setHandler($handler): static
    {
        if (!in_array($handler, [
            'symfony',
            'gettext',
        ])
        ) {
            throw new Exception("Handler '$handler' is not supported'");
        };

        $this->handler = $handler;

        return $this;
    }

    /**
     * Returns the handler name
     *
     * @return mixed
     */
    public function getHandler(): mixed
    {
        return !empty($this->handler)
            ? $this->handler
            : 'symfony';
    }
}
