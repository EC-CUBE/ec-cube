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
    public function getFullName()
    {
        return (string) $this->name01.' '.$this->name02;
    }

    public function getFullNameKana()
    {
        return (string) $this->kana01.' '.$this->kana02;
    }
}
