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
 * @subpackage varia
 */

/**
 * @ignore 
 */
if (!defined('LOG4PHP_DIR')) define('LOG4PHP_DIR', dirname(__FILE__) . '/..');
 
/**
 */
require_once(LOG4PHP_DIR . '/helpers/LoggerOptionConverter.php');
require_once(LOG4PHP_DIR . '/spi/LoggerFilter.php');

/**
 * This is a very simple filter based on level matching.
 *
 * <p>The filter admits two options <b><var>LevelToMatch</var></b> and
 * <b><var>AcceptOnMatch</var></b>. If there is an exact match between the value
 * of the <b><var>LevelToMatch</var></b> option and the level of the 
 * {@link LoggerLoggingEvent}, then the {@link decide()} method returns 
 * {@link LOG4PHP_LOGGER_FILTER_ACCEPT} in case the <b><var>AcceptOnMatch</var></b> 
 * option value is set to <i>true</i>, if it is <i>false</i> then 
 * {@link LOG4PHP_LOGGER_FILTER_DENY} is returned. If there is no match, 
 * {@link LOG4PHP_LOGGER_FILTER_NEUTRAL} is returned.</p>
 *
 * @author  Marco Vassura
 * @version $Revision: 635069 $
 * @package log4php
 * @subpackage varia
 * @since 0.6
 */
class LoggerLevelMatchFilter extends LoggerFilter {
  
    /**
     * @var boolean
     */
    var $acceptOnMatch = true;

    /**
     * @var LoggerLevel
     */
    var $levelToMatch;
  
    /**
     * @return boolean
     */
    function getAcceptOnMatch()
    {
        return $this->acceptOnMatch;
    }
    
    /**
     * @param boolean $acceptOnMatch
     */
    function setAcceptOnMatch($acceptOnMatch)
    {
        $this->acceptOnMatch = LoggerOptionConverter::toBoolean($acceptOnMatch, true); 
    }
    
    /**
     * @return LoggerLevel
     */
    function getLevelToMatch()
    {
        return $this->levelToMatch;
    }
    
    /**
     * @param string $l the level to match
     */
    function setLevelToMatch($l)
    {
        $this->levelToMatch = LoggerOptionConverter::toLevel($l, null);
    }

    /**
     * Return the decision of this filter.
     * 
     * Returns {@link LOG4PHP_LOGGER_FILTER_NEUTRAL} if the <b><var>LevelToMatch</var></b>
     * option is not set or if there is not match.  Otherwise, if there is a
     * match, then the returned decision is {@link LOG4PHP_LOGGER_FILTER_ACCEPT} if the
     * <b><var>AcceptOnMatch</var></b> property is set to <i>true</i>. The
     * returned decision is {@link LOG4PHP_LOGGER_FILTER_DENY} if the
     * <b><var>AcceptOnMatch</var></b> property is set to <i>false</i>.
     *
     * @param LoggerLoggingEvent $event
     * @return integer
     */
    function decide($event)
    {
        if($this->levelToMatch === null)
            return LOG4PHP_LOGGER_FILTER_NEUTRAL;
        
        if ($this->levelToMatch->equals($event->getLevel())) {  
            return $this->getAcceptOnMatch() ? 
                LOG4PHP_LOGGER_FILTER_ACCEPT : 
                LOG4PHP_LOGGER_FILTER_DENY;
        } else {
            return LOG4PHP_LOGGER_FILTER_NEUTRAL;
        }
    }
}
