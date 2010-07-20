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
 */

/**
 * @ignore 
 */
if (!defined('LOG4PHP_DIR')) define('LOG4PHP_DIR', dirname(__FILE__));

require_once(LOG4PHP_DIR . '/spi/LoggerConfigurator.php');
require_once(LOG4PHP_DIR . '/layouts/LoggerLayoutTTCC.php');
require_once(LOG4PHP_DIR . '/appenders/LoggerAppenderConsole.php');
require_once(LOG4PHP_DIR . '/LoggerManager.php');

/**
 * Use this class to quickly configure the package.
 *
 * <p>For file based configuration see {@link LoggerPropertyConfigurator}. 
 * <p>For XML based configuration see {@link LoggerDOMConfigurator}.
 *
 * @author  Marco Vassura
 * @version $Revision: 635069 $
 * @package log4php
 */
class LoggerBasicConfigurator implements LoggerConfigurator {

    /**
     * Add a {@link LoggerAppenderConsole} that uses 
     * the {@link LoggerLayoutTTCC} to the root category.
     * 
     * @param string $url not used here
     */
    public static function configure($url = null)
    {
        $root = LoggerManager::getRootLogger();
        $appender = new LoggerAppenderConsole('A1');
        $appender->setLayout( new LoggerLayoutTTCC() );
        $root->addAppender($appender);
    }

    /**
     * Reset the default hierarchy to its default. 
     * It is equivalent to
     * <code>
     * LoggerManager::resetConfiguration();
     * </code>
     *
     * @see LoggerHierarchy::resetConfiguration()
     * @static
     */
    public static function resetConfiguration()
    {
        LoggerManager::resetConfiguration();
    }
}
