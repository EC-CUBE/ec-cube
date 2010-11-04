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
 * @subpackage helpers
 */

/**
 * @ignore 
 */
if (!defined('LOG4PHP_DIR')) define('LOG4PHP_DIR', dirname(__FILE__) . '/..');
 
/**
 */
require_once(LOG4PHP_DIR . '/LoggerLog.php');

/**
 * This class encapsulates the information obtained when parsing
 * formatting modifiers in conversion modifiers.
 * 
 * @author  Marco Vassura
 * @package log4php
 * @subpackage spi
 * @since 0.3
 */
class LoggerFormattingInfo {

    var $min        = -1;
    var $max        = 0x7FFFFFFF;
    var $leftAlign  = false;

    /**
     * Constructor
     */
    function LoggerFormattingInfo() {}
    
    function reset()
    {
        $this->min          = -1;
        $this->max          = 0x7FFFFFFF;
        $this->leftAlign    = false;      
    }

    function dump()
    {
        LoggerLog::debug("LoggerFormattingInfo::dump() min={$this->min}, max={$this->max}, leftAlign={$this->leftAlign}");
    }
} 
