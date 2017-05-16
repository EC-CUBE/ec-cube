<?php
namespace Acme\ServiceProvider;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Silex\Api\BootableProviderInterface;
use Silex\Application;
use Eccube\Entity\Shipping;
use Eccube\Entity\ShipmentItem;
use Acme\Repository\ShippingRepository;
use Eccube\Repository\ShipmentItemRepository;

class ShippingServiceProvider implements ServiceProviderInterface, BootableProviderInterface
{

    /**
     * {@inheritDoc}
     */
    public function register(Container $app)
    {
        $app['eccube.repository.shipping'] = function () use ($app) {
            $Repository = new ShippingRepository($app['orm.em'], $app['orm.em']->getMetadataFactory()->getMetadataFor(Shipping::class));
            $Repository->setApplication($app);
            return $Repository;
        };
        $app['eccube.repository.shipment_item'] = function () use ($app) {
            $Repository = new ShipmentItemRepository($app['orm.em'], $app['orm.em']->getMetadataFactory()->getMetadataFor(ShipmentItem::class));
            $Repository->setApplication($app);
            return $Repository;
        };
    }

    /**
     * {@inheritDoc}
     */
    public function boot(Application $app)
    {
    }
}
