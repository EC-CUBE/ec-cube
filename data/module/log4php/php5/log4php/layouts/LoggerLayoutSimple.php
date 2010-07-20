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
 * @subpackage layouts
 */

/**
 * @ignore 
 */
if (!defined('LOG4PHP_DIR')) define('LOG4PHP_DIR', dirname(__FILE__) . '/..');

if (!defined('LOG4PHP_LINE_SEP')) {
    if (substr(php_uname(), 0, 7) == "Windows") { 
        define('LOG4PHP_LINE_SEP', "\r\n");
    } else {
        /**
         * @ignore
         */
        define('LOG4PHP_LINE_SEP', "\n");
    }
}

 
/**
 */
require_once(LOG4PHP_DIR . '/LoggerLayout.php');

/**
 * A simple layout.
 *
 * Returns the log statement in a format consisting of the
 * <b>level</b>, followed by " - " and then the <b>message</b>. 
 * For example, 
 * <samp> INFO - "A message" </samp>
 *
 * @author  Marco Vassura
 * @version $Revision: 635069 $
 * @package log4php
 * @subpackage layouts
 */  
class LoggerLayoutSimple extends LoggerLayout {
    
    /**
     * Constructor
     */
    function LoggerLayoutSimple()
    {
        return;
    }

    function activateOptions() 
    {
        return;
    }

    /**
     * Returns the log statement in a format consisting of the
     * <b>level</b>, followed by " - " and then the
     * <b>message</b>. For example, 
     * <samp> INFO - "A message" </samp>
     *
     * @param LoggerLoggingEvent $event
     * @return string
     */
    function format($event)
    {
        $level = $event->getLevel();
        return $level->toString() . ' - ' . $event->getRenderedMessage(). LOG4PHP_LINE_SEP;
    }
}
