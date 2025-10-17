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

namespace Eccube\Entity;

trait NameTrait
{
    /**
     * @return string
     */
    public function getFullName(): string
    {
        return $this->name01.' '.$this->name02;
    }

    /**
     * @return string
     */
    public function getFullNameKana(): string
    {
        return $this->kana01.' '.$this->kana02;
    }
}
