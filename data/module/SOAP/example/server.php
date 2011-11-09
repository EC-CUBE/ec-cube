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

/* If you want to implement Basic HTTP Authentication, uncomment the following
 * lines of code. */
// if (!isset($_SERVER['PHP_AUTH_USER']) ||
//     !isset($_SERVER['PHP_AUTH_PW']) ||
//     $_SERVER['PHP_AUTH_USER'] !== 'username' ||
//     $_SERVER['PHP_AUTH_PW'] !== 'password') {
//     header('WWW-Authenticate: Basic realm="My Realm"');
//     header('HTTP/1.0 401 Unauthorized');
//     echo 'Not authorized!';
//     exit;
// }

/* First, include the SOAP_Server class. */
require_once 'SOAP/Server.php';
$server = new SOAP_Server;

/* Tell server to translate to classes we provide if possible. */
$server->_auto_translation = true;

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
