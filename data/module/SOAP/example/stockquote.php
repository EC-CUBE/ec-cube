<html><body>
<?
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
// | Authors: Shane Caraveo <Shane@Caraveo.com>   Port to PEAR and more   |
// | Authors: Dietrich Ayala <dietrich@ganx4.com> Original Author         |
// +----------------------------------------------------------------------+
//
// $Id: stockquote.php,v 1.7 2005/03/10 23:16:40 yunosh Exp $
//
// include soap client class
include("SOAP/Client.php");

print "<br>\n<strong>wsdl:</strong>";
$wsdl = new SOAP_WSDL("http://services.xmethods.net/soap/urn:xmethods-delayed-quotes.wsdl");
$soapclient = $wsdl->getProxy();
print_r($soapclient->getQuote("ibm"));
print "\n\n";

if (extension_loaded('overload')) {
	print "\n<br><strong>overloaded:</strong>";
	$ret = $soapclient->getQuote("ibm");
	print_r($ret);
	print "\n\n";
}
unset($soapclient);

print "\n<br><strong>non wsdl:</strong>";
$soapclient = new SOAP_Client("http://services.xmethods.net:80/soap");
$namespace = "urn:xmethods-delayed-quotes";
/**
 * some soap servers require a Soapaction http header.  PEAR::SOAP does
 * not use them in any way, other to send them if you supply them.
 * soapaction is deprecated in later SOAP versions.
 */
$soapaction = "urn:xmethods-delayed-quotes#getQuote";
$ret = $soapclient->call("getQuote",array("symbol"=>"ibm"),$namespace,$soapaction);
print_r($ret);
print "\n\n";
unset($soapclient);

?>
</html></body>
