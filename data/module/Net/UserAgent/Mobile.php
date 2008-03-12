<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */

/**
 * PHP versions 4 and 5
 *
 * LICENSE: This source file is subject to version 3.0 of the PHP license
 * that is available through the world-wide-web at the following URI:
 * http://www.php.net/license/3_0.txt.  If you did not receive a copy of
 * the PHP License and are unable to obtain it through the web, please
 * send a note to license@php.net so we can mail you a copy immediately.
 *
 * @category   Networking
 * @package    Net_UserAgent_Mobile
 * @author     KUBO Atsuhiro <iteman@users.sourceforge.net>
 * @copyright  2003-2008 KUBO Atsuhiro <iteman@users.sourceforge.net>
 * @license    http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version    CVS: $Id$
 * @since      File available since Release 0.1
 */

require_once 'PEAR.php';

// {{{ constants

/**
 * Constants for error handling.
 */
define('NET_USERAGENT_MOBILE_OK',               1);
define('NET_USERAGENT_MOBILE_ERROR',           -1);
define('NET_USERAGENT_MOBILE_ERROR_NOMATCH',   -2);
define('NET_USERAGENT_MOBILE_ERROR_NOT_FOUND', -3);

// }}}
// {{{ GLOBALS

/**
 * globals for fallback on no match
 *
 * @global boolean $GLOBALS['_NET_USERAGENT_MOBILE_FALLBACK_ON_NOMATCH']
 */
$GLOBALS['_NET_USERAGENT_MOBILE_FALLBACK_ON_NOMATCH'] = false;

// }}}
// {{{ Net_UserAgent_Mobile

/**
 * HTTP mobile user agent string parser
 *
 * Net_UserAgent_Mobile parses HTTP_USER_AGENT strings of (mainly Japanese)
 * mobile HTTP user agents. It'll be useful in page dispatching by user
 * agents.
 * This package was ported from Perl's HTTP::MobileAgent.
 * See {@link http://search.cpan.org/search?mode=module&query=HTTP-MobileAgent}
 * The author of the HTTP::MobileAgent module is Tatsuhiko Miyagawa
 * <miyagawa@bulknews.net>
 *
 * SYNOPSIS:
 * <code>
 * require_once 'Net/UserAgent/Mobile.php';
 *
 * $agent = &Net_UserAgent_Mobile::factory($agent_string);
 * // or $agent = &Net_UserAgent_Mobile::factory(); // to get from $_SERVER
 *
 * if ($agent->isDoCoMo()) {
 *     // or if ($agent->getName() == 'DoCoMo')
 *     // or if (strtolower(get_class($agent)) == 'http_mobileagent_docomo')
 *     // it's NTT DoCoMo i-mode
 *     // see what's available in Net_UserAgent_Mobile_DoCoMo
 * } elseif ($agent->isVodafone()) {
 *     // it's Vodafone(J-PHONE)
 *     // see what's available in Net_UserAgent_Mobile_Vodafone
 * } elseif ($agent->isEZweb()) {
 *     // it's KDDI/EZWeb
 *     // see what's available in Net_UserAgent_Mobile_EZweb
 * } else {
 *     // may be PC
 *     // $agent is Net_UserAgent_Mobile_NonMobile
 * }
 *
 * $display = $agent->getDisplay();    // Net_UserAgent_Mobile_Display
 * if ($display->isColor()) {
 *    ...
 * }
 * </code>
 *
 * @category   Networking
 * @package    Net_UserAgent_Mobile
 * @author     KUBO Atsuhiro <iteman@users.sourceforge.net>
 * @copyright  2003-2008 KUBO Atsuhiro <iteman@users.sourceforge.net>
 * @license    http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version    Release: 0.31.0
 * @since      Class available since Release 0.1
 */
class Net_UserAgent_Mobile
{

    // {{{ properties

    /**#@+
     * @access public
     */

    /**#@-*/

    /**#@+
     * @access private
     */

    /**#@-*/

    /**#@+
     * @access public
     * @static
     */

    // }}}
    // {{{ factory()

