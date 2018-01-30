<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2018 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */

namespace Eccube\Service\PurchaseFlow;


use Eccube\Entity\ProductClass;
use Symfony\Component\Translation\Translator;
use Symfony\Component\Translation\TranslatorInterface;

trait ValidatorTrait
{
    /**
     * @var Translator
     */
    protected $translator;

    /**
     * @param TranslatorInterface $translator
     * @required
     */
    public function setTranslator(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * @param ProductClass $ProductClass
     * @param $errorCode
     * @throws InvalidItemException
     */
    protected function throwInvalidItemException($errorCode, ProductClass $ProductClass = null)
    {
        if ($ProductClass) {
            $productName = $ProductClass->getProduct()->getName();
            if ($ProductClass->hasClassCategory1()) {
                $productName .= " - ".$ProductClass->getClassCategory1()->getName();
            }
            if ($ProductClass->hasClassCategory2()) {
                $productName .= " - ".$ProductClass->getClassCategory2()->getName();
            }

            throw new InvalidItemException($this->translator->trans($errorCode, ['%product%' => $productName]));
        }
        throw new InvalidItemException($this->translator->trans($errorCode));
    }
}