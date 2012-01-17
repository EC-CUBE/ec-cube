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
 * @copyright  2003-2005 The PHP Group
 * @license    http://www.php.net/license/2_02.txt  PHP License 2.02
 * @link       http://pear.php.net/package/SOAP
 */

require 'SOAP/Client.php';

/**
 * This client runs against the example server in SOAP/example/server.php.  It
 * does not use WSDL to run these requests, but that can be changed easily by
 * simply adding '?wsdl' to the end of the url.
 */
$soapclient = new SOAP_Client('http://localhost/SOAP/example/server.php');
// This namespace is the same as declared in server.php.
$options = array('namespace' => 'urn:SOAP_Example_Server',
                 'trace' => true);

$ret = $soapclient->call('echoStringSimple',
                         $params = array('inputStringSimple' => 'this is a test string'),
                         $options);
// print $soapclient->getWire();
print_r($ret);
echo "<br>\n";

$ret = $soapclient->call('echoString',
                         $params = array('inputString' => 'this is a test string'),
                         $options);
print_r($ret);
echo "<br>\n";

$ret = $soapclient->call('divide',
                         $params = array('dividend' => 22, 'divisor' => 7),
                         $options);
// print $soapclient->getWire();
if (PEAR::isError($ret)) {
    echo 'Error: ' . $ret->getMessage() . "<br>\n";
} else {
    echo 'Quotient is ' . $ret . "<br>\n";
}

$ret = $soapclient->call('divide',
                         $params = array('dividend' => 22, 'divisor' => 0),
                         $options);
if (PEAR::isError($ret)) {
    echo 'Error: ' . $ret->getMessage() . "<br>\n";
} else {
    echo 'Quotient is ' . $ret . "<br>\n";
}


// SOAPStruct is defined in the following file.
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
$ret = $soapclient->call('echoStruct',
                         $p = array('inputStruct' => $struct->__to_soap()),
                         $options);
// print $soapclient->getWire();
print_r($ret);

/**
 * PHP doesn't support multiple OUT parameters in function calls, so we must
 * do a little work to make it happen here.  This requires knowledge on the
 * developers part to figure out how they want to deal with it.
 */
$ret = $soapclient->call('echoStructAsSimpleTypes',
                         $p = array('inputStruct' => $struct->__to_soap()),
                         $options);
if (PEAR::isError($ret)) {
    echo 'Error: ' . $ret->getMessage() . "<br>\n";
} else {
    list($string, $int, $float) = array_values($ret);
    echo "varString: $string<br>\nvarInt: $int<br>\nvarFloat: $float<br>\n";
}
