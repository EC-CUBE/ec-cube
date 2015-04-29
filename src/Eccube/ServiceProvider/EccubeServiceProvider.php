<?php

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
        $app['eccube.service.system'] = $app->share(function() use ($app) {
            return new \Eccube\Service\SystemService($app);
        });
        $app['view'] = $app->share(function() use ($app) {
            return new \Eccube\Service\ViewService($app);
        });
        $app['eccube.service.cart'] = $app->share(function() use ($app) {
            return new \Eccube\Service\CartService($app['session'], $app['orm.em']);
        });
        $app['eccube.service.order'] = $app->share(function() use ($app) {
            return new \Eccube\Service\OrderService($app);
        });
        $app['eccube.service.tax_rule'] = $app->share(function() use ($app) {
            return new \Eccube\Service\TaxRuleService($app['eccube.repository.tax_rule']);
        });

        // Repository
        $app['eccube.repository.master.constant'] = $app->share(function() use ($app) {
            return $app['orm.em']->getRepository('Eccube\Entity\Master\Constant');
        });
        $app['eccube.repository.category'] = $app->share(function() use ($app) {
            return $app['orm.em']->getRepository('Eccube\Entity\Category');
        });
        $app['eccube.repository.customer'] = $app->share(function() use ($app) {
            return $app['orm.em']->getRepository('Eccube\Entity\Customer');
        });
        $app['eccube.repository.mail_history'] = $app->share(function() use ($app) {
            return $app['orm.em']->getRepository('Eccube\Entity\MailHistory');
        });
        $app['eccube.repository.member'] = $app->share(function() use ($app) {
            return $app['orm.em']->getRepository('Eccube\Entity\Member');
        });
        $app['eccube.repository.order'] = $app->share(function() use ($app) {
            return $app['orm.em']->getRepository('Eccube\Entity\Order');
        });
        $app['eccube.repository.product'] = $app->share(function() use ($app) {
            $productRepository = $app['orm.em']->getRepository('Eccube\Entity\Product');
            $productRepository->setConfig($app['config']);

            return $productRepository;
        });
        $app['eccube.repository.customer_favorite_product'] = $app->share(function() use ($app) {
            return $app['orm.em']->getRepository('Eccube\Entity\CustomerFavoriteProduct');
        });
        $app['eccube.repository.base_info'] = $app->share(function() use ($app) {
            return $app['orm.em']->getRepository('Eccube\Entity\BaseInfo');
        });
        $app['eccube.repository.tax_rule'] = $app->share(function() use ($app) {
            $taxRuleRepository = $app['orm.em']->getRepository('Eccube\Entity\TaxRule');
            $taxRuleRepository->setApp($app);

            return $taxRuleRepository;
        });
        $app['eccube.repository.page_layout'] = $app->share(function() use ($app) {
            $pageLayoutRepository = $app['orm.em']->getRepository('Eccube\Entity\PageLayout');
            $pageLayoutRepository->setApp($app);

            return $pageLayoutRepository;
        });
        $app['eccube.repository.order'] = $app->share(function() use ($app) {
            $orderRepository = $app['orm.em']->getRepository('Eccube\Entity\Order');
            $orderRepository->setConfig($app['config']);

            return $orderRepository;
        });
        $app['eccube.repository.other_deliv'] = $app->share(function() use ($app) {
            return $app['orm.em']->getRepository('Eccube\Entity\OtherDeliv');
        });
        $app['eccube.repository.order_status'] = $app->share(function() use ($app) {
            return $app['orm.em']->getRepository('Eccube\Entity\Master\OrderStatus');
        });

        $app['paginator'] = $app->protect(function() {
            return new \Knp\Component\Pager\Paginator();
        });

        // em
        if (isset($app['orm.em'])) {
            $point_rule = $app['config']['point_rule'];
            $app['orm.em'] = $app->share($app->extend('orm.em', function (\Doctrine\ORM\EntityManager $em, \Silex\Application $app) use($point_rule) {
                // tax_rule
                $taxRuleRepository = $em->getRepository('Eccube\Entity\TaxRule');
                $taxRuleRepository->setApp($app);
                $taxRuleService = new \Eccube\Service\TaxRuleService($taxRuleRepository);
                $em->getEventManager()->addEventSubscriber(new \Eccube\Doctrine\EventSubscriber\TaxRuleEventSubscriber($taxRuleService));
                $em->getEventManager()->addEventSubscriber(new \Eccube\Doctrine\EventSubscriber\PointEventSubscriber($point_rule, $taxRuleService));

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
            $types[] = new \Eccube\Form\Type\PrefType();
            $types[] = new \Eccube\Form\Type\ZipType();
            $types[] = new \Eccube\Form\Type\AddressType();
            $types[] = new \Eccube\Form\Type\SexType();
            $types[] = new \Eccube\Form\Type\ProductListMaxType();
            $types[] = new \Eccube\Form\Type\JobType();
            $types[] = new \Eccube\Form\Type\ReminderType();
            $types[] = new \Eccube\Form\Type\MailMagazineType();
            $types[] = new \Eccube\Form\Type\CustomerStatusType();
            $types[] = new \Eccube\Form\Type\OrderStatusType();
            $types[] = new \Eccube\Form\Type\PaymentType();
            $types[] = new \Eccube\Form\Type\DelivType();
            $types[] = new \Eccube\Form\Type\DelivFeeType();
            $types[] = new \Eccube\Form\Type\DelivTimeType();
            $types[] = new \Eccube\Form\Type\ProductTypeType();
            $types[] = new \Eccube\Form\Type\CalcRuleType();
            $types[] = new \Eccube\Form\Type\PaymentRegisterType();
            $types[] = new \Eccube\Form\Type\MailType();
            $types[] = new \Eccube\Form\Type\MailTemplateType();

            $types[] = new \Eccube\Form\Type\EntryType($app);
            $types[] = new \Eccube\Form\Type\CustomerType($app);
            if (isset($app['security']) && isset($app['eccube.repository.customer_favorite_product'])) {
                $types[] = new \Eccube\Form\Type\AddCartType($app['config'], $app['security'], $app['eccube.repository.customer_favorite_product']);
            }
            $types[] = new \Eccube\Form\Type\SearchProductType();
            $types[] = new \Eccube\Form\Type\CustomerLoginType($app['session']);
            $types[] = new \Eccube\Form\Type\ContactType($app['config']);
            $types[] = new \Eccube\Form\Type\ShopMasterType($app);
            $types[] = new \Eccube\Form\Type\PointType($app);
            $types[] = new \Eccube\Form\Type\TaxRuleType($app);
            $types[] = new \Eccube\Form\Type\MainEditType($app);
            $types[] = new \Eccube\Form\Type\InstallType($app);
            $types[] = new \Eccube\Form\Type\OrderSearchType($app);
            $types[] = new \Eccube\Form\Type\CustomerSearchType($app);
            $types[] = new \Eccube\Form\Type\ShoppingType($app);
            $types[] = new \Eccube\Form\Type\ShippingMultiType($app);
            $types[] = new \Eccube\Form\Type\OtherDelivType($app['config']);

            // admin
            $types[] = new \Eccube\Form\Type\AdminLoginType($app['session']);

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
