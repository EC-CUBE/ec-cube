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
// $Id: tcp_server.php,v 1.4 2005/03/10 23:16:40 yunosh Exp $
//

// first, include the SOAP/Server class
require_once 'SOAP/Server/TCP.php';
set_time_limit(0);

$server = new SOAP_Server_TCP("127.0.0.1",82);
/* tell server to translate to classes we provide if possible */
$server->_auto_translation = true;

require_once 'example_server.php';

$soapclass = new SOAP_Example_Server();
$server->addObjectMap($soapclass,'urn:SOAP_Example_Server');
$server->run();
?>