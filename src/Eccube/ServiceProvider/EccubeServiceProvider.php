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


namespace Eccube\ServiceProvider;

use Eccube\Application;
use Silex\Application as BaseApplication;
use Silex\ServiceProviderInterface;

class EccubeServiceProvider implements ServiceProviderInterface
{
    /**
     * Registers services on the given app.
     *
     * This method should only be used to configure services and parameters.
     * It should not get services.
     *
     * @param BaseApplication $app An Application instance
     */
    public function register(BaseApplication $app)
    {
        // Service
        $app['eccube.service.system'] = $app->share(function () use ($app) {
            return new \Eccube\Service\SystemService($app);
        });
        $app['view'] = $app->share(function () use ($app) {
            return $app['twig'];
        });
        $app['eccube.service.cart'] = $app->share(function () use ($app) {
            return new \Eccube\Service\CartService($app);
        });
        $app['eccube.service.order'] = $app->share(function () use ($app) {
            return new \Eccube\Service\OrderService($app);
        });
        $app['eccube.service.tax_rule'] = $app->share(function () use ($app) {
            return new \Eccube\Service\TaxRuleService($app['eccube.repository.tax_rule']);
        });
        $app['eccube.service.plugin'] = $app->share(function () use ($app) {
            return new \Eccube\Service\PluginService($app);
        });
        $app['eccube.service.mail'] = $app->share(function () use ($app) {
            return new \Eccube\Service\MailService($app);
        });
        $app['eccube.service.csv.export'] = $app->share(function () use ($app) {
            $csvService = new \Eccube\Service\CsvExportService();
            $csvService->setEntityManager($app['orm.em']);
            $csvService->setConfig($app['config']);
            $csvService->setCsvRepository($app['eccube.repository.csv']);
            $csvService->setCsvTypeRepository($app['eccube.repository.master.csv_type']);
            $csvService->setOrderRepository($app['eccube.repository.order']);
            $csvService->setCustomerRepository($app['eccube.repository.customer']);
            $csvService->setProductRepository($app['eccube.repository.product']);

            return $csvService;
        });
        $app['eccube.service.shopping'] = $app->share(function () use ($app) {
            return new \Eccube\Service\ShoppingService($app, $app['eccube.service.cart'], $app['eccube.service.order']);
        });

        // Repository
        $app['eccube.repository.master.authority'] = $app->share(function () use ($app) {
            return $app['orm.em']->getRepository('Eccube\Entity\Master\Authority');
        });
        $app['eccube.repository.master.tag'] = $app->share(function () use ($app) {
            return $app['orm.em']->getRepository('Eccube\Entity\Master\Tag');
        });
        $app['eccube.repository.master.pref'] = $app->share(function () use ($app) {
            return $app['orm.em']->getRepository('Eccube\Entity\Master\Pref');
        });
        $app['eccube.repository.master.sex'] = $app->share(function () use ($app) {
            return $app['orm.em']->getRepository('Eccube\Entity\Master\Sex');
        });
        $app['eccube.repository.master.disp'] = $app->share(function () use ($app) {
            return $app['orm.em']->getRepository('Eccube\Entity\Master\Disp');
        });
        $app['eccube.repository.master.product_type'] = $app->share(function () use ($app) {
            return $app['orm.em']->getRepository('Eccube\Entity\Master\ProductType');
        });
        $app['eccube.repository.master.page_max'] = $app->share(function () use ($app) {
            return $app['orm.em']->getRepository('Eccube\Entity\Master\PageMax');
        });
        $app['eccube.repository.master.order_status'] = $app->share(function () use ($app) {
            return $app['orm.em']->getRepository('Eccube\Entity\Master\OrderStatus');
        });
        $app['eccube.repository.master.device_type'] = $app->share(function () use ($app) {
            return $app['orm.em']->getRepository('Eccube\Entity\Master\DeviceType');
        });
        $app['eccube.repository.master.csv_type'] = $app->share(function () use ($app) {
            return $app['orm.em']->getRepository('Eccube\Entity\Master\CsvType');
        });

        $app['eccube.repository.delivery'] = $app->share(function () use ($app) {
            return $app['orm.em']->getRepository('Eccube\Entity\Delivery');
        });
        $app['eccube.repository.delivery_date'] = $app->share(function () use ($app) {
            return $app['orm.em']->getRepository('Eccube\Entity\DeliveryDate');
        });
        $app['eccube.repository.delivery_fee'] = $app->share(function () use ($app) {
            return $app['orm.em']->getRepository('Eccube\Entity\DeliveryFee');
        });
        $app['eccube.repository.delivery_time'] = $app->share(function () use ($app) {
            return $app['orm.em']->getRepository('Eccube\Entity\DeliveryTime');
        });
        $app['eccube.repository.payment'] = $app->share(function () use ($app) {
            return $app['orm.em']->getRepository('Eccube\Entity\Payment');
        });
        $app['eccube.repository.payment_option'] = $app->share(function () use ($app) {
            return $app['orm.em']->getRepository('Eccube\Entity\PaymentOption');
        });
        $app['eccube.repository.category'] = $app->share(function () use ($app) {
            return $app['orm.em']->getRepository('Eccube\Entity\Category');
        });
        $app['eccube.repository.customer'] = $app->share(function () use ($app) {
            return $app['orm.em']->getRepository('Eccube\Entity\Customer');
        });
        $app['eccube.repository.news'] = $app->share(function () use ($app) {
            return $app['orm.em']->getRepository('Eccube\Entity\News');
        });
        $app['eccube.repository.mail_history'] = $app->share(function () use ($app) {
            return $app['orm.em']->getRepository('Eccube\Entity\MailHistory');
        });
        $app['eccube.repository.member'] = $app->share(function () use ($app) {
            $memberRepository = $app['orm.em']->getRepository('Eccube\Entity\Member');
            $memberRepository->setEncoderFactorty($app['security.encoder_factory']);
            return $memberRepository;
        });
        $app['eccube.repository.order'] = $app->share(function () use ($app) {
            return $app['orm.em']->getRepository('Eccube\Entity\Order');
        });
        $app['eccube.repository.product'] = $app->share(function () use ($app) {
            $productRepository = $app['orm.em']->getRepository('Eccube\Entity\Product');
            return $productRepository;
        });
        $app['eccube.repository.product_image'] = $app->share(function () use ($app) {
            return $app['orm.em']->getRepository('Eccube\Entity\ProductImage');
        });
        $app['eccube.repository.product_class'] = $app->share(function () use ($app) {
            return $app['orm.em']->getRepository('Eccube\Entity\ProductClass');
        });
        $app['eccube.repository.product_stock'] = $app->share(function () use ($app) {
            return $app['orm.em']->getRepository('Eccube\Entity\ProductStock');
        });
        $app['eccube.repository.class_name'] = $app->share(function () use ($app) {
            return $app['orm.em']->getRepository('Eccube\Entity\ClassName');
        });
        $app['eccube.repository.class_category'] = $app->share(function () use ($app) {
            return $app['orm.em']->getRepository('Eccube\Entity\ClassCategory');
        });
        $app['eccube.repository.customer_favorite_product'] = $app->share(function () use ($app) {
            return $app['orm.em']->getRepository('Eccube\Entity\CustomerFavoriteProduct');
        });
        $app['eccube.repository.base_info'] = $app->share(function () use ($app) {
            return $app['orm.em']->getRepository('Eccube\Entity\BaseInfo');
        });
        $app['eccube.repository.tax_rule'] = $app->share(function () use ($app) {
            $taxRuleRepository = $app['orm.em']->getRepository('Eccube\Entity\TaxRule');
            $taxRuleRepository->setApplication($app);

            return $taxRuleRepository;
        });
        $app['eccube.repository.page_layout'] = $app->share(function () use ($app) {
            $pageLayoutRepository = $app['orm.em']->getRepository('Eccube\Entity\PageLayout');
            $pageLayoutRepository->setApplication($app);

            return $pageLayoutRepository;
        });
        $app['eccube.repository.block'] = $app->share(function () use ($app) {
            $blockRepository = $app['orm.em']->getRepository('Eccube\Entity\Block');
            $blockRepository->setApplication($app);

            return $blockRepository;
        });
        $app['eccube.repository.order'] = $app->share(function () use ($app) {
            $orderRepository = $app['orm.em']->getRepository('Eccube\Entity\Order');
            $orderRepository->setApplication($app);

            return $orderRepository;
        });
        $app['eccube.repository.customer_address'] = $app->share(function () use ($app) {
            return $app['orm.em']->getRepository('Eccube\Entity\CustomerAddress');
        });
        $app['eccube.repository.shipping'] = $app->share(function () use ($app) {
            return $app['orm.em']->getRepository('Eccube\Entity\Shipping');
        });
        $app['eccube.repository.customer_status'] = $app->share(function () use ($app) {
            return $app['orm.em']->getRepository('Eccube\Entity\Master\CustomerStatus');
        });
        $app['eccube.repository.order_status'] = $app->share(function () use ($app) {
            return $app['orm.em']->getRepository('Eccube\Entity\Master\OrderStatus');
        });
        $app['eccube.repository.mail_template'] = $app->share(function () use ($app) {
            return $app['orm.em']->getRepository('Eccube\Entity\MailTemplate');
        });
        $app['eccube.repository.csv'] = $app->share(function () use ($app) {
            return $app['orm.em']->getRepository('Eccube\Entity\Csv');
        });
        $app['eccube.repository.template'] = $app->share(function () use ($app) {
            return $app['orm.em']->getRepository('Eccube\Entity\Template');
        });
        $app['eccube.repository.authority_role'] = $app->share(function () use ($app) {
            return $app['orm.em']->getRepository('Eccube\Entity\AuthorityRole');
        });

        $app['paginator'] = $app->protect(function () {
            return new \Knp\Component\Pager\Paginator();
        });

        $app['eccube.repository.help'] = $app->share(function () use ($app) {
            return $app['orm.em']->getRepository('Eccube\Entity\Help');
        });
        $app['eccube.repository.plugin'] = $app->share(function () use ($app) {
            return $app['orm.em']->getRepository('Eccube\Entity\Plugin');
        });
        $app['eccube.repository.plugin_event_handler'] = $app->share(function () use ($app) {
            return $app['orm.em']->getRepository('Eccube\Entity\PluginEventHandler');
        });
        // em
        if (isset($app['orm.em'])) {
            $app['orm.em'] = $app->share($app->extend('orm.em', function (\Doctrine\ORM\EntityManager $em, \Silex\Application $app) {
                // tax_rule
                $taxRuleRepository = $em->getRepository('Eccube\Entity\TaxRule');
                $taxRuleRepository->setApplication($app);
                $taxRuleService = new \Eccube\Service\TaxRuleService($taxRuleRepository);
                $em->getEventManager()->addEventSubscriber(new \Eccube\Doctrine\EventSubscriber\TaxRuleEventSubscriber($taxRuleService));

                // save
                $saveEventSubscriber = new \Eccube\Doctrine\EventSubscriber\SaveEventSubscriber($app);
                $em->getEventManager()->addEventSubscriber($saveEventSubscriber);

                // filters
                $config = $em->getConfiguration();
                $config->addFilter("soft_delete", '\Eccube\Doctrine\Filter\SoftDeleteFilter');
                $config->addFilter("nostock_hidden", '\Eccube\Doctrine\Filter\NoStockHiddenFilter');
                $config->addFilter("incomplete_order_status_hidden", '\Eccube\Doctrine\Filter\OrderStatusFilter');
                $em->getFilters()->enable('soft_delete');

                return $em;
            }));
        }

        // Form\Type
        $app['form.type.extensions'] = $app->share($app->extend('form.type.extensions', function ($extensions) use ($app) {
            $extensions[] = new \Eccube\Form\Extension\HelpTypeExtension();
            $extensions[] = new \Eccube\Form\Extension\FreezeTypeExtension();

            return $extensions;
        }));
        $app['form.types'] = $app->share($app->extend('form.types', function ($types) use ($app) {
            $types[] = new \Eccube\Form\Type\NameType($app['config']);
            $types[] = new \Eccube\Form\Type\KanaType($app['config']);
            $types[] = new \Eccube\Form\Type\TelType($app['config']);
            $types[] = new \Eccube\Form\Type\FaxType(); // 削除予定
            $types[] = new \Eccube\Form\Type\ZipType($app['config']);
            $types[] = new \Eccube\Form\Type\AddressType($app['config']);
            $types[] = new \Eccube\Form\Type\RepeatedEmailType();
            $types[] = new \Eccube\Form\Type\RepeatedPasswordType($app['config']);
            $types[] = new \Eccube\Form\Type\PriceType($app['config']);

            $types[] = new \Eccube\Form\Type\MasterType();
            $types[] = new \Eccube\Form\Type\Master\JobType();
            $types[] = new \Eccube\Form\Type\Master\CustomerStatusType();
            $types[] = new \Eccube\Form\Type\Master\OrderStatusType();
            $types[] = new \Eccube\Form\Type\Master\CalcRuleType();
            $types[] = new \Eccube\Form\Type\Master\SexType();
            $types[] = new \Eccube\Form\Type\Master\DispType();
            $types[] = new \Eccube\Form\Type\Master\PrefType();
            $types[] = new \Eccube\Form\Type\Master\ProductTypeType();
            $types[] = new \Eccube\Form\Type\Master\ProductListMaxType();
            $types[] = new \Eccube\Form\Type\Master\ProductListOrderByType();
            $types[] = new \Eccube\Form\Type\Master\PageMaxType();
            $types[] = new \Eccube\Form\Type\Master\CsvType();
            $types[] = new \Eccube\Form\Type\Master\DeliveryDateType();
            $types[] = new \Eccube\Form\Type\Master\PaymentType();
            $types[] = new \Eccube\Form\Type\Master\MailTemplateType();
            $types[] = new \Eccube\Form\Type\Master\CategoryType();
            $types[] = new \Eccube\Form\Type\Master\TagType();

            $types[] = new \Eccube\Form\Type\CustomerType($app); // 削除予定

            if (isset($app['security']) && isset($app['eccube.repository.customer_favorite_product'])) {
                $types[] = new \Eccube\Form\Type\AddCartType($app['config'], $app['security'], $app['eccube.repository.customer_favorite_product']);
            }
            $types[] = new \Eccube\Form\Type\SearchProductType($app);
            $types[] = new \Eccube\Form\Type\OrderSearchType($app);
            $types[] = new \Eccube\Form\Type\ShippingItemType($app);
            $types[] = new \Eccube\Form\Type\ShippingMultipleType($app);
            $types[] = new \Eccube\Form\Type\ShippingMultipleItemType($app);
            $types[] = new \Eccube\Form\Type\ShoppingType();

            // front
            $types[] = new \Eccube\Form\Type\Front\EntryType($app['config']);
            $types[] = new \Eccube\Form\Type\Front\ContactType($app['config']);
            $types[] = new \Eccube\Form\Type\Front\NonMemberType($app['config']);
            $types[] = new \Eccube\Form\Type\Front\ShoppingShippingType();
            $types[] = new \Eccube\Form\Type\Front\CustomerAddressType($app['config']);
            $types[] = new \Eccube\Form\Type\Front\ForgotType();
            $types[] = new \Eccube\Form\Type\Front\CustomerLoginType($app['session']);

            // admin
            $types[] = new \Eccube\Form\Type\Admin\LoginType($app['session']);
            $types[] = new \Eccube\Form\Type\Admin\ProductType($app);
            $types[] = new \Eccube\Form\Type\Admin\ProductClassType($app);
            $types[] = new \Eccube\Form\Type\Admin\SearchProductType($app);
            $types[] = new \Eccube\Form\Type\Admin\SearchCustomerType($app['config']);
            $types[] = new \Eccube\Form\Type\Admin\SearchOrderType($app['config']);
            $types[] = new \Eccube\Form\Type\Admin\CustomerType($app['config']);
            $types[] = new \Eccube\Form\Type\Admin\ClassNameType($app['config']);
            $types[] = new \Eccube\Form\Type\Admin\ClassCategoryType($app['config']);
            $types[] = new \Eccube\Form\Type\Admin\CategoryType($app['config']);
            $types[] = new \Eccube\Form\Type\Admin\MemberType($app['config']);
            $types[] = new \Eccube\Form\Type\Admin\AuthorityRoleType($app['config']);
            $types[] = new \Eccube\Form\Type\Admin\PageLayoutType();
            $types[] = new \Eccube\Form\Type\Admin\NewsType($app['config']);
            $types[] = new \Eccube\Form\Type\Admin\TemplateType($app['config']);
            $types[] = new \Eccube\Form\Type\Admin\SecurityType($app);
            $types[] = new \Eccube\Form\Type\Admin\CsvImportType($app);
            $types[] = new \Eccube\Form\Type\Admin\ShopMasterType($app['config']);
            $types[] = new \Eccube\Form\Type\Admin\TradelawType($app['config']);
            $types[] = new \Eccube\Form\Type\Admin\OrderType($app);
            $types[] = new \Eccube\Form\Type\Admin\OrderDetailType($app);
            $types[] = new \Eccube\Form\Type\Admin\ShippingType($app);
            $types[] = new \Eccube\Form\Type\Admin\ShipmentItemType($app);
            $types[] = new \Eccube\Form\Type\Admin\PaymentRegisterType();
            $types[] = new \Eccube\Form\Type\Admin\TaxRuleType();
            $types[] = new \Eccube\Form\Type\Admin\MainEditType($app);
            $types[] = new \Eccube\Form\Type\Admin\MailType();
            $types[] = new \Eccube\Form\Type\Admin\CustomerAgreementType($app);
            $types[] = new \Eccube\Form\Type\Admin\BlockType($app);
            $types[] = new \Eccube\Form\Type\Admin\DeliveryType();
            $types[] = new \Eccube\Form\Type\Admin\DeliveryFeeType();
            $types[] = new \Eccube\Form\Type\Admin\DeliveryTimeType($app['config']);
            $types[] = new \Eccube\Form\Type\Admin\LogType($app['config']);

            $types[] = new \Eccube\Form\Type\Admin\MasterdataType($app);
            $types[] = new \Eccube\Form\Type\Admin\MasterdataEditType($app);

            $types[] = new \Eccube\Form\Type\Admin\PluginLocalInstallType();
            $types[] = new \Eccube\Form\Type\Admin\PluginManagementType();

            return $types;
        }));
    }

    /**
     * Bootstraps the application.
     *
     * This method is called after all services are registered
     * and should be used for "dynamic" configuration (whenever
     * a service must be requested).
     */
    public function boot(BaseApplication $app)
    {
    }
}
