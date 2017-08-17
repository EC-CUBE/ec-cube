<?php

namespace Eccube\Tests\Di;

use Doctrine\Common\Annotations\AnnotationReader;
use Eccube\Di\Di;
use Eccube\Di\ProviderGenerator;
use Eccube\Tests\Di\Test\ComponentClass;
use Eccube\Tests\Di\Test\ComponentInjectClass;
use Eccube\Tests\Di\Test\FormExtensionClass;
use Eccube\Tests\Di\Test\FormTypeClass;
use Eccube\Tests\Di\Test\NoComponentClass;
use Eccube\Tests\Di\Test\RepositoryClazz;
use Eccube\Tests\Di\Test\ServiceClass;
use PHPUnit\Framework\TestCase;
use Pimple\Container;
use Silex\Provider\FormServiceProvider;

class DiTest extends TestCase
{
    /**
     * @var Di
     */
    protected $di;

    /**
     * @var ProviderGenerator
     */
    protected $generator;

    /**
     * @var Container
     */
    protected $container;

    public function setUp()
    {
        $this->generator = new ProviderGenerator(__DIR__, 'TestServiceProviderCache');
        $this->di = new Di([__DIR__], new AnnotationReader(), $this->generator, true);

        $this->container = new Container();
        $this->container->register(new FormServiceProvider());
    }

    public function tearDown()
    {
        $path = $this->generator->getProviderPath();

        if (file_exists($path)) {
            unlink($path);
        }
    }

    public function testNewInstance()
    {
        self::assertInstanceOf(Di::class, $this->di);
        self::assertInstanceOf(ProviderGenerator::class, $this->generator);
    }

    public function testFindClasses()
    {
        $classes = $this->di->findClasses([__DIR__.'/Test']);
        self::assertNotEmpty($classes);
    }

    public function testFindComponents()
    {
        $classes = $this->di->findClasses([__DIR__.'/Test']);
        $components = $this->di->findComponents($classes);
        self::assertNotEmpty($components);
    }

    public function testBuild()
    {
        $this->di->build($this->container);

        // コンテナに登録されていることを確認
        self::assertArrayHasKey(ComponentClass::class, $this->container);
        self::assertArrayHasKey(ComponentInjectClass::class, $this->container);
        self::assertArrayHasKey(FormExtensionClass::class, $this->container);
        self::assertArrayHasKey(FormTypeClass::class, $this->container);
        self::assertArrayHasKey(RepositoryClazz::class, $this->container);
        self::assertArrayHasKey(ServiceClass::class, $this->container);
        self::assertArrayNotHasKey(NoComponentClass::class, $this->container);

        // インジェクションされていることを確認
        $instance = $this->container[ComponentInjectClass::class];
        self::assertInstanceOf(ComponentInjectClass::class, $instance);
        self::assertInstanceOf(ComponentClass::class, $instance->component);
    }
}
