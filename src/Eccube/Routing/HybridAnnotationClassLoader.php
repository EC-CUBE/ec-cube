<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) EC-CUBE CO.,LTD. All Rights Reserved.
 *
 * http://www.ec-cube.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eccube\Routing;

use Doctrine\Common\Annotations\AnnotationReader;
use Symfony\Component\Routing\Attribute\Route as RouteAttribute;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

/**
 * Reads both #[Route] PHP 8 attributes and @Route annotations for backward compatibility.
 *
 * Symfony 7.x only reads PHP 8 attributes for routing.
 * This loader provides backward compatibility for plugins still using @Route annotations.
 *
 * Register as a service tagged with 'routing.route_loader' to enable.
 */
class HybridAnnotationClassLoader
{
    /**
     * Loads routes from @Route annotations for classes that don't have #[Route] attributes.
     *
     * This is called by Kernel::configureRoutes() for plugin directories.
     * If a controller method has #[Route] attributes, Symfony handles it natively.
     * If a controller method only has @Route annotations, this loader reads them.
     */
    public static function loadAnnotationRoutes(string $controllerDir): RouteCollection
    {
        $collection = new RouteCollection();

        if (!class_exists(AnnotationReader::class)) {
            return $collection;
        }

        $reader = new AnnotationReader();

        // Find all PHP files in the directory
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($controllerDir, \FilesystemIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::LEAVES_ONLY
        );

        foreach ($iterator as $file) {
            if ($file->getExtension() !== 'php') {
                continue;
            }

            $content = file_get_contents($file->getRealPath());

            // Extract namespace and class name
            $namespace = '';
            $className = '';
            if (preg_match('/namespace\s+([^;]+);/', $content, $m)) {
                $namespace = $m[1];
            }
            if (preg_match('/class\s+(\w+)/', $content, $m)) {
                $className = $m[1];
            }

            if (!$namespace || !$className) {
                continue;
            }

            $fqcn = $namespace.'\\'.$className;
            if (!class_exists($fqcn)) {
                continue;
            }

            $rc = new \ReflectionClass($fqcn);

            foreach ($rc->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
                // Skip if method already has #[Route] attributes (Symfony handles these)
                if (!empty($method->getAttributes(RouteAttribute::class))) {
                    continue;
                }

                // Check for @Route annotation
                try {
                    $annotations = $reader->getMethodAnnotations($method);
                } catch (\Throwable $e) {
                    continue;
                }

                foreach ($annotations as $annotation) {
                    if ($annotation instanceof \Symfony\Component\Routing\Annotation\Route) {
                        trigger_deprecation(
                            'ec-cube/ec-cube',
                            '4.3',
                            'Using @Route annotation in %s::%s() is deprecated, use #[Route] attribute instead.',
                            $fqcn,
                            $method->getName()
                        );

                        $path = $annotation->getPath() ?? $annotation->getLocalizedPaths()[0] ?? '/';
                        $route = new Route(
                            $path,
                            array_merge($annotation->getDefaults(), ['_controller' => $fqcn.'::'.$method->getName()]),
                            $annotation->getRequirements(),
                            $annotation->getOptions(),
                            $annotation->getHost() ?? '',
                            $annotation->getSchemes(),
                            $annotation->getMethods(),
                            $annotation->getCondition() ?? ''
                        );

                        $routeName = $annotation->getName() ?? strtolower(str_replace('\\', '_', $fqcn).'_'.$method->getName());
                        $collection->add($routeName, $route);
                    }
                }
            }
        }

        return $collection;
    }
}
