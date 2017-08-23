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

use Doctrine\Common\Collections\ArrayCollection;
use Eccube\Entity\ItemHolderInterface;
use Eccube\EventListener\TransactionListener;
use Eccube\Repository\BaseInfoRepository;
use Eccube\Repository\DeliveryRepository;
use Eccube\Service\PurchaseFlow\Processor\AdminOrderRegisterPurchaseProcessor;
use Eccube\Service\PurchaseFlow\Processor\DeletedProductValidator;
use Eccube\Service\PurchaseFlow\Processor\DeliveryFeeFreeProcessor;
use Eccube\Service\PurchaseFlow\Processor\DeliveryFeeProcessor;
use Eccube\Service\PurchaseFlow\Processor\DeliverySettingValidator;
use Eccube\Service\PurchaseFlow\Processor\DisplayStatusValidator;
use Eccube\Service\PurchaseFlow\Processor\PaymentProcessor;
use Eccube\Service\PurchaseFlow\Processor\PaymentTotalLimitValidator;
use Eccube\Service\PurchaseFlow\Processor\PaymentTotalNegativeValidator;
use Eccube\Service\PurchaseFlow\Processor\SaleLimitValidator;
use Eccube\Service\PurchaseFlow\Processor\StockValidator;
use Eccube\Service\PurchaseFlow\Processor\UpdateDatePurchaseProcessor;
use Eccube\Service\PurchaseFlow\PurchaseContext;
use Eccube\Service\PurchaseFlow\PurchaseFlow;
use Eccube\Service\TaxRuleService;
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
        $app['eccube.calculate.context'] = function () use ($app) {
                return new \Eccube\Service\Calculator\CalculateContext();
        };

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
            $Service = new $clazz($app['request_stack']);

            return $Service;
        });

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
            $processors->add(new PaymentProcessor($app[DeliveryRepository::class]));
            $processors->add(new PaymentTotalLimitValidator($app['config']['max_total_fee']));
            $processors->add(new DeliveryFeeFreeProcessor($app[BaseInfoRepository::class]));
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
            $flow->addItemHolderProcessor(new DeliveryFeeProcessor($app['orm.em']));
            $flow->addItemHolderProcessor(new PaymentTotalNegativeValidator());
            return $flow;
        };

        $app['eccube.purchase.flow.order'] = function () use ($app) {
            $flow = new PurchaseFlow();
            $flow->addItemProcessor(new StockValidator());
            $flow->addItemHolderProcessor(new PaymentTotalLimitValidator($app['config']['max_total_fee']));
            $flow->addPurchaseProcessor(new UpdateDatePurchaseProcessor($app['config']));
            $flow->addPurchaseProcessor(new AdminOrderRegisterPurchaseProcessor($app));
            return $flow;
        };
    }

    public function subscribe(Container $app, EventDispatcherInterface $dispatcher)
    {
        // Add event subscriber to TaxRuleEvent
        $app['orm.em']->getEventManager()->addEventSubscriber(new \Eccube\Doctrine\EventSubscriber\TaxRuleEventSubscriber($app[TaxRuleService::class]));

        $dispatcher->addSubscriber(new TransactionListener($app));
    }
}
