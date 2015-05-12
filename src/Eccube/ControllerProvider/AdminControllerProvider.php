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

        // 廃止
        // $c->match('/basis/zip_install.php', '\Eccube\Page\Admin\Basis\ZipInstall')->bind('admin_basis_zip_install');
        // $c->match('/basis/holiday.php', '\Eccube\Page\Admin\Basis\Holiday')->bind('admin_basis_holiday');
        // $c->match('/extension/owners_store/module', '\Eccube\Page\Admin\OwnersStore\Module')->bind('admin_ownersstore_module');
        // $c->match('/extension/owners_store/plugin_hookpoint_list', '\Eccube\Page\Admin\OwnersStore\PluginHookPointList')->bind('admin_ownersstore_plugin_hookpoint_list');
        // $c->match('/setting/system/editdb', '\Eccube\Page\Admin\System\Editdb')->bind('admin_system_editdb');
        // $c->match('/setting/system/bkup', '\Eccube\Page\Admin\System\Bkup')->bind('admin_system_bkup');


        // root
        $c->match('/', '\Eccube\Controller\Admin\AdminController::index')->bind('admin_homepage');

        // login
        $c->match('/login', '\Eccube\Controller\Admin\AdminController::login')->bind('admin_login');


        // product
        $c->match('/product', '\Eccube\Controller\Admin\Product\ProductController::index')->bind('admin_product');
        $c->match('/product/product/new', '\Eccube\Controller\Admin\Product\ProductController::edit')->bind('admin_product_product_new');
        $c->match('/product/product/{id}/edit', '\Eccube\Controller\Admin\Product\ProductController::edit')->bind('admin_product_product_edit');
        $c->match('/product/product/class/new', '\Eccube\Controller\Admin\Product\ProductClassController::index')->assert('id', '\d+')->bind('admin_product_product_class_new');
        $c->match('/product/product/class/edit/{id}', '\Eccube\Controller\Admin\Product\ProductClassController::index')->assert('id', '\d+')->bind('admin_product_product_class_edit');

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
        $c->match('/customer/new', '\Eccube\Controller\Admin\Customer\CustomerEditController::index')->bind('admin_customer_new');
        $c->match('/customer/{id}/edit', '\Eccube\Controller\Admin\Customer\CustomerEditController::index')->assert('id', '\d+')->bind('admin_customer_edit');
        $c->match('/customer/{id}/delete', '\Eccube\Controller\Admin\Customer\CustomerController::delete')->assert('id', '\d+')->bind('admin_customer_delete');
        $c->match('/customer/{id}/resend', '\Eccube\Controller\Admin\Customer\CustomerController::resend')->assert('id', '\d+')->bind('admin_customer_resend');



        // order
        $c->match('/order', '\Eccube\Controller\Admin\Order\OrderController::index')->bind('admin_order');
        $c->match('/order/new', '\Eccube\Controller\Admin\Order\EditController::index')->bind('admin_order_new');
        $c->match('/order/{id}/edit', '\Eccube\Controller\Admin\Order\EditController::index')->assert('id', '\d+')->bind('admin_order_edit');
        $c->match('/order/{id}/delete', '\Eccube\Controller\Admin\Order\OrderController::delete')->assert('id', '\d+')->bind('admin_order_delete');
        $c->match('/order/{id}/recalc', '\Eccube\Controller\Admin\Order\OrderController::recalculate')->assert('id', '\d+')->bind('admin_order_recalc');
        $c->match('/order/{id}/product/add/{shipping_id}', '\Eccube\Controller\Admin\Order\OrderController::addProduct')->assert('id', '\d+')->assert('shipping_id', '\d+')->bind('admin_order_product_add');
        $c->match('/order/{id}/product/select/{shipping_id}', '\Eccube\Controller\Admin\Order\OrderController::selectProduct')->assert('id', '\d+')->assert('shipping_id', '\d+')->bind('admin_order_product_select');
        $c->match('/order/{id}/product/delete/{shipping_id}', '\Eccube\Controller\Admin\Order\OrderController::deleteProduct')->assert('id', '\d+')->assert('shipping_id', '\d+')->bind('admin_order_product_delete');
        $c->match('/order/shipping/add', '\Eccube\Controller\Admin\Order\OrderController::addShipping')->bind('admin_order_shipping_add');

        $c->match('/order/{id}/mail', '\Eccube\Controller\Admin\Order\MailController::index')->assert('id', '\d+')->bind('admin_order_mail');
        $c->match('/order/mail/view/{sendId}', '\Eccube\Controller\Admin\Order\MailController::view')->assert('sendId', '\d+')->bind('admin_order_mail_view');

        $c->match('/order/status', '\Eccube\Controller\Admin\Order\StatusController::index')->bind('admin_order_status_default');
        $c->match('/order/status/{statusId}', '\Eccube\Controller\Admin\Order\StatusController::index')->bind('admin_order_status');

        // content
        $c->match('/content/file_manager', '\Eccube\Controller\Admin\Content\FileController::index')->bind('admin_content_file');
        $c->match('/content/file_view', '\Eccube\Controller\Admin\Content\FileController::view')->bind('admin_content_file_view');
        $c->match('/content/css', '\Eccube\Controller\Admin\Content\CssController::index')->bind('admin_content_css');
        $c->match('/content/css/delete', '\Eccube\Controller\Admin\Content\CssController::delete')->bind('admin_content_css_delete');

        $c->match('/content/layout', '\Eccube\Controller\Admin\Content\LayoutController::index')->bind('admin_content_layout');
        $c->match('/content/layout/{id}/edit', '\Eccube\Controller\Admin\Content\LayoutController::index')->assert('id', '\d+')->bind('admin_content_layout_edit');
        $c->match('/content/layout/{id}/preview', '\Eccube\Controller\Admin\Content\LayoutController::preview')->assert('id', '\d+')->bind('admin_content_layout_preview');

        $c->match('/content/block', '\Eccube\Controller\Admin\Content\BlockController::index')->bind('admin_content_block');
        $c->match('/content/block/{id}/edit', '\Eccube\Controller\Admin\Content\BlockController::index')->assert('id', '\d+')->bind('admin_content_block_edit');
        $c->match('/content/block/{id}/delete', '\Eccube\Controller\Admin\Content\BlockController::delete')->assert('id', '\d+')->bind('admin_content_block_delete');

        $c->match('/content/page', '\Eccube\Controller\Admin\Content\PageController::index')->bind('admin_content_page');
        $c->match('/content/page/{id}/edit', '\Eccube\Controller\Admin\Content\PageController::index')->assert('id', '\d+')->bind('admin_content_page_edit');
        $c->match('/content/page/{id}/delete', '\Eccube\Controller\Admin\Content\PageController::delete')->assert('id', '\d+')->bind('admin_content_page_delete');

        // shop
        $c->match('/setting/shop', '\Eccube\Controller\Admin\Setting\Shop\ShopController::index')->bind('admin_setting_shop');

        // delivery
        $c->match('/setting/shop/delivery', '\Eccube\Controller\Admin\Setting\Shop\DelivController::index')->bind('admin_setting_shop_delivery');
        $c->match('/setting/shop/delivery/new', '\Eccube\Controller\Admin\Setting\Shop\DelivController::edit')->bind('admin_setting_shop_delivery_new');
        $c->match('/setting/shop/delivery/{id}/edit', '\Eccube\Controller\Admin\Setting\Shop\DelivController::edit')->assert('id', '\d+')->bind('admin_setting_shop_delivery_edit');
        $c->match('/setting/shop/delivery/{id}/delete', '\Eccube\Controller\Admin\Setting\Shop\DelivController::delete')->assert('id', '\d+')->bind('admin_setting_shop_delivery_delete');
        $c->match('/setting/shop/delivery/{id}/up', '\Eccube\Controller\Admin\Setting\Shop\DelivController::up')->assert('id', '\d+')->bind('admin_setting_shop_delivery_up');
        $c->match('/setting/shop/delivery/{id}/down', '\Eccube\Controller\Admin\Setting\Shop\DelivController::down')->assert('id', '\d+')->bind('admin_setting_shop_delivery_down');

        // payment
        $c->match('/setting/shop/payment', '\Eccube\Controller\Admin\Setting\Shop\PaymentController::index')->bind('admin_setting_shop_payment');
        $c->match('/setting/shop/payment/new', '\Eccube\Controller\Admin\Setting\Shop\PaymentController::edit')->bind('admin_setting_shop_payment_new');
        $c->match('/setting/shop/payment/{id}/edit', '\Eccube\Controller\Admin\Setting\Shop\PaymentController::edit')->assert('id', '\d+')->bind('admin_setting_shop_payment_edit');
        $c->match('/setting/shop/payment/{id}/delete', '\Eccube\Controller\Admin\Setting\Shop\PaymentController::delete')->assert('id', '\d+')->bind('admin_setting_shop_payment_delete');
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

        // mail
        $c->match('/setting/shop/mail', '\Eccube\Controller\Admin\Setting\Shop\MailController::index')->bind('admin_setting_shop_mail');
        $c->match('/setting/shop/mail/new', '\Eccube\Controller\Admin\Setting\Shop\MailController::index')->assert('id', '\d+')->bind('admin_setting_shop_mail_edit');
        $c->match('/setting/shop/mail/{id}/edit', '\Eccube\Controller\Admin\Setting\Shop\MailController::index')->assert('id', '\d+')->bind('admin_setting_shop_mail_edit');

        // customer_agreement
        $c->match('/setting/shop/customer_agreement/', '\\Eccube\\Controller\\Admin\\Setting\\Shop\\CustomerAgreementController::index')->bind('admin_setting_shop_customer_agreement');

        // system
        $c->match('/setting/system/system', '\Eccube\Controller\Admin\System\SystemController::index')->bind('admin_setting_system_system');

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

        $c->match('/content', '\Eccube\Page\Admin\Content\Index')->bind('admin_content');
        $c->match('/content/recommend', '\Eccube\Page\Admin\Content\Recommend')->bind('admin_content_recommend');
        $c->match('/content/recommend_search', '\Eccube\Page\Admin\Content\RecommendSearch')->bind('admin_content_recommend_search');
        $c->match('/content/header', '\Eccube\Page\Admin\Design\Header')->bind('admin_design_header');
        $c->match('/content/main_edit', '\Eccube\Page\Admin\Design\MainEdit')->bind('admin_design_main_edit');
        $c->match('/content/up_down', '\Eccube\Page\Admin\Design\UpDown')->bind('admin_design_up_down');

        $c->match('/setting/system', '\Eccube\Page\Admin\System\Index')->bind('admin_setting_system_member');
        $c->match('/setting/system/adminarea', '\Eccube\Page\Admin\System\AdminArea')->bind('admin_setting_system_adminarea');
        $c->match('/setting/system/delete', '\Eccube\Page\Admin\System\Delete')->bind('admin_setting_system_delete');
        $c->match('/setting/system/input', '\Eccube\Page\Admin\System\Input')->bind('admin_setting_system_input');
        $c->match('/setting/system/log', '\Eccube\Page\Admin\System\Log')->bind('admin_setting_system_log');
        $c->match('/setting/system/masterdata', '\Eccube\Page\Admin\System\Masterdata')->bind('admin_setting_system_masterdata');
        $c->match('/setting/system/parameter', '\Eccube\Page\Admin\System\Parameter')->bind('admin_setting_system_parameter');
        $c->match('/setting/system/rank', '\Eccube\Page\Admin\System\Rank')->bind('admin_setting__system_rank');
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
