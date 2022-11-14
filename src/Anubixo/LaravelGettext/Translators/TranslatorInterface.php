<?php namespace Anubixo\LaravelGettext\Translators;

use Anubixo\LaravelGettext\Adapters\AdapterInterface;
use Anubixo\LaravelGettext\Config\Models\Config;
use Anubixo\LaravelGettext\Exceptions\UndefinedDomainException;
use Anubixo\LaravelGettext\FileSystem;
use Anubixo\LaravelGettext\Storages\Storage;

interface TranslatorInterface
{

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
        Config $config, AdapterInterface $adapter, FileSystem $fileSystem, Storage $storage);

    /**
     * Sets the current locale code
     */
    public function setLocale($locale);

    /**
     * Returns the current locale string identifier
     *
     * @return String
     */
    public function getLocale(): string;

    /**
     * Returns a boolean that indicates if $locale
     * is supported by configuration
     *
     * @param $locale
     * @return boolean
     */
    public function isLocaleSupported($locale): bool;

    /**
     * Returns supported locales
     *
     * @return array
     */
    public function supportedLocales(): array;

    /**
     * Return the current locale
     *
     * @return mixed
     */
    public function __toString();

    /**
     * Gets the Current encoding.
     *
     * @return mixed
     */
    public function getEncoding(): mixed;

    /**
     * Sets the Current encoding.
     *
     * @param mixed $encoding the encoding
     *
     * @return self
     */
    public function setEncoding(mixed $encoding): TranslatorInterface;

    /**
     * Sets the current domain and updates gettext domain application
     *
     * @param String $domain
     *
     * @return  self
     *@throws  UndefinedDomainException If domain is not defined
     */
    public function setDomain(String $domain): TranslatorInterface;

    /**
     * Returns the current domain
     *
     * @return String
     */
    public function getDomain(): string;

    /**
     * Translates a single message
     *
     * @param $message
     *
     * @return string
     */
    public function translate($message): string;

    /**
     * Translates a plural string
     *
     * @param $singular
     * @param $plural
     * @param $count
     *
     * @return mixed
     */
    public function translatePlural($singular, $plural, $count): mixed;

    /**
     * Translate a plural string that is only on one line separated with pipes
     *
     * @param $message
     * @param $amount
     *
     * @return string
     */
    public function translatePluralInline($message, $amount): string;
}