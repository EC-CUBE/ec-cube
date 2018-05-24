<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eccube\ServiceProvider;

use Doctrine\Common\Collections\ArrayCollection;
use Eccube\Entity\BaseInfo;
use Eccube\Entity\Customer;
use Eccube\Entity\ItemHolderInterface;
use Eccube\Repository\DeliveryRepository;
use Eccube\Service\PurchaseFlow\Processor as Processor;
use Eccube\Service\PurchaseFlow\PurchaseContext;
use Eccube\Service\PurchaseFlow\PurchaseFlow;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class PurchaseFlowServiceProvider implements ServiceProviderInterface
{
    public function register(Container $app)
    {
        $app['eccube.purchase.context'] = $app->protect(function (ItemHolderInterface $origin = null, Customer $user = null) {
            return new PurchaseContext($origin, $user);
        });

        $app['eccube.purchase.flow.cart.item_processors'] = function (Container $app) {
            $processors = new ArrayCollection();
            $processors[] = new Processor\DisplayStatusValidator();
            $processors[] = new Processor\SaleLimitValidator();
            $processors[] = new Processor\DeliverySettingValidator($app[DeliveryRepository::class]);
            $processors[] = new Processor\StockValidator();

            return $processors;
        };

        $app['eccube.purchase.flow.cart.holder_processors'] = function (Container $app) {
            $processors = new ArrayCollection();
            $processors[] = new Processor\PaymentProcessor($app[DeliveryRepository::class]);
            $processors[] = new Processor\PaymentTotalLimitValidator($app['config']['max_total_fee']);
            $processors[] = new Processor\DeliveryFeeFreeProcessor($app[BaseInfo::class]);
            $processors[] = new Processor\PaymentTotalNegativeValidator();

            return $processors;
        };

        $app['eccube.purchase.flow.cart'] = function (Container $app) {
            $flow = new PurchaseFlow();
            $flow->setItemProcessors($app['eccube.purchase.flow.cart.item_processors']);
            $flow->setItemHolderProcessors($app['eccube.purchase.flow.cart.holder_processors']);

            return $flow;
        };

        $app['eccube.purchase.flow.shopping.item_processors'] = function (Container $app) {
            $processors = new ArrayCollection();
            $processors[] = new Processor\StockValidator();
            $processors[] = new Processor\DisplayStatusValidator();

            return $processors;
        };

        $app['eccube.purchase.flow.shopping.holder_processors'] = function (Container $app) {
            $processors = new ArrayCollection();
            $processors[] = new Processor\PaymentTotalLimitValidator($app['config']['max_total_fee']);
            $processors[] = new Processor\DeliveryFeeProcessor($app['orm.em']);
            $processors[] = new Processor\PaymentTotalNegativeValidator();
            if ($app[BaseInfo::class]->isOptionPoint()) {
                $processors[] = new Processor\UsePointProcessor($app['orm.em'], $app[BaseInfo::class]);
                $processors[] = new Processor\AddPointProcessor($app['orm.em'], $app[BaseInfo::class]);
                $processors[] = new Processor\SubstractPointProcessor($app[BaseInfo::class]);
            }

            return $processors;
        };

        $app['eccube.purchase.flow.shopping.purchase'] = function (Container $app) {
            $processors = new ArrayCollection();
            if ($app[BaseInfo::class]->isOptionPoint()) {
                $processors[] = new Processor\UsePointToCustomerPurchaseProcessor();
            }
            $processors[] = new Processor\OrderCodePurchaseProcessor($app['orm.em'], $app['config']['order_code']);

            return $processors;
        };

        $app['eccube.purchase.flow.shopping'] = function (Container $app) {
            $flow = new PurchaseFlow();
            $flow->setItemProcessors($app['eccube.purchase.flow.shopping.item_processors']);
            $flow->setItemHolderProcessors($app['eccube.purchase.flow.shopping.holder_processors']);
            $flow->setPurchaseProcessors($app['eccube.purchase.flow.shopping.purchase']);

            return $flow;
        };

        $app['eccube.purchase.flow.order.item_processors'] = function (Container $app) {
            $processors = new ArrayCollection();
            $processors[] = new Processor\StockValidator();

            return $processors;
        };

        $app['eccube.purchase.flow.order.holder_processors'] = function (Container $app) {
            $processors = new ArrayCollection();
            $processors[] = new Processor\PaymentTotalLimitValidator($app['config']['max_total_fee']);
            $processors[] = new Processor\UpdateDatePurchaseProcessor($app['config']);
            if ($app[BaseInfo::class]->isOptionPoint()) {
                $processors[] = new Processor\UsePointProcessor($app['orm.em'], $app[BaseInfo::class]);
                $processors[] = new Processor\AddPointProcessor($app['orm.em'], $app[BaseInfo::class]);
                $processors[] = new Processor\SubstractPointProcessor($app[BaseInfo::class]);
            }

            return $processors;
        };

        $app['eccube.purchase.flow.order.purchase'] = function (Container $app) {
            $processors = new ArrayCollection();
            $processors[] = new Processor\AdminOrderRegisterPurchaseProcessor($app);
            $processors[] = new Processor\OrderCodePurchaseProcessor($app['orm.em'], $app['config']['order_code']);

            return $processors;
        };

        $app['eccube.purchase.flow.order'] = function (Container $app) {
            $flow = new PurchaseFlow();
            $flow->setItemProcessors($app['eccube.purchase.flow.order.item_processors']);
            $flow->setItemHolderProcessors($app['eccube.purchase.flow.order.holder_processors']);
            $flow->setPurchaseProcessors($app['eccube.purchase.flow.order.purchase']);

            return $flow;
        };
    }
}