    /**
     * create a new {@link Net_UserAgent_Mobile_Common} subclass instance
     *
     * parses HTTP headers and constructs {@link Net_UserAgent_Mobile_Common}
     * subclass instance.
     * If no argument is supplied, $_SERVER{'HTTP_*'} is used.
     *
     * @param string $userAgent User-Agent string
     * @return mixed a newly created Net_UserAgent_Mobile object, or a PEAR
     *     error object on error
     */
    function &factory($userAgent = null)
    {
        if (is_null($userAgent)) {
            $userAgent = $_SERVER['HTTP_USER_AGENT'];
        }

        // parse User-Agent string
        if (Net_UserAgent_Mobile::isDoCoMo($userAgent)) {
            $driver = 'DoCoMo';
        } elseif (Net_UserAgent_Mobile::isEZweb($userAgent)) {
            $driver = 'EZweb';
        } elseif (Net_UserAgent_Mobile::isSoftBank($userAgent)) {
            $driver = 'SoftBank';
        } elseif (Net_UserAgent_Mobile::isWillcom($userAgent)) {
            $driver = 'Willcom';
        } else {
            $driver = 'NonMobile';
        }

        $class = "Net_UserAgent_Mobile_$driver";

        if (!class_exists($class)) {
            $file = str_replace('_', '/', $class) . '.php';
            if (!include_once $file) {
                return PEAR::raiseError(null,
                                        NET_USERAGENT_MOBILE_ERROR_NOT_FOUND,
                                        null, null,
                                        "Unable to include the $file file",
                                        'Net_UserAgent_Mobile_Error', true
                                        );
            }
        }

        $instance = &new $class($userAgent);
        $error = &$instance->isError();
        if (Net_UserAgent_Mobile::isError($error)) {
            if ($GLOBALS['_NET_USERAGENT_MOBILE_FALLBACK_ON_NOMATCH']
                && $error->getCode() == NET_USERAGENT_MOBILE_ERROR_NOMATCH
                ) {
                $instance = &Net_UserAgent_Mobile::factory('Net_UserAgent_Mobile_Fallback_On_NoMatch');
                return $instance;
            }

            $instance = &$error;
        }

        return $instance;
    }

    // }}}
    // {{{ singleton()

    /**
     * creates a new {@link Net_UserAgent_Mobile_Common} subclass instance or
     * returns a instance from existent ones
     *
     * @param string $userAgent User-Agent string
     * @return mixed a newly created or a existent Net_UserAgent_Mobile
     *     object, or a PEAR error object on error
     * @see Net_UserAgent_Mobile::factory()
     */
    function &singleton($userAgent = null)
    {
        static $instances;

        if (!isset($instances)) {
            $instances = array();
        }

        if (is_null($userAgent)) {
            $userAgent = $_SERVER['HTTP_USER_AGENT'];
        }

        if (!array_key_exists($userAgent, $instances)) {
            $instances[$userAgent] = Net_UserAgent_Mobile::factory($userAgent);
        }

        return $instances[$userAgent];
    }

    // }}}
    // {{{ isError()

    /**
     * tell whether a result code from a Net_UserAgent_Mobile method
     * is an error
     *
     * @param integer $value result code
     * @return boolean whether $value is an {@link Net_UserAgent_Mobile_Error}
     */
    function isError($value)
    {
        return is_a($value, 'Net_UserAgent_Mobile_Error');
    }

    // }}}
    // {{{ errorMessage()

    /**
     * return a textual error message for a Net_UserAgent_Mobile error code
     *
     * @param integer $value error code
     * @return string error message, or false if the error code was not
     *     recognized
     */
    function errorMessage($value)
    {
        static $errorMessages;
        if (!isset($errorMessages)) {
            $errorMessages = array(
                                   NET_USERAGENT_MOBILE_ERROR           => 'unknown error',
                                   NET_USERAGENT_MOBILE_ERROR_NOMATCH   => 'no match',
                                   NET_USERAGENT_MOBILE_ERROR_NOT_FOUND => 'not found',
                                   NET_USERAGENT_MOBILE_OK              => 'no error'
                                   );
        }

        if (Net_UserAgent_Mobile::isError($value)) {
            $value = $value->getCode();
        }

        return isset($errorMessages[$value]) ?
            $errorMessages[$value] :
            $errorMessages[NET_USERAGENT_MOBILE_ERROR];
    }

    // }}}
    // {{{ isMobile()

    /**
     * Checks whether or not the user agent is mobile by a given user agent
     * string.
     *
     * @param string $userAgent
     * @return boolean
     * @since Method available since Release 0.31.0
     */
    function isMobile($userAgent = null)
    {
        if (Net_UserAgent_Mobile::isDoCoMo($userAgent)) {
            return true;
        } elseif (Net_UserAgent_Mobile::isEZweb($userAgent)) {
            return true;
        } elseif (Net_UserAgent_Mobile::isSoftBank($userAgent)) {
            return true;
        } elseif (Net_UserAgent_Mobile::isWillcom($userAgent)) {
            return true;
        }

        return false;
    }

    // }}}
    // {{{ isDoCoMo()

    /**
     * Checks whether or not the user agent is DoCoMo by a given user agent
     * string.
     *
     * @param string $userAgent
     * @return boolean
     * @since Method available since Release 0.31.0
     */
    function isDoCoMo($userAgent = null)
    {
        if (is_null($userAgent)) {
            $userAgent = $_SERVER['HTTP_USER_AGENT'];
        }

        if (preg_match('!^DoCoMo!', $userAgent)) {
            return true;
        }

        return false;
    }

