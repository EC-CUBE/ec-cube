<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4 foldmethod=marker: */

/**
 * Storage driver for use against Samba password files
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
 * @author     Michael Bretterklieber <michael@bretterklieber.com> 
 * @author     Adam Ashley <aashley@php.net>
 * @copyright  2001-2006 The PHP Group
 * @license    http://www.php.net/license/3_01.txt  PHP License 3.01
 * @version    CVS: $Id$
 * @link       http://pear.php.net/package/Auth
 * @since      File available since Release 1.2.3
 */

/**
 * Include PEAR File_SMBPasswd
 */
require_once "File/SMBPasswd.php";
/**
 * Include Auth_Container Base file
 */
require_once "Auth/Container.php";
/**
 * Include PEAR class for error handling
 */
require_once "PEAR.php";

/**
 * Storage driver for fetching login data from an SAMBA smbpasswd file.
 *
 * This storage container can handle SAMBA smbpasswd files.
 *
 * Example:
 * $a = new Auth("SMBPasswd", '/usr/local/private/smbpasswd');
 * $a->start();
 * if ($a->getAuth()) {
 *     printf ("AUTH OK<br>\n");
 *     $a->logout();
 * }
 *
 * @category   Authentication
 * @package    Auth
 * @author     Michael Bretterklieber <michael@bretterklieber.com>
 * @author     Adam Ashley <aashley@php.net>
 * @package    Auth
 * @copyright  2001-2006 The PHP Group
 * @license    http://www.php.net/license/3_01.txt  PHP License 3.01
 * @version    Release: 1.4.2  File: $Revision: 8713 $
 * @link       http://pear.php.net/package/Auth
 * @since      Class available since Release 1.2.3
 */
class Auth_Container_SMBPasswd extends Auth_Container
{

    // {{{ properties

    /**
     * File_SMBPasswd object
     * @var object
     */
    var $pwfile;

    // }}}

    // {{{ Auth_Container_SMBPasswd() [constructor]

    /**
     * Constructor of the container class
     *
     * @param  $filename   string filename for a passwd type file
     * @return object Returns an error object if something went wrong
     */
    function Auth_Container_SMBPasswd($filename)
    {
        $this->pwfile = new File_SMBPasswd($filename,0);

        if (!$this->pwfile->load()) {
            PEAR::raiseError("Error while reading file contents.", 41, PEAR_ERROR_DIE);
            return;
        }

    }

    // }}}
    // {{{ fetchData()

    /**
     * Get user information from pwfile
     *
     * @param   string Username
     * @param   string Password
     * @return  boolean
     */
    function fetchData($username, $password)
    {
        return $this->pwfile->verifyAccount($username, $password);
    }

    // }}}
    // {{{ listUsers()
    
    function listUsers()
    {
        return $this->pwfile->getAccounts();
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
    function addUser($username, $password, $additional = '')
    {
        $res = $this->pwfile->addUser($user, $additional['userid'], $pass);
        if ($res === true) {
            return $this->pwfile->save();
        }
        return $res;
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
        $res = $this->pwfile->delUser($username);
        if ($res === true) {
            return $this->pwfile->save();
        }
        return $res;
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
         $res = $this->pwfile->modUser($username, '', $password);
         if ($res === true) {
             return $this->pwfile->save();
         }
         return $res;
    }

    // }}}

}
?>
