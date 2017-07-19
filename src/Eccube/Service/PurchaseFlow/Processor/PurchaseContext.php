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
        $user = $app->user();
        if ($user instanceof Customer) {
            return new self($app->user(), $originHolder);
        } else {
            return new self(null, $originHolder);
        }
    }

    protected function __construct(Customer $user = null, ItemHolderInterface $originHolder = null)
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