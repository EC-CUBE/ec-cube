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

        // shop
//        $c->match('/setting/shop', '\Eccube\Controller\Admin\Setting\Shop\ShopController::index')->bind('admin_setting_shop');

        // delivery
//        $c->match('/setting/shop/delivery', '\Eccube\Controller\Admin\Setting\Shop\DeliveryController::index')->bind('admin_setting_shop_delivery');
//        $c->match('/setting/shop/delivery/new', '\Eccube\Controller\Admin\Setting\Shop\DeliveryController::edit')->bind('admin_setting_shop_delivery_new');
//        $c->match('/setting/shop/delivery/{id}/edit', '\Eccube\Controller\Admin\Setting\Shop\DeliveryController::edit')->assert('id', '\d+')->bind('admin_setting_shop_delivery_edit');
//        $c->delete('/setting/shop/delivery/{id}/delete', '\Eccube\Controller\Admin\Setting\Shop\DeliveryController::delete')->assert('id', '\d+')->bind('admin_setting_shop_delivery_delete');
        //$c->post('/setting/shop/delivery/rank/move', '\Eccube\Controller\Admin\Setting\Shop\DeliveryController::moveRank')->bind('admin_setting_shop_delivery_rank_move');

        // payment
        //$c->match('/setting/shop/payment', '\Eccube\Controller\Admin\Setting\Shop\PaymentController::index')->bind('admin_setting_shop_payment');
        //$c->match('/setting/shop/payment/new', '\Eccube\Controller\Admin\Setting\Shop\PaymentController::edit')->bind('admin_setting_shop_payment_new');
        //$c->match('/setting/shop/payment/image/add', '\Eccube\Controller\Admin\Setting\Shop\PaymentController::imageAdd')->bind('admin_payment_image_add');
        //$c->match('/setting/shop/payment/{id}/edit', '\Eccube\Controller\Admin\Setting\Shop\PaymentController::edit')->assert('id', '\d+')->bind('admin_setting_shop_payment_edit');
