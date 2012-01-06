<?php
/**
 * POP3 gateway.
 *
 * PHP versions 4 and 5
 *
 * license: This source file is subject to version 2.02 of the PHP license,
 * that is bundled with this package in the file LICENSE, and is available at
 * through the world-wide-web at http://www.php.net/license/2_02.txt.  If you
 * did not receive a copy of the PHP license and are unable to obtain it
 * through the world-wide-web, please send a note to license@php.net so we can
 * mail you a copy immediately.
 *
 * @category   Web Services
 * @package    SOAP
 * @author     Shane Caraveo <Shane@Caraveo.com>   Port to PEAR and more
 * @author     Jan Schneider <jan@horde.org>       Maintenance
 * @copyright  2003-2007 The PHP Group
 * @license    http://www.php.net/license/2_02.txt  PHP License 2.02
 * @link       http://pear.php.net/package/SOAP
 */

/* SOAP_Server_Email_Gateway */
require_once 'SOAP/Server/Email_Gateway.php';

/* Include a class to access POP3. */
require_once 'Net/POP3.php';

/* Create the SOAP Server object. */
$server = new SOAP_Server_Email_Gateway('http://localhost/soap_interop/server_round2.php');

/* Tell the server to translate to classes we provide if possible. */
$server->_auto_translation = true;

require_once './example_server.php';
$soapclass = new SOAP_Example_Server();
$server->addObjectMap($soapclass,'urn:SOAP_Example_Server');

/* Connect to a POP3 server and read the messages. */
$pop3 = new Net_POP3();
if ($pop3->connect('localhost', 110)) {
    if ($pop3->login('username', 'password')) {
        $listing = $pop3->getListing();

        /* Now loop through each message, and call the SOAP server with that
         * message. */
        foreach ($listing as $msg) {
            $email = $pop3->getMsg($msg['msg_id']);

            /* This is where we actually handle the SOAP response.  The
             * SOAP::Server::Email class we are using will send the SOAP
             * response to the sender via email. */
            if ($email) {
                $server->service($email);
            }

            // $pop3->deleteMsg($msg['msg_id']);
        }
    }
    $pop3->disconnect();
}
