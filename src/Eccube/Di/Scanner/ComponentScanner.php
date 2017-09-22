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

namespace Eccube\Di\Scanner;


use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\ORM\Mapping\Annotation;
use Eccube\Annotation\Component;
use Eccube\Di\ComponentDefinition;
use Eccube\Di\Scanner;

class ComponentScanner implements Scanner
{
    /**
     * @var string[]
     */
    private $scanDirs;

    /**
     * ComponentAutoWiring constructor.
     * @param string[] $scanDirs
     */
    public function __construct(array $scanDirs)
    {
        $this->scanDirs = $scanDirs;
    }

    /**
     * @param $reader AnnotationReader
     * @param $refClass \ReflectionClass
     * @return Annotation
     */
    public function findAnnotation($reader, $refClass)
    {
        return $reader->getClassAnnotation($refClass, $this->getAnnotationClass());
    }

    /**
     * @return string
     */
    public function getAnnotationClass()
    {
        return Component::class;
    }

    /**
     * @param $anno Component
     * @param $refClass \ReflectionClass
     * @return ComponentDefinition
     */
    public function createComponentDefinition($anno, $refClass)
    {
        return new ComponentDefinition($anno->value ?: $refClass->getName(), $refClass);
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
    $instance = $class->newInstanceWithoutConstructor();

    {% for dependency in component.dependencies -%}
    $property = $class->getProperty("{{ dependency.propertyName }}");
    $property->setAccessible(true);
    $property->setValue($instance, {% if is_app(dependency.id) %}$app{% else %}$app["{{ dependency.id }}"]{% endif %});
    {% endfor %}

    return $instance;
};
{% endfor %}')->render(['components' => $components]);
    }

    public function generateExtend(\Twig_Environment $twig, array $components)
    {
        return null;
    }

    /**
     * @return string[]
     */
    public function getScanDirs()
    {
        return $this->scanDirs;
    }
}