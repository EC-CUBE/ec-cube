<?php

namespace Eccube\Di;

use Doctrine\Common\Annotations\Reader;
use Eccube\Annotation\Component;
use Eccube\Annotation\FormExtension;
use Eccube\Annotation\FormType;
use Eccube\Annotation\Inject;
use Eccube\Annotation\Repository;
use Eccube\Annotation\Service;
use Eccube\Application;
use Pimple\Container;
use Symfony\Component\Finder\Finder;

class Di
{
    private $debug;

    private $dirs;

    private $reader;

    private $cacheDir;

    private $cacheFileName = 'ServiceProviderCache.php';

    private $cacheClass = '\Eccube\ServiceProvider\ServiceProviderCache';

    private $supportAnnotations = [
        Component::class,
        Repository::class,
        FormType::class,
        FormExtension::class,
        Service::class,
    ];

    public function __construct(array $dirs, Reader $reader, $cacheDir, $debug = false)
    {
        $this->debug = $debug;
        $this->dirs = $dirs;
        $this->reader = $reader;

        if (!is_dir($cacheDir) && !@mkdir($cacheDir, 0777, true) && !is_dir($cacheDir)) {
            throw new \RuntimeException(sprintf('Eccube Di was not able to create directory "%s"', $cacheDir));
        }
        $this->cacheDir = $cacheDir;
        $this->generator = new ProviderGenerator();
    }

    public function build(Container $container)
    {
        $classes = $this->findClasses($this->dirs);

        $components = $this->findComponents($classes);

        $cacheFile = $this->cacheDir.DIRECTORY_SEPARATOR.$this->cacheFileName;
        if ($this->debug || false === file_exists($cacheFile)) {
            $provider = $this->generator->generate($components);
            file_put_contents($cacheFile, $provider);
        }

        require_once $cacheFile;

        $container->register(new $this->cacheClass());
    }

    public function findClasses(array $dirs)
    {
        $files = Finder::create()
            ->in($dirs)
            ->name('*.php')
            ->files();

        $classes = [];
        $includedFiles = [];
        foreach ($files as $file) {
            $path = $file->getRealPath();
            require_once $path;
            $includedFiles[] = $path;
        }

        $declared = get_declared_classes();

        foreach ($declared as $className) {
            $rc = new \ReflectionClass($className);
            $sourceFile = $rc->getFileName();
            if (in_array($sourceFile, $includedFiles)) {
                $classes[] = $className;
            }
        }

        return $classes;
    }

    public function register(Container $container, array $components)
    {
        foreach ($components as $component) {
            $container[$component['id']] = function () use ($component, $container) {
                /** @var \ReflectionClass $class */
                $class = $component['ref_class'];
                $object = $class->newInstanceWithoutConstructor();

                foreach ($component['injects'] as $inject) {
                    /** @var \ReflectionProperty $property */
                    $property = $inject['ref_property'];
                    $property->setAccessible(true);
                    $property->setValue($object, $container[$inject['id']]);
                }

                return $object;
            };
        }
    }

    public function findComponents(array $classes)
    {
        $components = [];
        foreach ($classes as $class) {
            $refClass = new \ReflectionClass($class);
            $annotations = $this->reader->getClassAnnotations($refClass);
            foreach ($annotations as $anno) {
                if (false === $this->supports($anno)) {
                    continue;
                }
                $component = [
                    'id' => $anno->value ?: $refClass->getName(),
                    'anno' => $anno,
                    'ref_class' => $refClass,
                    'class_name' => $refClass->getName(),
                    'injects' => [],
                ];

                $refProps = $refClass->getProperties();
                foreach ($refProps as $refProp) {
                    $anno = $this->reader->getPropertyAnnotation($refProp, Inject::class);
                    if (is_null($anno)) {
                        continue;
                    }
                    $component['injects'][] = [
                        'id' => $anno->value,
                        'ref_property' => $refProp,
                        'property_name' => $refProp->getName(),
                    ];
                }
                $components[] = $component;
            }
        }

        return $components;
    }

    protected function supports($annotation)
    {
        return in_array(get_class($annotation), $this->supportAnnotations);
    }
}
