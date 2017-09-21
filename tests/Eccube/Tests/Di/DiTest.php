<?php

namespace Eccube\Tests\Di;

use Doctrine\Common\Annotations\AnnotationReader;
use Eccube\Di\Di;
use PHPUnit\Framework\TestCase;
use Pimple\Container;

class DiTest extends TestCase
{
    /**
     * @var Di
     */
    protected $di;

    /**
     * @var Container
     */
    protected $container;

    public function setUp()
    {
        $this->di = new Di(__DIR__, 'TestServiceProviderCache', [], new AnnotationReader(), true);

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
        self::assertInstanceOf(Di::class, $this->di);
    }

    public function testFindClasses()
    {
        $classes = $this->di->findClasses([__DIR__.'/Test']);
        self::assertNotEmpty($classes);
    }
}
