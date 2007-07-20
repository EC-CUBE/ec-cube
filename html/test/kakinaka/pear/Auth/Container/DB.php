<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4 foldmethod=marker: */

/**
 * Storage driver for use against PEAR DB
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
 */

/**
 * Include Auth_Container base class
 */
$include_dir = realpath(dirname( __FILE__));
require_once $include_dir . '/../Container.php';
/**
 * Include PEAR DB
 */
require_once 'DB.php';

/**
 * Storage driver for fetching login data from a database
 *
 * This storage driver can use all databases which are supported
 * by the PEAR DB abstraction layer to fetch login data.
 *
 * @category   Authentication
 * @package    Auth
 * @author     Martin Jansen <mj@php.net>
 * @author     Adam Ashley <aashley@php.net>
 * @copyright  2001-2006 The PHP Group
 * @license    http://www.php.net/license/3_01.txt  PHP License 3.01
 * @version    Release: 1.4.2  File: $Revision: 8720 $
 * @link       http://pear.php.net/package/Auth
 */
class Auth_Container_DB extends Auth_Container
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
    // {{{ Auth_Container_DB [constructor]

    /**
     * Constructor of the container class
     *
     * Save the initial options passed to the container. Initiation of the DB
     * connection is no longer performed here and is only done when needed.
     *
     * @param  string Connection data or DB object
     * @return object Returns an error object if something went wrong
     */
    function Auth_Container_DB($dsn)
    {
        $this->_setDefaults();

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
    function _connect($dsn)
    {
        if (is_string($dsn) || is_array($dsn)) {
            $this->db = DB::Connect($dsn, $this->options['db_options']);
        } elseif (is_subclass_of($dsn, 'db_common')) {
            $this->db = $dsn;
        } elseif (DB::isError($dsn)) {
            return PEAR::raiseError($dsn->getMessage(), $dsn->getCode());
        } else {
            return PEAR::raiseError('The given dsn was not valid in file ' . __FILE__ . ' at line ' . __LINE__,
                                    41,
                                    PEAR_ERROR_RETURN,
                                    null,
                                    null
                                    );
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
    // {{{ query()

    /**
     * Prepare query to the database
     *
     * This function checks if we have already opened a connection to
     * the database. If that's not the case, a new connection is opened.
     * After that the query is passed to the database.
     *
     * @access public
     * @param  string Query string
     * @return mixed  a DB_result object or DB_OK on success, a DB
     *                or PEAR error on failure
     */
    function query($query)
    {
        $err = $this->_prepare();
        if ($err !== true) {
            return $err;
        }
        return $this->db->query($query);
    }

    // }}}
    // {{{ _setDefaults()

    /**
     * Set some default options
     *
     * @access private
     * @return void
     */
    function _setDefaults()
    {
        $this->options['table']       = 'auth';
        $this->options['usernamecol'] = 'username';
        $this->options['passwordcol'] = 'password';
        $this->options['dsn']         = '';
        $this->options['db_fields']   = '';
        $this->options['cryptType']   = 'md5';
        $this->options['db_options']  = array();
        $this->options['auto_quote']  = true;
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
                        return $this->options['db_fields'];
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
     * @param   boolean If true password is secured using a md5 hash
     *                  the frontend and auth are responsible for making sure the container supports
     *                  challenge response password authentication
     * @return  mixed  Error object or boolean
     */
    function fetchData($username, $password, $isChallengeResponse=false)
    {
        // Prepare for a database query
        $err = $this->_prepare();
        if ($err !== true) {
            return PEAR::raiseError($err->getMessage(), $err->getCode());
        }

        // Find if db_fields contains a *, if so assume all columns are selected
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

        // Perform trimming here before the hashihg
        $password = trim($password, "\r\n");
        $res[$this->options['passwordcol']] = trim($res[$this->options['passwordcol']], "\r\n");

        // If using Challenge Response md5 the pass with the secret
        if ($isChallengeResponse) {
            $res[$this->options['passwordcol']] = md5($res[$this->options['passwordcol']]
                    .$this->_auth_obj->session['loginchallenege']);
            
            // UGLY cannot avoid without modifying verifyPassword
            if ($this->options['cryptType'] == 'md5') {
                $res[$this->options['passwordcol']] = md5($res[$this->options['passwordcol']]);
            }
            
            //print " Hashed Password [{$res[$this->options['passwordcol']]}]<br/>\n";
        }

        if ($this->verifyPassword($password,
                                  $res[$this->options['passwordcol']],
                                  $this->options['cryptType'])) {
            // Store additional field values in the session
            foreach ($res as $key => $value) {
                if ($key == $this->options['passwordcol'] ||
                    $key == $this->options['usernamecol']) {
                    continue;
                }
                // Use reference to the auth object if exists
                // This is because the auth session variable can change so a 
                // static call to setAuthData does not make sence
                $this->_auth_obj->setAuthData($key, $value);
            }
            return true;
        }
        $this->activeUser = $res[$this->options['usernamecol']];
        return false;
    }

    // }}}
    // {{{ listUsers()

    /**
     * Returns a list of users from the container
     *
     * @return mixed
     * @access public
     */
    function listUsers()
    {
        $err = $this->_prepare();
        if ($err !== true) {
            return PEAR::raiseError($err->getMessage(), $err->getCode());
        }

        $retVal = array();

        // Find if db_fields contains a *, if so assume all col are selected
        if (   is_string($this->options['db_fields'])
            && strstr($this->options['db_fields'], '*')) {
            $sql_from = "*";
        } else {
            $sql_from = $this->options['final_usernamecol'].
                ", ".$this->options['final_passwordcol'];

            if (strlen($fields = $this->_quoteDBFields()) > 0) {
                $sql_from .= ', '.$fields;
            }
        }

        $query = sprintf("SELECT %s FROM %s",
                         $sql_from,
                         $this->options['final_table']
                         );
        $res = $this->db->getAll($query, null, DB_FETCHMODE_ASSOC);

        if (DB::isError($res)) {
            return PEAR::raiseError($res->getMessage(), $res->getCode());
        } else {
            foreach ($res as $user) {
                $user['username'] = $user[$this->options['usernamecol']];
                $retVal[] = $user;
            }
        }
        return $retVal;
    }

    // }}}
    // {{{ addUser()

    /**
     * Add user to the storage container
     *
     * @access public
     * @param  string Username
     * @param  string Password
     * @param  mixed  Additional information that are stored in the DB
     *
     * @return mixed True on success, otherwise error object
     */
    function addUser($username, $password, $additional = "")
    {
        $err = $this->_prepare();
        if ($err !== true) {
            return PEAR::raiseError($err->getMessage(), $err->getCode());
        }

        if (   isset($this->options['cryptType']) 
            && $this->options['cryptType'] == 'none') {
            $cryptFunction = 'strval';
        } elseif (   isset($this->options['cryptType']) 
                  && function_exists($this->options['cryptType'])) {
            $cryptFunction = $this->options['cryptType'];
        } else {
            $cryptFunction = 'md5';
        }

        $password = $cryptFunction($password);

        $additional_key   = '';
        $additional_value = '';

        if (is_array($additional)) {
            foreach ($additional as $key => $value) {
                if ($this->options['auto_quote']) {
                    $additional_key .= ', ' . $this->db->quoteIdentifier($key);
                } else {
                    $additional_key .= ', ' . $key;
                }
                $additional_value .= ", " . $this->db->quoteSmart($value);
            }
        }

        $query = sprintf("INSERT INTO %s (%s, %s%s) VALUES (%s, %s%s)",
                         $this->options['final_table'],
                         $this->options['final_usernamecol'],
                         $this->options['final_passwordcol'],
                         $additional_key,
                         $this->db->quoteSmart($username),
                         $this->db->quoteSmart($password),
                         $additional_value
                         );

        $res = $this->query($query);

        if (DB::isError($res)) {
            return PEAR::raiseError($res->getMessage(), $res->getCode());
        } else {
            return true;
        }
    }

    // }}}
    // {{{ removeUser()

    /**
     * Remove user from the storage container
     *
     * @access public
     * @param  string Username
     *
     * @return mixed True on success, otherwise error object
     */
    function removeUser($username)
    {
        $err = $this->_prepare();
        if ($err !== true) {
            return PEAR::raiseError($err->getMessage(), $err->getCode());
        }

        $query = sprintf("DELETE FROM %s WHERE %s = %s",
                         $this->options['final_table'],
                         $this->options['final_usernamecol'],
                         $this->db->quoteSmart($username)
                         );

        $res = $this->query($query);

        if (DB::isError($res)) {
           return PEAR::raiseError($res->getMessage(), $res->getCode());
        } else {
          return true;
        }
    }

    // }}}
    // {{{ changePassword()

    /**
     * Change password for user in the storage container
     *
     * @param string Username
     * @param string The new password (plain text)
     */
    function changePassword($username, $password)
    {
        $err = $this->_prepare();
        if ($err !== true) {
            return PEAR::raiseError($err->getMessage(), $err->getCode());
        }

        if (   isset($this->options['cryptType']) 
            && $this->options['cryptType'] == 'none') {
            $cryptFunction = 'strval';
        } elseif (   isset($this->options['cryptType']) 
                  && function_exists($this->options['cryptType'])) {
            $cryptFunction = $this->options['cryptType'];
        } else {
            $cryptFunction = 'md5';
        }

        $password = $cryptFunction($password);

        $query = sprintf("UPDATE %s SET %s = %s WHERE %s = %s",
                         $this->options['final_table'],
                         $this->options['final_passwordcol'],
                         $this->db->quoteSmart($password),
                         $this->options['final_usernamecol'],
                         $this->db->quoteSmart($username)
                         );

        $res = $this->query($query);

        if (DB::isError($res)) {
            return PEAR::raiseError($res->getMessage(), $res->getCode());
        } else {
            return true;
        }
    }

    // }}}
    // {{{ supportsChallengeResponse()

    /**
     * Determine if this container supports
     * password authentication with challenge response
     *
     * @return bool
     * @access public
     */
    function supportsChallengeResponse()
    {
        return in_array($this->options['cryptType'], array('md5', 'none', ''));
    }

    // }}}
    // {{{ getCryptType()

    /**
      * Returns the selected crypt type for this container
      */
    function getCryptType()
    {
        return($this->options['cryptType']);
    }

    // }}}

}
?>
