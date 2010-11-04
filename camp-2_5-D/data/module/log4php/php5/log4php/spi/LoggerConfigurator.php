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
 * @subpackage spi
 */

/**
 * @ignore 
 */
if (!defined('LOG4PHP_DIR')) define('LOG4PHP_DIR', dirname(__FILE__)); 

/**
 * Special level value signifying inherited behaviour. The current
 * value of this string constant is <b>inherited</b>. 
 * {@link LOG4PHP_LOGGER_CONFIGURATOR_NULL} is a synonym.  
 */
define('LOG4PHP_LOGGER_CONFIGURATOR_INHERITED', 'inherited');

/**
 * Special level signifying inherited behaviour, same as 
 * {@link LOG4PHP_LOGGER_CONFIGURATOR_INHERITED}. 
 * The current value of this string constant is <b>null</b>. 
 */
define('LOG4PHP_LOGGER_CONFIGURATOR_NULL',      'null');

/**
 * Implemented by classes capable of configuring log4php using a URL.
 *  
 * @author  Marco Vassura
 * @version $Revision: 635069 $
 * @package log4php
 * @subpackage spi  
 */
interface LoggerConfigurator {

   /**
    * Interpret a resource pointed by a <var>url</var> and configure accordingly.
    *
    * The configuration is done relative to the <var>repository</var>
    * parameter.
    *
    * @param string $url The URL to parse
    */
    public static function configure($url=null);
    
}
