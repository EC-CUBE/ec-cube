<?php

namespace Eccube\Di;

use Doctrine\Common\Annotations\Reader;
use Eccube\Annotation\Component;
use Eccube\Annotation\FormExtension;
use Eccube\Annotation\FormType;
use Eccube\Annotation\Inject;
use Eccube\Annotation\Repository;
use Eccube\Annotation\Service;
use Pimple\Container;
use Symfony\Component\Finder\Finder;

class Di
{
    private $dirs;

    private $reader;

    private $generator;

    private $debug;

    private $supportAnnotations = [
        Component::class,
        Repository::class,
        FormType::class,
        FormExtension::class,
        Service::class,
    ];

    public function __construct(array $dirs, Reader $reader, ProviderGenerator $generator, $debug = false)
    {
        $this->dirs = $dirs;
        $this->reader = $reader;
        $this->generator = $generator;
        $this->debug = $debug;
    }

    public function build(Container $container)
    {
        if ($this->debug || false === $this->generator->providerExists()) {
            $classes = $this->findClasses($this->dirs);
            $components = $this->findComponents($classes);
            $this->generator->dump($components);
        }

        $path = $this->generator->getProviderPath();
        $class = $this->generator->getProviderClass();

        require_once $path;

        $container->register(new $class());
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
