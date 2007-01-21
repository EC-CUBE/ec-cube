<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4 foldmethod=marker: */

/**
 * Storage driver for use against RADIUS servers
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
 * @author     Michael Bretterklieber <michael@bretterklieber.com> 
 * @author     Adam Ashley <aashley@php.net>
 * @copyright  2001-2006 The PHP Group
 * @license    http://www.php.net/license/3_01.txt  PHP License 3.01
 * @version    CVS: $Id: RADIUS.php 8713 2006-12-01 05:08:34Z kakinaka $
 * @link       http://pear.php.net/package/Auth
 * @since      File available since Release 1.2.0
 */

/**
 * Include Auth_Container base class
 */
require_once "Auth/Container.php";
/**
 * Include PEAR Auth_RADIUS package
 */
require_once "Auth/RADIUS.php";

/**
 * Storage driver for authenticating users against RADIUS servers.
 *
 * @category   Authentication
 * @package    Auth
 * @author     Michael Bretterklieber <michael@bretterklieber.com>
 * @author     Adam Ashley <aashley@php.net>
 * @copyright  2001-2006 The PHP Group
 * @license    http://www.php.net/license/3_01.txt  PHP License 3.01
 * @version    Release: 1.4.2  File: $Revision: 8713 $
 * @link       http://pear.php.net/package/Auth
 * @since      Class available since Release 1.2.0
 */
class Auth_Container_RADIUS extends Auth_Container
{

    // {{{ properties

    /**
     * Contains a RADIUS object
     * @var object
     */
    var $radius;
    
    /**
     * Contains the authentication type
     * @var string
     */
    var $authtype;    

    // }}}
    // {{{ Auth_Container_RADIUS() [constructor]

    /**
     * Constructor of the container class.
     *
     * $options can have these keys:
     * 'servers'    an array containing an array: servername, port,
     *              sharedsecret, timeout, maxtries
     * 'configfile' The filename of the configuration file
     * 'authtype'   The type of authentication, one of: PAP, CHAP_MD5,
     *              MSCHAPv1, MSCHAPv2, default is PAP
     *
     * @param  $options associative array
     * @return object Returns an error object if something went wrong
     */
    function Auth_Container_RADIUS($options)
    {
        $this->authtype = 'PAP';
        if (isset($options['authtype'])) {
            $this->authtype = $options['authtype'];
        }
        $classname = 'Auth_RADIUS_' . $this->authtype;
        if (!class_exists($classname)) {
            PEAR::raiseError("Unknown Authtype, please use one of: "
                    ."PAP, CHAP_MD5, MSCHAPv1, MSCHAPv2!", 41, PEAR_ERROR_DIE);
        }
        
        $this->radius = new $classname;

        if (isset($options['configfile'])) {
            $this->radius->setConfigfile($options['configfile']);
        }

        $servers = $options['servers'];
        if (is_array($servers)) {
            foreach ($servers as $server) {
                $servername     = $server[0];
                $port           = isset($server[1]) ? $server[1] : 0;
                $sharedsecret   = isset($server[2]) ? $server[2] : 'testing123';
                $timeout        = isset($server[3]) ? $server[3] : 3;
                $maxtries       = isset($server[4]) ? $server[4] : 3;
                $this->radius->addServer($servername, $port, $sharedsecret, $timeout, $maxtries);
            }
        }
        
        if (!$this->radius->start()) {
            PEAR::raiseError($this->radius->getError(), 41, PEAR_ERROR_DIE);
        }
    }

    // }}}
    // {{{ fetchData()

    /**
     * Authenticate
     *
     * @param  string Username
     * @param  string Password
     * @return bool   true on success, false on reject
     */
    function fetchData($username, $password, $challenge = null)
    {
        switch($this->authtype) {
        case 'CHAP_MD5':
        case 'MSCHAPv1':
            if (isset($challenge)) {
                $this->radius->challenge = $challenge;
                $this->radius->chapid    = 1;
                $this->radius->response  = pack('H*', $password);
            } else {
                require_once 'Crypt/CHAP.php';
                $classname = 'Crypt_' . $this->authtype;
                $crpt = new $classname;
                $crpt->password = $password;
                $this->radius->challenge = $crpt->challenge;
                $this->radius->chapid    = $crpt->chapid;
                $this->radius->response  = $crpt->challengeResponse();
                break;
            }

        case 'MSCHAPv2':
            require_once 'Crypt/CHAP.php';
            $crpt = new Crypt_MSCHAPv2;
            $crpt->username = $username;
            $crpt->password = $password;
            $this->radius->challenge     = $crpt->authChallenge;
            $this->radius->peerChallenge = $crpt->peerChallenge;
            $this->radius->chapid        = $crpt->chapid;
            $this->radius->response      = $crpt->challengeResponse();
            break;

        default:
            $this->radius->password = $password;
            break;
        }

        $this->radius->username = $username;

        $this->radius->putAuthAttributes();
        $result = $this->radius->send();
        if (PEAR::isError($result)) {
            return false;
        }

        $this->radius->getAttributes();
//      just for debugging
//      $this->radius->dumpAttributes();

        return $result;
    }

    // }}}

}
?>
