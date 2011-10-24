<?php
/**
 * Server endpoint.
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
 * @author     Jan Schneider <jan@horde.org>       Maintenance
 * @copyright  2003-2007 The PHP Group
 * @license    http://www.php.net/license/2_02.txt  PHP License 2.02
 * @link       http://pear.php.net/package/SOAP
 */

/* First, include the SOAP_Server class. */
require_once 'SOAP/Server.php';
$server = new SOAP_Server;

/* Tell server to translate to classes we provide if possible. */
$server->_auto_translation = true;

/* This is a simple example of implementing a custom call handler.  If you do
 * this, the SOAP server will ignore objects or functions added to it, and
 * will call your handler for **ALL** SOAP calls the server receives, whether
 * the call is defined in your WSDL or not.  The handler receives two
 * arguments, the method name being called, and the arguments sent for that
 * call. */
function myCallHandler($methodname, $args)
{
    global $soapclass;
    return call_user_func_array(array($soapclass, $methodname), $args);
}
$server->setCallHandler('myCallHandler', false);

require_once dirname(__FILE__) . '/example_server.php';
$soapclass = new SOAP_Example_Server();
$server->addObjectMap($soapclass, 'urn:SOAP_Example_Server');

if (isset($_SERVER['REQUEST_METHOD']) &&
    $_SERVER['REQUEST_METHOD'] == 'POST') {
    $server->service($HTTP_RAW_POST_DATA);
} else {
    require_once 'SOAP/Disco.php';
    $disco = new SOAP_DISCO_Server($server, 'ServerExample');
    header('Content-type: text/xml');
    if (isset($_SERVER['QUERY_STRING']) &&
       strpos($_SERVER['QUERY_STRING'], 'wsdl') !== false) {
        echo $disco->getWSDL();
    } else {
        echo $disco->getDISCO();
    }
}
