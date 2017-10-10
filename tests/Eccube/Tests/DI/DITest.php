<?php

namespace Eccube\Tests\DI;

use Doctrine\Common\Annotations\AnnotationReader;
use Eccube\DI\DependencyBuilder;
use Eccube\Di\Di;
use PHPUnit\Framework\TestCase;
use Pimple\Container;

class DITest extends TestCase
{
    /**
     * @var DependencyBuilder
     */
    protected $di;

    /**
     * @var Container
     */
    protected $container;

    public function setUp()
    {
        $this->di = new DependencyBuilder(__DIR__, 'TestServiceProviderCache', [], new AnnotationReader(), true);

        $this->container = new Container();
    }

    public function tearDown()
    {
        $path = $this->di->getProviderPath();

        if (file_exists($path)) {
            unlink($path);
        }
    }

    public function testNewInstance()
    {
        self::assertInstanceOf(DependencyBuilder::class, $this->di);
    }

    public function testFindClasses()
    {
        $classes = $this->di->findClasses([__DIR__.'/Test']);
        self::assertNotEmpty($classes);
    }
}
