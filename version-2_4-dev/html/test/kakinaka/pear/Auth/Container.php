<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4 foldmethod=marker: */

/**
 * Auth_Container Base Class
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
 * Storage class for fetching login data
 *
 * @category   Authentication
 * @package    Auth
 * @author     Martin Jansen <mj@php.net>
 * @author     Adam Ashley <aashley@php.net>
 * @copyright  2001-2006 The PHP Group
 * @license    http://www.php.net/license/3_01.txt  PHP License 3.01
 * @version    Release: 1.4.2  File: $Revision: 8715 $
 * @link       http://pear.php.net/package/Auth
 */
class Auth_Container
{

    // {{{ properties

    /**
     * User that is currently selected from the storage container.
     *
     * @access public
     */
    var $activeUser = "";

    // }}}
    // {{{ Auth_Container() [constructor]

    /**
     * Constructor
     *
     * Has to be overwritten by each storage class
     *
     * @access public
     */
    function Auth_Container()
    {
    }

    // }}}
    // {{{ fetchData()

    /**
     * Fetch data from storage container
     *
     * Has to be overwritten by each storage class
     *
     * @access public
     */
    function fetchData($username, $password, $isChallengeResponse=false)
    {
    }

    // }}}
    // {{{ verifyPassword()

    /**
     * Crypt and verfiy the entered password
     *
     * @param  string Entered password
     * @param  string Password from the data container (usually this password
     *                is already encrypted.
     * @param  string Type of algorithm with which the password from
     *                the container has been crypted. (md5, crypt etc.)
     *                Defaults to "md5".
     * @return bool   True, if the passwords match
     */
    function verifyPassword($password1, $password2, $cryptType = "md5")
    {
        switch ($cryptType) {
            case "crypt" :
                return ((string)crypt($password1, $password2) === (string)$password2);
                break;
            case "none" :
            case "" :
                return ((string)$password1 === (string)$password2);
                break;
            case "md5" :
                return ((string)md5($password1) === (string)$password2);
                break;
            default :
                if (function_exists($cryptType)) {
                    return ((string)$cryptType($password1) === (string)$password2);
                } elseif (method_exists($this,$cryptType)) { 
                    return ((string)$this->$cryptType($password1) === (string)$password2);
                } else {
                    return false;
                }
                break;
        }
    }

    // }}}
    // {{{ supportsChallengeResponse()
    
    /**
      * Returns true if the container supports Challenge Response 
      * password authentication
      */
    function supportsChallengeResponse()
    {
        return(false);
    }

    // }}}
    // {{{ getCryptType()
    
    /**
      * Returns the crypt current crypt type of the container
      *
      * @return string
      */
    function getCryptType()
    {
        return('');
    }

    // }}}
    // {{{ listUsers()

    /**
     * List all users that are available from the storage container
     */
    function listUsers()
    {
        return AUTH_METHOD_NOT_SUPPORTED;
    }

    // }}}
    // {{{ getUser()

    /**
     * Returns a user assoc array
     *
     * Containers which want should overide this
     *
     * @param string The username
     */
    function getUser($username)
    {
        $users = $this->listUsers();
        if ($users === AUTH_METHOD_NOT_SUPPORTED) {
            return AUTH_METHOD_NOT_SUPPORTED;
        }
        for ($i=0; $c = count($users), $i<$c; $i++) {
            if ($users[$i]['username'] == $username) {
                return $users[$i];
            }
        }
        return false;
    }

    // }}}
    // {{{ addUser()

    /**
     * Add a new user to the storage container
     *
     * @param string Username
     * @param string Password
     * @param array  Additional information
     *
     * @return boolean
     */
    function addUser($username, $password, $additional=null)
    {
        return AUTH_METHOD_NOT_SUPPORTED;
    }

    // }}}
    // {{{ removeUser()

    /**
     * Remove user from the storage container
     *
     * @param string Username
     */
    function removeUser($username)
    {
        return AUTH_METHOD_NOT_SUPPORTED;
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
        return AUTH_METHOD_NOT_SUPPORTED;
    }

    // }}}

}

?>
