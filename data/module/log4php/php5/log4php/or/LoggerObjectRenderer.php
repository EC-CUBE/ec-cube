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
 * Subclass this abstract class in order to render objects as strings.
 *
 * @author  Marco Vassura
 * @version $Revision: 635069 $
 * @package log4php
 * @subpackage or
 * @abstract
 * @since 0.3
 */
abstract class LoggerObjectRenderer {

    /**
     * @param string $class classname
     * @return LoggerObjectRenderer create LoggerObjectRenderer instances
     */
    public static function factory($class) {
        if (!empty($class)) {
            $class = basename($class);
            include_once LOG4PHP_DIR."/or/{$class}.php";
            if (class_exists($class)) {
                return new $class();
            }
        }
        return null;
    }

    /**
     * Render the entity passed as parameter as a String.
     * @param mixed $o entity to render
     * @return string
     */
    abstract public function doRender($o);
}
