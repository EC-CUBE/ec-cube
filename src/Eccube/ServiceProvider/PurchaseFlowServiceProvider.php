<?php

namespace Eccube\ServiceProvider;

use Doctrine\Common\Collections\ArrayCollection;
use Eccube\Entity\BaseInfo;
use Eccube\Entity\ItemHolderInterface;
use Eccube\Repository\DeliveryRepository;
use Eccube\Service\PurchaseFlow\Processor\AdminOrderRegisterPurchaseProcessor;
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
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class PurchaseFlowServiceProvider implements ServiceProviderInterface
{
    public function register(Container $app)
    {
        $app['eccube.purchase.context'] = $app->protect(function (ItemHolderInterface $origin = null) {
            return new PurchaseContext($origin);
        });

        $app['eccube.purchase.flow.cart.item_processors'] = function (Container $app) {
            $processors = new ArrayCollection();
            $processors[] = new DisplayStatusValidator();
            $processors[] = new SaleLimitValidator();
            $processors[] = new DeliverySettingValidator($app[DeliveryRepository::class]);
            $processors[] = new StockValidator();

            return $processors;
        };

        $app['eccube.purchase.flow.cart.holder_processors'] = function (Container $app) {
            $processors = new ArrayCollection();
            $processors[] = new PaymentProcessor($app[DeliveryRepository::class]);
            $processors[] = new PaymentTotalLimitValidator($app['config']['max_total_fee']);
            $processors[] = new DeliveryFeeFreeProcessor($app[BaseInfo::class]);
            $processors[] = new PaymentTotalNegativeValidator();

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
            $processors[] = new StockValidator();
            $processors[] = new DisplayStatusValidator();

            return $processors;
        };

        $app['eccube.purchase.flow.shopping.holder_processors'] = function (Container $app) {
            $processors = new ArrayCollection();
            $processors[] = new PaymentTotalLimitValidator($app['config']['max_total_fee']);
            $processors[] = new DeliveryFeeProcessor($app['orm.em']);
            $processors[] = new PaymentTotalNegativeValidator();

            return $processors;
        };

        $app['eccube.purchase.flow.shopping'] = function (Container $app) {
            $flow = new PurchaseFlow();
            $flow->setItemProcessors($app['eccube.purchase.flow.shopping.item_processors']);
            $flow->setItemHolderProcessors($app['eccube.purchase.flow.shopping.holder_processors']);

            return $flow;
        };

        $app['eccube.purchase.flow.order.item_processors'] = function (Container $app) {
            $processors = new ArrayCollection();
            $processors[] = new StockValidator();

            return $processors;
        };

        $app['eccube.purchase.flow.order.holder_processors'] = function (Container $app) {
            $processors = new ArrayCollection();
            $processors[] = new PaymentTotalLimitValidator($app['config']['max_total_fee']);
            $processors[] = new UpdateDatePurchaseProcessor($app['config']);
            $processors[] = new AdminOrderRegisterPurchaseProcessor($app);

            return $processors;
        };

        $app['eccube.purchase.flow.order'] = function (Container $app) {
            $flow = new PurchaseFlow();
            $flow->setItemProcessors($app['eccube.purchase.flow.order.item_processors']);
            $flow->setItemHolderProcessors($app['eccube.purchase.flow.order.holder_processors']);

            return $flow;
        };
    }
}