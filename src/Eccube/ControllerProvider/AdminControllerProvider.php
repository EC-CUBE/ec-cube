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

use Eccube\Entity\Master\CsvType;
use Silex\Api\ControllerProviderInterface;
use Silex\Application;

class AdminControllerProvider implements ControllerProviderInterface
{
    public function connect(Application $app)
    {
        $c = $app['controllers_factory'];

        // 強制SSL
        if ($app['config']['force_ssl'] == \Eccube\Common\Constant::ENABLED) {
            $c->requireHttps();
        }

        // product

        $c->match('/product/category', '\Eccube\Controller\Admin\Product\CategoryController::index')->bind('admin_product_category');
        $c->match('/product/category/export', '\Eccube\Controller\Admin\Product\CategoryController::export')->bind('admin_product_category_export');
        $c->match('/product/category/{parent_id}', '\Eccube\Controller\Admin\Product\CategoryController::index')->assert('parent_id', '\d+')->bind('admin_product_category_show');
        $c->match('/product/category/{id}/edit', '\Eccube\Controller\Admin\Product\CategoryController::index')->assert('id', '\d+')->bind('admin_product_category_edit');
        $c->delete('/product/category/{id}/delete', '\Eccube\Controller\Admin\Product\CategoryController::delete')->assert('id', '\d+')->bind('admin_product_category_delete');
        $c->post('/product/category/rank/move', '\Eccube\Controller\Admin\Product\CategoryController::moveRank')->bind('admin_product_category_rank_move');

        $c->match('/product/class_name', '\Eccube\Controller\Admin\Product\ClassNameController::index')->bind('admin_product_class_name');
        $c->match('/product/class_name/{id}/edit', '\Eccube\Controller\Admin\Product\ClassNameController::index')->assert('id', '\d+')->bind('admin_product_class_name_edit');
        $c->delete('/product/class_name/{id}/delete', '\Eccube\Controller\Admin\Product\ClassNameController::delete')->assert('id', '\d+')->bind('admin_product_class_name_delete');
        $c->post('/product/class_name/rank/move', '\Eccube\Controller\Admin\Product\ClassNameController::moveRank')->bind('admin_product_class_name_rank_move');

        $c->match('/product/class_category/{class_name_id}', '\Eccube\Controller\Admin\Product\ClassCategoryController::index')->assert('class_name_id', '\d+')->bind('admin_product_class_category');
        $c->match('/product/class_category/{class_name_id}/{id}/edit', '\Eccube\Controller\Admin\Product\ClassCategoryController::index')->assert('class_name_id', '\d+')->assert('id', '\d+')->bind('admin_product_class_category_edit');
        $c->delete('/product/class_category/{class_name_id}/{id}/delete', '\Eccube\Controller\Admin\Product\ClassCategoryController::delete')->assert('class_name_id', '\d+')->assert('id', '\d+')->bind('admin_product_class_category_delete');
        $c->post('/product/class_category/rank/move', '\Eccube\Controller\Admin\Product\ClassCategoryController::moveRank')->bind('admin_product_class_category_rank_move');

        $c->match('/product/product_csv_upload', '\Eccube\Controller\Admin\Product\CsvImportController::csvProduct')->bind('admin_product_csv_import');
        $c->match('/product/category_csv_upload', '\Eccube\Controller\Admin\Product\CsvImportController::csvCategory')->bind('admin_product_category_csv_import');
        $c->match('/product/csv_template/{type}', '\Eccube\Controller\Admin\Product\CsvImportController::csvTemplate')->bind('admin_product_csv_template');

        // customer
        $c->match('/customer', '\Eccube\Controller\Admin\Customer\CustomerController::index')->bind('admin_customer');
        $c->match('/customer/page/{page_no}', '\Eccube\Controller\Admin\Customer\CustomerController::index')->assert('page_no', '\d+')->bind('admin_customer_page');
        $c->match('/customer/export', '\Eccube\Controller\Admin\Customer\CustomerController::export')->bind('admin_customer_export');
        $c->match('/customer/new', '\Eccube\Controller\Admin\Customer\CustomerEditController::index')->bind('admin_customer_new');
        $c->match('/customer/{id}/edit', '\Eccube\Controller\Admin\Customer\CustomerEditController::index')->assert('id', '\d+')->bind('admin_customer_edit');
        $c->delete('/customer/{id}/delete', '\Eccube\Controller\Admin\Customer\CustomerController::delete')->assert('id', '\d+')->bind('admin_customer_delete');
        $c->put('/customer/{id}/resend', '\Eccube\Controller\Admin\Customer\CustomerController::resend')->assert('id', '\d+')->bind('admin_customer_resend');

        // order
        $c->match('/order', '\Eccube\Controller\Admin\Order\OrderController::index')->bind('admin_order');
        $c->match('/order/page/{page_no}', '\Eccube\Controller\Admin\Order\OrderController::index')->assert('page_no', '\d+')->bind('admin_order_page');
        //$c->match('/order/new', '\Eccube\Controller\Admin\Order\EditController::index')->bind('admin_order_new');
        //$c->match('/order/{id}/edit', '\Eccube\Controller\Admin\Order\EditController::index')->assert('id', '\d+')->bind('admin_order_edit');
        $c->delete('/order/{id}/delete', '\Eccube\Controller\Admin\Order\OrderController::delete')->assert('id', '\d+')->bind('admin_order_delete');
        $c->match('/order/export/order', '\Eccube\Controller\Admin\Order\OrderController::exportOrder')->bind('admin_order_export_order');
        $c->match('/order/export/shipping', '\Eccube\Controller\Admin\Order\OrderController::exportShipping')->bind('admin_order_export_shipping');
        $c->post('/order/search/customer', '\Eccube\Controller\Admin\Order\EditController::searchCustomer')->bind('admin_order_search_customer');
        $c->post('/order/search/customer/html', '\Eccube\Controller\Admin\Order\EditController::searchCustomerHtml')->bind('admin_order_search_customer_html');
        $c->match('/order/search/customer/html/page/{page_no}', '\Eccube\Controller\Admin\Order\EditController::searchCustomerHtml')->assert('page_no', '\d+')->bind('admin_order_search_customer_html_page');
        $c->post('/order/search/customer/id', '\Eccube\Controller\Admin\Order\EditController::searchCustomerById')->bind('admin_order_search_customer_by_id');
        $c->post('/order/search/product', '\Eccube\Controller\Admin\Order\EditController::searchProduct')->bind('admin_order_search_product');
        $c->match('/order/search/product/page/{page_no}', '\Eccube\Controller\Admin\Order\EditController::searchProduct')->assert('page_no', '\d+')->bind('admin_order_search_product_page');
        $c->match('/order/search/product/id', '\Eccube\Controller\Admin\Order\EditController::searchProductById')->bind('admin_order_search_product_by_id');

        $c->match('/order/{id}/mail', '\Eccube\Controller\Admin\Order\MailController::index')->assert('id', '\d+')->bind('admin_order_mail');
        $c->match('/order/mail/mail_all', '\Eccube\Controller\Admin\Order\MailController::mailAll')->bind('admin_order_mail_all');
        $c->match('/order/mail_complete', '\Eccube\Controller\Admin\Order\MailController::complete')->bind('admin_order_mail_complete');
        $c->match('/order/mail/view', '\Eccube\Controller\Admin\Order\MailController::view')->bind('admin_order_mail_view');

        // content
        // deprecated /content/ 3.1 delete. use /content/news
        $c->match('/content', '\Eccube\Controller\Admin\Content\ContentsController::index')->bind('admin_content');
        $c->match('/content/new', '\Eccube\Controller\Admin\Content\ContentsController::edit')->bind('admin_content_new');
        $c->match('/content/{id}/edit', '\Eccube\Controller\Admin\Content\ContentsController::edit')->assert('id', '\d+')->bind('admin_content_edit');
        $c->delete('/content/{id}/delete', '\Eccube\Controller\Admin\Content\ContentsController::delete')->assert('id', '\d+')->bind('admin_content_delete');
        $c->put('/content/{id}/up', '\Eccube\Controller\Admin\Content\ContentsController::up')->assert('id', '\d+')->bind('admin_content_up');
        $c->put('/content/{id}/down', '\Eccube\Controller\Admin\Content\ContentsController::down')->assert('id', '\d+')->bind('admin_content_down');

        $c->match('/content/news', '\Eccube\Controller\Admin\Content\NewsController::index')->bind('admin_content_news');
        $c->match('/content/news/new', '\Eccube\Controller\Admin\Content\NewsController::edit')->bind('admin_content_news_new');
        $c->match('/content/news/{id}/edit', '\Eccube\Controller\Admin\Content\NewsController::edit')->assert('id', '\d+')->bind('admin_content_news_edit');
        $c->delete('/content/news/{id}/delete', '\Eccube\Controller\Admin\Content\NewsController::delete')->assert('id', '\d+')->bind('admin_content_news_delete');
        $c->put('/content/news/{id}/up', '\Eccube\Controller\Admin\Content\NewsController::up')->assert('id', '\d+')->bind('admin_content_news_up');
        $c->put('/content/news/{id}/down', '\Eccube\Controller\Admin\Content\NewsController::down')->assert('id', '\d+')->bind('admin_content_news_down');

        $c->match('/content/file_manager', '\Eccube\Controller\Admin\Content\FileController::index')->bind('admin_content_file');
        $c->match('/content/file_view', '\Eccube\Controller\Admin\Content\FileController::view')->bind('admin_content_file_view');
        $c->match('/content/file_download', '\Eccube\Controller\Admin\Content\FileController::download')->bind('admin_content_file_download');
        $c->delete('/content/file_delete', '\Eccube\Controller\Admin\Content\FileController::delete')->bind('admin_content_file_delete');

        $c->match('/content/layout', '\Eccube\Controller\Admin\Content\LayoutController::index')->bind('admin_content_layout');
        $c->match('/content/layout/new', '\Eccube\Controller\Admin\Content\LayoutController::edit')->bind('admin_content_layout_new');
        $c->match('/content/layout/{id}/edit', '\Eccube\Controller\Admin\Content\LayoutController::edit')->assert('id', '\d+')->bind('admin_content_layout_edit');
        $c->match('/content/layout/{id}/preview', '\Eccube\Controller\Admin\Content\LayoutController::preview')->assert('id', '\d+')->bind('admin_content_layout_preview');
        $c->delete('/content/layout/{id}/delete', '\Eccube\Controller\Admin\Content\LayoutController::delete')->assert('id', '\d+')->bind('admin_content_layout_delete');
        $c->post('/content/layout/view_block', '\Eccube\Controller\Admin\Content\LayoutController::viewBlock')->bind('admin_content_layout_view_block');

        $c->match('/content/block', '\Eccube\Controller\Admin\Content\BlockController::index')->bind('admin_content_block');
        $c->match('/content/block/new', '\Eccube\Controller\Admin\Content\BlockController::edit')->bind('admin_content_block_new');
        $c->match('/content/block/{id}/edit', '\Eccube\Controller\Admin\Content\BlockController::edit')->assert('id', '\d+')->bind('admin_content_block_edit');
        $c->delete('/content/block/{id}/delete', '\Eccube\Controller\Admin\Content\BlockController::delete')->assert('id', '\d+')->bind('admin_content_block_delete');

        $c->match('/content/page', '\Eccube\Controller\Admin\Content\PageController::index')->bind('admin_content_page');
        $c->match('/content/page/new', '\Eccube\Controller\Admin\Content\PageController::edit')->bind('admin_content_page_new');
        $c->match('/content/page/{id}/edit', '\Eccube\Controller\Admin\Content\PageController::edit')->assert('id', '\d+')->bind('admin_content_page_edit');
        $c->delete('/content/page/{id}/delete', '\Eccube\Controller\Admin\Content\PageController::delete')->assert('id', '\d+')->bind('admin_content_page_delete');

        $c->match('/content/cache', '\Eccube\Controller\Admin\Content\CacheController::index')->bind('admin_content_cache');

        // shop
        $c->match('/setting/shop', '\Eccube\Controller\Admin\Setting\Shop\ShopController::index')->bind('admin_setting_shop');

        // delivery
        $c->match('/setting/shop/delivery', '\Eccube\Controller\Admin\Setting\Shop\DeliveryController::index')->bind('admin_setting_shop_delivery');
        $c->match('/setting/shop/delivery/new', '\Eccube\Controller\Admin\Setting\Shop\DeliveryController::edit')->bind('admin_setting_shop_delivery_new');
        $c->match('/setting/shop/delivery/{id}/edit', '\Eccube\Controller\Admin\Setting\Shop\DeliveryController::edit')->assert('id', '\d+')->bind('admin_setting_shop_delivery_edit');
        $c->delete('/setting/shop/delivery/{id}/delete', '\Eccube\Controller\Admin\Setting\Shop\DeliveryController::delete')->assert('id', '\d+')->bind('admin_setting_shop_delivery_delete');
        $c->post('/setting/shop/delivery/rank/move', '\Eccube\Controller\Admin\Setting\Shop\DeliveryController::moveRank')->bind('admin_setting_shop_delivery_rank_move');

        // payment
        $c->match('/setting/shop/payment', '\Eccube\Controller\Admin\Setting\Shop\PaymentController::index')->bind('admin_setting_shop_payment');
        $c->match('/setting/shop/payment/new', '\Eccube\Controller\Admin\Setting\Shop\PaymentController::edit')->bind('admin_setting_shop_payment_new');
        $c->match('/setting/shop/payment/image/add', '\Eccube\Controller\Admin\Setting\Shop\PaymentController::imageAdd')->bind('admin_payment_image_add');
        $c->match('/setting/shop/payment/{id}/edit', '\Eccube\Controller\Admin\Setting\Shop\PaymentController::edit')->assert('id', '\d+')->bind('admin_setting_shop_payment_edit');
        $c->delete('/setting/shop/payment/{id}/delete', '\Eccube\Controller\Admin\Setting\Shop\PaymentController::delete')->assert('id', '\d+')->bind('admin_setting_shop_payment_delete');
        $c->put('/setting/shop/payment/{id}/up', '\Eccube\Controller\Admin\Setting\Shop\PaymentController::up')->assert('id', '\d+')->bind('admin_setting_shop_payment_up');
        $c->put('/setting/shop/payment/{id}/down', '\Eccube\Controller\Admin\Setting\Shop\PaymentController::down')->assert('id', '\d+')->bind('admin_setting_shop_payment_down');

        // tradelaw
        $c->match('/setting/shop/tradelaw', '\Eccube\Controller\Admin\Setting\Shop\TradelawController::index')->bind('admin_setting_shop_tradelaw');

        // tax
        $c->match('/setting/shop/tax', '\Eccube\Controller\Admin\Setting\Shop\TaxRuleController::index')->bind('admin_setting_shop_tax');
        $c->match('/setting/shop/tax/new', '\Eccube\Controller\Admin\Setting\Shop\TaxRuleController::index')->assert('id', '\d+')->bind('admin_setting_shop_tax_new');
        $c->match('/setting/shop/tax/{id}/edit', '\Eccube\Controller\Admin\Setting\Shop\TaxRuleController::index')->assert('id', '\d+')->bind('admin_setting_shop_tax_edit');
        $c->delete('/setting/shop/tax/{id}/delete', '\Eccube\Controller\Admin\Setting\Shop\TaxRuleController::delete')->assert('id', '\d+')->bind('admin_setting_shop_tax_delete');
        $c->match('/setting/shop/tax/edit_param', '\Eccube\Controller\Admin\Setting\Shop\TaxRuleController::editParameter')->assert('id', '\d+')->bind('admin_setting_shop_tax_edit_param');

        // mail
        $c->match('/setting/shop/mail', '\Eccube\Controller\Admin\Setting\Shop\MailController::index')->bind('admin_setting_shop_mail');
        $c->match('/setting/shop/mail/{id}', '\Eccube\Controller\Admin\Setting\Shop\MailController::index')->assert('id', '\d+')->bind('admin_setting_shop_mail_edit');

        // customer_agreement
        $c->match('/setting/shop/customer_agreement', '\Eccube\Controller\Admin\Setting\Shop\CustomerAgreementController::index')->bind('admin_setting_shop_customer_agreement');

        // csv
        $c->match('/setting/shop/csv/{id}', '\Eccube\Controller\Admin\Setting\Shop\CsvController::index')->assert('id', '\d+')->value('id', CsvType::CSV_TYPE_ORDER)->bind('admin_setting_shop_csv');

        // setting/system
        $c->match('/setting/system/system', '\Eccube\Controller\Admin\Setting\System\SystemController::index')->bind('admin_setting_system_system');
        // system/member
        $c->match('/setting/system/member', '\Eccube\Controller\Admin\Setting\System\MemberController::index')->bind('admin_setting_system_member');
        $c->match('/setting/system/member/new', '\Eccube\Controller\Admin\Setting\System\MemberController::edit')->bind('admin_setting_system_member_new');
        $c->match('/setting/system/member/{id}/edit', 'Eccube\Controller\Admin\Setting\System\MemberController::edit')->assert('id', '\d+')->bind('admin_setting_system_member_edit');
        $c->delete('/setting/system/member/{id}/delete', '\Eccube\Controller\Admin\Setting\System\MemberController::delete')->assert('id', '\d+')->bind('admin_setting_system_member_delete');
        $c->put('/setting/system/member/{id}/up', '\Eccube\Controller\Admin\Setting\System\MemberController::up')->assert('id', '\d+')->bind('admin_setting_system_member_up');
        $c->put('/setting/system/member/{id}/down', '\Eccube\Controller\Admin\Setting\System\MemberController::down')->assert('id', '\d+')->bind('admin_setting_system_member_down');
        // system/authority
        $c->match('/setting/system/authority', '\Eccube\Controller\Admin\Setting\System\AuthorityController::index')->bind('admin_setting_system_authority');
        // system/security
        $c->match('/setting/system/security', '\Eccube\Controller\Admin\Setting\System\SecurityController::index')->bind('admin_setting_system_security');
        // system/log
        $c->match('/setting/system/log', '\Eccube\Controller\Admin\Setting\System\LogController::index')->bind('admin_setting_system_log');

        // system/masterdata
        $c->match('/setting/system/masterdata', '\Eccube\Controller\Admin\Setting\System\MasterdataController::index')->bind('admin_setting_system_masterdata');
        $c->match('/setting/system/masterdata/{entity}/edit', '\Eccube\Controller\Admin\Setting\System\MasterdataController::index')->bind('admin_setting_system_masterdata_view');
        $c->match('/setting/system/masterdata/edit', '\Eccube\Controller\Admin\Setting\System\MasterdataController::edit')->bind('admin_setting_system_masterdata_edit');

        // store
        $c->match('/store/template', '\Eccube\Controller\Admin\Store\TemplateController::index')->bind('admin_store_template');
        $c->match('/store/template/install', '\Eccube\Controller\Admin\Store\TemplateController::add')->bind('admin_store_template_install');
        $c->match('/store/template/{id}/download', '\Eccube\Controller\Admin\Store\TemplateController::download')->assert('id', '\d+')->bind('admin_store_template_download');
        $c->delete('/store/template/{id}/delete', '\Eccube\Controller\Admin\Store\TemplateController::delete')->assert('id', '\d+')->bind('admin_store_template_delete');
        $c->match('/store/plugin', '\Eccube\Controller\Admin\Store\PluginController::index')->bind('admin_store_plugin');
        $c->match('/store/plugin/owners_install', '\Eccube\Controller\Admin\Store\PluginController::ownersInstall')->bind('admin_store_plugin_owners_install');
        $c->match('/store/plugin/install', '\Eccube\Controller\Admin\Store\PluginController::install')->bind('admin_store_plugin_install');
        $c->match('/store/plugin/upgrade/{action}/{id}/{version}', '\Eccube\Controller\Admin\Store\PluginController::upgrade')->assert('id', '\d+')->bind('admin_store_plugin_upgrade');
        $c->match('/store/plugin/handler', '\Eccube\Controller\Admin\Store\PluginController::handler')->bind('admin_store_plugin_handler');
        $c->match('/store/plugin/manage', '\Eccube\Controller\Admin\Store\PluginController::manage')->bind('admin_store_plugin_manage');
        $c->put('/store/plugin/{id}/enable', '\Eccube\Controller\Admin\Store\PluginController::enable')->assert('id', '\d+')->bind('admin_store_plugin_enable');
        $c->put('/store/plugin/{id}/disable', '\Eccube\Controller\Admin\Store\PluginController::disable')->assert('id', '\d+')->bind('admin_store_plugin_disable');
        $c->post('/store/plugin/{id}/update', '\Eccube\Controller\Admin\Store\PluginController::update')->assert('id', '\d+')->bind('admin_store_plugin_update');
        $c->delete('/store/plugin/{id}/uninstall', '\Eccube\Controller\Admin\Store\PluginController::uninstall')->assert('id', '\d+')->bind('admin_store_plugin_uninstall');
        $c->match('/store/plugin/handler_up/{handlerId}', '\Eccube\Controller\Admin\Store\PluginController::handler_up')->bind('admin_store_plugin_handler_up');
        $c->match('/store/plugin/handler_down/{handlerId}', '\Eccube\Controller\Admin\Store\PluginController::handler_down')->bind('admin_store_plugin_handler_down');
        $c->match('/store/plugin/authentication_setting', '\Eccube\Controller\Admin\Store\PluginController::authenticationSetting')->bind('admin_store_authentication_setting');

        return $c;
    }
}
