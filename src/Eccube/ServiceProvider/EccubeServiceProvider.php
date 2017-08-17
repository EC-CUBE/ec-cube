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
use Eccube\Annotation\Repository;
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
use Eccube\Service\PurchaseFlow\Processor\ProductClassComparer;
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
        $app['view'] = function () use ($app) {
            return $app['twig'];
        };
        $app['eccube.service.cart'] = function () use ($app) {
            return new \Eccube\Service\CartService($app['session'], $app['eccube.repository.product_class']);
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

        $app['paginator'] = $app->protect(function () {
            $paginator = new \Knp\Component\Pager\Paginator();
            $paginator->subscribe(new \Eccube\EventListener\PaginatorListener());

            return $paginator;
        });

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
            //$extensions[] = new \Eccube\Form\Extension\HelpTypeExtension();
            //$extensions[] = new \Eccube\Form\Extension\FreezeTypeExtension();
            //$extensions[] = new \Eccube\Form\Extension\DoctrineOrmExtension($app['orm.em']);
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

        $app['eccube.purchase.flow.cart.item_comparers'] = function ($app) {
            $comparers = new ArrayCollection();
            $comparers->add(new ProductClassComparer());

            return $comparers;
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
            $flow->setItemComparers($app['eccube.purchase.flow.cart.item_comparers']);

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
        // Add event subscriber to TaxRuleEvent
        $app['orm.em']->getEventManager()->addEventSubscriber(new \Eccube\Doctrine\EventSubscriber\TaxRuleEventSubscriber($app['eccube.service.tax_rule']));

        $dispatcher->addSubscriber(new TransactionListener($app));
    }
}
