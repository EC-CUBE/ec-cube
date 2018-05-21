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

namespace Eccube\Form\Type;

use Symfony\Component\Form\AbstractType;

// deprecated 3.1で削除予定

class FaxType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return TelType::class;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'fax';
    }
}