//        $c->delete('/setting/shop/payment/{id}/delete', '\Eccube\Controller\Admin\Setting\Shop\PaymentController::delete')->assert('id', '\d+')->bind('admin_setting_shop_payment_delete');
        //$c->put('/setting/shop/payment/{id}/up', '\Eccube\Controller\Admin\Setting\Shop\PaymentController::up')->assert('id', '\d+')->bind('admin_setting_shop_payment_up');
        //$c->put('/setting/shop/payment/{id}/down', '\Eccube\Controller\Admin\Setting\Shop\PaymentController::down')->assert('id', '\d+')->bind('admin_setting_shop_payment_down');

        // tradelaw
        //$c->match('/setting/shop/tradelaw', '\Eccube\Controller\Admin\Setting\Shop\TradelawController::index')->bind('admin_setting_shop_tradelaw');

        // tax
        //$c->match('/setting/shop/tax', '\Eccube\Controller\Admin\Setting\Shop\TaxRuleController::index')->bind('admin_setting_shop_tax');
        //$c->match('/setting/shop/tax/new', '\Eccube\Controller\Admin\Setting\Shop\TaxRuleController::index')->assert('id', '\d+')->bind('admin_setting_shop_tax_new');
        //$c->match('/setting/shop/tax/{id}/edit', '\Eccube\Controller\Admin\Setting\Shop\TaxRuleController::index')->assert('id', '\d+')->bind('admin_setting_shop_tax_edit');
        //$c->delete('/setting/shop/tax/{id}/delete', '\Eccube\Controller\Admin\Setting\Shop\TaxRuleController::delete')->assert('id', '\d+')->bind('admin_setting_shop_tax_delete');
        //$c->match('/setting/shop/tax/edit_param', '\Eccube\Controller\Admin\Setting\Shop\TaxRuleController::editParameter')->assert('id', '\d+')->bind('admin_setting_shop_tax_edit_param');

        // mail
        //$c->match('/setting/shop/mail', '\Eccube\Controller\Admin\Setting\Shop\MailController::index')->bind('admin_setting_shop_mail');
        //$c->match('/setting/shop/mail/{id}', '\Eccube\Controller\Admin\Setting\Shop\MailController::index')->assert('id', '\d+')->bind('admin_setting_shop_mail_edit');

        // customer_agreement
        //$c->match('/setting/shop/customer_agreement', '\Eccube\Controller\Admin\Setting\Shop\CustomerAgreementController::index')->bind('admin_setting_shop_customer_agreement');

        // csv
        //$c->match('/setting/shop/csv/{id}', '\Eccube\Controller\Admin\Setting\Shop\CsvController::index')->assert('id', '\d+')->value('id', CsvType::CSV_TYPE_ORDER)->bind('admin_setting_shop_csv');

        // setting/system
        //$c->match('/setting/system/system', '\Eccube\Controller\Admin\Setting\System\SystemController::index')->bind('admin_setting_system_system');
        // system/member
        //$c->match('/setting/system/member', '\Eccube\Controller\Admin\Setting\System\MemberController::index')->bind('admin_setting_system_member');
        //$c->match('/setting/system/member/new', '\Eccube\Controller\Admin\Setting\System\MemberController::edit')->bind('admin_setting_system_member_new');
        //$c->match('/setting/system/member/{id}/edit', 'Eccube\Controller\Admin\Setting\System\MemberController::edit')->assert('id', '\d+')->bind('admin_setting_system_member_edit');
        //$c->delete('/setting/system/member/{id}/delete', '\Eccube\Controller\Admin\Setting\System\MemberController::delete')->assert('id', '\d+')->bind('admin_setting_system_member_delete');
        //$c->put('/setting/system/member/{id}/up', '\Eccube\Controller\Admin\Setting\System\MemberController::up')->assert('id', '\d+')->bind('admin_setting_system_member_up');
        //$c->put('/setting/system/member/{id}/down', '\Eccube\Controller\Admin\Setting\System\MemberController::down')->assert('id', '\d+')->bind('admin_setting_system_member_down');
        // system/authority
        //$c->match('/setting/system/authority', '\Eccube\Controller\Admin\Setting\System\AuthorityController::index')->bind('admin_setting_system_authority');
        // system/security
        //$c->match('/setting/system/security', '\Eccube\Controller\Admin\Setting\System\SecurityController::index')->bind('admin_setting_system_security');
        // system/log
        //$c->match('/setting/system/log', '\Eccube\Controller\Admin\Setting\System\LogController::index')->bind('admin_setting_system_log');

        // system/masterdata
        //$c->match('/setting/system/masterdata', '\Eccube\Controller\Admin\Setting\System\MasterdataController::index')->bind('admin_setting_system_masterdata');
        //$c->match('/setting/system/masterdata/{entity}/edit', '\Eccube\Controller\Admin\Setting\System\MasterdataController::index')->bind('admin_setting_system_masterdata_view');
        //$c->match('/setting/system/masterdata//xxx/edit', '\Eccube\Controller\Admin\Setting\System\MasterdataController::edit')->bind('admin_setting_system_masterdata_edit');

        // store
        //$c->match('/store/template', '\Eccube\Controller\Admin\Store\TemplateController::index')->bind('admin_store_template');
        //$c->match('/store/template/install', '\Eccube\Controller\Admin\Store\TemplateController::add')->bind('admin_store_template_install');
        //$c->match('/store/template/{id}/download', '\Eccube\Controller\Admin\Store\TemplateController::download')->assert('id', '\d+')->bind('admin_store_template_download');
        //$c->delete('/store/template/{id}/delete', '\Eccube\Controller\Admin\Store\TemplateController::delete')->assert('id', '\d+')->bind('admin_store_template_delete');
        //$c->match('/store/plugin', '\Eccube\Controller\Admin\Store\PluginController::index')->bind('admin_store_plugin');
        //$c->match('/store/plugin/owners_install', '\Eccube\Controller\Admin\Store\PluginController::ownersInstall')->bind('admin_store_plugin_owners_install');
        //$c->match('/store/plugin/install', '\Eccube\Controller\Admin\Store\PluginController::install')->bind('admin_store_plugin_install');
        //$c->match('/store/plugin/upgrade/{action}/{id}/{version}', '\Eccube\Controller\Admin\Store\PluginController::upgrade')->assert('id', '\d+')->bind('admin_store_plugin_upgrade');
        //$c->match('/store/plugin/handler', '\Eccube\Controller\Admin\Store\PluginController::handler')->bind('admin_store_plugin_handler');
        //$c->put('/store/plugin/{id}/enable', '\Eccube\Controller\Admin\Store\PluginController::enable')->assert('id', '\d+')->bind('admin_store_plugin_enable');
        //$c->put('/store/plugin/{id}/disable', '\Eccube\Controller\Admin\Store\PluginController::disable')->assert('id', '\d+')->bind('admin_store_plugin_disable');
        //$c->post('/store/plugin/{id}/update', '\Eccube\Controller\Admin\Store\PluginController::update')->assert('id', '\d+')->bind('admin_store_plugin_update');
        //$c->delete('/store/plugin/{id}/uninstall', '\Eccube\Controller\Admin\Store\PluginController::uninstall')->assert('id', '\d+')->bind('admin_store_plugin_uninstall');
        //$c->match('/store/plugin/handler_up/{handlerId}', '\Eccube\Controller\Admin\Store\PluginController::handler_up')->bind('admin_store_plugin_handler_up');
        //$c->match('/store/plugin/handler_down/{handlerId}', '\Eccube\Controller\Admin\Store\PluginController::handler_down')->bind('admin_store_plugin_handler_down');
        //$c->match('/store/plugin/authentication_setting', '\Eccube\Controller\Admin\Store\PluginController::authenticationSetting')->bind('admin_store_authentication_setting');

        return $c;
    }
}
