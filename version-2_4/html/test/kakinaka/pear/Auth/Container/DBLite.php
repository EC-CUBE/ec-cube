<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4 foldmethod=marker: */

/**
 * Reduced storage driver for use against PEAR DB
 *
 * PHP versions 4 and 5
 *
 * LICENSE: This source file is subject to version 3.01 of the PHP license
 * that is available through the world-wide-web at the following URI:
 * http://www.php.net/license/3_01.txt.  If you did not receive a copy of
 * the PHP License and are unable to obtain it through the web, please
 * send a note to license@php.net so we can mail you a copy immediately.
 *
 * @category   Authentication
 * @package    Auth
 * @author     Martin Jansen <mj@php.net>
 * @author     Adam Ashley <aashley@php.net>
 * @copyright  2001-2006 The PHP Group
 * @license    http://www.php.net/license/3_01.txt  PHP License 3.01
 * @version    CVS: $Id$
 * @link       http://pear.php.net/package/Auth
 * @since      File available since Release 1.3.0
 */

/**
 * Include Auth_Container base class
 */
require_once 'Auth/Container.php';
/**
 * Include PEAR DB package
 */
require_once 'DB.php';

/**
 * A lighter storage driver for fetching login data from a database
 *
 * This driver is derived from the DB storage container but
 * with the user manipulation function removed for smaller file size
 * by the PEAR DB abstraction layer to fetch login data.
 *
 * @category   Authentication
 * @package    Auth
 * @author     Martin Jansen <mj@php.net>
 * @author     Adam Ashley <aashley@php.net>
 * @copyright  2001-2006 The PHP Group
 * @license    http://www.php.net/license/3_01.txt  PHP License 3.01
 * @version    Release: 1.4.2  File: $Revision: 8713 $
 * @link       http://pear.php.net/package/Auth
 * @since      Class available since Release 1.3.0
 */
class Auth_Container_DBLite extends Auth_Container
{

    // {{{ properties

    /**
     * Additional options for the storage container
     * @var array
     */
    var $options = array();

    /**
     * DB object
     * @var object
     */
    var $db = null;
    var $dsn = '';

    /**
     * User that is currently selected from the DB.
     * @var string
     */
    var $activeUser = '';

    // }}}
    // {{{ Auth_Container_DBLite() [constructor]

    /**
     * Constructor of the container class
     *
     * Initate connection to the database via PEAR::DB
     *
     * @param  string Connection data or DB object
     * @return object Returns an error object if something went wrong
     */
    function Auth_Container_DBLite($dsn)
    {
        $this->options['table']       = 'auth';
        $this->options['usernamecol'] = 'username';
        $this->options['passwordcol'] = 'password';
        $this->options['dsn']         = '';
        $this->options['db_fields']   = '';
        $this->options['cryptType']   = 'md5';
        $this->options['db_options']  = array();
        $this->options['auto_quote']  = true;

        if (is_array($dsn)) {
            $this->_parseOptions($dsn);
            if (empty($this->options['dsn'])) {
                PEAR::raiseError('No connection parameters specified!');
            }
        } else {
            $this->options['dsn'] = $dsn;
        }
    }

    // }}}
    // {{{ _connect()

    /**
     * Connect to database by using the given DSN string
     *
     * @access private
     * @param  string DSN string
     * @return mixed  Object on error, otherwise bool
     */
    function _connect(&$dsn)
    {
        if (is_string($dsn) || is_array($dsn)) {
            $this->db =& DB::connect($dsn, $this->options['db_options']);
        } elseif (is_subclass_of($dsn, "db_common")) {
            $this->db =& $dsn;
        } else {
            return PEAR::raiseError("Invalid dsn or db object given");
        }

        if (DB::isError($this->db) || PEAR::isError($this->db)) {
            return PEAR::raiseError($this->db->getMessage(), $this->db->getCode());
        } else {
            return true;
        }
    }

    // }}}
    // {{{ _prepare()

