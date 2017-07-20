<?php

namespace Eccube\Service\PurchaseFlow;


use Eccube\Application;
use Eccube\Entity\Customer;
use Eccube\Entity\ItemHolderInterface;

class PurchaseContext extends \SplObjectStorage
{
    private $user;

    private $originHolder;

    public static function create(ItemHolderInterface $originHolder = null)
    {
        return new self($originHolder);
    }

    protected function __construct(ItemHolderInterface $originHolder = null)
    {
        $this->originHolder = $originHolder;
    }

    public function getOriginHolder()
    {
        return $this->originHolder;
    }

    public function getUser()
    {
        return $this->user;
    }
}