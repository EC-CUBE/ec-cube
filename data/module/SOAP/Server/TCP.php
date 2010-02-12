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
    var $listen;
    var $reuse;

    function SOAP_Server_TCP($localaddr = '127.0.0.1', $port = 10000,
                             $listen = 5, $reuse = true)
    {
        parent::SOAP_Server();
        $this->localaddr = $localaddr;
        $this->port = $port;
        $this->listen = $listen;
        $this->reuse = $reuse;
    }

    function run()
    {
        if (($sock = socket_create(AF_INET, SOCK_STREAM, 0)) < 0) {
            return $this->_raiseSoapFault('socket_create() failed. Reason: ' . socket_strerror($sock));
        }
        if ($this->reuse &&
            !@socket_setopt($sock, SOL_SOCKET, SO_REUSEADDR, 1)) {
            return $this->_raiseSoapFault('socket_setopt() failed. Reason: ' . socket_strerror(socket_last_error($sock)));
        }
        if (($ret = socket_bind($sock, $this->localaddr, $this->port)) < 0) {
            return $this->_raiseSoapFault('socket_bind() failed. Reason: ' . socket_strerror($ret));
        }
        if (($ret = socket_listen($sock, $this->listen)) < 0) {
            return $this->_raiseSoapFault('socket_listen() failed. Reason: ' . socket_strerror($ret));
        }

        while (true) {
            $data = null;
            if (($msgsock = socket_accept($sock)) < 0) {
                $this->_raiseSoapFault('socket_accept() failed. Reason: ' . socket_strerror($msgsock));
                break;
            }
            while ($buf = socket_read($msgsock, 8192)) {
                if (!$buf = trim($buf)) {
                    continue;
                }
                $data .= $buf;
            }

            if ($data) {
                $response = $this->service($data);
                /* Write to the socket. */
                if (!socket_write($msgsock, $response, strlen($response))) {
                    return $this->_raiseSoapFault('Error sending response data reason ' . socket_strerror());
                }
            }

            socket_close ($msgsock);
        }

        socket_close ($sock);
    }

    function service(&$data)
    {
        /* TODO: we need to handle attachments somehow. */
        return $this->parseRequest($data, $attachments);
    }
}
