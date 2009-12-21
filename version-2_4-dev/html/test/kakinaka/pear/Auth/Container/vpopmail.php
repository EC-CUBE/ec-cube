<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4 foldmethod=marker: */

/**
 * Storage driver for use against vpopmail setups
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
 * @author     Stanislav Grozev <tacho@orbitel.bg> 
 * @author     Adam Ashley <aashley@php.net>
 * @copyright  2001-2006 The PHP Group
 * @license    http://www.php.net/license/3_01.txt  PHP License 3.01
 * @version    CVS: $Id$
 * @link       http://pear.php.net/package/Auth
 * @since      File available since Release 1.2.0
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
 * Storage driver for fetching login data from vpopmail
 *
 * @category   Authentication
 * @package    Auth
 * @author     Stanislav Grozev <tacho@orbitel.bg>
 * @author     Adam Ashley <aashley@php.net>
 * @copyright  2001-2006 The PHP Group
 * @license    http://www.php.net/license/3_01.txt  PHP License 3.01
 * @version    Release: 1.4.2  File: $Revision: 8713 $
 * @link       http://pear.php.net/package/Auth
 * @since      Class available since Release 1.2.0
 */
class Auth_Container_vpopmail extends Auth_Container {

    // {{{ Constructor

    /**
     * Constructor of the container class
     *
     * @return void
     */
    function Auth_Container_vpopmail()
    {
        if (!extension_loaded('vpopmail')) {
            return PEAR::raiseError('Cannot use VPOPMail authentication, '
                    .'VPOPMail extension not loaded!', 41, PEAR_ERROR_DIE);
        }
    }

    // }}}
    // {{{ fetchData()

    /**
     * Get user information from vpopmail
     *
     * @param   string Username - has to be valid email address
     * @param   string Password
     * @return  boolean
     */
    function fetchData($username, $password)
    {
        $userdata = array();
        $userdata = preg_split("/@/", $username, 2);
        $result = @vpopmail_auth_user($userdata[0], $userdata[1], $password);

        return $result;
    }

    // }}}

}
?>
