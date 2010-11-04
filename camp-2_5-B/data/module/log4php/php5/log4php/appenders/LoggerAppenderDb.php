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
require_once('DB.php');

/**
 * Appends log events to a db table using PEAR::DB class.
 *
 * <p>This appender uses a table in a database to log events.</p>
 * <p>Parameters are {@link $dsn}, {@link $createTable}, {@link table} and {@link $sql}.</p>
 * <p>See examples in test directory.</p>
 *
 * @author  Marco Vassura
 * @version $Revision: 635069 $
 * @package log4php
 * @subpackage appenders
 * @since 0.3
 */
class LoggerAppenderDb extends LoggerAppenderSkeleton {

    /**
     * Create the log table if it does not exists (optional).
     * @var boolean
     */
    var $createTable = true;
    
    /**
     * PEAR::Db Data source name. Read PEAR::Db for dsn syntax (mandatory).
     * @var string
     */
    var $dsn;
    
    /**
     * A {@link LoggerPatternLayout} string used to format a valid insert query (mandatory).
     * @var string
     */
    var $sql;
    
    /**
     * Table name to write events. Used only if {@link $createTable} is true.
     * @var string
     */    
    var $table;
    
    /**
     * @var object PEAR::Db instance
     * @access private
     */
    var $db = null;
    
    /**
     * @var boolean used to check if all conditions to append are true
     * @access private
     */
    var $canAppend = true;
    
    /**    
     * @access private
     */
    var $requiresLayout = false;
    
    /**
     * Constructor.
     *
     * @param string $name appender name
     */
    function LoggerAppenderDb($name)
    {
        $this->LoggerAppenderSkeleton($name);
    }

    /**
     * Setup db connection.
     * Based on defined options, this method connects to db defined in {@link $dsn}
     * and creates a {@link $table} table if {@link $createTable} is true.
     * @return boolean true if all ok.
     */
    function activateOptions()
    {
        $this->db = DB::connect($this->dsn);

        if (DB::isError($this->db)) {
            LoggerLog::debug("LoggerAppenderDb::activateOptions() DB Connect Error [".$this->db->getMessage()."]");            
            $this->db = null;
            $this->closed = true;
            $this->canAppend = false;

        } else {
        
            $this->layout = LoggerLayout::factory('LoggerPatternLayout');
            $this->layout->setConversionPattern($this->getSql());
        
            // test if log table exists
            $tableInfo = $this->db->tableInfo($this->table, $mode = null);
            if (DB::isError($tableInfo) and $this->getCreateTable()) {
                $query = "CREATE TABLE {$this->table} (timestamp varchar(32),logger varchar(32),level varchar(32),message varchar(64),thread varchar(32),file varchar(64),line varchar(4) );";

                LoggerLog::debug("LoggerAppenderDb::activateOptions() creating table '{$this->table}'... using sql='$query'");
                         
                $result = $this->db->query($query);
                if (DB::isError($result)) {
                    LoggerLog::debug("LoggerAppenderDb::activateOptions() error while creating '{$this->table}'. Error is ".$result->getMessage());
                    $this->closed = true;
                    $this->canAppend = false;
                    return;
                }
            }
            $this->canAppend = true;
            $this->closed = false;                        
        }

    }
    
    function append($event)
    {
        if ($this->canAppend) {

            $query = $this->layout->format($event);

            LoggerLog::debug("LoggerAppenderDb::append() query='$query'");

            $this->db->query($query);
        }
    }
    
    function close()
    {
        if ($this->db !== null)
            $this->db->disconnect();
        $this->closed = true;
    }
    
    /**
     * @return boolean
     */
    function getCreateTable()
    {
        return $this->createTable;
    }
    
    /**
     * @return string the defined dsn
     */
    function getDsn()
    {
        return $this->dsn;
    }
    
    /**
     * @return string the sql pattern string
     */
    function getSql()
    {
        return $this->sql;
    }
    
    /**
     * @return string the table name to create
     */
    function getTable()
    {
        return $this->table;
    }
    
    function setCreateTable($flag)
    {
        $this->createTable = LoggerOptionConverter::toBoolean($flag, true);
    }
    
    function setDsn($newDsn)
    {
        $this->dsn = $newDsn;
    }
    
    function setSql($sql)
    {
        $this->sql = $sql;    
    }
    
    function setTable($table)
    {
        $this->table = $table;
    }
    
}

