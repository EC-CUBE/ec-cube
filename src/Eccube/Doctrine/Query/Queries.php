<?php

namespace Eccube\Doctrine\Query;

use Doctrine\ORM\QueryBuilder;

class Queries
{
    /**
     * @var QueryCustomizer[]
     */
    private $customizers = [];

    public function addCustomizer(QueryCustomizer $customizer)
    {
        $queryKey = $customizer->getQueryKey();
        $this->customizers[$queryKey][] = $customizer;
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
