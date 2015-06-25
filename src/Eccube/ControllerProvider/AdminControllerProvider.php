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

class AdminControllerProvider implements ControllerProviderInterface
{
    public function connect(Application $app)
    {
        $c = $app['controllers_factory'];

        // 強制SSL
        if ($app['config']['force_ssl'] == \Eccube\Common\Constant::ENABLED) {
            $c->requireHttps();
        }

        // root
        $c->match('/', '\Eccube\Controller\Admin\AdminController::index')->bind('admin_homepage');

        // login
        $c->match('/login', '\Eccube\Controller\Admin\AdminController::login')->bind('admin_login');


        // product
        $c->match('/product', '\Eccube\Controller\Admin\Product\ProductController::index')->bind('admin_product');
        $c->match('/product/page/{page_no}', '\Eccube\Controller\Admin\Product\ProductController::index')->assert('page_no', '\d+')->bind('admin_product_page');
        $c->match('/product/product/new', '\Eccube\Controller\Admin\Product\ProductController::edit')->bind('admin_product_product_new');
        $c->match('/product/product/{id}/edit', '\Eccube\Controller\Admin\Product\ProductController::edit')->assert('id', '\d+')->bind('admin_product_product_edit');
        $c->match('/product/product/class/{id}', '\Eccube\Controller\Admin\Product\ProductClassController::index')->assert('id', '\d+')->bind('admin_product_product_class');
        $c->post('/product/product/class/edit/{id}', '\Eccube\Controller\Admin\Product\ProductClassController::edit')->assert('id', '\d+')->bind('admin_product_product_class_edit');
        $c->post('/product/product/image/add', '\Eccube\Controller\Admin\Product\ProductController::addImage')->bind('admin_product_image_add');

        $c->match('/product/category', '\Eccube\Controller\Admin\Product\CategoryController::index')->bind('admin_product_category');
        $c->match('/product/category/{parent_id}', '\Eccube\Controller\Admin\Product\CategoryController::index')->assert('parent_id', '\d+')->bind('admin_product_category_show');
        $c->match('/product/category/{id}/edit', '\Eccube\Controller\Admin\Product\CategoryController::index')->assert('id', '\d+')->bind('admin_product_category_edit');
        $c->post('/product/category/{id}/up', '\Eccube\Controller\Admin\Product\CategoryController::up')->assert('id', '\d+')->bind('admin_product_category_up');
        $c->post('/product/category/{id}/down', '\Eccube\Controller\Admin\Product\CategoryController::down')->assert('id', '\d+')->bind('admin_product_category_down');
        $c->post('/product/category/{id}/delete', '\Eccube\Controller\Admin\Product\CategoryController::delete')->assert('id', '\d+')->bind('admin_product_category_delete');

        $c->match('/product/class_name', '\Eccube\Controller\Admin\Product\ClassNameController::index')->bind('admin_product_class_name');
        $c->match('/product/class_name/{id}/edit', '\Eccube\Controller\Admin\Product\ClassNameController::index')->assert('id', '\d+')->bind('admin_product_class_name_edit');
        $c->post('/product/class_name/{id}/up', '\Eccube\Controller\Admin\Product\ClassNameController::up')->assert('id', '\d+')->bind('admin_product_class_name_up');
        $c->post('/product/class_name/{id}/down', '\Eccube\Controller\Admin\Product\ClassNameController::down')->assert('id', '\d+')->bind('admin_product_class_name_down');
        $c->post('/product/class_name/{id}/delete', '\Eccube\Controller\Admin\Product\ClassNameController::delete')->assert('id', '\d+')->bind('admin_product_class_name_delete');

        $c->match('/product/class_category/{class_name_id}', '\Eccube\Controller\Admin\Product\ClassCategoryController::index')->assert('class_name_id', '\d+')->bind('admin_product_class_category');
        $c->match('/product/class_category/{class_name_id}/{id}/edit', '\Eccube\Controller\Admin\Product\ClassCategoryController::index')->assert('class_name_id', '\d+')->assert('id', '\d+')->bind('admin_product_class_category_edit');
        $c->post('/product/class_category/{class_name_id}/{id}/up', '\Eccube\Controller\Admin\Product\ClassCategoryController::up')->assert('class_name_id', '\d+')->assert('id', '\d+')->bind('admin_product_class_category_up');
        $c->post('/product/class_category/{class_name_id}/{id}/down', '\Eccube\Controller\Admin\Product\ClassCategoryController::down')->assert('class_name_id', '\d+')->assert('id', '\d+')->bind('admin_product_class_category_down');
        $c->post('/product/class_category/{class_name_id}/{id}/delete', '\Eccube\Controller\Admin\Product\ClassCategoryController::delete')->assert('class_name_id', '\d+')->assert('id', '\d+')->bind('admin_product_class_category_delete');

        $c->match('/product/maker', '\Eccube\Controller\Admin\Product\MakerController::index')->bind('admin_product_maker');
        $c->match('/product/maker/{id}/edit', '\Eccube\Controller\Admin\Product\MakerController::index')->assert('id', '\d+')->bind('admin_product_maker_edit');
        $c->post('/product/maker/{id}/up', '\Eccube\Controller\Admin\Product\MakerController::up')->assert('id', '\d+')->bind('admin_product_maker_up');
        $c->post('/product/maker/{id}/down', '\Eccube\Controller\Admin\Product\MakerController::down')->assert('id', '\d+')->bind('admin_product_maker_down');
        $c->post('/product/maker/{id}/delete', '\Eccube\Controller\Admin\Product\MakerController::delete')->assert('id', '\d+')->bind('admin_product_maker_delete');

        // customer
        $c->match('/customer', '\Eccube\Controller\Admin\Customer\CustomerController::index')->bind('admin_customer');
        $c->match('/customer/page/{page_no}', '\Eccube\Controller\Admin\Customer\CustomerController::index')->assert('page_no', '\d+')->bind('admin_customer_page');
        $c->match('/customer/new', '\Eccube\Controller\Admin\Customer\CustomerEditController::index')->bind('admin_customer_new');
        $c->match('/customer/{id}/edit', '\Eccube\Controller\Admin\Customer\CustomerEditController::index')->assert('id', '\d+')->bind('admin_customer_edit');
        $c->post('/customer/{id}/delete', '\Eccube\Controller\Admin\Customer\CustomerController::delete')->assert('id', '\d+')->bind('admin_customer_delete');
        $c->post('/customer/{id}/resend', '\Eccube\Controller\Admin\Customer\CustomerController::resend')->assert('id', '\d+')->bind('admin_customer_resend');



        // order
        $c->match('/order', '\Eccube\Controller\Admin\Order\OrderController::index')->bind('admin_order');
        $c->match('/order/page/{page_no}', '\Eccube\Controller\Admin\Order\OrderController::index')->assert('page_no', '\d+')->bind('admin_order_page');
        $c->match('/order/new', '\Eccube\Controller\Admin\Order\EditController::index')->bind('admin_order_new');
        $c->match('/order/{id}/edit', '\Eccube\Controller\Admin\Order\EditController::index')->assert('id', '\d+')->bind('admin_order_edit');
        $c->match('/order/{id}/delete', '\Eccube\Controller\Admin\Order\OrderController::delete')->assert('id', '\d+')->bind('admin_order_delete');
        $c->post('/order/search/customer', '\Eccube\Controller\Admin\Order\EditController::searchCustomer')->bind('admin_order_search_customer');
        $c->post('/order/search/customer/id', '\Eccube\Controller\Admin\Order\EditController::searchCustomerById')->bind('admin_order_search_customer_by_id');
        $c->post('/order/search/product', '\Eccube\Controller\Admin\Order\EditController::searchProduct')->bind('admin_order_search_product');
        $c->match('/order/search/product/id', '\Eccube\Controller\Admin\Order\EditController::searchProductById')->bind('admin_order_search_product_by_id');

        $c->match('/order/{id}/mail', '\Eccube\Controller\Admin\Order\MailController::index')->assert('id', '\d+')->bind('admin_order_mail');
        $c->match('/order/mail/mail_all', '\Eccube\Controller\Admin\Order\MailController::mailAll')->bind('admin_order_mail_all');
        $c->match('/order/mail_complete', '\Eccube\Controller\Admin\Order\MailController::complete')->bind('admin_order_mail_complete');
        $c->match('/order/mail/view', '\Eccube\Controller\Admin\Order\MailController::view')->bind('admin_order_mail_view');

        $c->match('/order/status', '\Eccube\Controller\Admin\Order\StatusController::index')->bind('admin_order_status_default');
        $c->match('/order/status/{statusId}', '\Eccube\Controller\Admin\Order\StatusController::index')->bind('admin_order_status');

        // content
        $c->match('/content/', '\Eccube\Controller\Admin\Content\ContentsController::index')->bind('admin_content');
        $c->match('/content/new', '\Eccube\Controller\Admin\Content\ContentsController::edit')->bind('admin_content_new');
        $c->match('/content/{id}/edit', '\Eccube\Controller\Admin\Content\ContentsController::edit')->assert('id', '\d+')->bind('admin_content_edit');
        $c->match('/content/{id}/delete', '\Eccube\Controller\Admin\Content\ContentsController::delete')->assert('id', '\d+')->bind('admin_content_delete');
        $c->match('/content/{id}/up', '\Eccube\Controller\Admin\Content\ContentsController::up')->assert('id', '\d+')->bind('admin_content_up');
        $c->match('/content/{id}/down', '\Eccube\Controller\Admin\Content\ContentsController::down')->assert('id', '\d+')->bind('admin_content_down');

        $c->match('/content/file_manager', '\Eccube\Controller\Admin\Content\FileController::index')->bind('admin_content_file');
        $c->match('/content/file_view', '\Eccube\Controller\Admin\Content\FileController::view')->bind('admin_content_file_view');
        $c->match('/content/css', '\Eccube\Controller\Admin\Content\CssController::index')->bind('admin_content_css');
        $c->match('/content/css/delete', '\Eccube\Controller\Admin\Content\CssController::delete')->bind('admin_content_css_delete');

        $c->match('/content/layout', '\Eccube\Controller\Admin\Content\LayoutController::index')->bind('admin_content_layout');
        $c->match('/content/layout/{id}/edit', '\Eccube\Controller\Admin\Content\LayoutController::index')->assert('id', '\d+')->bind('admin_content_layout_edit');
        $c->match('/content/layout/{id}/preview', '\Eccube\Controller\Admin\Content\LayoutController::preview')->assert('id', '\d+')->bind('admin_content_layout_preview');

        $c->match('/content/block', '\Eccube\Controller\Admin\Content\BlockController::index')->bind('admin_content_block');
        $c->match('/content/block/new', '\Eccube\Controller\Admin\Content\BlockController::edit')->bind('admin_content_block_new');
        $c->match('/content/block/{id}/edit', '\Eccube\Controller\Admin\Content\BlockController::edit')->assert('id', '\d+')->bind('admin_content_block_edit');
        $c->match('/content/block/{id}/delete', '\Eccube\Controller\Admin\Content\BlockController::delete')->assert('id', '\d+')->bind('admin_content_block_delete');

        $c->match('/content/page', '\Eccube\Controller\Admin\Content\PageController::index')->bind('admin_content_page');
        $c->match('/content/page/new', '\Eccube\Controller\Admin\Content\PageController::edit')->bind('admin_content_page_new');
        $c->match('/content/page/{id}/edit', '\Eccube\Controller\Admin\Content\PageController::edit')->assert('id', '\d+')->bind('admin_content_page_edit');
        $c->post('/content/page/{id}/delete', '\Eccube\Controller\Admin\Content\PageController::delete')->assert('id', '\d+')->bind('admin_content_page_delete');

        $c->match('/content/template', '\Eccube\Controller\Admin\Content\TemplateController::index')->bind('admin_content_template');
        $c->match('/content/template/new', '\Eccube\Controller\Admin\Content\TemplateController::add')->bind('admin_content_template_new');
        $c->match('/content/template/{id}/download', '\Eccube\Controller\Admin\Content\TemplateController::download')->assert('id', '\d+')->bind('admin_content_template_download');
        $c->post('/content/template/{id}/delete', '\Eccube\Controller\Admin\Content\TemplateController::delete')->assert('id', '\d+')->bind('admin_content_template_delete');

        // shop
        $c->match('/setting/shop', '\Eccube\Controller\Admin\Setting\Shop\ShopController::index')->bind('admin_setting_shop');

        // delivery
        $c->match('/setting/shop/delivery', '\Eccube\Controller\Admin\Setting\Shop\DeliveryController::index')->bind('admin_setting_shop_delivery');
        $c->match('/setting/shop/delivery/new', '\Eccube\Controller\Admin\Setting\Shop\DeliveryController::edit')->bind('admin_setting_shop_delivery_new');
        $c->match('/setting/shop/delivery/{id}/edit', '\Eccube\Controller\Admin\Setting\Shop\DeliveryController::edit')->assert('id', '\d+')->bind('admin_setting_shop_delivery_edit');
        $c->match('/setting/shop/delivery/{id}/delete', '\Eccube\Controller\Admin\Setting\Shop\DeliveryController::delete')->assert('id', '\d+')->bind('admin_setting_shop_delivery_delete');
        $c->match('/setting/shop/delivery/{id}/up', '\Eccube\Controller\Admin\Setting\Shop\DeliveryController::up')->assert('id', '\d+')->bind('admin_setting_shop_delivery_up');
        $c->match('/setting/shop/delivery/{id}/down', '\Eccube\Controller\Admin\Setting\Shop\DeliveryController::down')->assert('id', '\d+')->bind('admin_setting_shop_delivery_down');

        // payment
        $c->match('/setting/shop/payment', '\Eccube\Controller\Admin\Setting\Shop\PaymentController::index')->bind('admin_setting_shop_payment');
        $c->match('/setting/shop/payment/new', '\Eccube\Controller\Admin\Setting\Shop\PaymentController::edit')->bind('admin_setting_shop_payment_new');
        $c->match('/setting/shop/payment/{id}/edit', '\Eccube\Controller\Admin\Setting\Shop\PaymentController::edit')->assert('id', '\d+')->bind('admin_setting_shop_payment_edit');
        $c->post('/setting/shop/payment/{id}/delete', '\Eccube\Controller\Admin\Setting\Shop\PaymentController::delete')->assert('id', '\d+')->bind('admin_setting_shop_payment_delete');
        $c->match('/setting/shop/payment/{id}/image/delete', '\Eccube\Controller\Admin\Setting\Shop\PaymentController::deleteImage')->assert('id', '\d+')->bind('admin_setting_shop_payment_delete_image');
        $c->match('/setting/shop/payment/{id}/up', '\Eccube\Controller\Admin\Setting\Shop\PaymentController::up')->assert('id', '\d+')->bind('admin_setting_shop_payment_up');
        $c->match('/setting/shop/payment/{id}/down', '\Eccube\Controller\Admin\Setting\Shop\PaymentController::down')->assert('id', '\d+')->bind('admin_setting_shop_payment_down');

        // tradelaw
        $c->match('/setting/shop/tradelaw', '\Eccube\Controller\Admin\Setting\Shop\TradelawController::index')->bind('admin_setting_shop_tradelaw');

        // shop
        $c->match('/setting/shop/point', '\Eccube\Controller\Admin\Setting\Shop\PointController::index')->bind('admin_setting_shop_point');

        // tax
        $c->match('/setting/shop/tax', '\Eccube\Controller\Admin\Setting\Shop\TaxRuleController::index')->bind('admin_setting_shop_tax');
        $c->match('/setting/shop/tax/new', '\Eccube\Controller\Admin\Setting\Shop\TaxRuleController::index')->assert('id', '\d+')->bind('admin_setting_shop_tax_new');
        $c->match('/setting/shop/tax/{id}/edit', '\Eccube\Controller\Admin\Setting\Shop\TaxRuleController::index')->assert('id', '\d+')->bind('admin_setting_shop_tax_edit');
        $c->match('/setting/shop/tax/{id}/delete', '\Eccube\Controller\Admin\Setting\Shop\TaxRuleController::delete')->assert('id', '\d+')->bind('admin_setting_shop_tax_delete');
        $c->match('/setting/shop/tax/edit_param', '\Eccube\Controller\Admin\Setting\Shop\TaxRuleController::editParameter')->assert('id', '\d+')->bind('admin_setting_shop_tax_edit_param');

        // mail
        $c->match('/setting/shop/mail', '\Eccube\Controller\Admin\Setting\Shop\MailController::index')->bind('admin_setting_shop_mail');
        $c->match('/setting/shop/mail/{id}', '\Eccube\Controller\Admin\Setting\Shop\MailController::index')->assert('id', '\d+')->bind('admin_setting_shop_mail_edit');

        // customer_agreement
        $c->match('/setting/shop/customer_agreement/', '\Eccube\Controller\Admin\Setting\Shop\CustomerAgreementController::index')->bind('admin_setting_shop_customer_agreement');

        // system/system
        $c->match('/setting/system/system', '\Eccube\Controller\Admin\Setting\System\SystemController::index')->bind('admin_setting_system_system');

        // system/plugin
        $c->match('/setting/system/plugin', '\Eccube\Controller\Admin\Setting\System\PluginController::index')->bind('admin_setting_system_plugin_index');
        $c->match('/setting/system/plugin/install', '\Eccube\Controller\Admin\Setting\System\PluginController::install')->bind('admin_setting_system_plugin_install');
        $c->match('/setting/system/plugin/handler', '\Eccube\Controller\Admin\Setting\System\PluginController::handler')->bind('admin_setting_system_plugin_handler');
        $c->match('/setting/system/plugin/manage', '\Eccube\Controller\Admin\Setting\System\PluginController::manage')->bind('admin_setting_system_plugin_manage');

        $c->match('/setting/system/plugin/handler_up/{handlerId}', '\Eccube\Controller\Admin\Setting\System\PluginController::handler_up')->bind('admin_setting_system_plugin_handler_up');
        $c->match('/setting/system/plugin/handler_down/{handlerId}', '\Eccube\Controller\Admin\Setting\System\PluginController::handler_down')->bind('admin_setting_system_plugin_handler_down');

        // system/member
        $c->match('/setting/system/member', '\Eccube\Controller\Admin\Setting\System\MemberController::index')->bind('admin_setting_system_member');
        $c->match('/setting/system/member/new', '\Eccube\Controller\Admin\Setting\System\MemberController::edit')->bind('admin_setting_system_member_new');
        $c->match('/setting/system/member/{id}/edit', 'Eccube\Controller\Admin\Setting\System\MemberController::edit')->assert('id', '\d+')->bind('admin_setting_system_member_edit');
        $c->match('/setting/system/member/{id}/delete', '\Eccube\Controller\Admin\Setting\System\MemberController::delete')->assert('id', '\d+')->bind('admin_setting_system_member_delete');
        $c->match('/setting/system/member/{id}/up', '\Eccube\Controller\Admin\Setting\System\MemberController::up')->assert('id', '\d+')->bind('admin_setting_system_member_up');
        $c->match('/setting/system/member/{id}/down', '\Eccube\Controller\Admin\Setting\System\MemberController::down')->assert('id', '\d+')->bind('admin_setting_system_member_down');

        // system/security
        $c->match('/setting/system/security', '\Eccube\Controller\Admin\Setting\System\SecurityController::index')->bind('admin_setting_system_security');


        // 未実装
        $c->match('/product/rank', '\Eccube\Page\Admin\Products\ProductRank')->bind('admin_product_product_rank');
        $c->match('/product/product_select', '\Eccube\Page\Admin\Products\ProductSelect')->bind('admin_product_product_select');
        $c->match('/product/upload_csv', '\Eccube\Page\Admin\Products\UploadCSV')->bind('admin_product_upload_csv');
        $c->match('/product/upload_csv_category', '\Eccube\Page\Admin\Products\UploadCSVCategory')->bind('admin_product_upload_csv_category');

        $c->match('/customer/search_customer', '\Eccube\Page\Admin\Customer\SearchCustomer')->bind('admin_customer_seaech_customer');
        $c->match('/customer/mail/', '\Eccube\Page\Admin\Mail\Index')->bind('admin_mail');
        $c->match('/customer/mail/history', '\Eccube\Page\Admin\Mail\History')->bind('admin_mail_history');
        $c->match('/customer/mail/preview', '\Eccube\Page\Admin\Mail\Preview')->bind('admin_mail_preview');
        $c->match('/customer/mail/template', '\Eccube\Page\Admin\Mail\Template')->bind('admin_mail_template');
        $c->match('/customer/mail/template_input', '\Eccube\Page\Admin\Mail\TemplateInput')->bind('admin_mail_template_input');

        $c->match('/order/disp', '\Eccube\Page\Admin\Order\Disp')->bind('admin_order_disp');
        $c->match('/order/multiple', '\Eccube\Page\Admin\Order\Multiple')->bind('admin_order_multiple');
        $c->match('/order/pdf', '\Eccube\Page\Admin\Order\Pdf')->bind('admin_order_pdf');
        $c->match('/order/product_select', '\Eccube\Page\Admin\Order\ProductSelect')->bind('admin_order_product_select');

        $c->match('/content/recommend', '\Eccube\Page\Admin\Content\Recommend')->bind('admin_content_recommend');
        $c->match('/content/recommend_search', '\Eccube\Page\Admin\Content\RecommendSearch')->bind('admin_content_recommend_search');
        $c->match('/content/header', '\Eccube\Page\Admin\Design\Header')->bind('admin_design_header');
        $c->match('/content/main_edit', '\Eccube\Page\Admin\Design\MainEdit')->bind('admin_design_main_edit');
        $c->match('/content/up_down', '\Eccube\Page\Admin\Design\UpDown')->bind('admin_design_up_down');

        $c->match('/setting/system/adminarea', '\Eccube\Page\Admin\System\AdminArea')->bind('admin_setting_system_adminarea');
        $c->match('/setting/system/log', '\Eccube\Page\Admin\System\Log')->bind('admin_setting_system_log');
        $c->match('/setting/system/masterdata', '\Eccube\Page\Admin\System\Masterdata')->bind('admin_setting_system_masterdata');
        $c->match('/setting/system/parameter', '\Eccube\Page\Admin\System\Parameter')->bind('admin_setting_system_parameter');
        $c->match('/setting/system/csv', '\Eccube\Page\Admin\Content\Csv')->bind('admin_setting_system_csv');
        $c->match('/setting/system/csv_sql', '\Eccube\Page\Admin\Content\CsvSql')->bind('admin_setting_system_csv_sql');

        $c->match('/extension/template', '\Eccube\Page\Admin\Design\Template')->bind('admin_extension_template');
        $c->match('/extension/owners_store', '\Eccube\Page\Admin\OwnersStore\Index')->bind('admin_extension_ownersstore');
        $c->match('/extension/owners_store/log', '\Eccube\Page\Admin\OwnersStore\Log')->bind('admin_extension_ownersstore_log');
        $c->match('/extension/owners_store/settings', '\Eccube\Page\Admin\OwnersStore\Settings')->bind('admin_extension_ownersstore_settings');

        $c->match('/report', '\Eccube\Page\Admin\Total\Index')->bind('admin_total');

        return $c;
    }
}
