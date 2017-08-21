<?php

namespace Eccube\Tests\Di;

use Eccube\Annotation\Component;
use Eccube\Annotation\FormExtension;
use Eccube\Annotation\FormType;
use Eccube\Annotation\Inject;
use Eccube\Annotation\Repository;
use Eccube\Annotation\Service;
use Eccube\Application;
use Eccube\Di\ProviderGenerator;
use Eccube\Entity\BaseInfo;
use Eccube\Entity\Master\Pref;
use Eccube\Repository\BaseInfoRepository;
use Eccube\Repository\Master\PrefRepository;
use Eccube\Repository\MemberRepository;
use Eccube\Tests\Di\Test\ComponentClass;
use Eccube\Tests\Di\Test\ComponentInjectClass;
use Eccube\Tests\Di\Test\FormExtensionClass;
use Eccube\Tests\Di\Test\FormTypeClass;
use Eccube\Tests\Di\Test\RepositoryClazz;
use Eccube\Tests\Di\Test\ServiceClass;
use PHPUnit\Framework\TestCase;

class ProviderGeneratorTest extends TestCase
{
    /**
     * @var ProviderGenerator
     */
    protected $generator;

    protected $providerDir = __DIR__;

    protected $providerClass = 'TestServiceProviderCache';

    public function setUp()
    {
        $this->generator = new ProviderGenerator($this->providerDir, $this->providerClass);
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
        self::assertInstanceOf(ProviderGenerator::class, $this->generator);
    }

    public function testGetProviderPath()
    {
        $expected = $this->providerDir.'/'.$this->providerClass.'.php';
        $actual = $this->generator->getProviderPath();
        self::assertSame($expected, $actual);
    }

    public function testGetProviderClass()
    {
        $expected = $this->providerClass;
        $actual = $this->generator->getProviderClass();
        self::assertSame($expected, $actual);
    }

    public function testProviderExists()
    {
        self::assertFalse($this->generator->providerExists());

        touch($this->generator->getProviderPath());
        self::assertTrue($this->generator->providerExists());
    }

    public function testConvertToEntity()
    {
        // Eccube\Repository\XxxRepository -> Eccube\Entity\Xxx
        $entity = $this->generator->convertToEntity(BaseInfoRepository::class);
        self::assertSame(BaseInfo::class, $entity);

        // Eccube\Repository\Master\XxxRepository -> Eccube\Entity\Master\Xxx
        $entity = $this->generator->convertToEntity(PrefRepository::class);
        self::assertSame(Pref::class, $entity);
    }

    public function testIsApplication()
    {
        self::assertTrue($this->generator->isApplication(Application::class));
        self::assertFalse($this->generator->isApplication(MemberRepository::class));
    }

    public function testIsRepository()
    {
        self::assertTrue($this->generator->isRepository(new Repository()));
        self::assertFalse($this->generator->isRepository(new Component()));
        self::assertFalse($this->generator->isRepository(new Service()));
        self::assertFalse($this->generator->isRepository(new FormType()));
        self::assertFalse($this->generator->isRepository(new FormExtension()));
        self::assertFalse($this->generator->isRepository(new Inject()));
    }

    public function testGenerate()
    {
        $provider = $this->generator->generate([]);
        self::assertStringStartsWith('<?php', $provider);
        self::assertContains($this->generator->getProviderClass(), $provider);
    }

    public function testGenerateWithComponent()
    {
        $component = [
            'id' => ComponentClass::class,
            'anno' => new Component(),
            'ref_class' => new \ReflectionClass(ComponentClass::class),
            'class_name' => ComponentClass::class,
            'injects' => [],
        ];
        $provider = $this->generator->generate([$component]);
        self::assertContains(ComponentClass::class, $provider);
    }

    public function testGenerateWithInject()
    {
        $component = [
            'id' => ComponentInjectClass::class,
            'anno' => new Component(),
            'ref_class' => new \ReflectionClass(ComponentInjectClass::class),
            'class_name' => ComponentInjectClass::class,
            'injects' => [
                [
                    'id' => ComponentClass::class,
                    'property_name' => 'component',
                ],
            ],
        ];
        $provider = $this->generator->generate([$component]);
        self::assertContains(ComponentInjectClass::class, $provider);
        self::assertContains(ComponentClass::class, $provider);
    }

    public function testGenerateWithService()
    {
        $component = [
            'id' => ServiceClass::class,
            'anno' => new Service(),
            'ref_class' => new \ReflectionClass(ServiceClass::class),
            'class_name' => ServiceClass::class,
            'injects' => [],
        ];
        $provider = $this->generator->generate([$component]);
        self::assertContains(ServiceClass::class, $provider);
    }

    public function testGenerateWithRepository()
    {
        $component = [
            'id' => RepositoryClazz::class,
            'anno' => new Repository(),
            'ref_class' => new \ReflectionClass(RepositoryClazz::class),
            'class_name' => RepositoryClazz::class,
            'injects' => [],
        ];
        $provider = $this->generator->generate([$component]);
        self::assertContains('$app["orm.em"]', $provider);
        self::assertContains(RepositoryClazz::class, $provider);
    }

    public function testGenerateWithFormType()
    {
        $component = [
            'id' => FormTypeClass::class,
            'anno' => new FormType(),
            'ref_class' => new \ReflectionClass(FormTypeClass::class),
            'class_name' => FormTypeClass::class,
            'injects' => [],
        ];
        $provider = $this->generator->generate([$component]);
        self::assertContains('$app->extend("form.types"', $provider);
        self::assertContains(FormTypeClass::class, $provider);
    }

    public function testGenerateWithFormExtension()
    {
        $component = [
            'id' => FormExtensionClass::class,
            'anno' => new FormExtension(),
            'ref_class' => new \ReflectionClass(FormExtensionClass::class),
            'class_name' => FormExtensionClass::class,
            'injects' => [],
        ];
        $provider = $this->generator->generate([$component]);
        self::assertContains('$app->extend("form.type.extensions"', $provider);
        self::assertContains(FormExtensionClass::class, $provider);
    }

    public function testDump()
    {
        self::assertNotFalse($this->generator->dump([]));
        self::assertTrue($this->generator->providerExists());
    }
}
