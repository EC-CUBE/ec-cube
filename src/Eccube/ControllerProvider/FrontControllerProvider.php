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

namespace Eccube\ControllerProvider;

use Silex\Application;
use Silex\ControllerProviderInterface;

class FrontControllerProvider implements ControllerProviderInterface
{
    public function connect(Application $app)
    {
        $controllers = $app['controllers_factory'];

        // root
        // $controllers->match('/', "\\Eccube\\Page\\Index")->bind('index');
        // $controllers->match('/', "\\Eccube\\Page\\Index")->bind('homepage');
        $controllers->match('/', "\\Eccube\\Controller\\TopController::index")->bind('top');
        $controllers->match('/', "\\Eccube\\Controller\\TopController::index")->bind('index');
        $controllers->match('/', "\\Eccube\\Controller\\TopController::index")->bind('homepage');
        $controllers->match('/input_zip.php', '\\Eccube\\Page\\InputZip')->bind('input_zip');
        $controllers->match('/sitemap.php', '\\Eccube\\Page\\Sitemap')->bind('sitemap');
        $controllers->match('/error.php', '\\Eccube\\Page\\Error\\SystemError')->bind('error');
        $controllers->match('/resize_image.php', '\\Eccube\\Page\\ResizeImage')->bind('resize_image');

        // api
        $controllers->match('/api/', '\\Eccube\\Page\\Api\\Index')->bind('api');
        $controllers->match('/api/json.php', '\\Eccube\\Page\\Api\\Json')->bind('api_json');
        $controllers->match('/api/php.php', '\\Eccube\\Page\\Api\\Php')->bind('api_php');
        $controllers->match('/api/xml.php', '\\Eccube\\Page\\Api\\Xml')->bind('api_xml');

        // cart
        $controllers->match('/cart/', '\\Eccube\\Controller\\CartController::index')->bind('cart');
        $controllers->post('/cart/add/', '\\Eccube\\Controller\\CartController::add')
            ->bind('cart_add');
        $controllers->match('/cart/up/{productClassId}', '\\Eccube\\Controller\\CartController::up')
            ->bind('cart_up')
            ->assert('productClassId', '\d+');
        $controllers->match('/cart/down/{productClassId}', '\\Eccube\\Controller\\CartController::down')
            ->bind('cart_down')
            ->assert('productClassId', '\d+');
        $controllers->match('/cart/setQuantity/{productClassId}/{quantity}', '\\Eccube\\Controller\\CartController::setQuantity')
            ->bind('cart_set_quantity')
            ->assert('productClassId', '\d+')
            ->assert('quantity', '\d+');
        $controllers->match('/cart/remove/{productClassId}', '\\Eccube\\Controller\\CartController::remove')
            ->bind('cart_remove')
            ->assert('productClassId', '\d+');
        $controllers->match('/cart/buystep', '\\Eccube\\Controller\\CartController::buystep')->bind('cart_buystep');

        // contact
        $controllers->match('/contact/', '\\Eccube\\Controller\\ContactController::index')->bind('contact');
        $controllers->match('/contact/complete.php', '\\Eccube\\Controller\\ContactController::complete')->bind('contact_complete');

        // entry
        $controllers->match('/entry/', '\\Eccube\\Controller\\EntryController::index')->bind('entry');
        $controllers->match('/entry/complete', '\\Eccube\\Controller\\EntryController::complete')->bind('entry_complete');
        $controllers->match('/entry/activate', '\\Eccube\\Controller\\EntryController::activate')->bind('entry_activate');

        // forgot
        $controllers->match('/forgot/', '\\Eccube\\Page\\Forgot\\Index')->bind('forgot');

        // frontparts
        $controllers->match('/frontparts/login_check.php', '\\Eccube\\Page\\FrontParts\\LoginCheck')->bind('frontparts_login_check');
        $controllers->match('/block/', '\\Eccube\\Controller\\BlockController::index')->bind('block');

        // block
        $controllers->match('/frontparts/block/category.php', '\\Eccube\\Controller\\Block\\CategoryController::index')->bind('block_category');
        $controllers->match('/frontparts/block/cart.php', '\\Eccube\\Controller\\Block\\CartController::index')->bind('block_cart');
        $controllers->match('/frontparts/block/search_product.php', '\\Eccube\\Controller\\Block\\SearchProductController::index')->bind('block_search_products');
        $controllers->match('/frontparts/block/news.php', '\\Eccube\\Controller\\Block\\NewsController::index')->bind('block_news');
        $controllers->match('/frontparts/block/login.php', '\\Eccube\\Controller\\Block\\LoginController::index')->bind('block_login');
        $controllers->match('/frontparts/block/recommend.php', '\\Eccube\\Controller\\Block\\RecommendController::index')->bind('block_recommend');
        $controllers->match('/frontparts/block/calendar.php', '\\Eccube\\Controller\\Block\\CalendarController::index')->bind('block_calendar');
        $controllers->match('/frontparts/block/login_header.php', '\\Eccube\\Controller\\Block\\LoginHeaderController::index')->bind('block_login_header');

        // mypage
        $controllers->match('/mypage/', '\Eccube\Controller\Mypage\MypageController::index')->bind('mypage');
        $controllers->match('/mypage/login.php', '\Eccube\Controller\Mypage\MypageController::login')->bind('mypage_login');
        $controllers->match('/mypage/change', '\Eccube\Controller\Mypage\ChangeController::index')->bind('mypage_change');
        $controllers->match('/mypage/change_complete', '\Eccube\Controller\Mypage\ChangeController::complete')->bind('mypage_change_complete');
        $controllers->match('/mypage/delivery.php', '\Eccube\Controller\Mypage\DeliveryController::index')->bind('mypage_delivery');
        $controllers->match('/mypage/delivery_addr.php', '\Eccube\Controller\Mypage\DeliveryController::address')->bind('mypage_delivery_address');
        $controllers->match('/mypage/download.php', '\Eccube\Page\Mypage\Download')->bind('mypage_download');
        $controllers->match('/mypage/favorite.php', '\Eccube\Controller\Mypage\MypageController::favorite')->bind('mypage_favorite');
        $controllers->match('/mypage/history/{orderId}', '\Eccube\Controller\Mypage\MypageController::history')
            ->bind('mypage_history')
            ->assert('orderId', '\d+');
        $controllers->match('/mypage/mail_view/{sendId}', '\Eccube\Controller\Mypage\MypageController::mailView')
            ->bind('mypage_mail_view')
            ->assert('sendId', '\d+');
        $controllers->match('/mypage/order.php', '\Eccube\Controller\Mypage\MypageController::order')->bind('mypage_order');
        $controllers->match('/mypage/refusal.php', '\Eccube\Controller\Mypage\RefusalController::index')->bind('mypage_refusal');
        $controllers->match('/mypage/refusal_complete.php', '\Eccube\Controller\Mypage\RefusalController::complete')->bind('mypage_refusal_complete');

        // 特定商取引 order -> help/traderaw
        $controllers->match('/help/about', '\\Eccube\\Controller\\HelpController::about')->bind('help_about');
        $controllers->match('/help/guide', '\\Eccube\\Controller\\HelpController::guide')->bind('help_guide');
        $controllers->match('/help/privacy', '\\Eccube\\Controller\\HelpController::privacy')->bind('help_privacy');
        $controllers->match('/help/tradelaw', '\\Eccube\\Controller\\HelpController::tradelaw')->bind('help_tradelaw');

        // preview
        $controllers->match('/preview/', '\\Eccube\\Page\\Preview\\Index')->bind('preview');

        // products
        $controllers->match('/products/list', '\Eccube\Controller\ProductController::index')->bind('product_list');
        $controllers->match('/products/detail/{productId}', '\Eccube\Controller\ProductController::detail')
            ->bind('product_detail')
            ->assert('productId', '\d+');
        $controllers->match('/products/seaech.php', '\\Eccube\\Page\\Products\\Search')->bind('products_seaech');
        $controllers->match('/products/category_list.php', '\\Eccube\\Page\\Products\\CategoryList')->bind('products_category_list');

        // regist
        $controllers->match('/regist/', '\\Eccube\\Page\\Regist\\Index')->bind('regist');
        $controllers->match('/regist/complete.php', '\\Eccube\\Page\\Regist\\Complete')->bind('regist_complete');

        // rss
        $controllers->match('/rss/', '\\Eccube\\Page\\Rss\\Index')->bind('rss');
        $controllers->match('/rss/product.php', '\\Eccube\\Page\\Rss\\Products')->bind('rss_product');
        $controllers->match('/rss/products.php', '\\Eccube\\Page\\Rss\\Products')->bind('rss_products');

        // shopping
        $controllers->match('/shopping/', '\\Eccube\\Controller\\ShoppingController::index')->bind('shopping');
        $controllers->match('/shopping/confirm/', '\\Eccube\\Controller\\ShoppingController::confirm')->bind('shopping_confirm');
        $controllers->match('/shopping/point/', '\\Eccube\\Controller\\ShoppingController::point')->bind('shopping_point');
        $controllers->match('/shopping/delivery/', '\\Eccube\\Controller\\ShoppingController::delivery')->bind('shopping_delivery');
        $controllers->match('/shopping/payment/', '\\Eccube\\Controller\\ShoppingController::payment')->bind('shopping_payment');
        $controllers->match('/shopping/shipping/', '\\Eccube\\Controller\\ShoppingController::shipping')->bind('shopping_shipping');
        $controllers->match('/shopping/shipping_multiple/', '\\Eccube\\Controller\\ShoppingController::shippingMultiple')->bind('shopping_shipping_multiple');
        $controllers->match('/shopping/complete/', '\\Eccube\\Controller\\ShoppingController::complete')->bind('shopping_complete');
        $controllers->match('/shopping/login/', '\\Eccube\\Controller\\ShoppingController::login')->bind('shopping_login');
        $controllers->match('/shopping/nonmember/', '\\Eccube\\Controller\\ShoppingController::nonmember')->bind('shopping_nonmember');
        $controllers->match('/shopping/test/', '\\Eccube\\Controller\\ShoppingController::test')->bind('shopping_test'); // todo テスト用

        // order
        $controllers->match('/unsupported/', '\\Eccube\\Page\\Unsupported\\Index')->bind('unsupported');

        return $controllers;
    }
}
