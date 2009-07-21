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
 
/**
 */
require_once(LOG4PHP_DIR . '/LoggerAppender.php');
require_once(LOG4PHP_DIR . '/LoggerLog.php');
require_once(LOG4PHP_DIR . '/helpers/LoggerOptionConverter.php');

/**
 * Abstract superclass of the other appenders in the package.
 *  
 * This class provides the code for common functionality, such as
 * support for threshold filtering and support for general filters.
 *
 * @author  Marco Vassura
 * @author  Sergio Strampelli 
 * @version $Revision: 635069 $
 * @package log4php
 * @abstract
 */
abstract class LoggerAppenderSkeleton extends LoggerAppender {

    /**
     * @var boolean closed appender flag
     */
    protected $closed = false;
    
    /**
     * @var object unused
     */
    protected $errorHandler;
           
    /**
     * The first filter in the filter chain
     * @var LoggerFilter
     */
    protected $headFilter = null;
            
    /**
     * LoggerLayout for this appender. It can be null if appender has its own layout
     * @var LoggerLayout
     */
    protected $layout = null; 
           
    /**
     * @var string Appender name
     */
    protected $name;
           
    /**
     * The last filter in the filter chain
     * @var LoggerFilter
     */
    protected $tailFilter = null; 
           
    /**
     * @var LoggerLevel There is no level threshold filtering by default.
     */
    protected $threshold = null;
    
    /**
     * @var boolean needs a layout formatting ?
     */
    protected $requiresLayout = false;
    
    /**
     * Constructor
     *
     * @param string $name appender name
     */
    public function __construct($name) {
        $this->name = $name;
        $this->clearFilters();
    }

    /**
     * @param LoggerFilter $newFilter add a new LoggerFilter
     * @see LoggerAppender::addFilter()
     */
    public function addFilter($newFilter) {
        if($this->headFilter === null) {
            $this->headFilter = $newFilter;
            $this->tailFilter = $this->headFilter;
        } else {
            $this->tailFilter->next = $newFilter;
            $this->tailFilter = $this->tailFilter->next;
        }
    }
    
    /**
     * Derived appenders should override this method if option structure
     * requires it.
     */
    abstract public function activateOptions();    
    
    /**
     * Subclasses of {@link LoggerAppenderSkeleton} should implement 
     * this method to perform actual logging.
     *
     * @param LoggerLoggingEvent $event
     * @see doAppend()
     * @abstract
     */
    abstract protected function append($event);
 
    /**
     * @see LoggerAppender::clearFilters()
     */
    public function clearFilters()
    {
        unset($this->headFilter);
        unset($this->tailFilter);
        $this->headFilter = null;
        $this->tailFilter = null;
    }
           
    /**
     * Finalize this appender by calling the derived class' <i>close()</i> method.
     */
    public function finalize() 
    {
        // An appender might be closed then garbage collected. There is no
        // point in closing twice.
        if ($this->closed) return;
        
        LoggerLog::debug("LoggerAppenderSkeleton::finalize():name=[{$this->name}].");
        
        $this->close();
    }
    
    /**
     * Do not use this method.
     * @see LoggerAppender::getErrorHandler()
     * @return object
     */
    public function getErrorHandler()
    {
        return $this->errorHandler;
    } 
           
    /**
     * @see LoggerAppender::getFilter()
     * @return LoggerFilter
     */
    public function getFilter()
    {
        return $this->headFilter;
    } 

    /** 
     * Return the first filter in the filter chain for this Appender. 
     * The return value may be <i>null</i> if no is filter is set.
     * @return LoggerFilter
     */
    public function getFirstFilter()
    {
        return $this->headFilter;
    }
            
    /**
     * @see LoggerAppender::getLayout()
     * @return LoggerLayout
     */
    public function getLayout()
    {
        return $this->layout;
    }
           
    /**
     * @see LoggerAppender::getName()
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
    
    /**
     * Returns this appenders threshold level. 
     * See the {@link setThreshold()} method for the meaning of this option.
     * @return LoggerLevel
     */
    public function getThreshold()
    { 
        return $this->threshold;
    }
    
    /**
     * Check whether the message level is below the appender's threshold. 
     *
     *
     * If there is no threshold set, then the return value is always <i>true</i>.
     * @param LoggerLevel $priority
     * @return boolean true if priority is greater or equal than threshold  
     */
    public function isAsSevereAsThreshold($priority)
    {
        if ($this->threshold === null)
            return true;
            
        return $priority->isGreaterOrEqual($this->getThreshold());
    }
    
    /**
     * @see LoggerAppender::doAppend()
     * @param LoggerLoggingEvent $event
     */
    public function doAppend($event)
    {
        LoggerLog::debug("LoggerAppenderSkeleton::doAppend()"); 

        if ($this->closed) {
            LoggerLog::debug("LoggerAppenderSkeleton::doAppend() Attempted to append to closed appender named [{$this->name}].");
            return;
        }
        if(!$this->isAsSevereAsThreshold($event->getLevel())) {
            LoggerLog::debug("LoggerAppenderSkeleton::doAppend() event level is less severe than threshold.");
            return;
        }

        $f = $this->getFirstFilter();
    
        while($f !== null) {
            switch ($f->decide($event)) {
                case LOG4PHP_LOGGER_FILTER_DENY: return;
                case LOG4PHP_LOGGER_FILTER_ACCEPT: return $this->append($event);
                case LOG4PHP_LOGGER_FILTER_NEUTRAL: $f = $f->getNext();
            }
        }
        $this->append($event);    
    }    
        
            
    /**
     * @see LoggerAppender::requiresLayout()
     * @return boolean
     */
    public function requiresLayout()
    {
        return $this->requiresLayout;
    }
            
    /**
     * @see LoggerAppender::setErrorHandler()
     * @param object
     */
    public function setErrorHandler($errorHandler)
    {
        if($errorHandler == null) {
          // We do not throw exception here since the cause is probably a
          // bad config file.
            LoggerLog::warn("You have tried to set a null error-handler.");
        } else {
            $this->errorHandler = $errorHandler;
        }
    } 
           
    /**
     * @see LoggerAppender::setLayout()
     * @param LoggerLayout $layout
     */
    public function setLayout($layout)
    {
        if ($this->requiresLayout())
            $this->layout = $layout;
    } 
 
    /**
     * @see LoggerAppender::setName()
     * @param string $name
     */
    public function setName($name) 
    {
        $this->name = $name;    
    }
    
    /**
     * Set the threshold level of this appender.
     *
     * @param mixed $threshold can be a {@link LoggerLevel} object or a string.
     * @see LoggerOptionConverter::toLevel()
     */
    public function setThreshold($threshold)
    {
        if (is_string($threshold)) {
           $this->threshold = LoggerOptionConverter::toLevel($threshold, null);
        }elseif ($threshold instanceof LoggerLevel) {
           $this->threshold = $threshold;
        }
    }
    
    /**
     * Perform actions before object serialization.
     *
     * Call {@link finalize()} to properly close the appender.
     */
    function __sleep()
    {
        $this->finalize();
        return array_keys(get_object_vars($this)); 
    }
    
    /**
     * Perform actions after object de-serialization.
     *
     * Call {@link activateOptions()} to properly setup the appender.
     */
    function __wakeup()
    {
        $this->activateOptions();
    }
    
}
