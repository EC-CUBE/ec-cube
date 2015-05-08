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
        $controllers = $app['controllers_factory'];

        // login
        $controllers->match('/login', '\\Eccube\\Controller\\Admin\\AdminController::login')->bind('admin_login');

        // root
        $controllers->match('/', '\\Eccube\\Controller\\Admin\\AdminController::index')->bind('admin_homepage');

        // basis
        //$controllers->match('/basis/', '\\Eccube\\Page\\Admin\\Basis\\Index')->bind('admin_basis');
        $controllers->match('/basis/delivery', '\\Eccube\\Controller\\Admin\\Basis\\DelivController::index')->bind('admin_basis_delivery');
        $controllers->match('/basis/delivery/edit', '\\Eccube\\Controller\\Admin\\Basis\\DelivController::edit')
            ->bind('admin_basis_delivery_edit_new');
        $controllers->match('/basis/delivery/edit/{delivId}', '\\Eccube\\Controller\\Admin\\Basis\\DelivController::edit')
            ->assert('delivId', '\d+')
            ->bind('admin_basis_delivery_edit');
        $controllers->match('/basis/delivery/delete/{delivId}', '\\Eccube\\Controller\\Admin\\Basis\\DelivController::delete')
            ->assert('delivId', '\d+')
            ->bind('admin_basis_delivery_delete');
        $controllers->match('/basis/delivery/up/{delivId}', '\\Eccube\\Controller\\Admin\\Basis\\DelivController::up')
            ->assert('delivId', '\d+')
            ->bind('admin_basis_delivery_up');
        $controllers->match('/basis/delivery/down/{delivId}', '\\Eccube\\Controller\\Admin\\Basis\\DelivController::down')
            ->assert('delivId', '\d+')
            ->bind('admin_basis_delivery_down');
        $controllers->match('/basis/', '\\Eccube\\Controller\\Admin\\Basis\\BasisController::index')->bind('admin_basis');
