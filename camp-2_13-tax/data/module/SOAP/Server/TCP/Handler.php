<?php
/**
 * This file contains the code for the TCP SOAP server.
 *
 * PHP versions 4 and 5
 *
 * LICENSE: This source file is subject to version 2.02 of the PHP license,
 * that is bundled with this package in the file LICENSE, and is available at
 * through the world-wide-web at http://www.php.net/license/2_02.txt.  If you
 * did not receive a copy of the PHP license and are unable to obtain it
 * through the world-wide-web, please send a note to license@php.net so we can
 * mail you a copy immediately.
 *
 * @category   Web Services
 * @package    SOAP
 * @author     Tomasz Rup <tomasz.rup@gmail.com>
 * @copyright  2003-2005 The PHP Group
 * @license    http://www.php.net/license/2_02.txt  PHP License 2.02
 * @link       http://pear.php.net/package/SOAP
 */

/**
 * server base class
 */
require_once 'Net/Server.php';

/**
 * base class for the handler
 */
require_once 'Net/Server/Handler.php';


/**
 * SOAP Server Class that implements a TCP SOAP Server.
 * http://www.pocketsoap.com/specs/smtpbinding/
 *
 * This class overrides the default HTTP server, providing the ability to
 * accept socket connections and execute SOAP calls.
 *
 * @access   public
 * @package  SOAP
 * @author   Tomasz Rup <tomasz.rup@gmail.com>
 */
class SOAP_Server_TCP_Handler extends Net_Server_Handler {

    var $_SOAP_Server;

    function setSOAPServer(&$server)
    {
        $this->_SOAP_Server =& $server;
    }
    
    /**
     * If the user sends data, send it back to him
     *
     * @access   public
     * @param    integer $clientId
     * @param    string  $data
     */
    function onReceiveData($clientId = 0, $data = '')
    {
        if (trim($data) <> '') {
            $response = $this->_SOAP_Server->service($data);
            $this->_server->sendData($clientId, $response);
        }
    }
    
    function onStart()
    {
        $this->_SOAP_Server->onStart();
    }
    
    function onIdle()
    {
        $this->_SOAP_Server->onIdle();
    }
}
