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
// | Authors: Shane Caraveo <Shane@Caraveo.com>   Port to PEAR and more   |
// +----------------------------------------------------------------------+
//
// $Id: email_server.php,v 1.5 2005/03/10 23:16:40 yunosh Exp $
//

/*
This reads a message from stdin, and calls the soap server defined

You can use this from qmail by creating a .qmail-soaptest file with:
    | /usr/bin/php /path/to/email_server.php
*/

# include the email server class
require_once 'SOAP/Server/Email.php';

$server = new SOAP_Server_Email;

/* tell server to translate to classes we provide if possible */
$server->_auto_translation = true;

require_once 'example_server.php';

$soapclass = new SOAP_Example_Server();
$server->addObjectMap($soapclass,'urn:SOAP_Example_Server');

# read stdin
$fin = fopen('php://stdin','rb');
if (!$fin) exit(0);

$email = '';
while (!feof($fin) && $data = fread($fin, 8096)) {
  $email .= $data;
}

fclose($fin);

# doit!
$server->service($email);

?>