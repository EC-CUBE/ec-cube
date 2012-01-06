<?php
/**
 * TCP server.
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

/* First, include the SOAP_Server_TCP class. */
set_time_limit(0);
require_once 'SOAP/Server/TCP.php';
$server = new SOAP_Server_TCP('127.0.0.1', 82);

/* Tell the server to translate to classes we provide if possible. */
$server->_auto_translation = true;

require_once dirname(__FILE__) . '/example_server.php';
$soapclass = new SOAP_Example_Server();
$server->addObjectMap($soapclass, 'urn:SOAP_Example_Server');
$server->run();
