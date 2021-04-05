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
    const PRODUCT_SEARCH = 'Product.getQueryBuilderBySearchData';
    const PRODUCT_SEARCH_ADMIN = 'Product.getQueryBuilderBySearchDataForAdmin';

    const CUSTOMER_SEARCH = 'Customer.getQueryBuilderBySearchData';

    const ORDER_SEARCH_ADMIN = 'Order.getQueryBuilderBySearchDataForAdmin';
    const ORDER_SEARCH_BY_CUSTOMER = 'Order.getQueryBuilderByCustomer';

    const LOGIN_HISTORY_SEARCH_ADMIN = 'LoginHistory.getQueryBuilderBySearchDataForAdmin';
}
