<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4 foldmethod=marker: */

/**
 * Storage driver for use against a POP3 server
 *
 * PHP versions 4 and 5
 *
 * LICENSE: This source file is subject to version 3.01 of the PHP license
 * that is available through the world-wide-web at the following URI:
 * http://www.php.net/license/3_01.txt.  If you did not receive a copy of
 * the PHP License and are unable to obtain it through the web, please
 * send a note to license@php.net so we can mail you a copy immediately.
 *
 * @category   Authentication
 * @package    Auth
 * @author     Stefan Ekman <stekman@sedata.org> 
 * @author     Martin Jansen <mj@php.net>
 * @author     Mika Tuupola <tuupola@appelsiini.net> 
 * @author     Adam Ashley <aashley@php.net>
 * @copyright  2001-2006 The PHP Group
 * @license    http://www.php.net/license/3_01.txt  PHP License 3.01
 * @version    CVS: $Id: POP3.php 8713 2006-12-01 05:08:34Z kakinaka $
 * @link       http://pear.php.net/package/Auth
 * @since      File available since Release 1.2.0
 */

/**
 * Include Auth_Container base class
 */
require_once 'Auth/Container.php';
/**
 * Include PEAR package for error handling
 */
require_once 'PEAR.php';
/**
 * Include PEAR Net_POP3 package
 */
require_once 'Net/POP3.php';

/**
 * Storage driver for Authentication on a POP3 server.
 *
 * @category   Authentication
 * @package    Auth
 * @author     Martin Jansen <mj@php.net>
 * @author     Mika Tuupola <tuupola@appelsiini.net> 
 * @author     Adam Ashley <aashley@php.net>
 * @copyright  2001-2006 The PHP Group
 * @license    http://www.php.net/license/3_01.txt  PHP License 3.01
 * @version    Release: 1.4.2  File: $Revision: 8713 $
 * @link       http://pear.php.net/package/Auth
 * @since      Class available since Release 1.2.0
 */
class Auth_Container_POP3 extends Auth_Container
{

    // {{{ properties

    /**
     * POP3 Server
     * @var string
     */
    var $server='localhost';

    /**
     * POP3 Server port
     * @var string
     */
    var $port='110';

    /**
     * POP3 Authentication method
     *
     * Prefered POP3 authentication method. Acceptable values:
     *      Boolean TRUE    - Use Net_POP3's autodetection
     *      String 'DIGEST-MD5','CRAM-MD5','LOGIN','PLAIN','APOP','USER'
     *                      - Attempt this authentication style first
     *                        then fallback to autodetection.
     * @var mixed 
     */
    var $method=true;

    // }}}
    // {{{ Auth_Container_POP3() [constructor]

    /**
     * Constructor of the container class
     *
     * @param  $server string server or server:port combination
     * @return object Returns an error object if something went wrong
     */
    function Auth_Container_POP3($server=null)
    {
        if (isset($server)) {
            if (is_array($server)) {
                if (isset($server['host'])) {
                    $this->server = $server['host'];
                }
                if (isset($server['port'])) {
                    $this->port = $server['port'];
                }
                if (isset($server['method'])) {
                    $this->method = $server['method'];
                }
            } else {
                if (strstr($server, ':')) {
                    $serverparts = explode(':', trim($server));
                    $this->server = $serverparts[0];
                    $this->port = $serverparts[1];
                } else {
                    $this->server = $server;
                }
            }
        }
    }

    // }}}
    // {{{ fetchData()

    /**
     * Try to login to the POP3 server
     *
     * @param   string Username
     * @param   string Password
     * @return  boolean
     */
    function fetchData($username, $password)
    {
        $pop3 =& new Net_POP3();
        $res = $pop3->connect($this->server, $this->port, $this->method);
        if (!$res) {
            return $res;
        }
        $result = $pop3->login($username, $password);
        $pop3->disconnect();
        return $result;
    }

    // }}}

}
?>
