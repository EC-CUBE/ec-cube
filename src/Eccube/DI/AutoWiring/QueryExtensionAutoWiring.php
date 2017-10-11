<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2017 LOCKON CO.,LTD. All Rights Reserved.
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

namespace Eccube\DI\AutoWiring;


use Eccube\Annotation\QueryExtension;
use Eccube\DI\ComponentDefinition;

class QueryExtensionAutoWiring extends ComponentAutoWiring
{
    /**
     * QueryExtensionScanner constructor.
     * @param array|string[] $scanDirs
     */
    public function __construct($scanDirs)
    {
        parent::__construct($scanDirs);
    }

    public function getAnnotationClass()
    {
        return QueryExtension::class;
    }

    public function createComponentDefinition($anno, $refClass)
    {
        return new ComponentDefinition($refClass->getName(), $refClass);
    }

    public function generateExtend(\Twig_Environment $twig, array $components)
    {
        return $twig->createTemplate(
'$app->extend("eccube.queries", function ($queries, $app) {
    {% for extension in extensions -%}
    $queries->addCustomizer($app["{{ extension.id }}"]);
    {% endfor %}

    return $queries;
});')->render(['extensions' => $components]);
    }
}