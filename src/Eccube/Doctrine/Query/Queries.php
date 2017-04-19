<?php


namespace Eccube\Doctrine\Query;


use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\ORM\QueryBuilder;
use Eccube\Annotation\QueryExtension;
use Psr\Log\InvalidArgumentException;

class Queries
{

    private $customizers = [];

    public function addCustomizer(QueryCustomizer $customizer) {
        if (!$customizer) {
            throw new InvalidArgumentException('Customizer should not be null.');
        }
        $reader = new AnnotationReader();
        $rc = new \ReflectionClass($customizer);
        $anno = $reader->getClassAnnotation($rc, QueryExtension::class);
        if (!$anno) {
            throw new InvalidArgumentException(get_class($customizer).' doesn\'t have any '.QueryExtension::class.' annotation.');
        }
        foreach ($anno->value as $queryKey) {
            $this->customizers[$queryKey][] = $customizer;
        }
    }

    public function customize($queryKey, QueryBuilder $builder, $params)
    {
        if (isset($this->customizers[$queryKey])) {
            /* @var QueryCustomizer $customizer */
            foreach ($this->customizers[$queryKey] as $customizer) {
                $customizer->customize($builder, $params, $queryKey);
            }
        }
        return $builder;
    }
}