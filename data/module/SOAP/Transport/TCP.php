<?php
/**
 * This file contains the code for a TCP transport layer.
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
 * @author     Shane Hanna <iordy_at_iordy_dot_com>
 * @author     Jan Schneider <jan@horde.org>
 * @copyright  2003-2006 The PHP Group
 * @license    http://www.php.net/license/2_02.txt  PHP License 2.02
 * @link       http://pear.php.net/package/SOAP
 */

require_once 'SOAP/Transport.php';

/**
 * TCP transport for SOAP.
 *
 * @todo    use Net_Socket; implement some security scheme; implement support
 *          for attachments
 * @access  public
 * @package SOAP
 * @author  Shane Hanna <iordy_at_iordy_dot_com>
 * @author  Jan Schneider <jan@horde.org>
 */
class SOAP_Transport_TCP extends SOAP_Transport
{
    /**
     * Socket.
     */
    var $socket = null;

    /**
     * Constructor.
     *
     * @param string $url  HTTP url to SOAP endpoint.
     *
     * @access public
     */
    function SOAP_Transport_TCP($url, $encoding = SOAP_DEFAULT_ENCODING)
    {
        parent::SOAP_Base_Object('TCP');
        $this->urlparts = @parse_url($url);
        $this->url = $url;
        $this->encoding = $encoding;
    }

    function _socket_ping()
    {
        // XXX how do we restart after socket_shutdown?
        //if (!$this->socket) {
            // Create socket resource.
            $this->socket = @socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
            if ($this->socket < 0) {
                return 0;
            }

            // Connect.
            $result = socket_connect($this->socket, $this->urlparts['host'],
                                     $this->urlparts['port']);
            if ($result < 0) {
                return 0;
            }
        //}
        return 1;
    }

    /**
     * Sends and receives SOAP data.
     *
     * @access public
     *
     * @param string  Outgoing SOAP data.
     * @param array   Options.
     *
     * @return string|SOAP_Fault
     */
    function send($msg, $options = array())
    {
        $this->fault = null;
        $this->incoming_payload = '';
        $this->outgoing_payload = $msg;
        if (!$this->_validateUrl()) {
            return $this->fault;
        }

        // Check for TCP scheme.
        if (strcasecmp($this->urlparts['scheme'], 'TCP') == 0) {
            // Check connection.
            if (!$this->_socket_ping()) {
                return $this->_raiseSoapFault('Error connecting to ' . $this->url . '; reason: ' . socket_strerror(socket_last_error($this->socket)));
            }

            // Write to the socket.
            if (!@socket_write($this->socket, $this->outgoing_payload,
                               strlen($this->outgoing_payload))) {
                return $this->_raiseSoapFault('Error sending data to ' . $this->url . '; reason: ' . socket_strerror(socket_last_error($this->socket)));
            }

            // Shutdown writing.
            if(!socket_shutdown($this->socket, 1)) {
                return $this->_raiseSoapFault('Cannot change socket mode to read.');
            }

            // Read everything we can.
            while ($buf = @socket_read($this->socket, 1024, PHP_BINARY_READ)) {
                $this->incoming_payload .= $buf;
            }

            // Return payload or die.
            if ($this->incoming_payload) {
                return $this->incoming_payload;
            }

            return $this->_raiseSoapFault('Error reveiving data from ' . $this->url);
        }

        return $this->_raiseSoapFault('Invalid url scheme ' . $this->url);
    }

    /**
     * Validates the url data passed to the constructor.
     *
     * @return boolean
     * @access private
     */
    function _validateUrl()
    {
        if (!is_array($this->urlparts) ) {
            $this->_raiseSoapFault("Unable to parse URL $this->url");
            return false;
        }
        if (!isset($this->urlparts['host'])) {
            $this->_raiseSoapFault("No host in URL $this->url");
            return false;
        }
        if (!isset($this->urlparts['path']) || !$this->urlparts['path']) {
            $this->urlparts['path'] = '/';
        }

        return true;
    }

}
