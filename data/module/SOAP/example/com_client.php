<?php
/**
 * This file contains an example SOAP client based on the Windows
 * MSSOAP.SoapClient30 COM object that calls methods on the example SOAP
 * server in this same directory.
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

/** SOAPStruct */
require_once './example_types.php';

/* just a simple example of using MS SOAP on win32 as a client
   to the php server.  */

//load COM SOAP client object
$soapclient = new COM("MSSOAP.SoapClient30");

//connect to web service
$soapclient->mssoapinit("http://localhost/SOAP/example/server.php?wsdl");

//obtain result from web service method
$ret = $soapclient->echoString("This is a test!");
print("$ret\n");

$ret = $soapclient->echoStringSimple("This is another test!");
print("$ret\n");

# the following causes an exception in the COM extension

#$ret = $soapclient->divide(22,7);
#print $soapclient->faultcode;
#print $soapclient->faultstring;
#print("22/7=$ret\n");
#print_r($ret);
#$ret = $soapclient->divide(22,0);
#print("22/0=$ret\n");

#$struct = new SOAPStruct('test string',123,123.123);
#$ret = $soapclient->echoStruct($struct);
#print_r($ret);

#$ret = $soapclient->echoStructAsSimpleTypes($struct);
#print_r($ret);

?>