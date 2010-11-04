<?php
/**
 * Licensed to the Apache Software Foundation (ASF) under one or more
 * contributor license agreements.  See the NOTICE file distributed with
 * this work for additional information regarding copyright ownership.
 * The ASF licenses this file to You under the Apache License, Version 2.0
 * (the "License"); you may not use this file except in compliance with
 * the License.  You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 *
 * @package log4php
 * @subpackage or
 */

/**
 * @ignore 
 */
if (!defined('LOG4PHP_DIR')) define('LOG4PHP_DIR', dirname(__FILE__) . '/..');
 
/**
 */
require_once(LOG4PHP_DIR . '/or/LoggerDefaultRenderer.php');
require_once(LOG4PHP_DIR . '/or/LoggerObjectRenderer.php');
require_once(LOG4PHP_DIR . '/LoggerLog.php');

/**
 * Map class objects to an {@link LoggerObjectRenderer}.
 *
 * @author  Marco Vassura
 * @version $Revision: 635069 $
 * @package log4php
 * @subpackage or
 * @since 0.3
 */
class LoggerRendererMap {

    /**
     * @var array
     */
    var $map;

    /**
     * @var LoggerDefaultRenderer
     */
    var $defaultRenderer;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->map = array();
        $this->defaultRenderer = new LoggerDefaultRenderer();
    }

    /**
     * Add a renderer to a hierarchy passed as parameter.
     * Note that hierarchy must implement getRendererMap() and setRenderer() methods.
     *
     * @param LoggerHierarchy $repository a logger repository.
     * @param string $renderedClassName
     * @param string $renderingClassName
     * @static
     */
    public static function addRenderer($repository, $renderedClassName, $renderingClassName)
    {
        LoggerLog::debug("LoggerRendererMap::addRenderer() Rendering class: [{$renderingClassName}], Rendered class: [{$renderedClassName}].");
        $renderer = LoggerObjectRenderer::factory($renderingClassName);
        if($renderer == null) {
            LoggerLog::warn("LoggerRendererMap::addRenderer() Could not instantiate renderer [{$renderingClassName}].");
            return;
        } else {
            $repository->setRenderer($renderedClassName, $renderer);
        }
    }


    /**
     * Find the appropriate renderer for the class type of the
     * <var>o</var> parameter. 
     *
     * This is accomplished by calling the {@link getByObject()} 
     * method if <var>o</var> is object or using {@link LoggerDefaultRenderer}. 
     * Once a renderer is found, it is applied on the object <var>o</var> and 
     * the result is returned as a string.
     *
     * @param mixed $o
     * @return string 
     */
    public function findAndRender($o)
    {
        if($o == null) {
            return null;
        } else {
            if (is_object($o)) {
                $renderer = $this->getByObject($o);
                if ($renderer !== null) {
                    return $renderer->doRender($o);
                } else {
                    return null;
                }
            } else {
                $renderer = $this->defaultRenderer;
                return $renderer->doRender($o);
            }
        }
    }

    /**
     * Syntactic sugar method that calls {@link PHP_MANUAL#get_class} with the
     * class of the object parameter.
     * 
     * @param mixed $o
     * @return string
     */
    public function getByObject($o)
    {
        return ($o == null) ? null : $this->getByClassName(get_class($o));
    }


    /**
     * Search the parents of <var>clazz</var> for a renderer. 
     *
     * The renderer closest in the hierarchy will be returned. If no
     * renderers could be found, then the default renderer is returned.
     *
     * @param string $class
     * @return LoggerObjectRenderer
     */
    public function getByClassName($class)
    {
        $r = null;
        for($c = strtolower($class); !empty($c); $c = get_parent_class($c)) {
            if (isset($this->map[$c])) {
                return  $this->map[$c];
            }
        }
        return $this->defaultRenderer;
    }

    /**
     * @return LoggerDefaultRenderer
     */
    public function getDefaultRenderer()
    {
        return $this->defaultRenderer;
    }


    public function clear()
    {
        $this->map = array();
    }

    /**
     * Register a {@link LoggerObjectRenderer} for <var>clazz</var>.
     * @param string $class
     * @param LoggerObjectRenderer $or
     */
    public function put($class, $or)
    {
        $this->map[strtolower($class)] = $or;
    }
    
    /**
     * @param string $class
     * @return boolean
     */
    public function rendererExists($class)
    {
        $class = basename($class);
        if (!class_exists($class)) {
            include_once(LOG4PHP_DIR ."/or/{$class}.php");
        }
        return class_exists($class);
    }
}
