<?php

namespace Eccube\Service\PurchaseFlow;

use Eccube\Entity\ItemHolderInterface;
use Eccube\Entity\Customer;

class PurchaseContext extends \SplObjectStorage
{
    private $user;

    private $originHolder;

    public function __construct(ItemHolderInterface $originHolder = null, Customer $user = null)
    {
        $this->originHolder = $originHolder;
        $this->user = $user;
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
