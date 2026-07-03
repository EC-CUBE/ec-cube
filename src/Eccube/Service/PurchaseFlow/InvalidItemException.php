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
    /**
     * @param array<int|string, string>|null $messageArgs
     */
    public function __construct(?string $message = null, private readonly ?array $messageArgs = [], private readonly bool $warning = false)
    {
        parent::__construct($message);
    }

    /**
     * @return array<int|string, string>
     */
    public function getMessageArgs(): array
    {
        return $this->messageArgs;
    }

    public function isWarning(): bool
    {
        return $this->warning;
    }

    public static function fromProductClass(?string $errorMessage, ProductClass $ProductClass): InvalidItemException
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
