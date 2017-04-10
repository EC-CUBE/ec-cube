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

namespace Eccube\Doctrine\Filter;


use Doctrine\ORM\Configuration;
use Eccube\Application;
use Psr\Log\InvalidArgumentException;
use Symfony\Component\HttpFoundation\Request;

/**
 * SQLFilterを設定するクラス
 */
class SQLFilterConfigurator
{

    /**
     * @var Application $app
     */
    private $app;

    /**
     * @var Configuration $config
     */
    private $config;

    function __construct(Application $app)
    {
        $this->app = $app;
        $this->config = $app['orm.em']->getConfiguration();
    }

    /**
     * @param $filterClassName|string フィルタークラス名
     */
    public function addFilter($filterClassName)
    {
        $interfaces = class_implements($filterClassName);
        if (array_search(ConditionalSQLFilter::class, $interfaces) == false) {
            throw new InvalidArgumentException("$filterClassName should implement ".ConditionalSQLFilter::class);
        }
        $this->config->addFilter($filterClassName, $filterClassName);
        $this->app->before(function (Request $request, Application $app) use ($filterClassName) {
            $enable = call_user_func($filterClassName.'::isApplicable', $request, $app);
            if ($enable) {
                $filters = $this->app['orm.em']->getFilters();
                $filters->enable($filterClassName);
                /** @var ConditionalSQLFilter $filter */
                $filters->getFilter($filterClassName);
            }
        });
    }
}