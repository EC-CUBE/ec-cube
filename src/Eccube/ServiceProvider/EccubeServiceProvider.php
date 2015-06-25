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
            return new \Eccube\Service\ViewService($app);
        });
        $app['eccube.service.cart'] = $app->share(function () use ($app) {
            return new \Eccube\Service\CartService($app['session'], $app['orm.em']);
        });
        $app['eccube.service.order'] = $app->share(function () use ($app) {
            return new \Eccube\Service\OrderService($app);
        });
        $app['eccube.service.tax_rule'] = $app->share(function () use ($app) {
            return new \Eccube\Service\TaxRuleService($app['eccube.repository.tax_rule']);
        });
        $app['eccube.service.plugin'] = $app->share(function () use ($app) {
            return new \Eccube\Service\PluginService( $app );
        });
        $app['eccube.service.mail'] = $app->share(function () use ($app) {
            return new \Eccube\Service\MailService($app);
        });

        // Repository
        $app['eccube.repository.master.constant'] = $app->share(function () use ($app) {
            return $app['orm.em']->getRepository('Eccube\Entity\Master\Constant');
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
        $app['eccube.repository.master.product_status'] = $app->share(function () use ($app) {
            return $app['orm.em']->getRepository('Eccube\Entity\Master\Status');
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

        $app['eccube.repository.delivery'] = $app->share(function () use ($app) {
            return $app['orm.em']->getRepository('Eccube\Entity\Delivery');
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
            $productRepository->setConfig($app['config']);

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
        $app['eccube.repository.maker'] = $app->share(function () use ($app) {
            return $app['orm.em']->getRepository('Eccube\Entity\Maker');
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
            $taxRuleRepository->setApp($app);

            return $taxRuleRepository;
        });
        $app['eccube.repository.page_layout'] = $app->share(function () use ($app) {
            $pageLayoutRepository = $app['orm.em']->getRepository('Eccube\Entity\PageLayout');
            $pageLayoutRepository->setApp($app);

            return $pageLayoutRepository;
        });
        $app['eccube.repository.block'] = $app->share(function () use ($app) {
            $blockRepository = $app['orm.em']->getRepository('Eccube\Entity\Block');
            $blockRepository->setApp($app);

            return $blockRepository;
        });
        $app['eccube.repository.order'] = $app->share(function () use ($app) {
            $orderRepository = $app['orm.em']->getRepository('Eccube\Entity\Order');
            $orderRepository->setConfig($app['config']);

            return $orderRepository;
        });
        $app['eccube.repository.customer_address'] = $app->share(function () use ($app) {
            return $app['orm.em']->getRepository('Eccube\Entity\CustomerAddress');
        });
        $app['eccube.repository.order_status'] = $app->share(function () use ($app) {
            return $app['orm.em']->getRepository('Eccube\Entity\Master\OrderStatus');
        });
        $app['eccube.repository.recommend_product'] = $app->share(function () use ($app) {
            $recommendRepository = $app['orm.em']->getRepository('Eccube\Entity\RecommendProduct');
            $recommendRepository->setApp($app);

            return $recommendRepository;
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
                $taxRuleRepository->setApp($app);
                $taxRuleService = new \Eccube\Service\TaxRuleService($taxRuleRepository);
                $em->getEventManager()->addEventSubscriber(new \Eccube\Doctrine\EventSubscriber\TaxRuleEventSubscriber($taxRuleService));

                // save
                $saveEventSubscriber = new \Eccube\Doctrine\EventSubscriber\SaveEventSubscriber($app);
                $em->getEventManager()->addEventSubscriber($saveEventSubscriber);

                //
                $config = $em->getConfiguration();
                $config->addFilter("soft_delete", "\Eccube\Doctrine\Filter\SoftDeleteFilter");
                $config->addFilter("nostock_hidden", "\Eccube\Doctrine\Filter\NoStockHiddenFilter");
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
            $types[] = new \Eccube\Form\Type\NameType();
            $types[] = new \Eccube\Form\Type\TelType();
            $types[] = new \Eccube\Form\Type\FaxType();
            $types[] = new \Eccube\Form\Type\AddressType();
            $types[] = new \Eccube\Form\Type\PaymentType();
            $types[] = new \Eccube\Form\Type\DeliveryType();
            $types[] = new \Eccube\Form\Type\DeliveryDateType();
            $types[] = new \Eccube\Form\Type\DeliveryFeeType();
            $types[] = new \Eccube\Form\Type\DeliveryTimeType();
            $types[] = new \Eccube\Form\Type\ProductTypeType();
            $types[] = new \Eccube\Form\Type\PaymentRegisterType();
            $types[] = new \Eccube\Form\Type\MailType();
            $types[] = new \Eccube\Form\Type\MailTemplateType();
            $types[] = new \Eccube\Form\Type\CategoryType();
            $types[] = new \Eccube\Form\Type\MakerType();

            $types[] = new \Eccube\Form\Type\Master\JobType();
            $types[] = new \Eccube\Form\Type\Master\ReminderType();
            $types[] = new \Eccube\Form\Type\Master\MailMagazineType();
            $types[] = new \Eccube\Form\Type\Master\CustomerStatusType();
            $types[] = new \Eccube\Form\Type\Master\OrderStatusType();
            $types[] = new \Eccube\Form\Type\Master\CalcRuleType();
            $types[] = new \Eccube\Form\Type\Master\SexType();
            $types[] = new \Eccube\Form\Type\Master\DispType();
            $types[] = new \Eccube\Form\Type\Master\StatusType();
            $types[] = new \Eccube\Form\Type\Master\PrefType();
            $types[] = new \Eccube\Form\Type\Master\ZipType();
            $types[] = new \Eccube\Form\Type\Master\ProductTypeType();
            $types[] = new \Eccube\Form\Type\Master\ProductListMaxType();
            $types[] = new \Eccube\Form\Type\Master\ProductListOrderByType();
            $types[] = new \Eccube\Form\Type\Master\PageMaxType();

            $types[] = new \Eccube\Form\Type\EntryType($app);
            $types[] = new \Eccube\Form\Type\CustomerType($app);
            if (isset($app['security']) && isset($app['eccube.repository.customer_favorite_product'])) {
                $types[] = new \Eccube\Form\Type\AddCartType($app['config'], $app['security'], $app['eccube.repository.customer_favorite_product']);
            }
            $types[] = new \Eccube\Form\Type\SearchProductType();
            $types[] = new \Eccube\Form\Type\CustomerLoginType($app['session']);
            $types[] = new \Eccube\Form\Type\CustomerAddressType($app['config']);
            $types[] = new \Eccube\Form\Type\ContactType($app['config']);
            $types[] = new \Eccube\Form\Type\ShopMasterType($app['config']);
            $types[] = new \Eccube\Form\Type\PointType($app);
            $types[] = new \Eccube\Form\Type\TradelawType($app);
            $types[] = new \Eccube\Form\Type\TaxRuleType();
            $types[] = new \Eccube\Form\Type\MainEditType($app);
            $types[] = new \Eccube\Form\Type\BlockType($app);
            $types[] = new \Eccube\Form\Type\OrderSearchType($app);
            $types[] = new \Eccube\Form\Type\ShoppingType($app);
            $types[] = new \Eccube\Form\Type\NonMemberType($app);
            $types[] = new \Eccube\Form\Type\ShippingMultiType($app);
            $types[] = new \Eccube\Form\Type\OrderType($app);
            $types[] = new \Eccube\Form\Type\OrderDetailType($app);
            $types[] = new \Eccube\Form\Type\ShippingType($app);
            $types[] = new \Eccube\Form\Type\ShoppingShippingType($app);
            $types[] = new \Eccube\Form\Type\ShipmentItemType();
            $types[] = new \Eccube\Form\Type\CustomerAgreementType($app);
            $types[] = new \Eccube\Form\Type\ForgotType();

            // admin
            $types[] = new \Eccube\Form\Type\Admin\LoginType($app['session']);
            $types[] = new \Eccube\Form\Type\Admin\ProductType($app);
            $types[] = new \Eccube\Form\Type\Admin\ProductClassType($app);
            $types[] = new \Eccube\Form\Type\Admin\SearchProductType($app);
            $types[] = new \Eccube\Form\Type\Admin\SearchCustomerType($app['config']);
            $types[] = new \Eccube\Form\Type\Admin\SearchOrderType($app['config']);
            $types[] = new \Eccube\Form\Type\Admin\CustomerType($app['config']);
            $types[] = new \Eccube\Form\Type\Admin\MakerType($app);
            $types[] = new \Eccube\Form\Type\Admin\ClassNameType($app['config']);
            $types[] = new \Eccube\Form\Type\Admin\ClassCategoryType($app['config']);
            $types[] = new \Eccube\Form\Type\Admin\CategoryType($app['config']);
            $types[] = new \Eccube\Form\Type\Admin\MemberType($app['config']);
            $types[] = new \Eccube\Form\Type\Admin\PageLayoutType();
            $types[] = new \Eccube\Form\Type\Admin\NewsType($app['config']);
            $types[] = new \Eccube\Form\Type\Admin\TemplateType($app['config']);
            $types[] = new \Eccube\Form\Type\Admin\SecurityType($app['config']);

            $types[] = new \Eccube\Form\Type\Admin\PluginLocalInstallType();
            $types[] = new \Eccube\Form\Type\Admin\PluginManagementType();

            $types[] = new \Eccube\Form\Type\Install\Step1Type($app);
            $types[] = new \Eccube\Form\Type\Install\Step3Type($app);
            $types[] = new \Eccube\Form\Type\Install\Step4Type($app);
            $types[] = new \Eccube\Form\Type\Install\Step5Type($app);

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
