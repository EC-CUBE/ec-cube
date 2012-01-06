<?php
/**
 * This file contains the code for the email SOAP server.
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

/** SOAP_Server */
require_once 'SOAP/Server.php';
require_once 'SOAP/Client.php';
require_once 'SOAP/Transport.php';
require_once 'Mail/mimeDecode.php';

/**
 * SOAP Server Class that implements an email SOAP server.
 * http://www.pocketsoap.com/specs/smtpbinding/
 *
 * This class overrides the default HTTP server, providing the ability to
 * parse an email message and execute SOAP calls.  This class DOES NOT pop the
 * message; the message, complete with headers, must be passed in as a
 * parameter to the service function call.
 *
 * @access   public
 * @package  SOAP
 * @author   Shane Caraveo <shane@php.net>
 */
class SOAP_Server_Email extends SOAP_Server {

    var $headers = array();

    function SOAP_Server_Email($send_response = true)
    {
        parent::SOAP_Server();
        $this->send_response = $send_response;
    }

    /**
     * Removes HTTP headers from response.
     *
     * TODO: use PEAR email classes
     *
     * @return boolean
     * @access private
     */
    function _parseEmail(&$data)
    {
        if (preg_match("/^(.*?)\r?\n\r?\n(.*)/s", $data, $match)) {

            if (preg_match_all('/^(.*?):\s+(.*)$/m', $match[1], $matches)) {
                $hc = count($matches[0]);
                for ($i = 0; $i < $hc; $i++) {
                    $this->headers[strtolower($matches[1][$i])] = trim($matches[2][$i]);
                }
            }

            if (!stristr($this->headers['content-type'], 'text/xml')) {
                    $this->_raiseSoapFault('Invalid Content Type', '', '', 'Client');
                    return false;
            }

            if (strcasecmp($this->headers['content-transfer-encoding'], 'base64')==0) {
                /* Unfold lines. */
                $enctext = preg_replace("/[\r|\n]/", '', $match[2]);
                $data = base64_decode($enctext);
            } else {
                $data = $match[2];
            }

            /* If no content, return false. */
            return strlen($this->request) > 0;
        }

        $this->_raiseSoapFault('Invalid Email Format', '', '', 'Client');

        return false;
    }

    function client(&$data)
    {
        $attachments = array();

        /* If neither matches, we'll just try it anyway. */
        if (stristr($data, 'Content-Type: application/dime')) {
            $this->_decodeDIMEMessage($data, $this->headers, $attachments);
        } elseif (stristr($data, 'MIME-Version:')) {
            /* This is a mime message, let's decode it. */
            $this->_decodeMimeMessage($data, $this->headers, $attachments);
        } else {
            /* The old fallback, but decodeMimeMessage handles things fine. */
            $this->_parseEmail($data);
        }

        /* Get the character encoding of the incoming request treat incoming
         * data as UTF-8 if no encoding set. */
        if (!$this->soapfault &&
            !$this->_getContentEncoding($this->headers['content-type'])) {
            $this->xml_encoding = SOAP_DEFAULT_ENCODING;
            /* An encoding we don't understand, return a fault. */
            $this->_raiseSoapFault('Unsupported encoding, use one of ISO-8859-1, US-ASCII, UTF-8', '', '', 'Server');
        }

        if ($this->soapfault) {
            return $this->soapfault->getFault();
        }

        $client =& new SOAP_Client(null);

        return $client->parseResponse($data, $this->xml_encoding, $this->attachments);
    }

    function service(&$data, $endpoint = '', $send_response = true,
                     $dump = false)
    {
        $this->endpoint = $endpoint;
        $attachments = array();
        $headers = array();

        /* If neither matches, we'll just try it anyway. */
        if (stristr($data, 'Content-Type: application/dime')) {
            $this->_decodeDIMEMessage($data, $this->headers, $attachments);
            $useEncoding = 'DIME';
        } elseif (stristr($data, 'MIME-Version:')) {
            /* This is a mime message, let's decode it. */
            $this->_decodeMimeMessage($data, $this->headers, $attachments);
            $useEncoding = 'Mime';
        } else {
            /* The old fallback, but decodeMimeMessage handles things fine. */
            $this->_parseEmail($data);
        }

        /* Get the character encoding of the incoming request treat incoming
         * data as UTF-8 if no encoding set. */
        if (!$this->_getContentEncoding($this->headers['content-type'])) {
            $this->xml_encoding = SOAP_DEFAULT_ENCODING;
            /* An encoding we don't understand, return a fault. */
            $this->_raiseSoapFault('Unsupported encoding, use one of ISO-8859-1, US-ASCII, UTF-8', '', '', 'Server');
            $response = $this->getFaultMessage();
        }

        if ($this->soapfault) {
            $response = $this->soapfault->message();
        } else {
            $soap_msg = $this->parseRequest($data,$attachments);

            /* Handle Mime or DIME encoding. */
            /* TODO: DIME Encoding should move to the transport, do it here
             * for now and for ease of getting it done. */
            if (count($this->_attachments)) {
                if ($useEncoding == 'Mime') {
                    $soap_msg = $this->_makeMimeMessage($soap_msg);
                } else {
                    /* Default is DIME. */
                    $soap_msg = $this->_makeDIMEMessage($soap_msg);
                    $soap_msg['headers']['Content-Type'] = 'application/dime';
                }
                if (PEAR::isError($soap_msg)) {
                    return $this->raiseSoapFault($soap_msg);
                }
            }

            if (is_array($soap_msg)) {
                $response = $soap_msg['body'];
                if (count($soap_msg['headers'])) {
                    $headers = $soap_msg['headers'];
                }
            } else {
                $response = $soap_msg;
            }
        }

        if ($this->send_response) {
            if ($dump) {
                print $response;
            } else {
                $from = array_key_exists('reply-to', $this->headers) ? $this->headers['reply-to'] : $this->headers['from'];

                $soap_transport =& SOAP_Transport::getTransport('mailto:' . $from, $this->response_encoding);
                $from = $this->endpoint ? $this->endpoint : $this->headers['to'];
                $headers['In-Reply-To'] = $this->headers['message-id'];
                $options = array('from' => $from, 'subject' => $this->headers['subject'], 'headers' => $headers);
                $soap_transport->send($response, $options);
            }
        }
    }
}
