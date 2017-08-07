<?php

namespace Eccube\Form;

use Eccube\Annotation\Inject;
use Eccube\Application;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\CachedReader;
use Doctrine\Common\Cache\ArrayCache;
use Symfony\Component\Form\FormRegistry as BaseFormRegistry;
use Symfony\Component\Form\ResolvedFormTypeFactoryInterface;

class FormRegistry extends BaseFormRegistry
{
    protected $app;
    protected $reader;

    public function __construct(array $extensions, ResolvedFormTypeFactoryInterface $resolvedTypeFactory, Application $app)
    {
        parent::__construct($extensions, $resolvedTypeFactory);
        $this->reader = new CachedReader(new AnnotationReader(), new ArrayCache());
        $this->app = $app;
    }

    public function getType($name)
    {
        $Type = parent::getType($name);
        $formType = $Type->getInnerType();
        $ReflectionClass = new \ReflectionClass($formType);
        $ReflectionProperties = $ReflectionClass->getProperties();
        foreach ($ReflectionProperties as $Property) {
            // プロパティに @Inject アノテーションを適用する
            $anno = $this->reader->getPropertyAnnotation($Property, Inject::class);
            if ($anno) {
                if ($anno->value == 'Eccube\Application') {
                    $Property->setAccessible(true);
                    $Property->setValue($formType, $this->app);
                } else {
                    if ($this->app->offsetExists($anno->value)) {
                        $Property->setAccessible(true);
                        $Property->setValue($formType, $this->app[$anno->value]);
                    }
                }
            }
        }
        return $Type;
    }
}
