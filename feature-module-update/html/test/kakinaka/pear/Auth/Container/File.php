<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4 foldmethod=marker: */

/**
 * Storage driver for use against a generic password file
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
 * @author     Stefan Ekman <stekman@sedata.org> 
 * @author     Martin Jansen <mj@php.net>
 * @author     Mika Tuupola <tuupola@appelsiini.net> 
 * @author     Michael Wallner <mike@php.net>
 * @author     Adam Ashley <aashley@php.net>
 * @copyright  2001-2006 The PHP Group
 * @license    http://www.php.net/license/3_01.txt  PHP License 3.01
 * @version    CVS: $Id$
 * @link       http://pear.php.net/package/Auth
 */

/**
 * Include PEAR File_Passwd package
 */
$include_dir = realpath(dirname( __FILE__));
require_once $include_dir . "/../../File/Passwd.php";
/**
 * Include Auth_Container base class
 */
require_once $include_dir . "/../Container.php";
/**
 * Include PEAR package for error handling
 */
//require_once $include_dir . "/../../PEAR.php";

/**
 * Storage driver for fetching login data from an encrypted password file.
 *
 * This storage container can handle CVS pserver style passwd files.
 *
 * @category   Authentication
 * @package    Auth
 * @author     Stefan Ekman <stekman@sedata.org> 
 * @author     Martin Jansen <mj@php.net>
 * @author     Mika Tuupola <tuupola@appelsiini.net> 
 * @author     Michael Wallner <mike@php.net>
 * @author     Adam Ashley <aashley@php.net>
 * @copyright  2001-2006 The PHP Group
 * @license    http://www.php.net/license/3_01.txt  PHP License 3.01
 * @version    Release: 1.4.2  File: $Revision: 8734 $
 * @link       http://pear.php.net/package/Auth
 */
class Auth_Container_File extends Auth_Container
{

    // {{{ properties

    /**
     * Path to passwd file
     * 
     * @var string
     */
    var $pwfile = '';

    /**
     * Options for container
     *
     * @var array
     */
    var $options = array();

    // }}}
    // {{{ Auth_Container_File() [constructor]

    /**
     * Constructor of the container class
     *
     * @param  string $filename             path to passwd file
     * @return object Auth_Container_File   new Auth_Container_File object
     */
    function Auth_Container_File($filename) {
        $this->_setDefaults();
        
        // Only file is a valid option here
        if(is_array($filename)) {
            $this->pwfile = $filename['file'];
            $this->_parseOptions($filename);
        } else {
            $this->pwfile = $filename;
        }
    }

    // }}}
    // {{{ fetchData()

    /**
     * Authenticate an user
     *
     * @param   string  username
     * @param   string  password
     * @return  mixed   boolean|PEAR_Error
     */
    function fetchData($user, $pass)
    {
        return File_Passwd::staticAuth($this->options['type'], $this->pwfile, $user, $pass);
    }

    // }}}
    // {{{ listUsers()
    
    /**
     * List all available users
     * 
     * @return   array
     */
    function listUsers()
    {
        $pw_obj = &$this->_load();
        if (PEAR::isError($pw_obj)) {
            return array();
        }

        $users  = $pw_obj->listUser();
        if (!is_array($users)) {
            return array();
        }

        foreach ($users as $key => $value) {
            $retVal[] = array("username" => $key, 
                              "password" => $value['passwd'],
                              "cvsuser"  => $value['system']);
        }

        return $retVal;
    }

    // }}}
    // {{{ addUser()

    /**
     * Add a new user to the storage container
     *
     * @param string username
     * @param string password
     * @param mixed  Additional parameters to File_Password_*::addUser()
     *
     * @return boolean
     */
    function addUser($user, $pass, $additional='')
    {
        $params = array($user, $pass);
        if (is_array($additional)) {
            foreach ($additional as $item) {
                $params[] = $item;
            }
        } else {
            $params[] = $additional;
        }

        $pw_obj = &$this->_load();
        if (PEAR::isError($pw_obj)) {
            return false;
        }
        
        $res = call_user_func_array(array(&$pw_obj, 'addUser'), $params);
        if (PEAR::isError($res)) {
            return false;
        }
        
        $res = $pw_obj->save();
        if (PEAR::isError($res)) {
            return false;
        }
        
        return true;
    }

    // }}}
    // {{{ removeUser()

    /**
     * Remove user from the storage container
     *
     * @param   string  Username
     * @return  boolean
     */
    function removeUser($user)
    {
        $pw_obj = &$this->_load();
        if (PEAR::isError($pw_obj)) {
            return false;
        }
        
        $res = $pw_obj->delUser($user);
        if (PEAR::isError($res)) {
            return false;
        }
        
        $res = $pw_obj->save();
        if (PEAR::isError($res)) {
            return false;
        }
        
        return true;
    }

    // }}}
    // {{{ changePassword()

    /**
     * Change password for user in the storage container
     *
     * @param string Username
     * @param string The new password 
     */
    function changePassword($username, $password)
    {
        $pw_obj = &$this->_load();
        if (PEAR::isError($pw_obj)) {
            return false;
        }
        
        $res = $pw_obj->changePasswd($username, $password);
        if (PEAR::isError($res)) {
            return false;
        }
        
        $res = $pw_obj->save();
        if (PEAR::isError($res)) {
            return false;
        }
        
        return true;
    }

    // }}}
    // {{{ _load()
    
    /**
     * Load and initialize the File_Passwd object
     * 
     * @return  object  File_Passwd_Cvs|PEAR_Error
     */
    function &_load()
    {
        static $pw_obj;
        
        if (!isset($pw_obj)) {
            $pw_obj = File_Passwd::factory($this->options['type']);
            if (PEAR::isError($pw_obj)) {
                return $pw_obj;
            }
            
            $pw_obj->setFile($this->pwfile);
            
            $res = $pw_obj->load();
            if (PEAR::isError($res)) {
                return $res;
            }
        }
        
        return $pw_obj;
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
        $this->options['type']       = 'Cvs';
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

}
?>
