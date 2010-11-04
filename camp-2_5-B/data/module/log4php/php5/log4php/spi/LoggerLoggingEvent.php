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
if (!defined('LOG4PHP_DIR')) define('LOG4PHP_DIR', dirname(__FILE__) . '/..');
 
/**
 */
require_once(LOG4PHP_DIR . '/spi/LoggerLocationInfo.php');
require_once(LOG4PHP_DIR . '/LoggerManager.php');
require_once(LOG4PHP_DIR . '/LoggerMDC.php');
require_once(LOG4PHP_DIR . '/LoggerNDC.php');

/**
 * The internal representation of logging event.
 *
 * @author  Marco Vassura
 * @version $Revision: 635069 $
 * @package log4php
 * @subpackage spi 
 */
class LoggerLoggingEvent {

        private static $startTime;

    /** 
    * @var string Fully Qualified Class Name of the calling category class.
    */
    var $fqcn;
    
    /**
    * @var Logger reference
    */
    var $logger = null;
    
    /** 
    * The category (logger) name.
    * This field will be marked as private in future
    * releases. Please do not access it directly. 
    * Use the {@link getLoggerName()} method instead.
    * @deprecated 
    */
    var $categoryName;
    
    /** 
    * Level of logging event.
    * <p> This field should not be accessed directly. You shoud use the
    * {@link getLevel()} method instead.
    *
    * @deprecated
    * @var LoggerLevel
    */
    protected $level;
    
    /** 
     * @var string The nested diagnostic context (NDC) of logging event. 
     */
    var $ndc;
    
    /** 
     * Have we tried to do an NDC lookup? If we did, there is no need
     * to do it again.  Note that its value is always false when
     * serialized. Thus, a receiving SocketNode will never use it's own
     * (incorrect) NDC. See also writeObject method.
     * @var boolean
     */
    var $ndcLookupRequired = true;
    
    /** 
     * Have we tried to do an MDC lookup? If we did, there is no need
     * to do it again.  Note that its value is always false when
     * serialized. See also the getMDC and getMDCCopy methods.
     * @var boolean  
     */
    var $mdcCopyLookupRequired = true;
    
    /** 
     * @var mixed The application supplied message of logging event. 
     */
    var $message;
    
    /** 
     * The application supplied message rendered through the log4php
     * objet rendering mechanism. At present renderedMessage == message.
     * @var string
     */
    var $renderedMessage = null;
    
    /** 
     * The name of thread in which this logging event was generated.
     * log4php saves here the process id via {@link PHP_MANUAL#getmypid getmypid()} 
     * @var mixed
     */
    var $threadName = null;
    
    /** 
    * The number of seconds elapsed from 1/1/1970 until logging event
    * was created plus microseconds if available.
    * @var float
    */
    public $timeStamp;
    
    /** 
    * @var LoggerLocationInfo Location information for the caller. 
    */
    var $locationInfo = null;
    
    /**
    * Instantiate a LoggingEvent from the supplied parameters.
    *
    * <p>Except {@link $timeStamp} all the other fields of
    * LoggerLoggingEvent are filled when actually needed.
    *
    * @param string $fqcn name of the caller class.
    * @param mixed $logger The {@link Logger} category of this event or the logger name.
    * @param LoggerLevel $priority The level of this event.
    * @param mixed $message The message of this event.
    * @param integer $timeStamp the timestamp of this logging event.
    */
    public function __construct($fqcn, $logger, $priority, $message, $timeStamp = null)
    {
        $this->fqcn = $fqcn;
        if ($logger instanceof Logger) {
            $this->logger = $logger;
            $this->categoryName = $logger->getName();
        } else {
            $this->categoryName = strval($logger);
        }
        $this->level = $priority;
        $this->message = $message;
        if ($timeStamp !== null && is_float($timeStamp)) {
            $this->timeStamp = $timeStamp;
        } else {
            if (function_exists('microtime')) {
                                // get microtime as float
                $this->timeStamp = microtime(true);
            } else {
                $this->timeStamp = floatval(time());
            }
        }
    }

