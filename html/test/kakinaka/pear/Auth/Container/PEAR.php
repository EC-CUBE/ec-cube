<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4 foldmethod=marker: */

/**
 * Storage driver for use against PEAR website
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
 * @author     Yavor Shahpasov <yavo@netsmart.com.cy>
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
 * Include PEAR XML_RPC
 */
require_once 'XML/RPC.php';

/**
 * Storage driver for authenticating against PEAR website
 *
 * This driver provides a method for authenticating against the pear.php.net
 * authentication system.
 *
 * @category   Authentication
 * @package    Auth
 * @author     Yavor Shahpasov <yavo@netsmart.com.cy>
 * @author     Adam Ashley <aashley@php.net>
 * @copyright  2001-2006 The PHP Group
 * @license    http://www.php.net/license/3_01.txt  PHP License 3.01
 * @version    Release: 1.4.2  File: $Revision: 8713 $
 * @link       http://pear.php.net/package/Auth
 * @since      Class available since Release 1.3.0
 */
class Auth_Container_Pear extends Auth_Container
{

    // {{{ Auth_Container_Pear() [constructor]

    /**
     * Constructor
     *
     * Currently does nothing
     * 
     * @return void
     */
    function Auth_Container_Pear()
    {
    
    }

    // }}}
    // {{{ fetchData()
    
    /**
     * Get user information from pear.php.net
     *
     * This function uses the given username and password to authenticate
     * against the pear.php.net website
     *
     * @param string    Username
     * @param string    Password
     * @return mixed    Error object or boolean
     */
    function fetchData($username, $password)
    {
        $rpc = new XML_RPC_Client('/xmlrpc.php', 'pear.php.net');
        $rpc_message = new XML_RPC_Message("user.info", array(new XML_RPC_Value($username, "string")) );
        
        // Error Checking howto ???
        $result = $rpc->send($rpc_message);
        $value = $result->value();
        $userinfo = xml_rpc_decode($value);
        if ($userinfo['password'] == md5($password)) {
            $this->activeUser = $userinfo['handle'];
            foreach ($userinfo as $uk=>$uv) {
                $this->_auth_obj->setAuthData($uk, $uv);
            }
            return true;
        }
        return false;
    }

    // }}}
    
}
?>
