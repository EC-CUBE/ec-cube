<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4 foldmethod=marker: */

/**
 * Storage driver for Authentication on a Kerberos V server.
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
 * @author     Andrew Teixeira <ateixeira@gmail.com>
 * @author     Adam Ashley <aashley@php.net>
 * @copyright  2001-2006 The PHP Group
 * @license    http://www.php.net/license/3_01.txt  PHP License 3.01
 * @version    CVS: $Id: KADM5.php 8713 2006-12-01 05:08:34Z kakinaka $
 * @link       http://pear.php.net/package/Auth
 * @since      File available since Release 1.4.0
 */

/**
 * Include Auth_Container base class
 */
require_once 'Auth/Container.php';
/**
 * Include PEAR for error handling
 */
require_once 'PEAR.php';

/**
 * Storage driver for Authentication on a Kerberos V server.
 *
 * Available options:
 * hostname:        The hostname of the kerberos server
 * realm:           The Kerberos V realm
 * timeout:         The timeout for checking the server
 * checkServer:     Set to true to check if the server is running when
 *                  constructing the object
 *
 * @category   Authentication
 * @package    Auth
 * @author     Andrew Teixeira <ateixeira@gmail.com>
 * @author     Adam Ashley <aashley@php.net>
 * @copyright  2001-2006 The PHP Group
 * @license    http://www.php.net/license/3_01.txt  PHP License 3.01
 * @version    Release: 1.4.2  File: $Revision: 8713 $
 * @link       http://pear.php.net/package/Auth
 * @since      Class available since Release 1.4.0
 */
class Auth_Container_KADM5 extends Auth_Container {

    // {{{ properties

    /**
     * Options for the class
     * @var string
     */
    var $options = array();

    // }}}
    // {{{ Auth_Container_KADM5()

    /**
     * Constructor of the container class
     *
     * $options can have these keys:
     * 'hostname'    The hostname of the kerberos server
     * 'realm'       The Kerberos V realm
     * 'timeout'     The timeout for checking the server
     * 'checkServer' Set to true to check if the server is running when
     *               constructing the object
     *
     * @param  $options associative array
     * @return object Returns an error object if something went wrong
     */
    function Auth_Container_KADM5($options) {
        if (!extension_loaded('kadm5')) {
            return PEAR::raiseError("Cannot use Kerberos V authentication, KADM5 extension not loaded!", 41, PEAR_ERROR_DIE);
        }
        
        $this->_setDefaults();
        
        if (isset($options['hostname'])) {
            $this->options['hostname'] = $options['hostname'];
        }
        if (isset($options['realm'])) {
            $this->options['realm'] = $options['realm'];
        }
        if (isset($options['timeout'])) {
            $this->options['timeout'] = $options['timeout'];
        }
        if (isset($options['checkServer'])) {
            $this->options['checkServer'] = $options['checkServer'];
        }
        
        if ($this->options['checkServer']) {
            $this->_checkServer();
        }
    }

    // }}}
    // {{{ fetchData()
    
    /**
     * Try to login to the KADM5 server
     *
     * @param   string Username
     * @param   string Password
     * @return  boolean
     */
    function fetchData($username, $password) {
        if ( ($username == NULL) || ($password == NULL) ) {
            return false;
        }
        
        $server = $this->options['hostname'];
        $realm = $this->options['realm'];
        $check = @kadm5_init_with_password($server, $realm, $username, $password);
        
        if ($check == false) {
            return false;
        } else {
            return true;
        }
    }
    
    // }}}
    // {{{ _setDefaults()
    
    /**
     * Set some default options
     *
     * @access private
     */
    function _setDefaults() {
        $this->options['hostname'] = 'localhost';
        $this->options['realm'] = NULL;
        $this->options['timeout'] = 10;
        $this->options['checkServer'] = false;
    }
    
    // }}}
    // {{{ _checkServer()
    
    /**
     * Check if the given server and port are reachable
     *
     * @access private
     */
    function _checkServer() {
        $fp = @fsockopen ($this->options['host'], 88, $errno, $errstr, $this->options['timeout']);
        if (is_resource($fp)) {
            @fclose($fp);
        } else {
            $message = "Error connecting to Kerberos V server "
                .$this->options['host'].":".$this->options['port'];
            return PEAR::raiseError($message, 41, PEAR_ERROR_DIE);
        }
    }
    
    // }}}

}

?>
