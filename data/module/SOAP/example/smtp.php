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
// | Authors: Shane Caraveo <Shane@Caraveo.com>                           |
// +----------------------------------------------------------------------+
//
// $Id: smtp.php,v 1.5 2005/03/10 23:16:40 yunosh Exp $
//
// include soap client class
include("SOAP/Client.php");

$soapclient = new SOAP_Client("mailto:user@domain.com");
$options = array('namespace'=>'http://soapinterop.org/','from'=>'user@domain.com','host'=>'localhost');
$return = $soapclient->call("echoString",array("inputString"=>"this is a test"), $options);
$return = $soapclient->call("echoStringArray",array('inputStringArray' => array('good','bad','ugly')), $options);
// don't expect much of a result!
print_r($return);
print "<br>\n".$soapclient->wire;
?>

