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

class AdminControllerProvider implements ControllerProviderInterface
{
    public function connect(Application $app)
    {
        $controllers = $app['controllers_factory'];

        // root
        $controllers->match('/', '\\Eccube\\Page\\Admin\\Index')->bind('admin_index');
        $controllers->match('/home.php', '\\Eccube\\Page\\Admin\\Home')->bind('admin_home');
        $controllers->match('/logout.php', '\\Eccube\\Page\\Admin\\Logout')->bind('admin_logout');

        // basis
        $controllers->match('/basis/', '\\Eccube\\Page\\Admin\\Basis\\Index')->bind('admin_basis');
        $controllers->match('/basis/delivery.php', '\\Eccube\\Page\\Admin\\Basis\\Delivery')->bind('admin_basis_delivery');
        $controllers->match('/basis/delivery_input.php', '\\Eccube\\Page\\Admin\\Basis\\DeliveryInput')->bind('admin_basis_delivery_input');
        $controllers->match('/basis/holiday.php', '\\Eccube\\Page\\Admin\\Basis\\Holiday')->bind('admin_basis_holiday');
        $controllers->match('/basis/kiyaku.php', '\\Eccube\\Page\\Admin\\Basis\\Kiyaku')->bind('admin_basis_kiyaku');
        $controllers->match('/basis/mail.php', '\\Eccube\\Page\\Admin\\Basis\\Mail')->bind('admin_basis_mail');
        $controllers->match('/basis/payment.php', '\\Eccube\\Page\\Admin\\Basis\\Payment')->bind('admin_basis_payment');
        $controllers->match('/basis/payment_input.php', '\\Eccube\\Page\\Admin\\Basis\\PaymentInput')->bind('admin_basis_payment_input');
        $controllers->match('/basis/point.php', '\\Eccube\\Page\\Admin\\Basis\\Point')->bind('admin_basis_point');
        $controllers->match('/basis/tax.php', '\\Eccube\\Page\\Admin\\Basis\\Tax')->bind('admin_basis_tax');
        $controllers->match('/basis/tradelaw.php', '\\Eccube\\Page\\Admin\\Basis\\Tradelaw')->bind('admin_basis_tradelaw');
        $controllers->match('/basis/zip_install.php', '\\Eccube\\Page\\Admin\\Basis\\ZipInstall')->bind('admin_basis_zip_install');

        // contents
        $controllers->match('/contents/', '\\Eccube\\Page\\Admin\\Contents\\Index')->bind('admin_contents');
        $controllers->match('/contents/csv.php', '\\Eccube\\Page\\Admin\\Contents\\Csv')->bind('admin_contents_csv');
        $controllers->match('/contents/csv_sql.php', '\\Eccube\\Page\\Admin\\Contents\\CsvSql')->bind('admin_contents_csv_sql');
        $controllers->match('/contents/file_manager.php', '\\Eccube\\Page\\Admin\\Contents\\FileManager')->bind('admin_contents_file_manager');
        $controllers->match('/contents/file_view.php', '\\Eccube\\Page\\Admin\\Contents\\FileView')->bind('admin_contents_file_view');
        $controllers->match('/contents/recommend.php', '\\Eccube\\Page\\Admin\\Contents\\Recommend')->bind('admin_contents_recommend');
        $controllers->match('/contents/recommend_search.php', '\\Eccube\\Page\\Admin\\Contents\\RecommendSearch')->bind('admin_contents_recommend_search');

        // customer
        $controllers->match('/customer/', '\\Eccube\\Page\\Admin\\Customer\\Index')->bind('admin_customer');
        $controllers->match('/customer/edit.php', '\\Eccube\\Page\\Admin\\Customer\\Edit')->bind('admin_customer_edit');
        $controllers->match('/customer/seaech_customer.php', '\\Eccube\\Page\\Admin\\Customer\\SearchCustomer')->bind('admin_customer_seaech_customer');

        // design
        $controllers->match('/design/', '\\Eccube\\Page\\Admin\\Design\\Index')->bind('admin_design');
        $controllers->match('/design/bloc.php', '\\Eccube\\Page\\Admin\\Design\\Bloc')->bind('admin_design_bloc');
        $controllers->match('/design/css.php', '\\Eccube\\Page\\Admin\\Design\\Css')->bind('admin_design_css');
        $controllers->match('/design/header.php', '\\Eccube\\Page\\Admin\\Design\\Header')->bind('admin_design_header');
        $controllers->match('/design/main_edit.php', '\\Eccube\\Page\\Admin\\Design\\MainEdit')->bind('admin_design_main_edit');
        $controllers->match('/design/template.php', '\\Eccube\\Page\\Admin\\Design\\Template')->bind('admin_design_template');
        $controllers->match('/design/up_down.php', '\\Eccube\\Page\\Admin\\Design\\UpDown')->bind('admin_design_up_down');

        // mail
        $controllers->match('/mail/', '\\Eccube\\Page\\Admin\\Mail\\Index')->bind('admin_mail');
        $controllers->match('/mail/history.php', '\\Eccube\\Page\\Admin\\Mail\\History')->bind('admin_mail_history');
        $controllers->match('/mail/preview.php', '\\Eccube\\Page\\Admin\\Mail\\Preview')->bind('admin_mail_preview');
        $controllers->match('/mail/template.php', '\\Eccube\\Page\\Admin\\Mail\\Template')->bind('admin_mail_template');
        $controllers->match('/mail/template_input.php', '\\Eccube\\Page\\Admin\\Mail\\TemplateInput')->bind('admin_mail_template_input');

        // order
        $controllers->match('/order/', '\\Eccube\\Page\\Admin\\Order\\Index')->bind('admin_order');
        $controllers->match('/order/disp.php', '\\Eccube\\Page\\Admin\\Order\\Disp')->bind('admin_order_disp');
        $controllers->match('/order/edit.php', '\\Eccube\\Page\\Admin\\Order\\Edit')->bind('admin_order_edit');
        $controllers->match('/order/mail.php', '\\Eccube\\Page\\Admin\\Order\\Mail')->bind('admin_order_mail');
        $controllers->match('/order/mail_view.php', '\\Eccube\\Page\\Admin\\Order\\MailView')->bind('admin_order_mail_view');
        $controllers->match('/order/multiple.php', '\\Eccube\\Page\\Admin\\Order\\Multiple')->bind('admin_order_multiple');
        $controllers->match('/order/pdf.php', '\\Eccube\\Page\\Admin\\Order\\Pdf')->bind('admin_order_pdf');
        $controllers->match('/order/product_select.php', '\\Eccube\\Page\\Admin\\Order\\ProductSelect')->bind('admin_order_product_select');
        $controllers->match('/order/status.php', '\\Eccube\\Page\\Admin\\Order\\Status')->bind('admin_order_status');

        // ownersstore
        $controllers->match('/ownersstore/', '\\Eccube\\Page\\Admin\\OwnersStore\\Index')->bind('admin_ownersstore');
        $controllers->match('/ownersstore/log.php', '\\Eccube\\Page\\Admin\\OwnersStore\\Log')->bind('admin_ownersstore_log');
        $controllers->match('/ownersstore/module.php', '\\Eccube\\Page\\Admin\\OwnersStore\\Module')->bind('admin_ownersstore_module');
        $controllers->match('/ownersstore/plugin_hookpoint_list.php', '\\Eccube\\Page\\Admin\\OwnersStore\\PluginHookPointList')->bind('admin_ownersstore_plugin_hookpoint_list');
        $controllers->match('/ownersstore/settings.php', '\\Eccube\\Page\\Admin\\OwnersStore\\Settings')->bind('admin_ownersstore_settings');

        // products
        $controllers->match('/products/', '\\Eccube\\Page\\Admin\\Products\\Index')->bind('admin_products');
        $controllers->match('/products/category.php', '\\Eccube\\Page\\Admin\\Products\\Category')->bind('admin_products_category');
        $controllers->match('/products/class.php', '\\Eccube\\Page\\Admin\\Products\\ClassList')->bind('admin_products_class');
        $controllers->match('/products/classcategory.php', '\\Eccube\\Page\\Admin\\Products\\ClassCategory')->bind('admin_products_classcategory');
        $controllers->match('/products/maker.php', '\\Eccube\\Page\\Admin\\Products\\Maker')->bind('admin_products_maker');
        $controllers->match('/products/product.php', '\\Eccube\\Page\\Admin\\Products\\ProductEdit')->bind('admin_products_product');
        $controllers->match('/products/product_class.php', '\\Eccube\\Page\\Admin\\Products\\ProductClass')->bind('admin_products_product_class');
        $controllers->match('/products/product_rank.php', '\\Eccube\\Page\\Admin\\Products\\ProductRank')->bind('admin_products_product_rank');
        $controllers->match('/products/product_select.php', '\\Eccube\\Page\\Admin\\Products\\ProductSelect')->bind('admin_products_product_select');
        $controllers->match('/products/upload_csv.php', '\\Eccube\\Page\\Admin\\Products\\UploadCSV')->bind('admin_products_upload_csv');
        $controllers->match('/products/upload_csv_category.php', '\\Eccube\\Page\\Admin\\Products\\UploadCSVCategory')->bind('admin_products_upload_csv_category');

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
        $controllers->match('/system/system.php', '\\Eccube\\Page\\Admin\\System\\System')->bind('admin_system_system');

        // total
        $controllers->match('/total/', '\\Eccube\\Page\\Admin\\Total\\Index')->bind('admin_total');

        return $controllers;
    }
}
