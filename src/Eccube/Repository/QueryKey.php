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

namespace Eccube\Repository;

final class QueryKey
{
    public const PRODUCT_SEARCH = 'Product.getQueryBuilderBySearchData';
    public const PRODUCT_SEARCH_ADMIN = 'Product.getQueryBuilderBySearchDataForAdmin';

    public const CUSTOMER_SEARCH = 'Customer.getQueryBuilderBySearchData';

    public const ORDER_SEARCH_ADMIN = 'Order.getQueryBuilderBySearchDataForAdmin';
    public const ORDER_SEARCH_BY_CUSTOMER = 'Order.getQueryBuilderByCustomer';

    public const LOGIN_HISTORY_SEARCH_ADMIN = 'LoginHistory.getQueryBuilderBySearchDataForAdmin';
}
