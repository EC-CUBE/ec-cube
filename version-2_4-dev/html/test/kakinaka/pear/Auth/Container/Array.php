<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4 foldmethod=marker: */

/**
 * Storage driver for use against a PHP Array
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
 * @author     georg_1 at have2 dot com
 * @author     Adam Ashley <aashley@php.net>
 * @copyright  2001-2006 The PHP Group
 * @license    http://www.php.net/license/3_01.txt  PHP License 3.01
 * @version    CVS: $Id$
 * @since      File available since Release 1.4.0
 */

/**
 * Include Auth_Container base class
 */
require_once "Auth/Container.php";
/**
 * Include PEAR package for error handling
 */
require_once "PEAR.php";

/**
 * Storage driver for fetching authentication data from a PHP Array
 *
 * This container takes two options when configuring:
 *
 * cryptType:   The crypt used to store the password. Currently recognised
 *              are: none, md5 and crypt. default: none
 * users:       A named array of usernames and passwords.
 *              Ex:
 *              array(
 *                  'guest' => '084e0343a0486ff05530df6c705c8bb4', // password guest
 *                  'georg' => 'fc77dba827fcc88e0243404572c51325'  // password georg
 *              )
 *
 * Usage Example:
 * <?php
 * $AuthOptions = array(
 *      'users' => array(
 *          'guest' => '084e0343a0486ff05530df6c705c8bb4', // password guest
 *          'georg' => 'fc77dba827fcc88e0243404572c51325'  // password georg
 *      ),
 *      'cryptType'=>'md5',
 *  );
 *
 * $auth = new Auth("Array", $AuthOptions);
 * ?>
 *
 * @category   Authentication
 * @package    Auth
 * @author     georg_1 at have2 dot com
 * @author     Adam Ashley <aashley@php.net>
 * @copyright  2001-2006 The PHP Group
 * @license    http://www.php.net/license/3_01.txt  PHP License 3.01
 * @version    Release: 1.4.2  File: $Revision: 8713 $
 * @since      File available since Release 1.4.0
 */

class Auth_Container_Array extends Auth_Container {

    // {{{ properties

    /**
     * The users and their password to authenticate against
     *
     * @var array $users
     */
    var $users;

    /**
     * The cryptType used on the passwords
     *
     * @var string $cryptType
     */
    var $cryptType = 'none';

    // }}}
    // {{{ Auth_Container_Array()

    /**
     * Constructor for Array Container
     *
     * @param array $data Options for the container
     * @return void
     */
    function Auth_Container_Array($data)
    {
        if (!is_array($data)) {
            PEAR::raiseError('The options for Auth_Container_Array must be an array');
        } 
        if (isset($data['users']) && is_array($data['users'])) {
            $this->users = $data['users'];
        } else {
            $this->users = array();
            PEAR::raiseError('Auth_Container_Array: no user data found inoptions array');
        } 
        if (isset($data['cryptType'])) {
            $this->cryptType = $data['cryptType'];
        } 
    }

    // }}}
    // {{{ fetchData()

    /**
     * Get user information from array
     *
     * This function uses the given username to fetch the corresponding
     * login data from the array. If an account that matches the passed
     * username and password is found, the function returns true.
     * Otherwise it returns false.
     *
     * @param  string Username
     * @param  string Password
     * @return boolean|PEAR_Error Error object or boolean
     */
    function fetchData($user, $pass)
    {
        if (   isset($this->users[$user])
            && $this->verifyPassword($pass, $this->users[$user], $this->cryptType)) {
            return true;
        }
        return false;
    } 

    // }}}
    // {{{ listUsers()

    /**
     * Returns a list of users available within the container
     *
     * @return array
     */
    function listUsers()
    {
        $ret = array();
        foreach ($this->users as $username => $password) {
            $ret[]['username'] = $username;
        } 
        return $ret;
    } 

    // }}}

} 

?>
