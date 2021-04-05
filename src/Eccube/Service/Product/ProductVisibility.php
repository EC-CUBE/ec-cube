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

namespace Eccube\Service\Product;


use Eccube\Doctrine\Query\WhereCustomizer;
use Eccube\Entity\Product;
use Eccube\Repository\QueryKey;

abstract class ProductVisibility extends WhereCustomizer
{
    /**
     * @param Product $Product
     * @return boolean
     */
    public abstract function checkVisibility(Product $Product);

    public function getQueryKey()
    {
        return QueryKey::PRODUCT_SEARCH;
    }

}
