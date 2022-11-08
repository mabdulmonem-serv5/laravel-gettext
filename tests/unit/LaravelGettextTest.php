<?php

use \Mockery as m;

use Anubixo\LaravelGettext\Adapters\AdapterInterface;
use Anubixo\LaravelGettext\Storages\MemoryStorage;
use Anubixo\LaravelGettext\Testing\Adapter\TestAdapter;
use Anubixo\LaravelGettext\Testing\BaseTestCase;
use Anubixo\LaravelGettext\Config\ConfigManager;
use Anubixo\LaravelGettext\Adapters\LaravelAdapter;
use Anubixo\LaravelGettext\FileSystem;
use Anubixo\LaravelGettext\Translators\Symfony;

class LaravelGettextTest extends BaseTestCase
{
    /**
     * Base app path
     *
     * @var string
     */
    protected string $appPath = __DIR__.'/../../vendor/laravel/laravel/bootstrap/app.php';

    /**
     * @var Symfony
     */
    protected $translator;

    protected function setUp(): void
    {
        parent::setUp();
        $testConfig = include __DIR__ . '/../config/config.php';

        $config = ConfigManager::create($testConfig);
        $adapter = app($config->get()->getAdapter());
        $fileSystem = new FileSystem($config->get(), app_path(), storage_path());

        $translator = new Symfony(
            $config->get(),
            $adapter,
            $fileSystem,
            new MemoryStorage($config->get())
        );

        $this->translator = $translator;
    }

    public function testAdapter() {
        $testConfig = include __DIR__ . '/../config/config.php';
        $config = ConfigManager::create($testConfig);
        $adapter = app($config->get()->getAdapter());
        $this->assertInstanceOf(AdapterInterface::class, $adapter);
        $this->assertInstanceOf(TestAdapter::class, $adapter);
    }

    /**
     * Test setting locale.
     */
    public function testSetLocale()
    {
        $response = $this->translator->setLocale('en_US');

        $this->assertEquals('en_US', $response);
    }

    /**
     * Test getting locale.
     * It should receive locale from mocked config.
     */
    public function testGetLocale()
    {
        $response = $this->translator->getLocale();

        $this->assertEquals('en_US', $response);
    }

    public function testIsLocaleSupported()
    {
        $this->assertTrue($this->translator->isLocaleSupported('en_US'));
    }

    /**
     * Test dumping locale to string
     */
    public function testToString()
    {
        $response = $this->translator->__toString();

        $this->assertEquals('en_US', $response);
    }

    public function testGetEncoding()
    {
        $response = $this->translator->getEncoding();
        $this->assertNotEmpty($response);
        $this->assertEquals('UTF-8', $response);
    }

    public function testSetEncoding()
    {
        $response = $this->translator->setEncoding('UTF-8');
        $this->assertNotEmpty($response);
        $this->assertInstanceOf('Anubixo\LaravelGettext\Translators\Symfony', $response);
    }

    protected function tearDown(): void
    {
        m::close();
    }
}
