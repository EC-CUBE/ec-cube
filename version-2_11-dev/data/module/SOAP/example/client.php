<?php
/**
 * This file contains an example SOAP client that calls methods on the example
 * SOAP server in this same directory.
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

/** SOAP_Client */
require 'SOAP/Client.php';

/* This client runs against the example server in SOAP/example/server.php.  It
 * does not use WSDL to run these requests, but that can be changed easily by
 * simply adding '?wsdl' to the end of the url. */
$soapclient = new SOAP_Client('http://localhost/SOAP/example/server.php');

/* Set a few options. */
$options = array();

/* This namespace is the same as declared in server.php. */
$options['namespace'] = 'urn:SOAP_Example_Server';

/* Trace the communication for debugging purposes, so we can later inspect the
 * data with getWire(). */
$options['trace'] = true;

/* Uncomment the following lines if you want to use Basic HTTP
 * Authentication. */
// $options['user'] = 'username';
// $options['pass'] = 'password';

header('Content-Type: text/plain');

/* Calling echoStringSimple. */
$ret = $soapclient->call('echoStringSimple',
                         array('inputStringSimple' => 'this is a test string'),
                         $options);
// echo $soapclient->getWire();
print_r($ret);
echo "\n";

/* Calling echoString. */
$ret = $soapclient->call('echoString',
                         array('inputString' => 'this is a test string'),
                         $options);
// echo $soapclient->getWire();
print_r($ret);
echo "\n";

/* Calling divide with valid parameters. */
$ret = $soapclient->call('divide',
                         array('dividend' => 22, 'divisor' => 7),
                         $options);
// echo $soapclient->getWire();
if (PEAR::isError($ret)) {
    echo 'Error: ' . $ret->getMessage();
} else {
    echo 'Quotient is ' . $ret;
}
echo "\n";

/* Calling divide with invalid parameters. */
$ret = $soapclient->call('divide',
                         array('dividend' => 22, 'divisor' => 0),
                         $options);
// echo $soapclient->getWire();
if (PEAR::isError($ret)) {
    echo 'Error: ' . $ret->getMessage();
} else {
    echo 'Quotient is ' . $ret;
}
echo "\n";

/* The SOAPStruct class is defined in example_types.php. */
require_once 'example_types.php';
$struct = new SOAPStruct('test string', 123, 123.123);

/* Send an object, get an object back. Tell the client to translate to classes
 * we provide if possible. */
$soapclient->_auto_translation = true;

/* You can explicitly set the translation for a specific
 * class. auto_translation works for all cases, but opens ANY class in the
 * script to be used as a data type, and may not be desireable. Both can be
 * used on client or server. */
$soapclient->setTypeTranslation('{http://soapinterop.org/xsd}SOAPStruct',
                                'SOAPStruct');

/* Calling echoStruct. */
$ret = $soapclient->call('echoStruct',
                         array('inputStruct' => $struct->__to_soap()),
                         $options);
// echo $soapclient->getWire();
print_r($ret);

/* Calling echoStructAsSimpleTypes.
 * PHP doesn't support multiple return values in function calls, so we must do
 * a little work to make it happen here, for example returning an array
 * instead.  This requires knowledge on the developers part to figure out how
 * they want to deal with it. */
$ret = $soapclient->call('echoStructAsSimpleTypes',
                         array('inputStruct' => $struct->__to_soap()),
                         $options);
// echo $soapclient->getWire();
if (PEAR::isError($ret)) {
    echo 'Error: ' . $ret->getMessage();
} else {
    list($string, $int, $float) = array_values($ret);
    echo "varString: $string\nvarInt: $int\nvarFloat: $float";
}
echo "\n";

/* Calling echoMimeAttachment.
 * We want to use MIME encoding here, the default is to use DIME encoding. */
$options['attachments'] = 'Mime';
$attachment = new SOAP_Attachment('attachment', 'text/plain', null,
                                  'This is a MIME attachment');
$ret = $soapclient->call('echoMimeAttachment',
                         array($attachment),
                         $options);
// echo $soapclient->getWire();
print_r($ret);
