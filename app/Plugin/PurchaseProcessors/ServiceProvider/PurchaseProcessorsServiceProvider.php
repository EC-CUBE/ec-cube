<?php

namespace Plugin\PurchaseProcessors\ServiceProvider;

use Doctrine\Common\Collections\ArrayCollection;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Plugin\PurchaseProcessors\Processor\EmptyProcessor;
use Plugin\PurchaseProcessors\Processor\ValidatableEmptyProcessor;

class PurchaseProcessorsServiceProvider implements ServiceProviderInterface
{
    public function register(Container $app)
    {
        $app->extend(
            'eccube.purchase.flow.cart.item_processors',
            function (ArrayCollection $processors, Container $app) {
                $processors[] = new EmptyProcessor();
                $processors[] = new ValidatableEmptyProcessor();

                return $processors;
            }
        );
    }
}
