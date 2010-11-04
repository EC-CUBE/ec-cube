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
require_once(LOG4PHP_DIR . '/helpers/LoggerOptionConverter.php');
require_once(LOG4PHP_DIR . '/LoggerLog.php');

/**
 * FileAppender appends log events to a file.
 *
 * Parameters are ({@link $fileName} but option name is <b>file</b>), 
 * {@link $append}.
 *
 * @author  Marco Vassura
 * @author Knut Urdalen <knut.urdalen@gmail.com>
 * @version $Revision: 640255 $
 * @package log4php
 * @subpackage appenders
 */
class LoggerAppenderFile extends LoggerAppenderSkeleton {

    /**
     * @var boolean if {@link $file} exists, appends events.
     */
    private $append = true;
    /**
     * @var string the file name used to append events
     */
	protected $fileName;
    /**
     * @var mixed file resource
     */
	protected $fp = false;
    
	public function __construct($name) {
		parent::__construct($name);
		$this->requiresLayout = true;
	}

    public function activateOptions() {
        $fileName = $this->getFile();
        LoggerLog::debug("LoggerAppenderFile::activateOptions() opening file '{$fileName}'");
        $this->fp = fopen($fileName, ($this->getAppend()? 'a':'w'));
        if ($this->fp) {
            if ($this->getAppend())
                fseek($this->fp, 0, SEEK_END);
            fwrite($this->fp, $this->layout->getHeader());
            $this->closed = false;
        } else {
            $this->closed = true;
        }
    }
    
    public function close() {
        if($this->fp and $this->layout !== null) {
                        fwrite($this->fp, $this->layout->getFooter());
                }
            
        $this->closeFile();
        $this->closed = true;
    }

    /**
     * Closes the previously opened file.
     */
    public function closeFile() {
        if ($this->fp)
            fclose($this->fp);
    }
    
    /**
     * @return boolean
     */
    public function getAppend() {
        return $this->append;
    }

    /**
     * @return string
     */
    public function getFile() {
        return $this->getFileName();
    }
    
    /**
     * @return string
     */
    public function getFileName() {
        return $this->fileName;
    } 
 
    /**
     * Close any previously opened file and call the parent's reset.
     */
    public function reset() {
        $this->closeFile();
        $this->fileName = null;
        parent::reset();
    }

    public function setAppend($flag) {
        $this->append = LoggerOptionConverter::toBoolean($flag, true);        
    } 
  
    /**
     * Sets and opens the file where the log output will go.
     *
     * This is an overloaded method. It can be called with:
     * - setFile(string $fileName) to set filename.
     * - setFile(string $fileName, boolean $append) to set filename and append.
     */
    public function setFile() {
        $numargs = func_num_args();
        $args    = func_get_args();

        if ($numargs == 1 and is_string($args[0])) {
            $this->setFileName($args[0]);
        } elseif ($numargs >=2 and is_string($args[0]) and is_bool($args[1])) {
            $this->setFile($args[0]);
            $this->setAppend($args[1]);
        }
    }
    
    public function setFileName($fileName) {
        $this->fileName = $fileName;
    }

    public function append($event) {
        if ($this->fp and $this->layout !== null) {
            LoggerLog::debug("LoggerAppenderFile::append()");
            fwrite($this->fp, $this->layout->format($event));
        } 
    }
}
