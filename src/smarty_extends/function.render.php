<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2014 LOCKON CO.,LTD. All Rights Reserved.
 * http://www.lockon.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Eccube\Framework\Display;
use Eccube\Framework\Helper\MobileHelper;

/**
 * Compile {render ...} tag
 *
 * @param array $params
 * @return string
 */
function smarty_function_render($params, &$smarty)
{
    $filename = $params['items']['filename'];

    $classes = array(
        'calendar' => 'Eccube\\Page\\Bloc\\Calendar',
        'cart' => 'Eccube\\Page\\Bloc\\Cart',
        'category' => 'Eccube\\Page\\Bloc\\Category',
        'login' => 'Eccube\\Page\\Bloc\\Login',
        'login_footer' => 'Eccube\\Page\\Bloc\\LoginFooter',
        'login_header' => 'Eccube\\Page\\Bloc\\LoginHeader',
        'navi_footer' => 'Eccube\\Page\\Bloc\\NaviFooter',
        'navi_header' => 'Eccube\\Page\\Bloc\\NaviHeader',
        'news' => 'Eccube\\Page\\Bloc\\News',
        'recommend' => 'Eccube\\Page\\Bloc\\Recommend',
        'search_products' => 'Eccube\\Page\\Bloc\\SearchProducts',
    );

    $objPage = new $classes[$filename]();
    $objPage->blocItems = $params['items'];

    // 絵文字変換 (除去) フィルターを組み込む。
    ob_start(array('Eccube\\Framework\\MobileEmoji', 'handler'));

    if (Display::detectDevice() == DEVICE_TYPE_MOBILE) {
        // resize_image.phpは除外
        if (!$objPage instanceof \Eccube\Page\ResizeImage) {
            $objMobile = new MobileHelper();
            $objMobile->sfMobileInit();
        }
    }

    $objPage->init();
    $objPage->process();

    $response = ob_get_contents();
    ob_end_clean();

    return $response;
}
