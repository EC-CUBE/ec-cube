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


use Eccube\Annotation\Repository;

class RepositoryAutoWiring extends ComponentAutoWiring
{
    /**
     * RepositoryAutoWiring constructor.
     * @param array|string[] $scanDirs
     */
    public function __construct($scanDirs)
    {
        parent::__construct($scanDirs);
    }

    /**
     * @return string
     */
    public function getAnnotationClass()
    {
        return Repository::class;
    }

    /**
     * @param \Eccube\Annotation\Component $anno
     * @param \ReflectionClass $refClass
     * @return RepositoryDefinition
     */
    public function createComponentDefinition($anno, $refClass)
    {
        return new RepositoryDefinition($refClass->getName(), $refClass);
    }

    /**
     * @param \Twig_Environment $twig
     * @param array $components
     * @return string
     */
    public function generate(\Twig_Environment $twig, array $components)
    {
        return $twig->createTemplate(
'{% for component in components -%}
$app["{{ component.id }}"] = function (\Pimple\Container $app) {
    $class = new \ReflectionClass(\{{ component.className }}::class);
    $instance = $app["orm.em"]->getRepository(\{{ component.entityName }}::class);

    {% for dependency in component.dependencies -%}
    $property = $class->getProperty("{{ dependency.propertyName }}");
    $property->setAccessible(true);
    $property->setValue($instance, {% if is_app(dependency.id) %}$app{% else %}$app["{{ dependency.id }}"]{% endif %});
    {% endfor %}

    return $instance;
};
{% endfor %}')->render(['components' => $components]);
    }
}