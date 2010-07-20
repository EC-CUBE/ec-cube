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
require_once(LOG4PHP_DIR . '/spi/LoggerFilter.php');

/**
 * This filter drops all logging events. 
 * 
 * <p>You can add this filter to the end of a filter chain to
 * switch from the default "accept all unless instructed otherwise"
 * filtering behaviour to a "deny all unless instructed otherwise"
 * behaviour.</p>
 *
 * @author  Marco Vassura
 * @version $Revision: 635069 $
 * @package log4php
 * @subpackage varia
 * @since 0.3
 */
class LoggerDenyAllFilter extends LoggerFilter {

  /**
   * Always returns the integer constant {@link LOG4PHP_LOGGER_FILTER_DENY}
   * regardless of the {@link LoggerLoggingEvent} parameter.
   * 
   * @param LoggerLoggingEvent $event The {@link LoggerLoggingEvent} to filter.
   * @return LOG4PHP_LOGGER_FILTER_DENY Always returns {@link LOG4PHP_LOGGER_FILTER_DENY}
   */
  function decide($event)
  {
    return LOG4PHP_LOGGER_FILTER_DENY;
  }
}