    /**
     * Set the location information for this logging event. The collected
     * information is cached for future use.
     *
     * <p>This method uses {@link PHP_MANUAL#debug_backtrace debug_backtrace()} function (if exists)
     * to collect informations about caller.</p>
     * <p>It only recognize informations generated by {@link Logger} and its subclasses.</p>
     * @return LoggerLocationInfo
     */
    public function getLocationInformation()
    {
        if($this->locationInfo === null) {

            $locationInfo = array();

            if (function_exists('debug_backtrace')) {
                $trace = debug_backtrace();
                $prevHop = null;
                // make a downsearch to identify the caller
                $hop = array_pop($trace);
                while ($hop !== null) {
                    $className = @strtolower($hop['class']);
                    if ( !empty($className) and ($className == 'logger' or $className == 'loggercategory' or 
                                @strtolower(get_parent_class($className)) == 'logger' or
                                @strtolower(get_parent_class($className)) == 'loggercategory')) {
                        $locationInfo['line'] = $hop['line'];
                        $locationInfo['file'] = $hop['file'];                         
                        break;
                    }
                    $prevHop = $hop;
                    $hop = array_pop($trace);
                }
                $locationInfo['class'] = isset($prevHop['class']) ? $prevHop['class'] : 'main';
                if (isset($prevHop['function']) and
                    $prevHop['function'] !== 'include' and
                    $prevHop['function'] !== 'include_once' and
                    $prevHop['function'] !== 'require' and
                    $prevHop['function'] !== 'require_once') {                                        
    
                    $locationInfo['function'] = $prevHop['function'];
                } else {
                    $locationInfo['function'] = 'main';
                }
            }
                     
            $this->locationInfo = new LoggerLocationInfo($locationInfo, $this->fqcn);
        }
        return $this->locationInfo;
    }

    /**
     * Return the level of this event. Use this form instead of directly
     * accessing the {@link $level} field.
     * @return LoggerLevel  
     */
    public function getLevel()
    {
        return $this->level;
    }

    /**
     * Return the name of the logger. Use this form instead of directly
     * accessing the {@link $categoryName} field.
     * @return string  
     */
    public function getLoggerName()
    {
        return $this->categoryName;
    }

    /**
     * Return the message for this logging event.
     *
     * <p>Before serialization, the returned object is the message
     * passed by the user to generate the logging event. After
     * serialization, the returned value equals the String form of the
     * message possibly after object rendering.
     * @return mixed
     */
    public function getMessage()
    {
        if($this->message !== null) {
            return $this->message;
        } else {
            return $this->getRenderedMessage();
        }
    }

    /**
     * This method returns the NDC for this event. It will return the
     * correct content even if the event was generated in a different
     * thread or even on a different machine. The {@link LoggerNDC::get()} method
     * should <b>never</b> be called directly.
     * @return string  
     */
    public function getNDC()
    {
        if ($this->ndcLookupRequired) {
            $this->ndcLookupRequired = false;
            $this->ndc = implode(' ',LoggerNDC::get());
        }
        return $this->ndc;
    }


    /**
     * Returns the the context corresponding to the <code>key</code>
     * parameter.
     * @return string
     */
    public function getMDC($key)
    {
        return LoggerMDC::get($key);
    }

    /**
     * Render message.
     * @return string
     */
    public function getRenderedMessage()
    {
        if($this->renderedMessage === null and $this->message !== null) {
            if (is_string($this->message)) {
                    $this->renderedMessage = $this->message;
            } else {
                if ($this->logger !== null) {
                    $repository = $this->logger->getLoggerRepository();
                } else {
                    $repository = LoggerManager::getLoggerRepository();
                }
                if (method_exists($repository, 'getRendererMap')) {
                    $rendererMap = $repository->getRendererMap();
                        $this->renderedMessage= $rendererMap->findAndRender($this->message);
                    } else {
                        $this->renderedMessage = (string)$this->message;
                    }
            }
        }
        return $this->renderedMessage;
    }

    /**
     * Returns the time when the application started, in seconds
     * elapsed since 01.01.1970 plus microseconds if available.
     *
     * @return float
     * @static
     */
    public static function getStartTime() {
        if (!isset(self::$startTime)) {
            if (function_exists('microtime')) {
                // microtime as float
                self::$startTime = microtime(true);
            } else {
                self::$startTime = floatval(time());
            }
        }
        return self::$startTime; 
    }
    
    /**
     * @return float
     */
    public function getTimeStamp()
    {
        return $this->timeStamp;
    }
    
    /**
     * @return mixed
     */
    public function getThreadName()
    {
        if ($this->threadName === null)
            $this->threadName = (string)getmypid();
        return $this->threadName;
    }

    /**
     * @return mixed null
     */
    public function getThrowableInformation()
    {
        return null;
    }
    
    /**
     * Serialize this object
     * @return string
     */
    public function toString()
    {
        serialize($this);
    }
    
    /**
     * Avoid serialization of the {@link $logger} object
     */
    public function __sleep()
    {
        return array(
            'fqcn','categoryName',
            'level',
            'ndc','ndcLookupRequired',
            'message','renderedMessage',
            'threadName',
            'timeStamp',
            'locationInfo'
        );
    }

}

LoggerLoggingEvent::getStartTime();

