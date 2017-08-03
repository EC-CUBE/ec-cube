<?php

namespace Eccube\Form;

use Doctrine\Common\Annotations\AnnotationReader;
use Symfony\Component\Form\Exception\ExceptionInterface;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\Form\Exception\InvalidArgumentException;
use Symfony\Component\Form\FormRegistry as BaseFormRegistry;
use Symfony\Component\Form\ResolvedFormTypeFactoryInterface;

class FormRegistry extends BaseFormRegistry
{
    protected $app;
    public function __construct(array $extensions, ResolvedFormTypeFactoryInterface $resolvedTypeFactory, $app)
    {
        parent::__construct($extensions, $resolvedTypeFactory);
        $this->app = $app;
    }

    public function getType($name)
    {
        $reader = new AnnotationReader();
        $Type = parent::getType($name);
        $formType = $Type->getInnerType();
        $ReflectionClass = new \ReflectionClass($formType);
        $ReflectionProperties = $ReflectionClass->getProperties();
        foreach ($ReflectionProperties as $Property) {
            $anno = $reader->getPropertyAnnotation($Property, \Eccube\Annotation\Inject::class);
            if ($anno) {
                if ($anno->value == 'Eccube\Application') {
                    $Property->setAccessible(true);
                    $Property->setValue($formType, $this->app);
                } else {
                    if ($this->app->offsetExists($anno->value)) {
                        $Property->setAccessible(true);
                        $Property->setValue($formType, $this[$anno->value]);
                    }
                }
            }
        }
        return $Type;
    }
}
