<?php

namespace Eccube\Service\PurchaseFlow;

use Eccube\Entity\ItemHolderInterface;

class PurchaseContext extends \SplObjectStorage
{
    private $user;

    private $originHolder;

    public function __construct(ItemHolderInterface $originHolder = null)
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
