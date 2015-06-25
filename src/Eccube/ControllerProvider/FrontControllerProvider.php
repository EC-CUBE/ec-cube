<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2015 LOCKON CO.,LTD. All Rights Reserved.
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


namespace Eccube\ControllerProvider;

use Silex\Application;
use Silex\ControllerProviderInterface;

class FrontControllerProvider implements ControllerProviderInterface
{
    public function connect(Application $app)
    {
        $c = $app['controllers_factory'];

        // 強制SSL
        if ($app['config']['force_ssl'] == \Eccube\Common\Constant::ENABLED) {
            $c->requireHttps();
        }

        // user定義
        $c->match('/' . $app['config']['user_data_route'] . '/{route}', '\Eccube\Controller\UserDataController::index')->bind('user_data');

        // root
        $c->match('/', '\Eccube\Controller\TopController::index')->bind('homepage');
        $c->match('/', '\Eccube\Controller\TopController::index')->bind('top');
        $c->match('/', '\Eccube\Controller\TopController::index')->bind('index');
        $c->match('/input_zip', '\Eccube\Page\InputZip')->bind('input_zip');
        $c->match('/sitemap', '\Eccube\Page\Sitemap')->bind('sitemap');
        $c->match('/error', '\Eccube\Page\Error\SystemError')->bind('error');
        $c->match('/resize_image', '\Eccube\Page\ResizeImage')->bind('resize_image');

        // api
        $c->match('/api', '\Eccube\Page\Api\Index')->bind('api');
        $c->match('/api/json', '\Eccube\Page\Api\Json')->bind('api_json');
        $c->match('/api/php', '\Eccube\Page\Api\Php')->bind('api_php');
        $c->match('/api/xml', '\Eccube\Page\Api\Xml')->bind('api_xml');

        // block
        $c->match('/block', '\Eccube\Controller\BlockController::index')->bind('block');

        // cart
        $c->match('/cart', '\Eccube\Controller\CartController::index')->bind('cart');
        $c->post('/cart/add', '\Eccube\Controller\CartController::add')->bind('cart_add');
        $c->match('/cart/up/{productClassId}', '\Eccube\Controller\CartController::up')->bind('cart_up')->assert('productClassId', '\d+');
        $c->match('/cart/down/{productClassId}', '\Eccube\Controller\CartController::down')->bind('cart_down')->assert('productClassId', '\d+');
        $c->match('/cart/setQuantity/{productClassId}/{quantity}', '\Eccube\Controller\CartController::setQuantity')->bind('cart_set_quantity')->assert('productClassId', '\d+')->assert('quantity', '\d+');
        $c->match('/cart/remove/{productClassId}', '\Eccube\Controller\CartController::remove')->bind('cart_remove')->assert('productClassId', '\d+');
        $c->match('/cart/buystep', '\Eccube\Controller\CartController::buystep')->bind('cart_buystep');

        // contact
        $c->match('/contact', '\Eccube\Controller\ContactController::index')->bind('contact');
        $c->match('/contact/complete', '\Eccube\Controller\ContactController::complete')->bind('contact_complete');

        // entry
        $c->match('/entry', '\Eccube\Controller\EntryController::index')->bind('entry');
        $c->match('/entry/complete', '\Eccube\Controller\EntryController::complete')->bind('entry_complete');
        $c->match('/entry/activate/{secret_key}', '\Eccube\Controller\EntryController::activate')->bind('entry_activate');

        // forgot
        $c->match('/forgot', '\Eccube\Controller\ForgotController::index')->bind('forgot');
        $c->match('/forgot/reset/{reset_key}', '\Eccube\Controller\ForgotController::reset')->bind('forgot_reset');

        // block
        $c->match('/block/category', '\Eccube\Controller\Block\CategoryController::index')->bind('block_category');
        $c->match('/block/cart', '\Eccube\Controller\Block\CartController::index')->bind('block_cart');
        $c->match('/block/search_product', '\Eccube\Controller\Block\SearchProductController::index')->bind('block_search_product');
        $c->match('/block/news', '\Eccube\Controller\Block\NewsController::index')->bind('block_news');
        $c->match('/block/login', '\Eccube\Controller\Block\LoginController::index')->bind('block_login');

        // 特定商取引 order -> help/traderaw
        $c->match('/help/about', '\Eccube\Controller\HelpController::about')->bind('help_about');
        $c->match('/help/guide', '\Eccube\Controller\HelpController::guide')->bind('help_guide');
        $c->match('/help/privacy', '\Eccube\Controller\HelpController::privacy')->bind('help_privacy');
        $c->match('/help/tradelaw', '\Eccube\Controller\HelpController::tradelaw')->bind('help_tradelaw');
        $c->match('/help/agreement', '\Eccube\Controller\HelpController::agreement')->bind('help_agreement');

        // mypage
        $c->match('/mypage', '\Eccube\Controller\Mypage\MypageController::index')->bind('mypage');
        $c->match('/mypage/login', '\Eccube\Controller\Mypage\MypageController::login')->bind('mypage_login');
        $c->match('/mypage/change', '\Eccube\Controller\Mypage\ChangeController::index')->bind('mypage_change');
        $c->match('/mypage/change_complete', '\Eccube\Controller\Mypage\ChangeController::complete')->bind('mypage_change_complete');

        $c->match('/mypage/delivery', '\Eccube\Controller\Mypage\DeliveryController::index')->bind('mypage_delivery');
        $c->match('/mypage/delivery/new', '\Eccube\Controller\Mypage\DeliveryController::edit')->bind('mypage_delivery_new');
        $c->match('/mypage/delivery/{id}/edit', '\Eccube\Controller\Mypage\DeliveryController::edit')->assert('id', '\d+')->bind('mypage_delivery_edit');
        $c->match('/mypage/delivery/{id}/delete', '\Eccube\Controller\Mypage\DeliveryController::delete')->assert('id', '\d+')->bind('mypage_delivery_delete');

        $c->match('/mypage/download', '\Eccube\Page\Mypage\Download')->bind('mypage_download');
        $c->match('/mypage/favorite', '\Eccube\Controller\Mypage\MypageController::favorite')->bind('mypage_favorite');
        $c->match('/mypage/history/{id}', '\Eccube\Controller\Mypage\MypageController::history')->bind('mypage_history')->assert('id', '\d+');
        $c->match('/mypage/mail_view/{id}', '\Eccube\Controller\Mypage\MypageController::mailView')->bind('mypage_mail_view')->assert('id', '\d+');
        $c->match('/mypage/order', '\Eccube\Controller\Mypage\MypageController::order')->bind('mypage_order');
        $c->match('/mypage/withdraw', '\Eccube\Controller\Mypage\WithdrawController::index')->bind('mypage_withdraw');
        $c->match('/mypage/withdraw_complete', '\Eccube\Controller\Mypage\WithdrawController::complete')->bind('mypage_withdraw_complete');

        // products
        $c->match('/products/list', '\Eccube\Controller\ProductController::index')->bind('product_list');
        $c->match('/products/detail/{id}', '\Eccube\Controller\ProductController::detail')->bind('product_detail')->assert('id', '\d+');
        $c->match('/products/search', '\Eccube\Page\Products\Search')->bind('product_search');
        $c->match('/products/category_list', '\Eccube\Page\Products\CategoryList')->bind('products_category_list');

        // regist
        $c->match('/regist', '\Eccube\Page\Regist\Index')->bind('regist');
        $c->match('/regist/complete', '\Eccube\Page\Regist\Complete')->bind('regist_complete');

        // rss
        $c->match('/rss', '\Eccube\Page\Rss\Index')->bind('rss');
        $c->match('/rss/product', '\Eccube\Page\Rss\Products')->bind('rss_product');
        $c->match('/rss/products', '\Eccube\Page\Rss\Products')->bind('rss_products');

        // shopping
        $c->match('/shopping', '\Eccube\Controller\ShoppingController::index')->bind('shopping');
        $c->match('/shopping/confirm', '\Eccube\Controller\ShoppingController::confirm')->bind('shopping_confirm');
        $c->match('/shopping/point', '\Eccube\Controller\ShoppingController::point')->bind('shopping_point');
        $c->match('/shopping/delivery', '\Eccube\Controller\ShoppingController::delivery')->bind('shopping_delivery');
        $c->match('/shopping/payment', '\Eccube\Controller\ShoppingController::payment')->bind('shopping_payment');
        $c->match('/shopping/shipping', '\Eccube\Controller\ShoppingController::shipping')->bind('shopping_shipping');
        $c->match('/shopping/shipping_multiple', '\Eccube\Controller\ShoppingController::shippingMultiple')->bind('shopping_shipping_multiple');
        $c->match('/shopping/shipping_edit', '\Eccube\Controller\ShoppingController::shippingEdit')->bind('shopping_shipping_edit');
        $c->match('/shopping/complete', '\Eccube\Controller\ShoppingController::complete')->bind('shopping_complete');
        $c->match('/shopping/login', '\Eccube\Controller\ShoppingController::login')->bind('shopping_login');
        $c->match('/shopping/nonmember', '\Eccube\Controller\ShoppingController::nonmember')->bind('shopping_nonmember');
        $c->match('/shopping/customer', '\Eccube\Controller\ShoppingController::customer')->bind('shopping_customer');
        $c->match('/shopping/shopping_error', '\Eccube\Controller\ShoppingController::shoppingError')->bind('shopping_error');
        $c->match('/shopping/test', '\Eccube\Controller\ShoppingController::test')->bind('shopping_test'); // todo テスト用

        // unsupported
        $c->match('/unsupported', '\Eccube\Page\Unsupported\Index')->bind('unsupported');

        return $c;
    }
}
