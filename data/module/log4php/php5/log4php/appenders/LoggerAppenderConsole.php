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

/** @ignore */
if (!defined('LOG4PHP_DIR')) define('LOG4PHP_DIR', dirname(__FILE__) . '/..');
 
/**
 */
require_once(LOG4PHP_DIR . '/LoggerAppenderSkeleton.php');
require_once(LOG4PHP_DIR . '/LoggerLog.php');


/**
 * ConsoleAppender appends log events to STDOUT or STDERR using a layout specified by the user. 
 * 
 * <p>Optional parameter is {@link $target}. The default target is Stdout.</p>
 * <p><b>Note</b>: Use this Appender with command-line php scripts. 
 * On web scripts this appender has no effects.</p>
 * <p>This appender requires a layout.</p>  
 *
 * @author  Marco Vassura
 * @author Knut Urdalen <knut.urdalen@gmail.com>
 * @version $Revision: 635069 $
 * @package log4php
 * @subpackage appender
 */
class LoggerAppenderConsole extends LoggerAppenderSkeleton {

    const STDOUT = 'php://stdout';
    const STDERR = 'php://stderr';

    /**
     * Can be 'php://stdout' or 'php://stderr'. But it's better to use keywords <b>STDOUT</b> and <b>STDERR</b> (case insensitive). 
     * Default is STDOUT
     * @var string    
     */
    protected $target = 'php://stdout';
    
    /**
     * @var boolean
     * @access private     
     */
    protected $requiresLayout = true;

    /**
     * @var mixed the resource used to open stdout/stderr
     * @access private     
     */
    protected $fp = false;

    /**
     * Set console target.
     * @param mixed $value a constant or a string
     */
    public function setTarget($value) {
        $v = trim($value);
        if ($v == self::STDOUT || strtoupper($v) == 'STDOUT') {
            $this->target = self::STDOUT;
        } elseif ($v == self::STDERR || strtoupper($v) == 'STDERR') {
            $target = self::STDERR;
        } else {
            LoggerLog::debug("Invalid target. Using '".self::STDOUT."' by default.");        
        }
    }

    public function getTarget() {
        return $this->target;
    }

    public function activateOptions() {
        $this->fp = fopen($this->getTarget(), 'w');
        if($this->fp !== false && $this->layout !== null) {
            fwrite($this->fp, $this->layout->getHeader());
        }
        $this->closed = (bool)($this->fp === false);
    }
    
    /**
     * @see LoggerAppender::close()
     */
    public function close() {
        if ($this->fp && $this->layout !== null) {
            fwrite($this->fp, $this->layout->getFooter());
                        fclose($this->fp);
        }        
        $this->closed = true;
    }

    protected function append($event) {
        if ($this->fp && $this->layout !== null) {
            fwrite($this->fp, $this->layout->format($event));
        } 
    }
}

