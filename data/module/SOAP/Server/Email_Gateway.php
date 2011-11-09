<?php
/**
 * This file contains the code for the email-HTTP SOAP gateway server.
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

/** SOAP_Server_Email */
require_once 'SOAP/Server/Email.php';
require_once 'SOAP/Transport.php';

/**
 * SOAP Server Class that implements an email SOAP server.
 * http://www.pocketsoap.com/specs/smtpbinding/
 *
 * This class overrides the default HTTP server, providing the ability to
 * parse an email message and execute soap calls.  This class DOES NOT pop the
 * message; the message, complete with headers, must be passed in as a
 * parameter to the service function call.
 *
 * This class calls a provided HTTP SOAP server, forwarding the email request,
 * then sending the HTTP response out as an email.
 *
 * @access   public
 * @package  SOAP
 * @author   Shane Caraveo <shane@php.net>
 */
class SOAP_Server_Email_Gateway extends SOAP_Server_Email {

    var $gateway = null;
    var $dump = false;

    function SOAP_Server_Email_Gateway($gateway = '', $send_response = true,
                                       $dump = false)
    {
        parent::SOAP_Server();
        $this->send_response = $send_response;
        $this->gateway = $gateway;
        $this->dump = $dump;
    }

    function service(&$data, $gateway = '', $endpoint = '',
                     $send_response = true, $dump = false)
    {
        $this->endpoint = $endpoint;
        $response = '';
        $useEncoding = 'Mime';
        $options = array();
        if (!$gateway) {
            $gateway = $this->gateway;
        }

        /* We have a full set of headers, need to find the first blank
         * line. */
        $this->_parseEmail($data);
        if ($this->fault) {
            $response = $this->fault->message();
        }
        if ($this->headers['content-type'] == 'application/dime')
            $useEncoding = 'DIME';

        /* Call the HTTP Server. */
        if (!$response) {
            $soap_transport =& SOAP_Transport::getTransport($gateway, $this->xml_encoding);
            if ($soap_transport->fault) {
                $response = $soap_transport->fault->message();
            }
        }

        /* Send the message. */
        if (!$response) {
            $options['soapaction'] = $this->headers['soapaction'];
            $options['headers']['Content-Type'] = $this->headers['content-type'];

            $response = $soap_transport->send($data, $options);
            if (isset($this->headers['mime-version']))
                $options['headers']['MIME-Version'] = $this->headers['mime-version'];

            if ($soap_transport->fault) {
                $response = $soap_transport->fault->message();
            } else {
                foreach ($soap_transport->transport->attachments as $cid => $body) {
                    $this->attachments[] = array('body' => $body, 'cid' => $cid, 'encoding' => 'base64');
                }
                if (count($this->_attachments)) {
                    if ($useEncoding == 'Mime') {
                        $soap_msg = $this->_makeMimeMessage($response);
                        $options['headers']['MIME-Version'] = '1.0';
                    } else {
                        /* Default is DIME. */
                        $soap_msg = $this->_makeDIMEMessage($response);
                        $options['headers']['Content-Type'] = 'application/dime';
                    }
                    if (PEAR::isError($soap_msg)) {
                        return $this->_raiseSoapFault($soap_msg);
                    }
                    if (is_array($soap_msg)) {
                        $response = $soap_msg['body'];
                        if (count($soap_msg['headers'])) {
                            if (isset($options['headers'])) {
                                $options['headers'] = array_merge($options['headers'], $soap_msg['headers']);
                            } else {
                                $options['headers'] = $soap_msg['headers'];
                            }
                        }
                    }
                }
            }
        }

        if ($this->send_response) {
            if ($this->dump || $dump) {
                print $response;
            } else {
                $from = array_key_exists('reply-to', $this->headers) ? $this->headers['reply-to'] : $this->headers['from'];

                $soap_transport =& SOAP_Transport::getTransport('mailto:' . $from, $this->response_encoding);
                $from = $this->endpoint ? $this->endpoint : $this->headers['to'];
                $headers = array('In-Reply-To' => $this->headers['message-id']);
                $options = array('from' => $from, 'subject'=> $this->headers['subject'], 'headers' => $headers);
                $soap_transport->send($response, $options);
            }
        }
    }
}
