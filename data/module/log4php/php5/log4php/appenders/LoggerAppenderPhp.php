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
 * @subpackage appenders
 */

/**
 * @ignore 
 */
if (!defined('LOG4PHP_DIR')) define('LOG4PHP_DIR', dirname(__FILE__) . '/..');
 
require_once(LOG4PHP_DIR . '/LoggerAppenderSkeleton.php');
require_once(LOG4PHP_DIR . '/LoggerLevel.php');
require_once(LOG4PHP_DIR . '/LoggerLog.php');

/**
 * Log events using php {@link PHP_MANUAL#trigger_error} function and a {@link LoggerLayoutTTCC} default layout.
 *
 * <p>Levels are mapped as follows:</p>
 * - <b>level &lt; WARN</b> mapped to E_USER_NOTICE
 * - <b>WARN &lt;= level &lt; ERROR</b> mapped to E_USER_WARNING
 * - <b>level &gt;= ERROR</b> mapped to E_USER_ERROR  
 *
 * @author  Marco Vassura
 * @version $Revision: 635069 $
 * @package log4php
 * @subpackage appenders
 */ 
class LoggerAppenderPhp extends LoggerAppenderSkeleton {

    
    public function activateOptions() {
        $this->layout = LoggerLayout::factory('LoggerLayoutTTCC');
        $this->closed = false;
    }

    public function close() {
        $this->closed = true;
    }

    public function append($event) {
        if ($this->layout !== null) {
            LoggerLog::debug("LoggerAppenderPhp::append()");
            $level = $event->getLevel();
            if ($level->isGreaterOrEqual(LoggerLevel::getLevelError())) {
                trigger_error($this->layout->format($event), E_USER_ERROR);
            } elseif ($level->isGreaterOrEqual(LoggerLevel::getLevelWarn())) {
                trigger_error($this->layout->format($event), E_USER_WARNING);
            } else {
                trigger_error($this->layout->format($event), E_USER_NOTICE);
            }
        }
    }
}
