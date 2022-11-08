<?php namespace Anubixo\LaravelGettext\Translators;

use Anubixo\LaravelGettext\Exceptions\UndefinedDomainException;
use Symfony\Component\Translation\Loader\PoFileLoader;
use Symfony\Component\Translation\Translator as SymfonyTranslator;
use Anubixo\LaravelGettext\Adapters\AdapterInterface;
use Anubixo\LaravelGettext\Config\Models\Config;
use Anubixo\LaravelGettext\FileLoader\Cache\ApcuFileCacheLoader;
use Anubixo\LaravelGettext\FileLoader\MoFileLoader;
use Anubixo\LaravelGettext\FileSystem;
use Anubixo\LaravelGettext\Storages\Storage;

/**
 * Class implemented by Symfony translation component
 *
 * @package Anubixo\LaravelGettext\Translators
 */
class Symfony extends BaseTranslator
{

    /**
     * Symfony translator
     *
     * @var SymfonyTranslator
     */
    protected SymfonyTranslator $symfonyTranslator;

    /**
     * @var array[]
     */
    protected array $loadedResources = [];

    public function __construct(Config $config, AdapterInterface $adapter, FileSystem $fileSystem, Storage $storage)
    {
        parent::__construct($config, $adapter, $fileSystem, $storage);
        $this->setLocale($this->storage->getLocale());
        $this->loadLocaleFile();
    }


    /**
     * Translates a message using the Symfony translation component
     *
     * @param $message
     *
     * @return string
     */
    public function translate($message): string
    {
        return $this->symfonyTranslator->trans($message, [], $this->getDomain(), $this->getLocale());
    }

    /**
     * Returns the translator instance
     *
     * @return SymfonyTranslator
     */
    protected function getTranslator(): SymfonyTranslator
    {
        if (isset($this->symfonyTranslator)) {
            return $this->symfonyTranslator;
        }

        return $this->symfonyTranslator = $this->createTranslator();
    }

    /**
     * Set locale overload.
     * Needed to re-build the catalogue when locale changes.
     *
     * @param $locale
     *
     * @return $this
     */
    public function setLocale($locale): static
    {
        parent::setLocale($locale);
        $this->getTranslator()->setLocale($locale);
        $this->loadLocaleFile();

        if ($locale != $this->adapter->getLocale()) {
            $this->adapter->setLocale($locale);
        }

        return $this;
    }

    /**
     * Set domain overload.
     * Needed to re-build the catalogue when domain changes.
     *
     *
     * @param String $domain
     *
     * @return $this
     * @throws UndefinedDomainException
     */
    public function setDomain(string $domain): static
    {
        parent::setDomain($domain);

        $this->loadLocaleFile();

        return $this;
    }

    /**
     * Creates a new translator instance
     *
     * @return SymfonyTranslator
     */
    protected function createTranslator(): SymfonyTranslator
    {
        $translator = new SymfonyTranslator($this->configuration->getLocale());
        $translator->setFallbackLocales([$this->configuration->getFallbackLocale()]);
        $translator->addLoader('mo', new ApcuFileCacheLoader(new MoFileLoader()));
        $translator->addLoader('po', new ApcuFileCacheLoader(new PoFileLoader()));

        return $translator;
    }

    /**
     * Translates a plural string
     *
     * @param $singular
     * @param $plural
     * @param $count
     *
     * @return string
     */
    public function translatePlural($singular, $plural, $count): string
    {
        return $this->symfonyTranslator->trans(
            $count > 1
                ? $plural
                : $singular,
            ['%count%' => $count],
            $this->getDomain(),
            $this->getLocale()
        );
    }

    /**
     * Translate a plural string that is only on one line separated with pipes
     *
     * @param $message
     * @param $amount
     *
     * @return string
     */
    public function translatePluralInline($message, $amount): string
    {
        return $this->symfonyTranslator->trans(
            $message,
            [
                '%count%' => $amount
            ],
            $this->getDomain(),
            $this->getLocale()
        );
    }

    /**
     * @internal param $translator
     */
    protected function loadLocaleFile()
    {
        if (isset($this->loadedResources[$this->getDomain()])
            && isset($this->loadedResources[$this->getDomain()][$this->getLocale()])
        ) {
            return;
        }
        $translator = $this->getTranslator();

        $fileMo = $this->fileSystem->makeFilePath($this->getLocale(), $this->getDomain(), 'mo');
        if (file_exists($fileMo)) {
            $translator->addResource('mo', $fileMo, $this->getLocale(), $this->getDomain());
        } else {
            $file = $this->fileSystem->makeFilePath($this->getLocale(), $this->getDomain());
            $translator->addResource('po', $file, $this->getLocale(), $this->getDomain());
        }

        $this->loadedResources[$this->getDomain()][$this->getLocale()] = true;
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
    public function __toString(): string
    {
        return $this->getLocale();
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
