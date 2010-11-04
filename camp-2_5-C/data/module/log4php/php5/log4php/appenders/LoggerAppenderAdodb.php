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

require_once(ADODB_DIR . '/adodb.inc.php');

/**
 * Appends log events to a db table using adodb class.
 *
 * <p>This appender uses a table in a database to log events.</p>
 * <p>Parameters are {@link $host}, {@link $user}, {@link $password},
 * {@link $database}, {@link $createTable}, {@link $table} and {@link $sql}.</p>
 * <p>See examples in test directory.</p>
 *
 * @author sbw <sbw@ibiblio.org>
 * @package log4php
 * @subpackage appenders
 * @since 0.9
 */
class LoggerAppenderAdodb extends LoggerAppenderSkeleton {

    /**
     * Create the log table if it does not exists (optional).
     * @var boolean
     */
    var $createTable = true;
    
    /**
     * The type of database to connect to
     * @var string
     */
    var $type;
    
    /**
     * Database user name
     * @var string
     */
    var $user;
    
    /**
     * Database password
     * @var string
     */
    var $password;
    
    /**
     * Database host to connect to
     * @var string
     */
    var $host;
    
    /**
     * Name of the database to connect to
     * @var string
     */
    var $database;
    
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
     * @var object Adodb instance
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
        $this->db = &ADONewConnection($this->type);
        if (! $this->db->PConnect($this->host, $this->user, $this->password, $this->database)) {
          LoggerLog::debug("LoggerAppenderAdodb::activateOptions() DB Connect Error [".$this->db->ErrorMsg()."]");            
          $this->db = null;
          $this->closed = true;
          $this->canAppend = false;
          return;
        }
        
        $this->layout = LoggerLayout::factory('LoggerPatternLayout');
        $this->layout->setConversionPattern($this->getSql());
    
        // test if log table exists
        $sql = 'select * from ' . $this->table . ' where 1 = 0';
        $dbrs = $this->db->Execute($sql);
        if ($dbrs == false and $this->getCreateTable()) {
            $query = "CREATE TABLE {$this->table} (timestamp varchar(32),logger varchar(32),level varchar(32),message varchar(64),thread varchar(32),file varchar(64),line varchar(4) );";

            LoggerLog::debug("LoggerAppenderAdodb::activateOptions() creating table '{$this->table}'... using sql='$query'");
                     
            $result = $this->db->Execute($query);
            if (! $result) {
                LoggerLog::debug("LoggerAppenderAdodb::activateOptions() error while creating '{$this->table}'. Error is ".$this->db->ErrorMsg());
                $this->canAppend = false;
                return;
            }
        }
        $this->canAppend = true;
    }
    
    function append($event)
    {
        if ($this->canAppend) {

            $query = $this->layout->format($event);

            LoggerLog::debug("LoggerAppenderAdodb::append() query='$query'");

            $this->db->Execute($query);
        }
    }
    
    function close()
    {
        if ($this->db !== null)
            $this->db->Close();
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
    
    /**
     * @return string the database to connect to
     */
    function getDatabase() {
        return $this->database;
    }
    
    /**
     * @return string the database to connect to
     */
    function getHost() {
        return $this->host;
    }
    
    /**
     * @return string the user to connect with
     */
    function getUser() {
        return $this->user;
    }
    
    /**
     * @return string the password to connect with
     */
    function getPassword() {
        return $this->password;
    }
    
    /**
     * @return string the type of database to connect to
     */
    function getType() {
        return $this->type;
    }
    
    function setCreateTable($flag)
    {
        $this->createTable = LoggerOptionConverter::toBoolean($flag, true);
    }
    
    function setType($newType)
    {
        $this->type = $newType;
    }
    
    function setDatabase($newDatabase)
    {
        $this->database = $newDatabase;
    }
    
    function setHost($newHost)
    {
        $this->host = $newHost;
    }
    
    function setUser($newUser)
    {
        $this->user = $newUser;
    }
    
    function setPassword($newPassword)
    {
        $this->password = $newPassword;
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

