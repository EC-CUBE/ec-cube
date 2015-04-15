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
        $controllers->match('/', "\\Eccube\\Page\\Index")->bind('index');
        $controllers->match('/', "\\Eccube\\Page\\Index")->bind('homepage');
        $controllers->match('/input_zip.php', '\\Eccube\\Page\\InputZip')->bind('input_zip');
        $controllers->match('/sitemap.php', '\\Eccube\\Page\\Sitemap')->bind('sitemap');
        $controllers->match('/error.php', '\\Eccube\\Page\\Error\\SystemError')->bind('error');
        $controllers->match('/resize_image.php', '\\Eccube\\Page\\ResizeImage')->bind('resize_image');

        // api
        $controllers->match('/api/', '\\Eccube\\Page\\Api\\Index')->bind('api');
        $controllers->match('/api/json.php', '\\Eccube\\Page\\Api\\Json')->bind('api_json');
        $controllers->match('/api/php.php', '\\Eccube\\Page\\Api\\Php')->bind('api_php');
        $controllers->match('/api/xml.php', '\\Eccube\\Page\\Api\\Xml')->bind('api_xml');

        // abouts
        $controllers->match('/abouts/', '\\Eccube\\Page\\Abouts\\Index')->bind('abouts');

        // cart
        $controllers->match('/cart/', '\\Eccube\\Page\\Cart\\Index')->bind('cart');

        // contact
        // $controllers->match('/contact/', '\\Eccube\\Page\\Contact\\Index')->bind('contact');
        // $controllers->match('/contact/complete.php', '\\Eccube\\Page\\Contact\\Complete')->bind('contact_complete');
        $controllers->match('/contact/', '\\Eccube\\Controller\\ContactController::index')->bind('contact');
        $controllers->match('/contact/complete.php', '\\Eccube\\Controller\\ContactController::complete')->bind('contact_complete');


        // entry
        $controllers->match('/entry/', '\\Eccube\\Controller\\EntryController::index')->bind('entry');
        $controllers->match('/entry/kiyaku.php', '\\Eccube\\Controller\\EntryController::kiyaku')->bind('entry_kiyaku');
        $controllers->match('/entry/confirm.php', '\\Eccube\\Controller\\EntryController::confirm')->bind('entry_confirm');
        $controllers->match('/entry/complete.php', '\\Eccube\\Controller\\EntryController::complete')->bind('entry_complete');

        // forgot
        $controllers->match('/forgot/', '\\Eccube\\Page\\Forgot\\Index')->bind('forgot');

        // frontparts
        $controllers->match('/frontparts/login_check.php', '\\Eccube\\Page\\FrontParts\\LoginCheck')->bind('frontparts_login_check');
        $controllers->match('/bloc/', '\\Eccube\\Controller\\BlocController::index')->bind('bloc');

        // bloc
        $controllers->match('/frontparts/bloc/category.php', '\\Eccube\\Controller\\Bloc\\CategoryController::index')->bind('bloc_category');
        $controllers->match('/frontparts/bloc/cart.php', '\\Eccube\\Controller\\Bloc\\CartController::index')->bind('bloc_cart');
        $controllers->match('/frontparts/bloc/search_product.php', '\\Eccube\\Controller\\Bloc\\SearchProductController::index')->bind('bloc_search_product');
        $controllers->match('/frontparts/bloc/news.php', '\\Eccube\\Controller\\Bloc\\NewsController::index')->bind('bloc_news');
        $controllers->match('/frontparts/bloc/login.php', '\\Eccube\\Controller\\Bloc\\LoginController::index')->bind('bloc_login');
        $controllers->match('/frontparts/bloc/recommend.php', '\\Eccube\\Controller\\Bloc\\RecommendController::index')->bind('bloc_recommend');
        $controllers->match('/frontparts/bloc/calendar.php', '\\Eccube\\Controller\\Bloc\\CalendarController::index')->bind('bloc_calendar');
        $controllers->match('/frontparts/bloc/login_header.php', '\\Eccube\\Controller\\Bloc\\LoginHeaderController::index')->bind('bloc_login_header');

        // guide
        $controllers->match('/guide/', '\\Eccube\\Page\\Guide\\Index')->bind('guide');
        $controllers->match('/guide/about.php', '\\Eccube\\Page\\Guide\\About')->bind('guide_about');
        $controllers->match('/guide/charge.php', '\\Eccube\\Page\\Guide\\Charge')->bind('guide_charge');
        $controllers->match('/guide/kiyaku.php', '\\Eccube\\Page\\Guide\\Kiyaku')->bind('guide_kiyaku');
        $controllers->match('/guide/privacy.php', '\\Eccube\\Page\\Guide\\Privacy')->bind('guide_privacy');
        $controllers->match('/guide/usage.php', '\\Eccube\\Page\\Guide\\Usage')->bind('guide_usage');

        // mypage
        $controllers->match('/mypage/', '\\Eccube\\Controller\\MypageController::index')->bind('mypage');
        $controllers->match('/mypage/login.php', '\\Eccube\\Controller\\MypageController::login')->bind('mypage_login');
        $controllers->match('/mypage/change.php', '\\Eccube\\Page\\Mypage\\Change')->bind('mypage_change');
        $controllers->match('/mypage/change_complete.php', '\\Eccube\\Page\\Mypage\\ChangeComplete')->bind('mypage_change_complete');
        $controllers->match('/mypage/delivery.php', '\\Eccube\\Page\\Mypage\\Delivery')->bind('mypage_delivery');
        $controllers->match('/mypage/delivery_addr.php', '\\Eccube\\Page\\Mypage\\DeliveryAddr')->bind('mypage_delivery_addr');
        $controllers->match('/mypage/download.php', '\\Eccube\\Page\\Mypage\\Download')->bind('mypage_download');
        $controllers->match('/mypage/favorite.php', '\\Eccube\\Page\\Mypage\\Favorite')->bind('mypage_favorite');
        $controllers->match('/mypage/history.php', '\\Eccube\\Page\\Mypage\\History')->bind('mypage_history');
        $controllers->match('/mypage/mail_view.php', '\\Eccube\\Page\\Mypage\\MailView')->bind('mypage_mail_view');
        $controllers->match('/mypage/order.php', '\\Eccube\\Page\\Mypage\\Order')->bind('mypage_order');
        $controllers->match('/mypage/refusal.php', '\\Eccube\\Page\\Mypage\\Refusal')->bind('mypage_refusal');
        $controllers->match('/mypage/refusal_complete.php', '\\Eccube\\Page\\Mypage\\RefusalComplete')->bind('mypage_refusal_complete');

        // 特定商取引 order -> help/traderaw
        $controllers->match('/help/tradelaw/', '\\Eccube\\Controller\\HelpController::tradelaw')->bind('help_tradelaw');
        $controllers->match('/help/guide/', '\\Eccube\\Controller\\HelpController::guide')->bind('help_guide');
        $controllers->match('/help/about/', '\\Eccube\\Controller\\HelpController::about')->bind('help_about');
        $controllers->match('/help/privacy/', '\\Eccube\\Controller\\HelpController::privacy')->bind('help_privacy');

        // preview
        $controllers->match('/preview/', '\\Eccube\\Page\\Preview\\Index')->bind('preview');

        // products
        $controllers->match('/products/list.php', '\\Eccube\\Page\\Products\\ProductsList')->bind('products_list');
        $controllers->match('/products/seaech.php', '\\Eccube\\Page\\Products\\Search')->bind('products_seaech');
        $controllers->match('/products/category_list.php', '\\Eccube\\Page\\Products\\CategoryList')->bind('products_category_list');
        $controllers->match('/products/detail.php', '\\Eccube\\Page\\Products\\Detail')->bind('products_detail');

        // regist
        $controllers->match('/regist/', '\\Eccube\\Page\\Regist\\Index')->bind('regist');
        $controllers->match('/regist/complete.php', '\\Eccube\\Page\\Regist\\Complete')->bind('regist_complete');

        // rss
        $controllers->match('/rss/', '\\Eccube\\Page\\Rss\\Index')->bind('rss');
        $controllers->match('/rss/product.php', '\\Eccube\\Page\\Rss\\Products')->bind('rss_product');
        $controllers->match('/rss/products.php', '\\Eccube\\Page\\Rss\\Products')->bind('rss_products');

        // shopping
        $controllers->match('/shopping/', '\\Eccube\\Page\\Shopping\\Index')->bind('shopping');
        $controllers->match('/shopping/deliv.php', '\\Eccube\\Page\\Shopping\\Deliv')->bind('shopping_deliv');
        $controllers->match('/shopping/multiple.php', '\\Eccube\\Page\\Shopping\\Multiple')->bind('shopping_multiple');
        $controllers->match('/shopping/payment.php', '\\Eccube\\Page\\Shopping\\Payment')->bind('shopping_payment');
        $controllers->match('/shopping/confirm.php', '\\Eccube\\Page\\Shopping\\Confirm')->bind('shopping_confirm');
        $controllers->match('/shopping/load_payment_module.php', '\\Eccube\\Page\\Shopping\\LoadPaymentModule')->bind('shopping_load_payment_module');
        $controllers->match('/shopping/complete.php', '\\Eccube\\Page\\Shopping\\Complete')->bind('shopping_complete');

        // order
        $controllers->match('/unsupported/', '\\Eccube\\Page\\Unsupported\\Index')->bind('unsupported');

        return $controllers;
    }
}
