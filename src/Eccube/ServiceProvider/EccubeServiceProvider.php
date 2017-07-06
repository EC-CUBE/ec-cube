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

use Eccube\EventListener\TransactionListener;
use Eccube\Service\OrderHelper;
use Eccube\Service\PurchaseFlow\Processor\DeliveryFeeProcessor;
use Eccube\Service\PurchaseFlow\Processor\PaymentTotalLimitValidator;
use Eccube\Service\PurchaseFlow\Processor\StockValidator;
use Eccube\Service\PurchaseFlow\PurchaseFlow;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Silex\Api\EventListenerProviderInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\ParameterBag;

class EccubeServiceProvider implements ServiceProviderInterface, EventListenerProviderInterface
{
    /**
     * Registers services on the given app.
     *
     * This method should only be used to configure services and parameters.
     * It should not get services.
     *
     * @param BaseApplication $app An Application instance
     */
    public function register(Container $app)
    {
        // Service
        $app['eccube.service.system'] = function () use ($app) {
            return new \Eccube\Service\SystemService($app);
        };
        $app['view'] = function () use ($app) {
            return $app['twig'];
        };
        $app['eccube.service.cart'] = function () use ($app) {
            return new \Eccube\Service\CartService($app);
        };
        $app['eccube.service.order'] = function () use ($app) {
            return new \Eccube\Service\OrderService($app);
        };
        $app['eccube.service.tax_rule'] = function () use ($app) {
            return new \Eccube\Service\TaxRuleService($app['eccube.repository.tax_rule']);
        };
        $app['eccube.service.plugin'] = function () use ($app) {
            return new \Eccube\Service\PluginService($app);
        };
        $app['eccube.service.mail'] = function () use ($app) {
            return new \Eccube\Service\MailService($app);
        };
        $app['eccube.calculate.context'] = function () use ($app) {
                return new \Eccube\Service\Calculator\CalculateContext();
        };
        $app['eccube.service.calculate'] = $app->protect(function ($Order, $Customer) use ($app) {
                $Service = new \Eccube\Service\CalculateService($Order, $Customer);
                $Context = $app['eccube.calculate.context'];
                $app['eccube.calculate.strategies']->setOrder($Order);
                $Context->setCalculateStrategies($app['eccube.calculate.strategies']);
                $Context->setOrder($Order);
                $Service->setContext($Context);
                return $Service;
        });

        $app['eccube.service.payment'] = $app->protect(function ($clazz) use ($app) {
                $Service = new $clazz;
                $Service->setApplication($app);
                return $Service;
        });

        $app['eccube.calculate.strategies'] = function () use ($app) {
            $Collection = new \Eccube\Service\Calculator\CalculateStrategyCollection();
            $Collection->setApplication($app);
            //$Collection->setOrder($Order);
            // デフォルトのストラテジーをセットしておく
            $Collection->add($app['eccube.calculate.strategy.shipping']);
            $Collection->add($app['eccube.calculate.strategy.charge']);
            $Collection->add($app['eccube.calculate.strategy.tax']);
            $Collection->add($app['eccube.calculate.strategy.calculate_delivery_fee']);
            $Collection->add($app['eccube.calculate.strategy.calculate_charge']);
            $Collection->add($app['eccube.calculate.strategy.calculate_total']);
            return $Collection;
        };
        $app['eccube.calculate.strategy.shipping'] = function () use ($app) {
                $Strategy = new \Eccube\Service\Calculator\Strategy\ShippingStrategy();
                $Strategy->setApplication($app);
                return $Strategy;
        };
        $app['eccube.calculate.strategy.charge'] = function () use ($app) {
                $Strategy = new \Eccube\Service\Calculator\Strategy\ChargeStrategy();
                $Strategy->setApplication($app);
                return $Strategy;
        };

        $app['eccube.calculate.strategy.tax'] = function () use ($app) {
                $Strategy = new \Eccube\Service\Calculator\Strategy\TaxStrategy();
                $Strategy->setApplication($app);
                return $Strategy;
        };

        $app['eccube.calculate.strategy.calculate_delivery_fee'] = function () use ($app) {
            $Strategy = new \Eccube\Service\Calculator\Strategy\CalculateDeliveryFeeStrategy();
            $Strategy->setApplication($app);
            return $Strategy;
        };
        $app['eccube.calculate.strategy.calculate_charge'] = function () use ($app) {
            $Strategy = new \Eccube\Service\Calculator\Strategy\CalculateChargeStrategy();
            $Strategy->setApplication($app);
            return $Strategy;
        };
        $app['eccube.calculate.strategy.calculate_total'] = function () use ($app) {
            $Strategy = new \Eccube\Service\Calculator\Strategy\CalculateTotalStrategy();
            $Strategy->setApplication($app);
            return $Strategy;
        };

        $app['payment.method'] = $app->protect(function ($clazz, $form) use ($app) {
                $PaymentMethod = new $clazz;
                $PaymentMethod->setApplication($app);
                $PaymentMethod->setFormType($form);
                return $PaymentMethod;
        });

        $app['payment.method.request'] = $app->protect(function ($clazz, $form, $request) use ($app) {
                $PaymentMethod = new $clazz;
                $PaymentMethod->setApplication($app);
                $PaymentMethod->setFormType($form);
                $PaymentMethod->setRequest($request);
                return $PaymentMethod;
        });

        $app['eccube.helper.order'] = function ($app) {
            return new OrderHelper($app);
        };

        $app['eccube.service.csv.export'] = function () use ($app) {
            $csvService = new \Eccube\Service\CsvExportService();
            $csvService->setEntityManager($app['orm.em']);
            $csvService->setConfig($app['config']);
            $csvService->setCsvRepository($app['eccube.repository.csv']);
            $csvService->setCsvTypeRepository($app['eccube.repository.master.csv_type']);
            $csvService->setOrderRepository($app['eccube.repository.order']);
            $csvService->setCustomerRepository($app['eccube.repository.customer']);
            $csvService->setProductRepository($app['eccube.repository.product']);

            return $csvService;
        };
        $app['eccube.service.shopping'] = function () use ($app) {
            return new \Eccube\Service\ShoppingService($app, $app['eccube.service.cart'], $app['eccube.service.order']);
        };

        // Repository
        $app['eccube.repository.master.authority'] = function () use ($app) {
            return $app['orm.em']->getRepository('Eccube\Entity\Master\Authority');
        };
        $app['eccube.repository.master.tag'] = function () use ($app) {
            return $app['orm.em']->getRepository('Eccube\Entity\Master\Tag');
        };
        $app['eccube.repository.master.pref'] = function () use ($app) {
            return $app['orm.em']->getRepository('Eccube\Entity\Master\Pref');
        };
        $app['eccube.repository.master.sex'] = function () use ($app) {
            return $app['orm.em']->getRepository('Eccube\Entity\Master\Sex');
        };
        $app['eccube.repository.master.disp'] = function () use ($app) {
            return $app['orm.em']->getRepository('Eccube\Entity\Master\Disp');
        };
        $app['eccube.repository.master.product_type'] = function () use ($app) {
            return $app['orm.em']->getRepository('Eccube\Entity\Master\ProductType');
        };
        $app['eccube.repository.master.page_max'] = function () use ($app) {
            return $app['orm.em']->getRepository('Eccube\Entity\Master\PageMax');
        };
        $app['eccube.repository.master.order_status'] = function () use ($app) {
        };
        $app['eccube.repository.master.product_list_max'] = function () use ($app) {
            return $app['orm.em']->getRepository('Eccube\Entity\Master\ProductListMax');
        };
        $app['eccube.repository.master.product_list_order_by'] = function () use ($app) {
            return $app['orm.em']->getRepository('Eccube\Entity\Master\ProductListOrderBy');
        };
        $app['eccube.repository.master.order_status'] = function () use ($app) {
            return $app['orm.em']->getRepository('Eccube\Entity\Master\OrderStatus');
        };
        $app['eccube.repository.master.device_type'] = function () use ($app) {
            return $app['orm.em']->getRepository('Eccube\Entity\Master\DeviceType');
        };
        $app['eccube.repository.master.csv_type'] = function () use ($app) {
            return $app['orm.em']->getRepository('Eccube\Entity\Master\CsvType');
        };
        $app['eccube.repository.master.order_item_type'] = function () use ($app) {
            return $app['orm.em']->getRepository('Eccube\Entity\Master\OrderItemType');
        };

        $app['eccube.repository.delivery'] = function () use ($app) {
            return $app['orm.em']->getRepository('Eccube\Entity\Delivery');
        };
        $app['eccube.repository.delivery_date'] = function () use ($app) {
            return $app['orm.em']->getRepository('Eccube\Entity\DeliveryDate');
        };
        $app['eccube.repository.delivery_fee'] = function () use ($app) {
            return $app['orm.em']->getRepository('Eccube\Entity\DeliveryFee');
        };
        $app['eccube.repository.delivery_time'] = function () use ($app) {
            return $app['orm.em']->getRepository('Eccube\Entity\DeliveryTime');
        };
        $app['eccube.repository.payment'] = function () use ($app) {
            return $app['orm.em']->getRepository('Eccube\Entity\Payment');
        };
        $app['eccube.repository.payment_option'] = function () use ($app) {
            return $app['orm.em']->getRepository('Eccube\Entity\PaymentOption');
        };
        $app['eccube.repository.category'] = function () use ($app) {
            $CategoryRepository = $app['orm.em']->getRepository('Eccube\Entity\Category');
            $CategoryRepository->setApplication($app);

            return $CategoryRepository;
        };
        $app['eccube.repository.customer'] = function () use ($app) {
            $customerRepository = $app['orm.em']->getRepository('Eccube\Entity\Customer');
            $customerRepository->setApplication($app);
            return $customerRepository;
        };
        $app['eccube.repository.news'] = function () use ($app) {
            return $app['orm.em']->getRepository('Eccube\Entity\News');
        };
        $app['eccube.repository.mail_history'] = function () use ($app) {
            return $app['orm.em']->getRepository('Eccube\Entity\MailHistory');
        };
        $app['eccube.repository.member'] = function () use ($app) {
            $memberRepository = $app['orm.em']->getRepository('Eccube\Entity\Member');
            $memberRepository->setEncoderFactorty($app['security.encoder_factory']);
            return $memberRepository;
        };
        $app['eccube.repository.order'] = function () use ($app) {
            return $app['orm.em']->getRepository('Eccube\Entity\Order');
        };
        $app['eccube.repository.product'] = function () use ($app) {
            $productRepository = $app['orm.em']->getRepository('Eccube\Entity\Product');
            $productRepository->setApplication($app);

            return $productRepository;
        };
        $app['eccube.repository.product_image'] = function () use ($app) {
            return $app['orm.em']->getRepository('Eccube\Entity\ProductImage');
        };
        $app['eccube.repository.product_class'] = function () use ($app) {
            return $app['orm.em']->getRepository('Eccube\Entity\ProductClass');
        };
        $app['eccube.repository.product_stock'] = function () use ($app) {
            return $app['orm.em']->getRepository('Eccube\Entity\ProductStock');
        };
        $app['eccube.repository.product_tag'] = function () use ($app) {
            return $app['orm.em']->getRepository('Eccube\Entity\ProductTag');
        };
        $app['eccube.repository.class_name'] = function () use ($app) {
            return $app['orm.em']->getRepository('Eccube\Entity\ClassName');
        };
        $app['eccube.repository.class_category'] = function () use ($app) {
            return $app['orm.em']->getRepository('Eccube\Entity\ClassCategory');
        };
        $app['eccube.repository.customer_favorite_product'] = function () use ($app) {
            return $app['orm.em']->getRepository('Eccube\Entity\CustomerFavoriteProduct');
        };
        $app['eccube.repository.base_info'] = function () use ($app) {
            $BaseInfoRepository = $app['orm.em']->getRepository('Eccube\Entity\BaseInfo');
            $BaseInfoRepository->setApplication($app);

            return $BaseInfoRepository;
        };
        $app['eccube.repository.tax_rule'] = function () use ($app) {
            $taxRuleRepository = $app['orm.em']->getRepository('Eccube\Entity\TaxRule');
            $taxRuleRepository->setApplication($app);

            return $taxRuleRepository;
        };
        $app['eccube.repository.page_layout'] = function () use ($app) {
            $pageLayoutRepository = $app['orm.em']->getRepository('Eccube\Entity\PageLayout');
            $pageLayoutRepository->setApplication($app);

            return $pageLayoutRepository;
        };
        $app['eccube.repository.block'] = function () use ($app) {
            $blockRepository = $app['orm.em']->getRepository('Eccube\Entity\Block');
            $blockRepository->setApplication($app);

            return $blockRepository;
        };
        $app['eccube.repository.order'] = function () use ($app) {
            $orderRepository = $app['orm.em']->getRepository('Eccube\Entity\Order');
            $orderRepository->setApplication($app);

            return $orderRepository;
        };
        $app['eccube.repository.customer_address'] = function () use ($app) {
            return $app['orm.em']->getRepository('Eccube\Entity\CustomerAddress');
        };
        $app['eccube.repository.shipping'] = function () use ($app) {
            return $app['orm.em']->getRepository('Eccube\Entity\Shipping');
        };
        $app['eccube.repository.shipment_item'] = function () use ($app) {
            return $app['orm.em']->getRepository('Eccube\Entity\ShipmentItem');
        };
        $app['eccube.repository.customer_status'] = function () use ($app) {
            return $app['orm.em']->getRepository('Eccube\Entity\Master\CustomerStatus');
        };
        $app['eccube.repository.order_status'] = function () use ($app) {
            return $app['orm.em']->getRepository('Eccube\Entity\Master\OrderStatus');
        };
        $app['eccube.repository.mail_template'] = function () use ($app) {
            return $app['orm.em']->getRepository('Eccube\Entity\MailTemplate');
        };
        $app['eccube.repository.csv'] = function () use ($app) {
            return $app['orm.em']->getRepository('Eccube\Entity\Csv');
        };
        $app['eccube.repository.template'] = function () use ($app) {
            return $app['orm.em']->getRepository('Eccube\Entity\Template');
        };
        $app['eccube.repository.authority_role'] = function () use ($app) {
            return $app['orm.em']->getRepository('Eccube\Entity\AuthorityRole');
        };

        $app['paginator'] = $app->protect(function () {
            $paginator = new \Knp\Component\Pager\Paginator();
            $paginator->subscribe(new \Eccube\EventListener\PaginatorListener());

            return $paginator;
        });

        $app['eccube.repository.help'] = function () use ($app) {
            return $app['orm.em']->getRepository('Eccube\Entity\Help');
        };
        $app['eccube.repository.plugin'] = function () use ($app) {
            return $app['orm.em']->getRepository('Eccube\Entity\Plugin');
        };
        $app['eccube.repository.plugin_event_handler'] = function () use ($app) {
            return $app['orm.em']->getRepository('Eccube\Entity\PluginEventHandler');
        };
        $app['eccube.repository.layout'] = function () use ($app) {
            return $app['orm.em']->getRepository('Eccube\Entity\Layout');
        };

        $app['request_scope'] = function () {
            return new ParameterBag();
        };
        // TODO 使用するか検討
        $app['eccube.twig.node.hello'] = $app->protect(function ($node, $compiler) {
            $compiler
            ->addDebugInfo($node)
            ->write("echo 'Helloooooo ' . ")
            ->subcompile($node->getNode('expr'))
            ->raw(" . '!';\n")
            ;

        });
        // TODO 使用するか検討
        $app['eccube.twig.node.jiro'] = $app->protect(function ($node, $compiler) {
            $compiler
            ->addDebugInfo($node)
            ->write("echo 'jirooooooo ' . ")
            ->subcompile($node->getNode('expr'))
            ->raw(" . '!';\n")
            ;

        });

        // TODO 使用するか検討
        $app['eccube.twig.generic_node_names'] = function () use ($app) {
            return [
                'hello',
                'jiro',
                'bbb'
            ];
        };

        // TODO 使用するか検討
        $app['twig_parsers'] = function () use ($app) {
            $GenericTokenParsers = [];
            foreach ($app['eccube.twig.generic_node_names'] as $tagName) {
                $GenericTokenParsers[] = new \Eccube\Twig\Extension\GenericTokenParser($app, $tagName);
            }
            return $GenericTokenParsers;
        };

        // TODO ServiceProvider から追加できるよう Collection にする
        $app['eccube.twig.block.templates'] = function () {
            return [
                'render_block.twig',
            ];
        };

        // Form\Type
        $app->extend('form.type.extensions', function ($extensions) use ($app) {
            $extensions[] = new \Eccube\Form\Extension\HelpTypeExtension();
            $extensions[] = new \Eccube\Form\Extension\FreezeTypeExtension();
            $extensions[] = new \Eccube\Form\Extension\DoctrineOrmExtension($app['orm.em']);
            return $extensions;
        });
        $app->extend('form.types', function ($types) use ($app) {
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

            if (isset($app['security.token_storage']) && isset($app['eccube.repository.customer_favorite_product'])) {
                $types[] = new \Eccube\Form\Type\AddCartType($app['config'], $app['eccube.repository.customer_favorite_product']);
            }
            $types[] = new \Eccube\Form\Type\SearchProductType($app);
            $types[] = new \Eccube\Form\Type\SearchProductBlockType($app);
            $types[] = new \Eccube\Form\Type\OrderSearchType($app);
            $types[] = new \Eccube\Form\Type\ShippingItemType($app);
            $types[] = new \Eccube\Form\Type\ShippingMultipleType($app);
            $types[] = new \Eccube\Form\Type\ShippingMultipleItemType($app);
            $types[] = new \Eccube\Form\Type\ShoppingType($app);

            $types[] = new \Eccube\Form\Type\Shopping\OrderType($app, $app['eccube.repository.order'], $app['eccube.repository.delivery']);
            $types[] = new \Eccube\Form\Type\Shopping\ShippingType($app);
            $types[] = new \Eccube\Form\Type\Shopping\ShipmentItemType($app);

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
            $types[] = new \Eccube\Form\Type\Admin\ChangePasswordType($app);
            $types[] = new \Eccube\Form\Type\Admin\ProductType($app);
            $types[] = new \Eccube\Form\Type\Admin\ProductClassType($app);
            $types[] = new \Eccube\Form\Type\Admin\SearchProductType($app);
            $types[] = new \Eccube\Form\Type\Admin\SearchCustomerType($app['config']);
            $types[] = new \Eccube\Form\Type\Admin\SearchOrderType($app['config']);
            $types[] = new \Eccube\Form\Type\Admin\SearchShippingType($app['config']);
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
            $types[] = new \Eccube\Form\Type\Admin\PaymentRegisterType($app);
            $types[] = new \Eccube\Form\Type\Admin\TaxRuleType();
            $types[] = new \Eccube\Form\Type\Admin\MainEditType($app);
            $types[] = new \Eccube\Form\Type\Admin\MailType();
            $types[] = new \Eccube\Form\Type\Admin\CustomerAgreementType($app);
            $types[] = new \Eccube\Form\Type\Admin\BlockType($app);
            $types[] = new \Eccube\Form\Type\Admin\DeliveryType();
            $types[] = new \Eccube\Form\Type\Admin\DeliveryFeeType();
            $types[] = new \Eccube\Form\Type\Admin\DeliveryTimeType($app['config']);
            $types[] = new \Eccube\Form\Type\Admin\LogType($app['config']);
            $types[] = new \Eccube\Form\Type\Admin\CacheType($app['config']);

            $types[] = new \Eccube\Form\Type\Admin\MasterdataType($app);
            $types[] = new \Eccube\Form\Type\Admin\MasterdataDataType($app);
            $types[] = new \Eccube\Form\Type\Admin\MasterdataEditType($app);

            $types[] = new \Eccube\Form\Type\Admin\PluginLocalInstallType();
            $types[] = new \Eccube\Form\Type\Admin\PluginManagementType();

            return $types;
        });
        $app['eccube.entity.event.dispatcher']->addEventListener(new \Acme\Entity\SoldOutEventListener());
        $app['eccube.queries'] = function () {
            return new \Eccube\Doctrine\Query\Queries();
        };
        // TODO QueryCustomizerの追加方法は要検討
        $app['eccube.queries']->addCustomizer(new \Acme\Entity\AdminProductListCustomizer());

        $app['eccube.purchase.flow.cart'] = function () use ($app) {
            $flow = new PurchaseFlow();
            $flow->addItemProcessor(new StockValidator());
            $flow->addItemHolderProcessor(new PaymentTotalLimitValidator($app['config']['max_total_fee']));
            return $flow;
        };

        $app['eccube.purchase.flow.shopping'] = function () use ($app) {
            $flow = new PurchaseFlow();
            $flow->addItemProcessor(new StockValidator());
            $flow->addItemHolderProcessor(new PaymentTotalLimitValidator($app['config']['max_total_fee']));
            $flow->addItemHolderProcessor(new DeliveryFeeProcessor($app));
            return $flow;
        };

        $app['eccube.purchase.flow.order'] = function () use ($app) {
            $flow = new PurchaseFlow();
            $flow->addItemProcessor(new StockValidator());
            $flow->addItemHolderProcessor(new PaymentTotalLimitValidator($app['config']['max_total_fee']));
            return $flow;
        };
    }

    public function subscribe(Container $app, EventDispatcherInterface $dispatcher)
    {
        $dispatcher->addSubscriber(new TransactionListener($app));
    }
}
