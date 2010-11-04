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
 
define('LOG4PHP_LOGGER_TRANSFORM_CDATA_START',          '<![CDATA[');
define('LOG4PHP_LOGGER_TRANSFORM_CDATA_END',            ']]>');
define('LOG4PHP_LOGGER_TRANSFORM_CDATA_PSEUDO_END',     ']]&gt;');
define('LOG4PHP_LOGGER_TRANSFORM_CDATA_EMBEDDED_END',   
    LOG4PHP_LOGGER_TRANSFORM_CDATA_END .
    LOG4PHP_LOGGER_TRANSFORM_CDATA_PSEUDO_END .
    LOG4PHP_LOGGER_TRANSFORM_CDATA_START 
);

/**
 * Utility class for transforming strings.
 *
 * @author  Marco Vassura
 * @package log4php
 * @subpackage helpers
 * @since 0.7
 */
class LoggerTransform {

    /**
    * This method takes a string which may contain HTML tags (ie,
    * &lt;b&gt;, &lt;table&gt;, etc) and replaces any '&lt;' and '&gt;'
    * characters with respective predefined entity references.
    *
    * @param string $input The text to be converted.
    * @return string The input string with the characters '&lt;' and '&gt;' replaced with
    *                &amp;lt; and &amp;gt; respectively.
    * @static  
    */
    function escapeTags($input)
    {
        //Check if the string is null or zero length -- if so, return
        //what was sent in.

        if(empty($input))
            return $input;

        //Use a StringBuffer in lieu of String concatenation -- it is
        //much more efficient this way.

        return htmlspecialchars($input, ENT_NOQUOTES);
    }

    /**
    * Ensures that embeded CDEnd strings (]]&gt;) are handled properly
    * within message, NDC and throwable tag text.
    *
    * @param string $buf    String holding the XML data to this point.  The
    *                       initial CDStart (<![CDATA[) and final CDEnd (]]>) 
    *                       of the CDATA section are the responsibility of 
    *                       the calling method.
    * @param string &str    The String that is inserted into an existing 
    *                       CDATA Section within buf.
    * @static  
    */
    function appendEscapingCDATA(&$buf, $str)
    {
        if(empty($str))
            return;
    
        $rStr = str_replace(
            LOG4PHP_LOGGER_TRANSFORM_CDATA_END,
            LOG4PHP_LOGGER_TRANSFORM_CDATA_EMBEDDED_END,
            $str
        );
        $buf .= $rStr;
    }
}
