<?php

namespace Anubixo\LaravelGettext;

use Anubixo\LaravelGettext\Composers\LanguageSelector;
use Anubixo\LaravelGettext\Exceptions\UndefinedDomainException;
use Anubixo\LaravelGettext\Translators\TranslatorInterface;
use Exception;

class LaravelGettext
{
    /**
     * Translator handler
     *
     * @var TranslatorInterface $translator
     */
    protected static TranslatorInterface $translator;
    private string $encoding;

    /**
     * @param TranslatorInterface $gettext
     */
    public function __construct(TranslatorInterface $gettext)
    {
        self::$translator = $gettext;
    }

    /**
     * Get the current encoding
     *
     * @return string
     */
    public function getEncoding(): string
    {
        return self::$translator->getEncoding();
    }

    /**
     * Set the current encoding
     *
     * @param string $encoding
     * @return $this
     */
    public function setEncoding(string $encoding): static
    {
        $this->encoding = $encoding;
        return $this;
    }

    /**
     * Gets the Current locale.
     *
     * @return string
     */
    public static function getLocale(): string
    {
        if (isset(self::$translator))
            return self::$translator->getLocale();

        return app()->getLocale();
    }

    /**
     * Set current locale
     *
     * @param string $locale
     * @return $this
     * @throws Exception
     */
    public function setLocale(string $locale): static
    {
        if ($locale != $this->getLocale()) {
            if (isset(self::$translator)) {
                self::$translator->setLocale($locale);
            }
        }

        return $this;
    }

    /**
     * Get the language portion of the locale
     * (ex. en_GB returns en)
     *
     * @param string|null $locale
     * @return string|null
     */
    public function getLocaleLanguage(string $locale = null): ?string
    {
        if (is_null($locale)) {
            $locale = $this->getLocale();
        }

        $localeArray = explode('_', $locale);

        if (!isset($localeArray[0])) {
            return null;
        }

        return $localeArray[0];
    }

    /**
     * Get the language selector object
     *
     * @param array $labels
     * @return LanguageSelector
     */
    public function getSelector(array $labels = []): LanguageSelector
    {
        return LanguageSelector::create($this, $labels);
    }

    /**
     * Sets the current domain
     *
     * @param string $domain
     * @return $this
     * @throws UndefinedDomainException
     */
    public function setDomain(string $domain): static
    {
        self::$translator->setDomain($domain);
        return $this;
    }

    /**
     * Returns the current domain
     *
     * @return string
     */
    public function getDomain(): string
    {
        return self::$translator->getDomain();
    }

    /**
     * Translates a message with the current handler
     *
     * @param $message
     * @return string
     */
    public function translate($message): string
    {
        return self::$translator->translate($message);
    }

    /**
     * Translates a plural string with the current handler
     *
     * @param $singular
     * @param $plural
     * @param $count
     * @return string
     */
    public function translatePlural($singular, $plural, $count): string
    {
        return self::$translator->translatePlural($singular, $plural, $count);
    }

    /**
     * Returns the translator.
     *
     * @return TranslatorInterface
     */
    public function getTranslator(): TranslatorInterface
    {
        return self::$translator;
    }

    /**
     * Sets the translator
     *
     * @param TranslatorInterface $translator
     * @return $this
     */
    public function setTranslator(TranslatorInterface $translator): static
    {
        self::$translator = $translator;
        return $this;
    }

    /**
     * Returns supported locales
     *
     * @return array
     */
    public function getSupportedLocales(): array
    {
        return self::$translator->supportedLocales();
    }

    /**
     * Indicates if given locale is supported
     *
     * @param $locale
     * @return bool
     */
    public function isLocaleSupported($locale): bool
    {
        return self::$translator->isLocaleSupported($locale);
    }
}
