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

use Eccube\Entity\ProductClass;

class InvalidItemException extends \Exception
{
    private $messageArgs = [];

    public function __construct($message = null, $messageArgs = [])
    {
        parent::__construct($message);
        $this->messageArgs = $messageArgs;
    }

    /**
     * @return array
     */
    public function getMessageArgs()
    {
        return $this->messageArgs;
    }

    /**
     * @return InvalidItemException
     */
    public static function fromProductClass($errorMessage, ProductClass $ProductClass)
    {
        $productName = $ProductClass->getProduct()->getName();
        if ($ProductClass->hasClassCategory1()) {
            $productName .= ' - '.$ProductClass->getClassCategory1()->getName();
        }
        if ($ProductClass->hasClassCategory2()) {
            $productName .= ' - '.$ProductClass->getClassCategory2()->getName();
        }

        return new self($errorMessage, ['%product%' => $productName]);
    }
}