//        $controllers->match('/basis/delivery.php', '\\Eccube\\Page\\Admin\\Basis\\Delivery')->bind('admin_basis_delivery');
//        $controllers->match('/basis/delivery_input.php', '\\Eccube\\Page\\Admin\\Basis\\DeliveryInput')->bind('admin_basis_delivery_input');
        $controllers->match('/basis/holiday.php', '\\Eccube\\Page\\Admin\\Basis\\Holiday')->bind('admin_basis_holiday');
        $controllers->match('/basis/kiyaku.php', '\\Eccube\\Page\\Admin\\Basis\\Kiyaku')->bind('admin_basis_kiyaku');

        $controllers->match('/basis/mail', '\\Eccube\\Controller\\Admin\\Basis\\MailController::index')->bind('admin_basis_mail');
        $controllers->match('/basis/mail/{mailId}', '\\Eccube\\Controller\\Admin\\Basis\\MailController::index')
            ->assert('mailId', '\d+')
            ->bind('admin_basis_mail_edit');

        $controllers->match('/basis/payment', '\\Eccube\\Controller\\Admin\\Basis\\PaymentController::index')->bind('admin_basis_payment');
        $controllers->match('/basis/payment/edit', '\\Eccube\\Controller\\Admin\\Basis\\PaymentController::edit')->bind('admin_basis_payment_edit_new');
        $controllers->match('/basis/payment/edit/{paymentId}', '\\Eccube\\Controller\\Admin\\Basis\\PaymentController::edit')
            ->assert('paymentId', '\d+')
            ->bind('admin_basis_payment_edit');
        $controllers->match('/basis/payment/delete/{paymentId}', '\\Eccube\\Controller\\Admin\\Basis\\PaymentController::delete')
            ->assert('paymentId', '\d+')
            ->bind('admin_basis_payment_delete');
        $controllers->match('/basis/payment/image/delete/{paymentId}', '\\Eccube\\Controller\\Admin\\Basis\\PaymentController::deleteImage')
            ->assert('paymentId', '\d+')
            ->bind('admin_basis_payment_delete_image');
        $controllers->match('/basis/payment/up/{paymentId}', '\\Eccube\\Controller\\Admin\\Basis\\PaymentController::up')
            ->assert('paymentId', '\d+')
            ->bind('admin_basis_payment_up');
        $controllers->match('/basis/payment/down/{paymentId}', '\\Eccube\\Controller\\Admin\\Basis\\PaymentController::down')
            ->assert('paymentId', '\d+')
            ->bind('admin_basis_payment_down');

        $controllers->match('/basis/point', '\\Eccube\Controller\Admin\Basis\PointController::index')->bind('admin_basis_point');
        $controllers->match('/basis/tax/', '\\Eccube\\Controller\\Admin\\Basis\\TaxRuleController::index')->bind('admin_basis_tax_rule');
        $controllers->match('/basis/tax/{tax_rule_id}', '\\Eccube\\Controller\\Admin\\Basis\\TaxRuleController::index')
            ->assert('tax_rule_id', '\d+')
            ->bind('admin_basis_tax_rule_edit');
        $controllers->match('/basis/tax/delete/{tax_rule_id}', '\\Eccube\\Controller\\Admin\\Basis\\TaxRuleController::delete')
            ->assert('tax_rule_id', '\d+')
            ->bind('admin_basis_tax_rule_delete');
        $controllers->match('/basis/tradelaw', '\\Eccube\\Controller\\Admin\\Basis\\TradelawController::index')->bind('admin_basis_tradelaw');
        $controllers->match('/basis/zip_install.php', '\\Eccube\\Page\\Admin\\Basis\\ZipInstall')->bind('admin_basis_zip_install');

        // contents
        $controllers->match('/content/', '\\Eccube\\Page\\Admin\\Content\\Index')->bind('admin_content');
        $controllers->match('/content/csv.php', '\\Eccube\\Page\\Admin\\Content\\Csv')->bind('admin_content_csv');
        $controllers->match('/content/csv_sql.php', '\\Eccube\\Page\\Admin\\Content\\CsvSql')->bind('admin_content_csv_sql');
        $controllers->match('/content/file_manager.php', '\\Eccube\\Controller\\Admin\\Content\\FileController::index')->bind('admin_content_file');
        $controllers->match('/content/file_view.php', '\\Eccube\\Controller\\Admin\\Content\\FileController::view')->bind('admin_content_file_view');
        $controllers->match('/content/recommend.php', '\\Eccube\\Page\\Admin\\Content\\Recommend')->bind('admin_content_recommend');
        $controllers->match('/content/recommend_search.php', '\\Eccube\\Page\\Admin\\Content\\RecommendSearch')->bind('admin_content_recommend_search');
        $controllers->match('/content/css/', '\\Eccube\\Controller\\Admin\\Content\\CssController::index')->bind('admin_content_css');
        $controllers->match('/content/css/delete', '\\Eccube\\Controller\\Admin\\Content\\CssController::delete')->bind('admin_content_css_delete');

        // customer
        $controllers->match('/customer/', '\\Eccube\\Controller\\Admin\\Customer\\CustomerController::index')->bind('admin_customer');
        $controllers->match('/customer/resend/{customerId}', '\\Eccube\\Controller\\Admin\\Customer\\CustomerController::resend')
            ->assert('customerId', '\d+')
            ->bind('admin_customer_resend');
        $controllers->match('/customer/delete/{customerId}', '\\Eccube\\Controller\\Admin\\Customer\\CustomerController::delete')
            ->assert('customerId', '\d+')
            ->bind('admin_customer_delete');
        $controllers->match('/customer/edit/{customerId}', '\\Eccube\\Controller\\Admin\\Customer\\CustomerEditController::index')
            ->assert('customerId', '\d+')
            ->bind('admin_customer_edit');
        $controllers->match('/customer/edit/', '\\Eccube\\Controller\\Admin\\Customer\\CustomerEditController::index')
            ->bind('admin_customer_new');
        $controllers->match('/customer/search_customer.php', '\\Eccube\\Page\\Admin\\Customer\\SearchCustomer')->bind('admin_customer_seaech_customer');

        // design
        // $controllers->match('/design/', '\\Eccube\\Page\\Admin\\Design\\Index')->bind('admin_design_old');
        $controllers->match('/design', '\\Eccube\\Controller\\Admin\\Design\\DesignController::index')->bind('admin_design');
        $controllers->match('/design/{pageId}', '\\Eccube\\Controller\\Admin\\Design\\DesignController::index')
            ->assert('pageId', '\d+')
            ->bind('admin_design_edit');
        $controllers->match('/design/preview', '\\Eccube\\Controller\\Admin\\Design\\DesignController::preview')->bind('admin_design_preview');

        $controllers->match('/design/bloc.php', '\\Eccube\\Page\\Admin\\Design\\Bloc')->bind('admin_design_bloc');
        $controllers->match('/content/block/', '\\Eccube\\Controller\\Admin\\Content\\BlockController::index')->bind('admin_content_block');
        $controllers->match('/content/block/{block_id}', '\\Eccube\\Controller\\Admin\\Content\\BlockController::index')
            ->assert('block_id', '\d+')
            ->bind('admin_content_block_edit');
        $controllers->match('/content/block/{block_id}/{device_id}', '\\Eccube\\Controller\\Admin\\Content\\BlockController::index')
            ->assert('block_id', '\d+')
            ->assert('device_type_id', '\d+')
            ->bind('admin_content_block_edit_withDevice');
        $controllers->match('/content/block/delete/{block_id}', '\\Eccube\\Controller\\Admin\\Content\\BlockController::delete')
            ->assert('block_id', '\d+')
            ->bind('admin_content_block_delete');
        $controllers->match('/content/block/delete/{block_id}/{device_type_id}', '\\Eccube\\Controller\\Admin\\Content\\BlockController::delete')
            ->assert('block_id', '\d+')
            ->assert('device_type_id', '\d+')
            ->bind('admin_content_block_delete_withDevice');
        $controllers->match('/design/header.php', '\\Eccube\\Page\\Admin\\Design\\Header')->bind('admin_design_header');
        $controllers->match('/design/main_edit.php', '\\Eccube\\Page\\Admin\\Design\\MainEdit')->bind('admin_design_main_edit');
        $controllers->match('/content/page/', '\\Eccube\\Controller\\Admin\\Content\\PageController::index')->bind('admin_content_page');
        $controllers->match('/content/page/{page_id}', '\\Eccube\\Controller\\Admin\\Content\\PageController::index')
            ->assert('page_id', '\d+')
            ->bind('admin_content_page_edit');
        $controllers->match('/content/page/{page_id}/{device_id}', '\\Eccube\\Controller\\Admin\\Content\\PageController::index')
            ->assert('page_id', '\d+')
            ->assert('device_id', '\d+')
            ->bind('admin_content_page_edit_withDevice');
        $controllers->match('/content/page/delete/{page_id}', '\\Eccube\\Controller\\Admin\\Content\\PageController::delete')
            ->assert('page_id', '\d+')
            ->bind('admin_content_page_delete');
        $controllers->match('/content/page/delete/{page_id}/{device_id}', '\\Eccube\\Controller\\Admin\\Content\\PageController::delete')
            ->assert('page_id', '\d+')
            ->assert('device_id', '\d+')
            ->bind('admin_content_page_delete_withDevice');
        $controllers->match('/design/template.php', '\\Eccube\\Page\\Admin\\Design\\Template')->bind('admin_design_template');
        $controllers->match('/design/up_down.php', '\\Eccube\\Page\\Admin\\Design\\UpDown')->bind('admin_design_up_down');

        // mail
        $controllers->match('/mail/', '\\Eccube\\Page\\Admin\\Mail\\Index')->bind('admin_mail');
        $controllers->match('/mail/history.php', '\\Eccube\\Page\\Admin\\Mail\\History')->bind('admin_mail_history');
        $controllers->match('/mail/preview.php', '\\Eccube\\Page\\Admin\\Mail\\Preview')->bind('admin_mail_preview');
        $controllers->match('/mail/template.php', '\\Eccube\\Page\\Admin\\Mail\\Template')->bind('admin_mail_template');
        $controllers->match('/mail/template_input.php', '\\Eccube\\Page\\Admin\\Mail\\TemplateInput')->bind('admin_mail_template_input');

        // order
        $controllers->match('/order/', '\\Eccube\\Controller\\Admin\\Order\\OrderController::index')->bind('admin_order');
        $controllers->match('/order/edit/{orderId}', '\\Eccube\\Controller\\Admin\\Order\\EditController::index')
            ->assert('orderId', '\d+')
            ->bind('admin_order_edit');
        $controllers->match('/order/edit/', '\\Eccube\\Controller\\Admin\\Order\\EditController::index')
            ->bind('admin_order_new');
        $controllers->match('/order/delete/{orderId}', '\\Eccube\\Controller\\Admin\\Order\\OrderController::delete')
            ->assert('orderId', '\d+')
            ->bind('admin_order_delete');
        $controllers->match('/order/recalc/', '\\Eccube\\Controller\\Admin\\Order\\OrderController::recalculate')
            ->assert('orderId', '\d+')
            ->bind('admin_order_recalc');
        $controllers->match('/order/product/add/{orderId}/{shippingId}', '\\Eccube\\Controller\\Admin\\Order\\OrderController::addProduct')
            ->assert('orderId', '\d+')
            ->assert('shippingId', '\d+')
            ->bind('admin_order_product_add');
        $controllers->match('/order/product/select/{orderId}/{shippingId}', '\\Eccube\\Controller\\Admin\\Order\\OrderController::selectProduct')
            ->assert('orderId', '\d+')
            ->assert('shippingId', '\d+')
            ->bind('admin_order_product_select');
        $controllers->match('/order/product/delete/{orderId}/{shippingId}', '\\Eccube\\Controller\\Admin\\Order\\OrderController::deleteProduct')
            ->assert('orderId', '\d+')
            ->assert('shippingId', '\d+')
            ->bind('admin_order_product_delete');
        $controllers->match('/order/shipping/add/', '\\Eccube\\Controller\\Admin\\Order\\OrderController::addShipping')
            ->bind('admin_order_shipping_add');

        $controllers->match('/order/disp.php', '\\Eccube\\Page\\Admin\\Order\\Disp')->bind('admin_order_disp');
        $controllers->match('/order/mail/{orderId}', '\\Eccube\\Controller\\Admin\\Order\\MailController::index')
            ->assert('orderId', '\d+')
            ->bind('admin_order_mail');
        $controllers->match('/order/mail/view/{sendId}', '\\Eccube\\Controller\\Admin\\Order\\MailController::view')
            ->assert('sendId', '\d+')
            ->bind('admin_order_mail_view');
        $controllers->match('/order/multiple.php', '\\Eccube\\Page\\Admin\\Order\\Multiple')->bind('admin_order_multiple');
        $controllers->match('/order/pdf.php', '\\Eccube\\Page\\Admin\\Order\\Pdf')->bind('admin_order_pdf');
        $controllers->match('/order/product_select.php', '\\Eccube\\Page\\Admin\\Order\\ProductSelect')->bind('admin_order_product_select');
        
        $controllers->match('/order/status', '\\Eccube\\Controller\\Admin\\Order\\StatusController::index')->bind('admin_order_status_default');
        $controllers->match('/order/status/{statusId}', '\\Eccube\\Controller\\Admin\\Order\\StatusController::index')->bind('admin_order_status');

        // ownersstore
        $controllers->match('/ownersstore/', '\\Eccube\\Page\\Admin\\OwnersStore\\Index')->bind('admin_ownersstore');
        $controllers->match('/ownersstore/log.php', '\\Eccube\\Page\\Admin\\OwnersStore\\Log')->bind('admin_ownersstore_log');
        $controllers->match('/ownersstore/module.php', '\\Eccube\\Page\\Admin\\OwnersStore\\Module')->bind('admin_ownersstore_module');
        $controllers->match('/ownersstore/plugin_hookpoint_list.php', '\\Eccube\\Page\\Admin\\OwnersStore\\PluginHookPointList')->bind('admin_ownersstore_plugin_hookpoint_list');
        $controllers->match('/ownersstore/settings.php', '\\Eccube\\Page\\Admin\\OwnersStore\\Settings')->bind('admin_ownersstore_settings');

        // products
        $controllers->match('/products/', '\Eccube\Controller\Admin\Product\ProductController::index')->bind('admin_product');
        $controllers->match('/products/product.php', '\Eccube\Controller\Admin\Product\ProductController::edit')->bind('admin_product_product');
        $controllers->match('/products/product/class/{product_id}', '\Eccube\Controller\Admin\Product\ProductClassController::index')
            ->assert('product_id', '\d+') // todo 変数名調整
            ->bind('admin_product_product_class');

        // category
        $controllers->match('/products/category/', '\Eccube\Controller\Admin\Product\CategoryController::index')->bind('admin_category');
        $controllers->match('/products/category/{parentId}/', '\Eccube\Controller\Admin\Product\CategoryController::index')
            ->assert('parentId', '\d+')
            ->bind('admin_category_show');
        $controllers->match('/products/category/{categoryId}/edit', '\Eccube\Controller\Admin\Product\CategoryController::index')
            ->assert('categoryId', '\d+')
            ->bind('admin_category_edit');
        $controllers->post('/products/category/{categoryId}/up', '\Eccube\Controller\Admin\Product\CategoryController::up')
            ->assert('parentCategoryId', '\d+')
            ->assert('categoryId', '\d+')
            ->bind('admin_category_up');
        $controllers->post('/products/category/{categoryId}/down', '\Eccube\Controller\Admin\Product\CategoryController::down')
            ->assert('categoryId', '\d+')
            ->bind('admin_category_down');
        $controllers->post('/products/category/{categoryId}/delete', '\Eccube\Controller\Admin\Product\CategoryController::delete')
            ->assert('categoryId', '\d+')
            ->bind('admin_category_delete');

        // class_name
        $controllers->match('/products/class_name/', '\Eccube\Controller\Admin\Product\ClassNameController::index')->bind('admin_class_name');
        $controllers->match('/products/class_name/{classNameId}/edit', '\Eccube\Controller\Admin\Product\ClassNameController::index')
            ->assert('classNameId', '\d+')
            ->bind('admin_class_name_edit');
        $controllers->post('/products/class_name/{classNameId}/up', '\Eccube\Controller\Admin\Product\ClassNameController::up')
            ->assert('classNameId', '\d+')
            ->bind('admin_class_name_up');
        $controllers->post('/products/class_name/{classNameId}/down', '\Eccube\Controller\Admin\Product\ClassNameController::down')
            ->assert('classNameId', '\d+')
            ->bind('admin_class_name_down');
        $controllers->post('/products/class_name/{classNameId}/delete', '\Eccube\Controller\Admin\Product\ClassNameController::delete')
            ->assert('classNameId', '\d+')
            ->bind('admin_class_name_delete');

        // class_category
        $controllers->match('/products/class_category/{classNameId}/', '\Eccube\Controller\Admin\Product\ClassCategoryController::index')
            ->assert('classNameId', '\d+')
            ->bind('admin_class_category');
        $controllers->match('/products/class_category/{classNameId}/{classCategoryId}/edit', '\Eccube\Controller\Admin\Product\ClassCategoryController::index')
            ->assert('classNameId', '\d+')
            ->assert('classCategoryId', '\d+')
            ->bind('admin_class_category_edit');
        $controllers->post('/products/class_category/{classNameId}/{classCategoryId}/up', '\Eccube\Controller\Admin\Product\ClassCategoryController::up')
            ->assert('classNameId', '\d+')
            ->assert('classCategoryId', '\d+')
            ->bind('admin_class_category_up');
        $controllers->post('/products/class_category/{classNameId}/{classCategoryId}/down', '\Eccube\Controller\Admin\Product\ClassCategoryController::down')
            ->assert('classNameId', '\d+')
            ->assert('classCategoryId', '\d+')
            ->bind('admin_class_category_down');
        $controllers->post('/products/class_category/{classNameId}/{classCategoryId}/delete', '\Eccube\Controller\Admin\Product\ClassCategoryController::delete')
            ->assert('classNameId', '\d+')
            ->assert('classCategoryId', '\d+')
            ->bind('admin_class_category_delete');

        //$controllers->match('/products/product_class.php', '\\Eccube\\Page\\Admin\\Products\\ProductClass')->bind('admin_product_product_class');
        $controllers->match('/products/product_rank.php', '\\Eccube\\Page\\Admin\\Products\\ProductRank')->bind('admin_product_product_rank');
        $controllers->match('/products/product_select.php', '\\Eccube\\Page\\Admin\\Products\\ProductSelect')->bind('admin_product_product_select');
        $controllers->match('/products/upload_csv.php', '\\Eccube\\Page\\Admin\\Products\\UploadCSV')->bind('admin_product_upload_csv');
        $controllers->match('/products/upload_csv_category.php', '\\Eccube\\Page\\Admin\\Products\\UploadCSVCategory')->bind('admin_product_upload_csv_category');

        // Maker (plugin化予定)
        $controllers->match('/products/maker/', '\Eccube\Controller\Admin\MakerController::index')->bind('admin_maker');
        $controllers->match('/products/maker/{makerId}/edit', '\Eccube\Controller\Admin\MakerController::index')
            ->assert('makerId', '\d+')
            ->bind('admin_maker_edit');
        $controllers->post('/products/maker/{makerId}/up', '\Eccube\Controller\Admin\MakerController::up')
            ->assert('makerId', '\d+')
            ->bind('admin_maker_up');
        $controllers->post('/products/maker/{makerId}/down', '\Eccube\Controller\Admin\MakerController::down')
            ->assert('makerId', '\d+')
            ->bind('admin_maker_down');
        $controllers->post('/products/maker/{makerId}/delete', '\Eccube\Controller\Admin\MakerController::delete')
            ->assert('makerId', '\d+')
            ->bind('admin_maker_delete');

        // system
        $controllers->match('/system/', '\\Eccube\\Page\\Admin\\System\\Index')->bind('admin_system');
        $controllers->match('/system/adminarea.php', '\\Eccube\\Page\\Admin\\System\\AdminArea')->bind('admin_system_adminarea');
        $controllers->match('/system/bkup.php', '\\Eccube\\Page\\Admin\\System\\Bkup')->bind('admin_system_bkup');
        $controllers->match('/system/delete.php', '\\Eccube\\Page\\Admin\\System\\Delete')->bind('admin_system_delete');
        $controllers->match('/system/editdb.php', '\\Eccube\\Page\\Admin\\System\\Editdb')->bind('admin_system_editdb');
        $controllers->match('/system/input.php', '\\Eccube\\Page\\Admin\\System\\Input')->bind('admin_system_input');
        $controllers->match('/system/log.php', '\\Eccube\\Page\\Admin\\System\\Log')->bind('admin_system_log');
        $controllers->match('/system/masterdata.php', '\\Eccube\\Page\\Admin\\System\\Masterdata')->bind('admin_system_masterdata');
        $controllers->match('/system/parameter.php', '\\Eccube\\Page\\Admin\\System\\Parameter')->bind('admin_system_parameter');
        $controllers->match('/system/rank.php', '\\Eccube\\Page\\Admin\\System\\Rank')->bind('admin_system_rank');
        $controllers->match('/system/system.php', '\\Eccube\\Controller\\Admin\\System\\SystemController::index')->bind('admin_system_system');

        // total
        $controllers->match('/total/', '\\Eccube\\Page\\Admin\\Total\\Index')->bind('admin_total');

        return $controllers;
    }
}
