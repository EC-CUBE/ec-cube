<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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
