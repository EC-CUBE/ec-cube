<?php
/**
 * This file contains the code for a local transport layer for testing
 * purposes.
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
 * @author     Jan Schneider <jan@horde.org>
 * @copyright  2008 The PHP Group
 * @license    http://www.php.net/license/2_02.txt  PHP License 2.02
 * @link       http://pear.php.net/package/SOAP
 */

require_once 'SOAP/Transport.php';

/**
 * Test transport for SOAP.
 *
 * @access  public
 * @package SOAP
 * @author  Jan Schneider <jan@horde.org>
 */
class SOAP_Transport_TEST extends SOAP_Transport
{
    /**
     * Sends and receives SOAP data.
     *
     * @param string $msg     Outgoing SOAP data.
     * @param array $options  Options.
     *
     * @return string|SOAP_Fault
     */
    function send($msg, $options = array())
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $this->outgoing_payload = $msg;
        ob_start();
        $server = clone($options['server']);
        $server->service($msg);
        $this->incoming_payload = ob_get_contents();
        ob_end_clean();
        return $this->incoming_payload;
    }

}
