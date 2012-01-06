<?php
/**
 * WSDL client.
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

require_once 'SOAP/Client.php';

/* This client runs against the example server in SOAP/example/server.php.  It
 * does not use WSDL to run these requests, but that can be changed easily by
 * simply adding '?wsdl' to the end of the url. */
$wsdl = new SOAP_WSDL('http://localhost/SOAP/example/server.php?wsdl');
$soapclient = $wsdl->getProxy();

$ret = $soapclient->echoStringSimple('this is a test string');
// echo $soapclient->getWire();
print_r($ret);

$ret = $soapclient->echoString('this is a test string');
print_r($ret);

$ret = $soapclient->divide(22, 7);
// echo $soapclient->getWire();
if (is_a($ret, 'PEAR_Error')) {
    echo 'Error: ' . $ret->getMessage() . "\n";
} else {
    echo 'Quotient is ' . $ret . "\n";
}

$ret = $soapclient->divide(22, 0);
if (is_a($ret, 'PEAR_Error')) {
    echo 'Error: ' . $ret->getMessage() . "\n";
} else {
    echo 'Quotient is ' . $ret . "\n";
}

/* SOAPStruct is defined in the following file. */
require_once './example_types.php';
$struct = new SOAPStruct('test string', 123, 123.123);

/* Tell the client to translate to classes we provide if possible.
 * You can explicitly set the translation for a specific class.
 * auto_translation works for all cases, but opens ANY class in the script to
 * be used as a data type, and may not be desireable.  Both can be used on
 * client or server. */
$soapclient->_auto_translation = true;

$soapclient->setTypeTranslation('{http://soapinterop.org/xsd}SOAPStruct',
                                'SOAPStruct');
$ret = $soapclient->echoStruct($struct->__to_soap());
// echo $soapclient->getWire();
print_r($ret);

/* PHP doesn't support multiple OUT parameters in function calls, so we must
 * do a little work to make it happen here.  This requires knowledge on the
 * developers part to figure out how they want to deal with it. */
$ret = $soapclient->echoStructAsSimpleTypes($struct->__to_soap());
if (is_a($ret, 'PEAR_Error')) {
    echo 'Error: ' . $ret->getMessage() . "\n";
} else {
    list($string, $int, $float) = array_values($ret);
}
echo "varString: $string\nvarInt: $int\nvarFloat: $float\n";
