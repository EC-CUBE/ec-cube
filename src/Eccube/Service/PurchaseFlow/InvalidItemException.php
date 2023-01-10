<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) EC-CUBE CO.,LTD. All Rights Reserved.
 *
 * http://www.ec-cube.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eccube\Service\PurchaseFlow;

use Eccube\Entity\ProductClass;

class InvalidItemException extends \Exception
{
    private $messageArgs;

    private $warning;

    public function __construct($message = null, $messageArgs = [], $warning = false)
    {
        parent::__construct($message);
        $this->messageArgs = $messageArgs;
        $this->warning = $warning;
    }

    /**
     * @return array
     */
    public function getMessageArgs(): array
    {
        return $this->messageArgs;
    }

    /**
     * @return bool
     */
    public function isWarning(): bool
    {
        return $this->warning;
    }

    /**
     * @return InvalidItemException
     */
    public static function fromProductClass($errorMessage, ProductClass $ProductClass): self
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
