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
require_once(LOG4PHP_DIR . '/LoggerLog.php');

define('LOG4PHP_LEVEL_OFF_INT',     2147483647); 
define('LOG4PHP_LEVEL_FATAL_INT',        50000);
define('LOG4PHP_LEVEL_ERROR_INT',        40000);
define('LOG4PHP_LEVEL_WARN_INT',         30000);
define('LOG4PHP_LEVEL_INFO_INT',         20000);
define('LOG4PHP_LEVEL_DEBUG_INT',        10000);
define('LOG4PHP_LEVEL_ALL_INT',    -2147483647);

/**
 * Defines the minimum set of levels recognized by the system, that is
 * <i>OFF</i>, <i>FATAL</i>, <i>ERROR</i>,
 * <i>WARN</i>, <i>INFO</i, <i>DEBUG</i> and
 * <i>ALL</i>.
 *
 * <p>The <i>LoggerLevel</i> class may be subclassed to define a larger
 * level set.</p>
 *
 * @author  Marco Vassura
 * @version $Revision: 635069 $
 * @package log4php
 * @since 0.5
 */
class LoggerLevel {

    /**
     * @var integer
     */
    public $level;
  
    /**
     * @var string
     */
    public $levelStr;
  
    /**
     * @var integer
     */
    public $syslogEquivalent;

    /**
     * Constructor
     *
     * @param integer $level
     * @param string $levelStr
     * @param integer $syslogEquivalent
     */
    public function __construct($level, $levelStr, $syslogEquivalent)
    {
        $this->level = $level;
        $this->levelStr = $levelStr;
        $this->syslogEquivalent = $syslogEquivalent;
    }

    /**
     * Two priorities are equal if their level fields are equal.
     *
     * @param object $o
     * @return boolean 
     */
    public function equals($o)
    {
        if ($o instanceof LoggerLevel) {
            return ($this->level == $o->level);
        } else {
            return false;
        }
    }
    
    /**
     * Returns an Off Level
     * @static
     * @return LoggerLevel
     */
    public static function getLevelOff()
    {
        static $level;
        if (!isset($level)) $level = new LoggerLevel(LOG4PHP_LEVEL_OFF_INT, 'OFF', 0);
        return $level;
    }

    /**
     * Returns a Fatal Level
     * @static
     * @return LoggerLevel
     */
    public static function getLevelFatal()
    {
        static $level;
        if (!isset($level)) $level = new LoggerLevel(LOG4PHP_LEVEL_FATAL_INT, 'FATAL', 0);
        return $level;
    }
    
    /**
     * Returns an Error Level
     * @static
     * @return LoggerLevel
     */
    public static function getLevelError()
    {
        static $level;
        if (!isset($level)) $level = new LoggerLevel(LOG4PHP_LEVEL_ERROR_INT, 'ERROR', 3);
        return $level;
    }
    
    /**
     * Returns a Warn Level
     * @static
     * @return LoggerLevel
     */
    public static function getLevelWarn()
    {
        static $level;
        if (!isset($level)) $level = new LoggerLevel(LOG4PHP_LEVEL_WARN_INT, 'WARN', 4);
        return $level;
    }

    /**
     * Returns an Info Level
     * @static
     * @return LoggerLevel
     */
    public static function getLevelInfo()
    {
        static $level;
        if (!isset($level)) $level = new LoggerLevel(LOG4PHP_LEVEL_INFO_INT, 'INFO', 6);
        return $level;
    }

    /**
     * Returns a Debug Level
     * @static
     * @return LoggerLevel
     */
    public static function getLevelDebug()
    {
        static $level;
        if (!isset($level)) $level = new LoggerLevel(LOG4PHP_LEVEL_DEBUG_INT, 'DEBUG', 7);
        return $level;
    }

    /**
     * Returns an All Level
     * @static
     * @return LoggerLevel
     */
    public static function getLevelAll()
    {
        static $level;
        if (!isset($level)) $level = new LoggerLevel(LOG4PHP_LEVEL_ALL_INT, 'ALL', 7);
        return $level;
    }
    
    /**
     * Return the syslog equivalent of this priority as an integer.
     * @final
     * @return integer
     */
    public function getSyslogEquivalent()
    {
        return $this->syslogEquivalent;
    }

    /**
     * Returns <i>true</i> if this level has a higher or equal
     * level than the level passed as argument, <i>false</i>
     * otherwise.  
     * 
     * <p>You should think twice before overriding the default
     * implementation of <i>isGreaterOrEqual</i> method.
     *
     * @param LoggerLevel $r
     * @return boolean
     */
    public function isGreaterOrEqual($r)
    {
        return $this->level >= $r->level;
    }

    /**
     * Returns the string representation of this priority.
     * @return string
     * @final
     */
    public function toString()
    {
        return $this->levelStr;
    }

    /**
     * Returns the integer representation of this level.
     * @return integer
     */
    public function toInt()
    {
        return $this->level;
    }

    /**
     * Convert the string passed as argument to a level. If the
     * conversion fails, then this method returns a DEBUG Level.
     *
     * @param mixed $arg
     * @param LoggerLevel $default
     * @static 
     */
    public static function toLevel($arg, $defaultLevel = null)
    {
        if ($defaultLevel === null) {
            return self::toLevel($arg, self::getLevelDebug());
        } else {
            if (is_int($arg)) {
                switch($arg) {
                    case LOG4PHP_LEVEL_ALL_INT:     return self::getLevelAll();
                    case LOG4PHP_LEVEL_DEBUG_INT:   return self::getLevelDebug();
                    case LOG4PHP_LEVEL_INFO_INT:    return self::getLevelInfo();
                    case LOG4PHP_LEVEL_WARN_INT:    return self::getLevelWarn();
                    case LOG4PHP_LEVEL_ERROR_INT:   return self::getLevelError();
                    case LOG4PHP_LEVEL_FATAL_INT:   return self::getLevelFatal();
                    case LOG4PHP_LEVEL_OFF_INT:     return self::getLevelOff();
                    default:                        return $defaultLevel;
                }
            } else {
                switch(strtoupper($arg)) {
                    case 'ALL':     return self::getLevelAll();
                    case 'DEBUG':   return self::getLevelDebug();
                    case 'INFO':    return self::getLevelInfo();
                    case 'WARN':    return self::getLevelWarn();
                    case 'ERROR':   return self::getLevelError();
                    case 'FATAL':   return self::getLevelFatal();
                    case 'OFF':     return self::getLevelOff();
                    default:        return $defaultLevel;
                }
            }
        }
    }
}
