<?php

namespace Eccube\Service\PurchaseFlow\Processor;


use Eccube\Application;
use Eccube\Entity\Customer;
use Eccube\Entity\ItemHolderInterface;

class PurchaseContext extends \SplObjectStorage
{
    private $user;

    private $originHolder;

    public static function create(Application $app, ItemHolderInterface $originHolder = null)
    {
        return new self($app->user(), $originHolder);
    }

    protected function __construct(Customer $user, ItemHolderInterface $originHolder = null)
    {
        $this->user = $user;
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