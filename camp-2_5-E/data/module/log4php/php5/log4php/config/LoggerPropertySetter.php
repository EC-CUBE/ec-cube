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
 * @subpackage config
 */

/**
 * @ignore 
 */
if (!defined('LOG4PHP_DIR')) define('LOG4PHP_DIR', dirname(__FILE__) . '/..');

require_once(LOG4PHP_DIR . '/LoggerLog.php');
require_once(LOG4PHP_DIR . '/helpers/LoggerOptionConverter.php');

/**
 * General purpose Object property setter. Clients repeatedly invokes
 * {@link setProperty()} in order to invoke setters
 * on the Object specified in the constructor.
 *  
 * Usage:
 * <code>
 * $ps = new LoggerPropertySetter($anObject);
 * $ps->set("name", "Joe");
 * $ps->set("age", 32);
 * $ps->set("isMale", true);
 * </code>
 * will cause the invocations 
 * <code>
 * $anObject->setName("Joe");
 * $anObject->setAge(32);
 * $anObject->setMale(true)
 * </code>
 * if such methods exist.
 *  
 * @author  Marco Vassura
 * @version $Revision: 663201 $
 * @package log4php
 * @subpackage config
 * @since 0.5
 */
class LoggerPropertySetter {

    /**
     * @var object the target object
     * @access private
     */
    var $obj;
  
    /**
     * Create a new LoggerPropertySetter for the specified Object. 
     * This is done in prepartion for invoking {@link setProperty()} 
     * one or more times.
     * @param object &$obj the object for which to set properties
     */
    function LoggerPropertySetter(&$obj)
    {
        $this->obj =& $obj;
    }
  
    /**
     * Set the properties of an object passed as a parameter in one
     * go. The <code>properties</code> are parsed relative to a
     * <code>prefix</code>.
     *
     * @param object &$obj The object to configure.
     * @param array $properties An array containing keys and values.
     * @param string $prefix Only keys having the specified prefix will be set.
     * @static
     */
    public static function setPropertiesByObject(&$obj, $properties, $prefix)
    {
        $pSetter = new LoggerPropertySetter($obj);
        return $pSetter->setProperties($properties, $prefix);
    }
  

    /**
     * Set the properites for the object that match the
     * <code>prefix</code> passed as parameter.
     *
     * @param array $properties An array containing keys and values.
     * @param string $prefix Only keys having the specified prefix will be set.
     */
    function setProperties($properties, $prefix)
    {
        LoggerLog::debug("LoggerOptionConverter::setProperties():prefix=[{$prefix}]");

        $len = strlen($prefix);
        while (list($key,) = each($properties)) {
            if (strpos($key, $prefix) === 0) {
                if (strpos($key, '.', ($len + 1)) > 0)
                    continue;
                $value = LoggerOptionConverter::findAndSubst($key, $properties);
                $key = substr($key, $len);
                if ($key == 'layout' and ($this->obj instanceof LoggerAppender)) {
                    continue;
                }
                $this->setProperty($key, $value);
            }
        }
        $this->activate();
    }
    
    /**
     * Set a property on this PropertySetter's Object. If successful, this
     * method will invoke a setter method on the underlying Object. The
     * setter is the one for the specified property name and the value is
     * determined partly from the setter argument type and partly from the
     * value specified in the call to this method.
     *
     * <p>If the setter expects a String no conversion is necessary.
     * If it expects an int, then an attempt is made to convert 'value'
     * to an int using new Integer(value). If the setter expects a boolean,
     * the conversion is by new Boolean(value).
     *
     * @param string $name    name of the property
     * @param string $value   String value of the property
     */
    function setProperty($name, $value)
    {
        LoggerLog::debug("LoggerOptionConverter::setProperty():name=[{$name}]:value=[{$value}]");

        if ($value === null) return;
        
        $method = "set" . ucfirst($name);
        
        if (!method_exists($this->obj, $method)) {
            LoggerLog::warn(
                "LoggerOptionConverter::setProperty() No such setter method for [{$name}] property in " .
                        get_class($this->obj) . "." 
            );
        } else {
            return call_user_func(array(&$this->obj, $method), $value);
        } 
    }
  
    function activate()
    {
        LoggerLog::debug("LoggerOptionConverter::activate()");
    
        if (method_exists($this->obj, 'activateoptions')) {
            return call_user_func(array(&$this->obj, 'activateoptions'));
        } else {
            LoggerLog::debug("LoggerOptionConverter::activate() Nothing to activate.");
        }
    }
}
