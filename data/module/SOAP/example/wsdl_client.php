<?php
//
// +----------------------------------------------------------------------+
// | PHP Version 4                                                        |
// +----------------------------------------------------------------------+
// | Copyright (c) 1997-2003 The PHP Group                                |
// +----------------------------------------------------------------------+
// | This source file is subject to version 2.02 of the PHP license,      |
// | that is bundled with this package in the file LICENSE, and is        |
// | available at through the world-wide-web at                           |
// | http://www.php.net/license/2_02.txt.                                 |
// | If you did not receive a copy of the PHP license and are unable to   |
// | obtain it through the world-wide-web, please send a note to          |
// | license@php.net so we can mail you a copy immediately.               |
// +----------------------------------------------------------------------+
// | Authors: Shane Caraveo <Shane@Caraveo.com>                           |
// +----------------------------------------------------------------------+
//
// $Id: wsdl_client.php,v 1.5 2007/01/22 11:33:27 yunosh Exp $
//
include("SOAP/Client.php");

/**
 * this client runs against the example server in SOAP/example/server.php
 * it does not use WSDL to run these requests, but that can be changed easily by simply
 * adding '?wsdl' to the end of the url.
 */
$wsdl = new SOAP_WSDL("http://localhost/SOAP/example/server.php?wsdl");
$soapclient = $wsdl->getProxy();

$ret = $soapclient->echoStringSimple("this is a test string");
//print $soapclient->getWire();
print_r($ret);echo "<br>\n";

$ret = $soapclient->echoString("this is a test string");
print_r($ret);echo "<br>\n";

$ret = $soapclient->divide(22,7);
// print $soapclient->getWire();
if (PEAR::isError($ret))
    print("Error: " . $ret->getMessage() . "<br>\n");
else
    print("Quotient is " . $ret . "<br>\n");

$ret = $soapclient->divide(22,0);
if (PEAR::isError($ret))
    print("Error: " . $ret->getMessage() . "<br>\n");
else
    print("Quotient is " . $ret . "<br>\n");


// SOAPStruct is defined in the following file
require_once 'example_types.php';

$struct = new SOAPStruct('test string',123,123.123);

/* Send an object, get an object back.
 * Tell the client to translate to classes we provide if possible. */
$soapclient->_auto_translation = true;
/* Or you can explicitly set the translation for a specific class.
 * auto_translation works for all cases, but opens ANY class in the script to
 * be used as a data type, and may not be desireable.  both can be used on
 * client or server. */
$soapclient->setTypeTranslation('{http://soapinterop.org/xsd}SOAPStruct',
                                'SOAPStruct');
$ret = $soapclient->echoStruct($struct->__to_soap());
//print $soapclient->getWire();
print_r($ret);

/**
 * PHP doesn't support multiple OUT parameters in function calls, so we must
 * do a little work to make it happen here.  This requires knowledge on the
 * developers part to figure out how they want to deal with it.
 */
$ret = $soapclient->echoStructAsSimpleTypes($struct->__to_soap());
if (PEAR::isError($ret)) {
    print("Error: " . $ret->getMessage() . "<br>\n");
} else {
    list($string, $int, $float) = array_values($ret);
}
echo "varString: $string<br>\nvarInt: $int<br>\nvarFloat: $float<br>\n";

?>