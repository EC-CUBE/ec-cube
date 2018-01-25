<?php


namespace Eccube\Doctrine\Query;


use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\Reader;
use Doctrine\ORM\QueryBuilder;
use Eccube\Annotation\QueryExtension;

class Queries
{
    /**
     * @var AnnotationReader
     */
    protected $reader;

    private $customizers = [];

    public function __construct(Reader $reader)
    {
        $this->reader = $reader;
    }

    public function addCustomizer(QueryCustomizer $customizer)
    {
        if (!$customizer) {
            throw new \InvalidArgumentException('Customizer should not be null.');
        }
        $rc = new \ReflectionClass($customizer);
        $anno = $this->reader->getClassAnnotation($rc, QueryExtension::class);
        if (!$anno) {
            throw new \InvalidArgumentException(get_class($customizer).' doesn\'t have any '.QueryExtension::class.' annotation.');
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