    /**
     * Prepare database connection
     *
     * This function checks if we have already opened a connection to
     * the database. If that's not the case, a new connection is opened.
     *
     * @access private
     * @return mixed True or a DB error object.
     */
    function _prepare()
    {
        if (!DB::isConnection($this->db)) {
            $res = $this->_connect($this->options['dsn']);
            if (DB::isError($res) || PEAR::isError($res)) {
                return $res;
            }
        }
        if ($this->options['auto_quote'] && $this->db->dsn['phptype'] != 'sqlite') {
            $this->options['final_table'] = $this->db->quoteIdentifier($this->options['table']);
            $this->options['final_usernamecol'] = $this->db->quoteIdentifier($this->options['usernamecol']);
            $this->options['final_passwordcol'] = $this->db->quoteIdentifier($this->options['passwordcol']);
        } else {
            $this->options['final_table'] = $this->options['table'];
            $this->options['final_usernamecol'] = $this->options['usernamecol'];
            $this->options['final_passwordcol'] = $this->options['passwordcol'];
        }
        return true;
    }

    // }}}
    // {{{ _parseOptions()

    /**
     * Parse options passed to the container class
     *
     * @access private
     * @param  array
     */
    function _parseOptions($array)
    {
        foreach ($array as $key => $value) {
            if (isset($this->options[$key])) {
                $this->options[$key] = $value;
            }
        }
    }

    // }}}
    // {{{ _quoteDBFields()

    /**
     * Quote the db_fields option to avoid the possibility of SQL injection.
     *
     * @access private
     * @return string A properly quoted string that can be concatenated into a
     * SELECT clause.
     */
    function _quoteDBFields()
    {
        if (isset($this->options['db_fields'])) {
            if (is_array($this->options['db_fields'])) {
                if ($this->options['auto_quote']) {
                    $fields = array();
                    foreach ($this->options['db_fields'] as $field) {
                        $fields[] = $this->db->quoteIdentifier($field);
                    }
                    return implode(', ', $fields);
                } else {
                    return implode(', ', $this->options['db_fields']);
                }
            } else {
                if (strlen($this->options['db_fields']) > 0) {
                    if ($this->options['auto_quote']) {
                        return $this->db->quoteIdentifier($this->options['db_fields']);
                    } else {
                        $this->options['db_fields'];
                    }
                }
            }
        }

        return '';
    }
    
    // }}}
    // {{{ fetchData()

    /**
     * Get user information from database
     *
     * This function uses the given username to fetch
     * the corresponding login data from the database
     * table. If an account that matches the passed username
     * and password is found, the function returns true.
     * Otherwise it returns false.
     *
     * @param   string Username
     * @param   string Password
     * @return  mixed  Error object or boolean
     */
    function fetchData($username, $password)
    {
        // Prepare for a database query
        $err = $this->_prepare();
        if ($err !== true) {
            return PEAR::raiseError($err->getMessage(), $err->getCode());
        }

        // Find if db_fields contains a *, if so assume all col are selected
        if (is_string($this->options['db_fields'])
            && strstr($this->options['db_fields'], '*')) {
            $sql_from = "*";
        } else {
            $sql_from = $this->options['final_usernamecol'].
                ", ".$this->options['final_passwordcol'];

            if (strlen($fields = $this->_quoteDBFields()) > 0) {
                $sql_from .= ', '.$fields;
            }
        }
        
        $query = "SELECT ".$sql_from.
                " FROM ".$this->options['final_table'].
                " WHERE ".$this->options['final_usernamecol']." = ".$this->db->quoteSmart($username);
        $res = $this->db->getRow($query, null, DB_FETCHMODE_ASSOC);

        if (DB::isError($res)) {
            return PEAR::raiseError($res->getMessage(), $res->getCode());
        }
        if (!is_array($res)) {
            $this->activeUser = '';
            return false;
        }
        if ($this->verifyPassword(trim($password, "\r\n"),
                                  trim($res[$this->options['passwordcol']], "\r\n"),
                                  $this->options['cryptType'])) {
            // Store additional field values in the session
            foreach ($res as $key => $value) {
                if ($key == $this->options['passwordcol'] ||
                    $key == $this->options['usernamecol']) {
                    continue;
                }
                // Use reference to the auth object if exists
                // This is because the auth session variable can change so a static call to setAuthData does not make sence
                if (is_object($this->_auth_obj)) {
                    $this->_auth_obj->setAuthData($key, $value);
                } else {
                    Auth::setAuthData($key, $value);
                }
            }
            $this->activeUser = $res[$this->options['usernamecol']];
            return true;
        }
        $this->activeUser = $res[$this->options['usernamecol']];
        return false;
    }

    // }}}

}
?>
