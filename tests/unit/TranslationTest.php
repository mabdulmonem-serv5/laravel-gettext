<?php
/**
 * Created by PhpStorm.
 * User: aaflalo
 * Date: 17-08-01
 * Time: 10:22
 */

namespace unit;

use Anubixo\LaravelGettext\Adapters\LaravelAdapter;
use Anubixo\LaravelGettext\Config\ConfigManager;
use Anubixo\LaravelGettext\FileSystem;
use Anubixo\LaravelGettext\Storages\MemoryStorage;
use Anubixo\LaravelGettext\Testing\BaseTestCase;
use Anubixo\LaravelGettext\Translators\Symfony;

class TranslationTest extends BaseTestCase
{

    /**
     * Base app path
     *
     * @var string
     */
    protected string $appPath = __DIR__ . '/../../vendor/laravel/laravel/bootstrap/app.php';
    /**
     * @var FileSystem
     */
    protected $fileSystem;

    /**
     * @var Symfony
     */
    protected $translator;

    protected function setUp(): void
    {
        parent::setUp();
        $testConfig = include __DIR__ . '/../config/config_fr.php';

        $config           = ConfigManager::create($testConfig);
        $adapter          = new LaravelAdapter();
        $this->fileSystem = new FileSystem($config->get(), __DIR__ . '/../', __DIR__ . '/../storage');

        $translator = new Symfony(
            $config->get(),
            $adapter,
            $this->fileSystem,
            new MemoryStorage($config->get())
        );

        $this->translator = $translator;
    }

    /**
     * View compiler tests
     */
    public function testCompileViews()
    {
        $viewPaths = ['views'];

        $result = $this->fileSystem->compileViews($viewPaths, "messages");
        $this->assertTrue($result);

    }

    public function testFrenchTranslation()
    {
        $string = $this->translator->setLocale('fr_FR')->translate('Controller string');
        $this->assertEquals('Chaine de caractère du controlleur', $string);
    }

    public function testFrenchTranslationReplacement()
    {
        $string = $this->translator->setLocale('fr_FR')->translate('Hello %s, how are you ?');
        $this->assertEquals('Salut %s, comment va ?', $string);
    }

    public function testFrenchTranslationPluralNone()
    {
        $string = $this->translator->setLocale('fr_FR')
                                   ->translatePluralInline(
                                       ' {0} There are no apples|{1} There is one apple|]1,Inf[ There are %count% apples',
                                       0);
        $this->assertEquals('Il n\'y a pas de pommes', $string);
    }

    public function testFrenchTranslationPluralOne()
    {
        $string = $this->translator->setLocale('fr_FR')
                                   ->translatePluralInline(
                                       ' {0} There are no apples|{1} There is one apple|]1,Inf[ There are %count% apples',
                                       1);
        $this->assertEquals('Il y a une pomme', $string);
    }

    public function testFrenchTranslationPluralMultiple()
    {
        $string = $this->translator->setLocale('fr_FR')
                                   ->translatePluralInline(
                                       ' {0} There are no apples|{1} There is one apple|]1,Inf[ There are %count% apples',
                                       5);
        $this->assertEquals('Il y a 5 pommes', $string);
    }

    public function testTranslatePluralSingle()
    {
        $string = $this->translator->translatePlural('Il y a une pomme', 'Il y a %s pommes', 1);

        $this->assertEquals('Il y a une pomme', $string);
    }

    public function testTranslatePluralMultiple()
    {
        $string = $this->translator->translatePlural('Il y a une pomme', 'Il y a %count% pommes', 5);

        $this->assertEquals('Il y a 5 pommes', $string);
    }
}