    // }}}
    // {{{ isEZweb()

    /**
     * Checks whether or not the user agent is EZweb by a given user agent
     * string.
     *
     * @param string $userAgent
     * @return boolean
     * @since Method available since Release 0.31.0
     */
    function isEZweb($userAgent = null)
    {
        if (is_null($userAgent)) {
            $userAgent = $_SERVER['HTTP_USER_AGENT'];
        }

        if (preg_match('!^KDDI-!', $userAgent)) {
            return true;
        } elseif (preg_match('!^UP\.Browser!', $userAgent)) {
            return true;
        }

        return false;
    }

    // }}}
    // {{{ isSoftBank()

    /**
     * Checks whether or not the user agent is SoftBank by a given user agent
     * string.
     *
     * @param string $userAgent
     * @return boolean
     * @since Method available since Release 0.31.0
     */
    function isSoftBank($userAgent = null)
    {
        if (is_null($userAgent)) {
            $userAgent = $_SERVER['HTTP_USER_AGENT'];
        }

        if (preg_match('!^SoftBank!', $userAgent)) {
            return true;
        } elseif (preg_match('!^Semulator!', $userAgent)) {
            return true;
        } elseif (preg_match('!^Vodafone!', $userAgent)) {
            return true;
        } elseif (preg_match('!^Vemulator!', $userAgent)) {
            return true;
        } elseif (preg_match('!^MOT-!', $userAgent)) {
            return true;
        } elseif (preg_match('!^MOTEMULATOR!', $userAgent)) {
            return true;
        } elseif (preg_match('!^J-PHONE!', $userAgent)) {
            return true;
        } elseif (preg_match('!^J-EMULATOR!', $userAgent)) {
            return true;
        }

        return false;
    }

    // }}}
    // {{{ isWillcom()

    /**
     * Checks whether or not the user agent is Willcom by a given user agent
     * string.
     *
     * @param string $userAgent
     * @return boolean
     * @since Method available since Release 0.31.0
     */
    function isWillcom($userAgent = null)
    {
        if (is_null($userAgent)) {
            $userAgent = $_SERVER['HTTP_USER_AGENT'];
        }

        if (preg_match('!^Mozilla/3\.0\((?:DDIPOCKET|WILLCOM);!', $userAgent)) {
            return true;
        }

        return false;
    }

    /**#@-*/

    /**#@+
     * @access private
     */

    /**#@-*/

    // }}}
}

// }}}
// {{{ Net_UserAgent_Mobile_Error

/**
 * Net_UserAgent_Mobile_Error implements a class for reporting user
 * agent error messages
 *
 * @category   Networking
 * @package    Net_UserAgent_Mobile
 * @author     KUBO Atsuhiro <iteman@users.sourceforge.net>
 * @copyright  2003-2007 KUBO Atsuhiro <iteman@users.sourceforge.net>
 * @license    http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version    Release: 0.31.0
 * @since      Class available since Release 0.1
 */
class Net_UserAgent_Mobile_Error extends PEAR_Error
{

    // {{{ properties

    /**#@+
     * @access public
     */

    /**#@-*/

    /**#@+
     * @access private
     */

    /**#@-*/

    /**#@+
     * @access public
     */

    // }}}
    // {{{ constructor

    /**
     * constructor
     *
     * @param mixed   $code     Net_UserAgent_Mobile error code, or string
     *     with error message.
     * @param integer $mode     what 'error mode' to operate in
     * @param integer $level    what error level to use for $mode and
     *     PEAR_ERROR_TRIGGER
     * @param mixed   $userinfo additional user/debug info
     * @access public
     */
    function Net_UserAgent_Mobile_Error($code = NET_USERAGENT_MOBILE_ERROR,
                                        $mode = PEAR_ERROR_RETURN,
                                        $level = E_USER_NOTICE,
                                        $userinfo = null
                                        )
    {
        if (is_int($code)) {
            $this->PEAR_Error('Net_UserAgent_Mobile Error: ' .
                              Net_UserAgent_Mobile::errorMessage($code),
                              $code, $mode, $level, $userinfo
                              );
        } else {
            $this->PEAR_Error("Net_UserAgent_Mobile Error: $code",
                              NET_USERAGENT_MOBILE_ERROR, $mode, $level,
                              $userinfo
                              );
        }
    }

    /**#@-*/

    /**#@+
     * @access private
     */

    /**#@-*/

    // }}}
}

// }}}

/*
 * Local Variables:
 * mode: php
 * coding: iso-8859-1
 * tab-width: 4
 * c-basic-offset: 4
 * c-hanging-comment-ender-p: nil
 * indent-tabs-mode: nil
 * End:
 */
