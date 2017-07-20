<?php

namespace Eccube\Service\CartComparator;

class CompareContext
{
    /** @var \Doctrine\Common\Collections\ArrayCollection */
    protected $compareStrategies;

    function __construct()
    {
        $this->compareStrategies = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * @param $CartItem1
     * @param $CartItem2
     * @return bool
     */
    public function compare($CartItem1, $CartItem2)
    {
        $result = !$this->compareStrategies->isEmpty();

        /** @var \Eccube\Service\CartComparator\Strategy\CartComparatorStrategyInterface $strategy */
        foreach ($this->compareStrategies as $strategy) {
            $result = $result && $strategy->compare($CartItem1, $CartItem2);
        }

        return $result;
    }

    /**
     * @param Strategy\CartComparatorStrategyInterface $strategy
     * @return $this
     */
    public function addStrategy(\Eccube\Service\CartComparator\Strategy\CartComparatorStrategyInterface $strategy)
    {
        $this->compareStrategies->add($strategy);

        return $this;
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getStrategies()
    {
        return $this->compareStrategies;
    }
}
