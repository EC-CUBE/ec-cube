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

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\CachedReader;
use Doctrine\Common\Cache\ArrayCache;
use Doctrine\Common\Collections\ArrayCollection;
use Eccube\Annotation\Component;
use Eccube\Entity\ItemHolderInterface;
use Eccube\EventListener\TransactionListener;
use Eccube\Service\OrderHelper;
use Eccube\Service\PurchaseFlow\Processor\AdminOrderRegisterPurchaseProcessor;
use Eccube\Service\PurchaseFlow\Processor\DeletedProductValidator;
use Eccube\Service\PurchaseFlow\Processor\DeliveryFeeFreeProcessor;
use Eccube\Service\PurchaseFlow\Processor\DeliveryFeeProcessor;
use Eccube\Service\PurchaseFlow\Processor\DeliverySettingValidator;
use Eccube\Service\PurchaseFlow\Processor\DisplayStatusValidator;
use Eccube\Service\PurchaseFlow\Processor\PaymentTotalNegativeValidator;
use Eccube\Service\PurchaseFlow\Processor\PaymentProcessor;
use Eccube\Service\PurchaseFlow\Processor\PaymentTotalLimitValidator;
use Eccube\Service\PurchaseFlow\Processor\SaleLimitValidator;
use Eccube\Service\PurchaseFlow\Processor\StockValidator;
use Eccube\Service\PurchaseFlow\Processor\UpdateDatePurchaseProcessor;
use Eccube\Service\PurchaseFlow\PurchaseContext;
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
            return new \Eccube\Service\CartService($app['session'], $app['orm.em']);
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

        $reader = new CachedReader(new AnnotationReader(), new ArrayCache());
        $classMetadatas = $app['orm.em']->getMetaDataFactory()->getAllMetaData();
        foreach ($classMetadatas as $Metadata) {
            if (class_exists($Metadata->customRepositoryClassName)) {
                $rc = new \ReflectionClass($Metadata->customRepositoryClassName);
                $annotation = $reader->getClassAnnotation($rc, Component::class);
                if ($annotation) {
                    if (!$app->offsetExists($annotation->value)) {
                        $app[$annotation->value] = function () use ($app, $Metadata) {
                            return $app['orm.em']->getRepository($Metadata->name);
                        };
                    }
                }
            }
        }

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

        $app['eccube.twig.block.templates'] = function () {
            $templates = new ArrayCollection();
            $templates[] = 'render_block.twig';

            return $templates;
        };

        // Form\Type
        $app->extend('form.type.extensions', function ($extensions) use ($app) {
            $extensions[] = new \Eccube\Form\Extension\HelpTypeExtension();
            $extensions[] = new \Eccube\Form\Extension\FreezeTypeExtension();
            $extensions[] = new \Eccube\Form\Extension\DoctrineOrmExtension($app['orm.em']);
            return $extensions;
        });

        $app['eccube.entity.event.dispatcher']->addEventListener(new \Acme\Entity\SoldOutEventListener());
        $app['eccube.queries'] = function () {
            return new \Eccube\Doctrine\Query\Queries();
        };
        // TODO QueryCustomizerの追加方法は要検討
        $app['eccube.queries']->addCustomizer(new \Acme\Entity\AdminProductListCustomizer());

        $app['eccube.purchase.context'] = $app->protect(function (ItemHolderInterface $origin = null) {
            return new PurchaseContext($origin);
        });

        $app['eccube.purchase.flow.cart.item_processors'] = function ($app) {
            $processors = new ArrayCollection();
            $processors->add(new DeletedProductValidator());
            $processors->add(new DisplayStatusValidator());
            $processors->add(new SaleLimitValidator());
            $processors->add(new DeliverySettingValidator($app['eccube.repository.delivery']));

            return $processors;
        };

        $app['eccube.purchase.flow.cart.holder_processors'] = function ($app) {
            $processors = new ArrayCollection();
            $processors->add(new PaymentProcessor($app));
            $processors->add(new PaymentTotalLimitValidator($app['config']['max_total_fee']));
            $processors->add(new DeliveryFeeFreeProcessor($app));
            $processors->add(new PaymentTotalNegativeValidator());

            return $processors;
        };

        // example
        $app->extend('eccube.purchase.flow.cart.item_processors', function ($processors, $app) {

            $processors->add(new StockValidator());

            return $processors;
        });

        $app['eccube.purchase.flow.cart'] = function ($app) {
            $flow = new PurchaseFlow();
            $flow->setItemProcessors($app['eccube.purchase.flow.cart.item_processors']);
            $flow->setItemHolderProcessors($app['eccube.purchase.flow.cart.holder_processors']);

            return $flow;
        };

        $app['eccube.purchase.flow.shopping'] = function () use ($app) {
            $flow = new PurchaseFlow();
            $flow->addItemProcessor(new StockValidator());
            $flow->addItemProcessor(new DisplayStatusValidator());
            $flow->addItemHolderProcessor(new PaymentTotalLimitValidator($app['config']['max_total_fee']));
            $flow->addItemHolderProcessor(new DeliveryFeeProcessor($app));
            $flow->addItemHolderProcessor(new PaymentTotalNegativeValidator());
            return $flow;
        };

        $app['eccube.purchase.flow.order'] = function () use ($app) {
            $flow = new PurchaseFlow();
            $flow->addItemProcessor(new StockValidator());
            $flow->addItemHolderProcessor(new PaymentTotalLimitValidator($app['config']['max_total_fee']));
            $flow->addPurchaseProcessor(new UpdateDatePurchaseProcessor($app));
            $flow->addPurchaseProcessor(new AdminOrderRegisterPurchaseProcessor($app));
            return $flow;
        };
    }

    public function subscribe(Container $app, EventDispatcherInterface $dispatcher)
    {
        $dispatcher->addSubscriber(new TransactionListener($app));
    }
}
