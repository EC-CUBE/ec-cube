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

namespace Plugin\SamplePayment\Entity;

use Eccube\Annotation\EntityExtension;
use Doctrine\ORM\Mapping as ORM;

/**
 * @EntityExtension("Eccube\Entity\Customer")
 */
trait CustomerTrait
{
    /**
     * カードの記憶用カラム.
     *
     * @var string
     * @ORM\Column(type="smallint", nullable=true)
     *
     * TODO 命名規約が必要.
     */
    public $sample_token_cards;
}
