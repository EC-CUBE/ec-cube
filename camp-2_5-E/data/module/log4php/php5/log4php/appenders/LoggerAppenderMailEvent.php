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

/**
 */
require_once(LOG4PHP_DIR . '/LoggerAppenderSkeleton.php');
require_once(LOG4PHP_DIR . '/LoggerLog.php');

/**
 * Log events to an email address. It will be created an email for each event. 
 *
 * <p>Parameters are 
 * {@link $smtpHost} (optional), 
 * {@link $port} (optional), 
 * {@link $from} (optional), 
 * {@link $to}, 
 * {@link $subject} (optional).</p>
 * <p>A layout is required.</p>
 *
 * @author  Domenico Lordi <lordi@interfree.it>
 * @author  Marco Vassura
 * @version $Revision: 635069 $
 * @package log4php
 * @subpackage appenders
 */
class LoggerAppenderMailEvent extends LoggerAppenderSkeleton {

    /**
     * @var string 'from' field
     */
    var $from           = null;

    /**
     * @var integer 'from' field
     */
    var $port           = 25;

    /**
     * @var string hostname. 
     */
    var $smtpHost       = null;

    /**
     * @var string 'subject' field
     */
    var $subject        = '';

    /**
     * @var string 'to' field
     */
    var $to             = null;
    
    /**
     * @access private
     */
    var $requiresLayout = true;

    /**
     * Constructor.
     *
     * @param string $name appender name
     */
    function LoggerAppenderMailEvent($name)
    {
        $this->LoggerAppenderSkeleton($name);
    }

    function activateOptions()
    { 
        $this->closed = false;
    }
    
    function close()
    {
        $this->closed = true;
    }

    /**
     * @return string
     */
    function getFrom()      { return $this->from; }
    
    /**
     * @return integer
     */
    function getPort()      { return $this->port; }
    
    /**
     * @return string
     */
    function getSmtpHost()  { return $this->smtpHost; }
    
    /**
     * @return string
     */
    function getSubject()   { return $this->subject; }

    /**
     * @return string
     */
    function getTo()        { return $this->to; }

    function setFrom($from)             { $this->from = $from; }
    function setPort($port)             { $this->port = (int)$port; }
    function setSmtpHost($smtpHost)     { $this->smtpHost = $smtpHost; }
    function setSubject($subject)       { $this->subject = $subject; }
    function setTo($to)                 { $this->to = $to; }

    function append($event)
    {
        $from = $this->getFrom();
        $to   = $this->getTo();
        if (empty($from) or empty($to))
            return;
    
        $smtpHost = $this->getSmtpHost();
        $prevSmtpHost = ini_get('SMTP');
        if (!empty($smtpHost)) {
            ini_set('SMTP', $smtpHost);
        } else {
            $smtpHost = $prevSmtpHost;
        } 

        $smtpPort = $this->getPort();
        $prevSmtpPort= ini_get('smtp_port');        
        if ($smtpPort > 0 and $smtpPort < 65535) {
            ini_set('smtp_port', $smtpPort);
        } else {
            $smtpPort = $prevSmtpPort;
        } 
        
        LoggerLog::debug(
            "LoggerAppenderMailEvent::append()" . 
            ":from=[{$from}]:to=[{$to}]:smtpHost=[{$smtpHost}]:smtpPort=[{$smtpPort}]"
        ); 
        
        if (!@mail( $to, $this->getSubject(), 
            $this->layout->getHeader() . $this->layout->format($event) . $this->layout->getFooter($event), 
            "From: {$from}\r\n"
        )) {
            LoggerLog::debug("LoggerAppenderMailEvent::append() mail error");
        }
            
        ini_set('SMTP',         $prevSmtpHost);
        ini_set('smtp_port',    $prevSmtpPort);
    }
}

