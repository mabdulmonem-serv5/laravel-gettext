<?php

namespace Anubixo\LaravelGettext\Translators;

use Anubixo\LaravelGettext\Adapters\LaravelAdapter;
use Anubixo\LaravelGettext\FileSystem;
use Anubixo\LaravelGettext\Adapters\AdapterInterface;
use Anubixo\LaravelGettext\Config\Models\Config;
use Anubixo\LaravelGettext\Exceptions\LocaleNotSupportedException;
use Anubixo\LaravelGettext\Exceptions\UndefinedDomainException;

use Exception;
use Anubixo\LaravelGettext\Storages\Storage;
use RuntimeException;

/**
 * Class implemented by the php-gettext module translator
 * @package Anubixo\LaravelGettext\Translators
 */
class Gettext extends BaseTranslator implements TranslatorInterface
{
    /**
     * Config container
     * @type Config
     */
    protected Config $configuration;

    /**
     * Current encoding
     * @type String
     */
    protected string $encoding;

    /**
     * Current locale
     * @type String
     */
    protected string $locale;

    /**
     * Locale categories
     * @type array
     */
    protected array $categories;

    /**
     * Framework adapter
     * @type LaravelAdapter|AdapterInterface
     */
    protected LaravelAdapter|AdapterInterface $adapter;

    /**
     * File system helper
     * @var FileSystem
     */
    protected FileSystem $fileSystem;

    /**
     * @var String
     */
    protected string $domain;

    /**
     * @throws LocaleNotSupportedException
     * @throws Exception
     */
    public function __construct(
        Config  $config,
        AdapterInterface $adapter,
        FileSystem $fileSystem,
        Storage $storage
    ) {
        parent::__construct($config, $adapter, $fileSystem, $storage);

        // General domain
        $this->domain = $this->storage->getDomain();

        // Encoding is set from configuration
        $this->encoding = $this->storage->getEncoding();

        // Categories are set from configuration
        $this->categories = $this->configuration->getCategories();

        // Sets defaults for boot
        $locale = $this->storage->getLocale();

        $this->setLocale($locale);
    }


    /**
     * Sets the current locale code
     * @throws Exception
     */
    public function setLocale($locale): static
    {
        if (!$this->isLocaleSupported($locale)) {
            throw new LocaleNotSupportedException(
                sprintf('Locale %s is not supported', $locale)
            );
        }

        try {
            $customLocale = $this->configuration->getCustomLocale() ? "C." : $locale . ".";
            $gettextLocale = $customLocale . $this->getEncoding();

            // Update all categories set in config
            foreach ($this->categories as $category) {
                putenv("$category=$gettextLocale");
                setlocale(constant($category), $gettextLocale);
            }

            parent::setLocale($locale);

            // Laravel built-in locale
            if ($this->configuration->isSyncLaravel()) {
                $this->adapter->setLocale($locale);
            }

            return $this->getLocale();
        } catch (Exception $e) {
            $this->locale = $this->configuration->getFallbackLocale();
            $exceptionPosition = $e->getFile() . ":" . $e->getLine();
            throw new Exception($exceptionPosition . $e->getMessage());

        }
    }

    /**
     * Returns a boolean that indicates if $locale
     * is supported by configuration
     *
     * @param $locale
     * @return boolean
     */
    public function isLocaleSupported($locale): bool
    {
        if ($locale) {
            return in_array($locale, $this->supportedLocales());
        }

        return false;
    }

    /**
     * Return the current locale
     *
     * @return String
     */
    public function __toString(): string
    {
        return $this->getLocale();
    }


    /**
     * Gets the Current encoding.
     *
     * @return mixed
     */
    public function getEncoding(): string
    {
        return $this->encoding;
    }

    /**
     * Sets the Current encoding.
     *
     * @param mixed $encoding the encoding
     * @return self
     */
    public function setEncoding(mixed $encoding): static
    {
        $this->encoding = $encoding;
        return $this;
    }

    /**
     * Sets the current domain and updates gettext domain application
     *
     * @param String $domain
     * @return  self
     *@throws  UndefinedDomainException    If domain is not defined
     */
    public function setDomain(String $domain): static
    {
        parent::setDomain($domain);

        $customLocale = $this->configuration->getCustomLocale() ? "/" . $this->getLocale() : "";
        
        bindtextdomain($domain, $this->fileSystem->getDomainPath() . $customLocale);
        bind_textdomain_codeset($domain, $this->getEncoding());

        $this->domain = textdomain($domain);



        return $this;
    }

    /**
     * Translates a message with gettext
     *
     * @param $message
     * @return string
     */
    public function translate($message): string
    {
        return gettext($message);
    }

    /**
     * Translates a plural message with gettext
     *
     * @param $singular
     * @param $plural
     * @param $count
     *
     * @return string
     */
    public function translatePlural($singular, $plural, $count): string
    {
        return ngettext($singular, $plural, $count);
    }

    public function translatePluralInline($message, $amount): string
    {
        throw new RuntimeException('Not supported by gettext, please use Symfony');
    }
}
