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
 * @author     Shane Caraveo <Shane@Caraveo.com>   Port to PEAR and more
 * @copyright  2003-2005 The PHP Group
 * @license    http://www.php.net/license/2_02.txt  PHP License 2.02
 * @link       http://pear.php.net/package/SOAP
 */

require_once 'SOAP/Server.php';

require_once 'SOAP/Server/TCP/Handler.php';


/**
 * SOAP Server Class that implements a TCP SOAP Server.
 * http://www.pocketsoap.com/specs/smtpbinding/
 *
 * This class overrides the default HTTP server, providing the ability to
 * accept socket connections and execute SOAP calls.
 *
 * TODO:
 *   use Net_Socket
 *   implement some security scheme
 *   implement support for attachments
 *
 * @access   public
 * @package  SOAP
 * @author   Shane Caraveo <shane@php.net>
 */
class SOAP_Server_TCP extends SOAP_Server {

    var $headers = array();
    var $localaddr;
    var $port;
    var $type;

    function SOAP_Server_TCP($localaddr = '127.0.0.1', $port = 10000,
                             $type = 'sequential')
    {
        parent::SOAP_Server();
        $this->localaddr = $localaddr;
        $this->port = $port;
        $this->type = $type;
    }

    function run($idleTimeout = null)
    {        
        $server = &Net_Server::create($this->type, $this->localaddr,
                                      $this->port);
        if (PEAR::isError($server)) {
            echo $server->getMessage()."\n";
        }
        
        $handler = &new SOAP_Server_TCP_Handler;
        $handler->setSOAPServer($this);
        
        // hand over the object that handles server events
        $server->setCallbackObject($handler);
        $server->readEndCharacter = '</SOAP-ENV:Envelope>';
        $server->setIdleTimeout($idleTimeout);
        
        // start the server
        $server->start();
    }

    function service(&$data)
    {
        /* TODO: we need to handle attachments somehow. */
        $response = $this->parseRequest($data);
        if ($this->fault) {
            $response = $this->fault->message($this->response_encoding);
        }
        return $response;
    }
    
    function onStart()
    {
    }
    
    function onIdle()
    {
    }
}
