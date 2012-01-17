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
// | Authors: Shane Hanna <iordy_at_iordy_dot_com>                        |
// +----------------------------------------------------------------------+
//
// $Id: tcp_client.php,v 1.5 2007/01/22 11:33:27 yunosh Exp $
//

require_once('SOAP/Client.php');

// client
$soapclient = new SOAP_Client("tcp://127.0.0.1:82");

// namespace
$options = array('namespace' => 'urn:SOAP_Example_Server', 'trace' => true);

// one
$params = array("string" => "this is string 1");
$ret1 = $soapclient->call("echoString", $params, $options);
// echo "WIRE: \n".$soapclient->getWire();
print_r($ret1);
echo "<br />\n";

// two
$params = array("string" => "this is string 2");
$ret2 = $soapclient->call("echoString", $params, $options);
// echo "WIRE: \n".$soapclient->getWire();
print_r($ret2);
echo "<br />\n";
