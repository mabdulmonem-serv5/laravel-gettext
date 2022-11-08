<?php namespace Anubixo\LaravelGettext\Translators;

use Anubixo\LaravelGettext\Adapters\AdapterInterface;
use Anubixo\LaravelGettext\Adapters\LaravelAdapter;
use Anubixo\LaravelGettext\Config\Models\Config;
use Anubixo\LaravelGettext\Exceptions\UndefinedDomainException;
use Anubixo\LaravelGettext\FileSystem;
use Anubixo\LaravelGettext\Storages\Storage;

abstract class BaseTranslator implements TranslatorInterface
{
    /**
     * Config container
     *
     * @type Config
     */
    protected Config $configuration;

    /**
     * Framework adapter
     *
     * @type LaravelAdapter
     */
    protected LaravelAdapter|AdapterInterface $adapter;

    /**
     * File system helper
     *
     * @var FileSystem
     */
    protected FileSystem $fileSystem;

    /**
     * @var Storage
     */
    protected Storage $storage;


    /**
     * Initializes the module translator
     *
     * @param Config           $config
     * @param AdapterInterface $adapter
     * @param FileSystem       $fileSystem
     *
     * @param Storage          $storage
     */
    public function __construct(
        Config $config, AdapterInterface $adapter, FileSystem $fileSystem, Storage $storage)
    {
        // Sets the package configuration and session handler
        $this->configuration = $config;
        $this->adapter       = $adapter;
        $this->fileSystem    = $fileSystem;
        $this->storage       = $storage;
    }

    /**
     * Returns the current locale string identifier
     *
     * @return String
     */
    public function getLocale(): string
    {
        return $this->storage->getLocale();
    }

    /**
     * Sets and stores on session the current locale code
     *
     * @param $locale
     *
     * @return BaseTranslator
     */
    public function setLocale($locale): static
    {
        if ($locale == $this->storage->getLocale()) {
            return $this;
        }

        $this->storage->setLocale($locale);

        return $this;
    }

    /**
     * Returns a boolean that indicates if $locale
     * is supported by configuration
     *
     * @param $locale
     *
     * @return bool
     */
    public function isLocaleSupported($locale): bool
    {
        if ($locale) {
            return in_array($locale, $this->configuration->getSupportedLocales());
        }

        return false;
    }

    /**
     * Return the current locale
     *
     * @return String
     */
    public function __toString()
    {
        return $this->getLocale();
    }

    /**
     * Gets the Current encoding.
     *
     * @return String
     */
    public function getEncoding(): string
    {
        return $this->storage->getEncoding();
    }

    /**
     * Sets the Current encoding.
     *
     * @param mixed $encoding the encoding
     *
     * @return self
     */
    public function setEncoding(mixed $encoding): static
    {
        $this->storage->setEncoding($encoding);

        return $this;
    }

    /**
     * Sets the current domain and updates gettext domain application
     *
     * @param String $domain
     *
     * @return  self
     *@throws  UndefinedDomainException    If domain is not defined
     */
    public function setDomain(String $domain): static
    {
        if (!in_array($domain, $this->configuration->getAllDomains())) {
            throw new UndefinedDomainException("Domain '$domain' is not registered.");
        }

        $this->storage->setDomain($domain);

        return $this;
    }

    /**
     * Returns the current domain
     *
     * @return String
     */
    public function getDomain(): string
    {
        return $this->storage->getDomain();
    }


    /**
     * Returns supported locales
     *
     * @return array
     */
    public function supportedLocales(): array
    {
        return $this->configuration->getSupportedLocales();
    }

}