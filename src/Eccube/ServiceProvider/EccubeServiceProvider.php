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
use Eccube\Doctrine\EventSubscriber\TaxRuleEventSubscriber;
use Eccube\Application;
use Eccube\Entity\BaseInfo;
use Eccube\Repository\BaseInfoRepository;
use Eccube\Service\Cart\CartItemAllocator;
use Eccube\Service\Cart\CartItemComparator;
use Eccube\Service\Cart\ProductClassComparator;
use Eccube\Service\Cart\SaleTypeCartAllocator;
use Eccube\ServiceProvider\ServiceProviderInterface;
use Symfony\Component\HttpFoundation\ParameterBag;

class EccubeServiceProvider implements ServiceProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function register(Application $app)
    {
        $app[BaseInfo::class] = function () use ($app) {
            return $app[BaseInfoRepository::class]->get();
        };

        $app['request_scope'] = function () {
            return new ParameterBag();
        };

        // Application::initRenderingと一緒に修正
        $app['eccube.twig.block.templates'] = function () {
            $templates = new ArrayCollection();
            $templates[] = 'render_block.twig';

            return $templates;
        };

        $app[CartItemComparator::class] = function() {
            return new ProductClassComparator();
        };

        $app[CartItemAllocator::class] = function() {
            return new SaleTypeCartAllocator();
        };

        $em = $app->getParentContainer()->get('doctrine')->getManager();
        $app['orm.em'] = $app->share(function () use ($em) {
            return $em;
        });

        $app['config'] = $app->share(function () {
            if ($this->getParentContainer()->hasParameter('eccube.app')) {
                return $this->getParentContainer()->getParameter('eccube.app');
            }

            return [];
        });
    }

    /**
     * {@inheritdoc}
     */
    public function boot(Application $app)
    {
    }
}
