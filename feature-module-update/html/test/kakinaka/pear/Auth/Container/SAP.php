<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4 foldmethod=marker: */

/**
 * Storage driver for use against a SAP system using the SAPRFC PHP extension.
 *
 * Requires the SAPRFC ext available at http://saprfc.sourceforge.net/
 *
 * PHP version 5
 *
 * LICENSE: This source file is subject to version 3.01 of the PHP license
 * that is available through the world-wide-web at the following URI:
 * http://www.php.net/license/3_01.txt.  If you did not receive a copy of
 * the PHP License and are unable to obtain it through the web, please
 * send a note to license@php.net so we can mail you a copy immediately.
 *
 * @category   Authentication
 * @package    Auth
 * @author     Stoyan Stefanov <ssttoo@gmail.com>
 * @author     Adam Ashley <aashley@php.net>
 * @copyright  2001-2006 The PHP Group
 * @license    http://www.php.net/license/3_01.txt  PHP License 3.01
 * @version    CVS: $Id$
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
 * Performs authentication against a SAP system using the SAPRFC PHP extension.
 *
 * When the option GETSSO2 is TRUE (default)
 * the Single Sign-On (SSO) ticket is retrieved
 * and stored as an Auth attribute called 'sap'
 * in order to be reused for consecutive connections.
 *
 * @category   Authentication
 * @package    Auth
 * @author     Stoyan Stefanov <ssttoo@gmail.com>
 * @author     Adam Ashley <aashley@php.net>
 * @copyright  2001-2006 The PHP Group
 * @license    http://www.php.net/license/3_01.txt  PHP License 3.01
 * @version    Release: 1.4.2  File: $Revision: 8713 $
 * @since      Class available since Release 1.4.0
 */
class Auth_Container_SAP extends Auth_Container {

    // {{{ properties
    
    /**
     * @var array Default options
     */
    var $options = array(
        'CLIENT'    => '000',
        'LANG'      => 'EN',
        'GETSSO2'   => true,
    );

    // }}}
    // {{{ Auth_Container_SAP()

    /**
     * Class constructor. Checks that required options
     * are present and that the SAPRFC extension is loaded
     *
     * Options that can be passed and their defaults:
     * <pre>
     * array(
     *   'ASHOST' => "",
     *   'SYSNR'  => "",
     *   'CLIENT' => "000",
     *   'GWHOST' =>"",
     *   'GWSERV' =>"",
     *   'MSHOST' =>"",
     *   'R3NAME' =>"",
     *   'GROUP'  =>"",
     *   'LANG'   =>"EN",
     *   'TRACE'  =>"",
     *   'GETSSO2'=> true
     * )
     * </pre>
     *
     * @param array array of options.
     * @return void
     */
    function Auth_Container_SAP($options)
    {
        $saprfc_loaded = PEAR::loadExtension('saprfc');
        if (!$saprfc_loaded) {
            return PEAR::raiseError('Cannot use SAP authentication, '
                    .'SAPRFC extension not loaded!');
        }
        if (empty($options['R3NAME']) && empty($options['ASHOST'])) {
            return PEAR::raiseError('R3NAME or ASHOST required for authentication');
        }
        $this->options = array_merge($this->options, $options);
    }

    // }}}
    // {{{ fetchData()

    /**
     * Performs username and password check
     *
     * @param string Username
     * @param string Password
     * @return boolean TRUE on success (valid user), FALSE otherwise
     */
    function fetchData($username, $password)
    {
        $connection_options = $this->options;
        $connection_options['USER'] = $username;
        $connection_options['PASSWD'] = $password;
        $rfc = saprfc_open($connection_options);
        if (!$rfc) {
            $message = "Couldn't connect to the SAP system.";
            $error = $this->getError();
            if ($error['message']) {
                $message .= ': ' . $error['message'];
            }
            PEAR::raiseError($message, null, null, null, @$erorr['all']);
            return false;
        } else {
            if (!empty($this->options['GETSSO2'])) {
                if ($ticket = @saprfc_get_ticket($rfc)) {
                    $this->options['MYSAPSSO2'] = $ticket;
                    unset($this->options['GETSSO2']);
                    $this->_auth_obj->setAuthData('sap', $this->options);
                } else {
                    PEAR::raiseError("SSO ticket retrieval failed");
                }
            }
            @saprfc_close($rfc);
            return true;
        }
    
    }

    // }}}
    // {{{ getError()

    /**
     * Retrieves the last error from the SAP connection
     * and returns it as an array.
     *
     * @return array Array of error information
     */
    function getError()
    {

        $error = array();
        $sap_error = saprfc_error();
        if (empty($err)) {
            return $error;
        }
        $err = explode("n", $sap_error);
        foreach ($err AS $line) {
            $item = split(':', $line);
            $error[strtolower(trim($item[0]))] = trim($item[1]);
        }
        $error['all'] = $sap_error;
        return $error;
    }

    // }}}

}

?>
