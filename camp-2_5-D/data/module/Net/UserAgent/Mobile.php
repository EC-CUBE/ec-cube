<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */

/**
 * PHP versions 4 and 5
 *
 * Copyright (c) 2003-2009 KUBO Atsuhiro <kubo@iteman.jp>,
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *
 *     * Redistributions of source code must retain the above copyright
 *       notice, this list of conditions and the following disclaimer.
 *     * Redistributions in binary form must reproduce the above copyright
 *       notice, this list of conditions and the following disclaimer in the
 *       documentation and/or other materials provided with the distribution.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
 * ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE
 * LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
 * CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
 * SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
 * INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @category   Networking
 * @package    Net_UserAgent_Mobile
 * @author     KUBO Atsuhiro <kubo@iteman.jp>
 * @copyright  2003-2009 KUBO Atsuhiro <kubo@iteman.jp>
 * @license    http://www.opensource.org/licenses/bsd-license.php  New BSD License
 * @version    CVS: $Id$
 * @since      File available since Release 0.1
 */

require_once dirname(__FILE__) . '/../../PEAR.php';
require_once dirname(__FILE__) . '/Mobile/Error.php';

// {{{ GLOBALS

/**
 * globals for fallback on no match
 *
 * @global boolean $GLOBALS['NET_USERAGENT_MOBILE_FallbackOnNomatch']
 */
$GLOBALS['NET_USERAGENT_MOBILE_FallbackOnNomatch'] = false;

// }}}
// {{{ Net_UserAgent_Mobile

/**
 * HTTP mobile user agent string parser
 *
 * Net_UserAgent_Mobile parses HTTP_USER_AGENT strings of (mainly Japanese) mobile
 * HTTP user agents. It'll be useful in page dispatching by user agents.
 * This package was ported from Perl's HTTP::MobileAgent.
 * See {@link http://search.cpan.org/search?mode=module&query=HTTP-MobileAgent}
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
 * } elseif ($agent->isSoftBank()) {
 *     // it's SoftBank
 *     // see what's available in Net_UserAgent_Mobile_SoftBank
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
 * @author     KUBO Atsuhiro <kubo@iteman.jp>
 * @copyright  2003-2009 KUBO Atsuhiro <kubo@iteman.jp>
 * @license    http://www.opensource.org/licenses/bsd-license.php  New BSD License
 * @version    Release: 1.0.0
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
     * @return Net_UserAgent_Mobile_Common a newly created or an existing
     *     Net_UserAgent_Mobile_Common object
     * @throws Net_UserAgent_Mobile_Error
     */
    function &factory($userAgent = null)
    {
        if (is_null($userAgent)) {
            $userAgent = @$_SERVER['HTTP_USER_AGENT'];
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
            $file = dirname(__FILE__) . "/Mobile/{$driver}.php";
            if (!include_once $file) {
                return PEAR::raiseError(null,
                                        NET_USERAGENT_MOBILE_ERROR_NOT_FOUND,
                                        null, null,
                                        "Unable to include the $file file",
                                        'Net_UserAgent_Mobile_Error', true
                                        );
            }
        }

        PEAR::staticPushErrorHandling(PEAR_ERROR_RETURN);
        $instance = new $class($userAgent);
        PEAR::staticPopErrorHandling();
        $error = &$instance->getError();
        if (Net_UserAgent_Mobile::isError($error)) {
            if ($GLOBALS['NET_USERAGENT_MOBILE_FallbackOnNomatch']
                && $error->getCode() == NET_USERAGENT_MOBILE_ERROR_NOMATCH
                ) {
                $instance = &Net_UserAgent_Mobile::factory('Net_UserAgent_Mobile_Fallback_On_NoMatch');
                return $instance;
            }

            return PEAR::raiseError($error);
        }

        return $instance;
    }

    // }}}
    // {{{ singleton()

    /**
     * creates a new {@link Net_UserAgent_Mobile_Common} subclass instance or returns
     * a instance from existent ones
     *
     * @param string $userAgent User-Agent string
     * @return Net_UserAgent_Mobile_Common a newly created or an existing
     *     Net_UserAgent_Mobile_Common object
     * @throws Net_UserAgent_Mobile_Error
     */
    function &singleton($userAgent = null)
    {
        static $instances;

        if (!isset($instances)) {
            $instances = array();
        }

        if (is_null($userAgent)) {
            $userAgent = @$_SERVER['HTTP_USER_AGENT'];
        }

        if (!array_key_exists($userAgent, $instances)) {
            $instances[$userAgent] = Net_UserAgent_Mobile::factory($userAgent);
        }

        return $instances[$userAgent];
    }

    // }}}
    // {{{ isError()

    /**
     * tell whether a result code from a Net_UserAgent_Mobile method is an error
     *
     * @param integer $value result code
     * @return boolean whether $value is an {@link Net_UserAgent_Mobile_Error}
     */
    function isError($value)
    {
        return is_object($value)
            && (strtolower(get_class($value)) == strtolower('Net_UserAgent_Mobile_Error')
                || is_subclass_of($value, 'Net_UserAgent_Mobile_Error'));
    }

    // }}}
    // {{{ errorMessage()

    /**
     * return a textual error message for a Net_UserAgent_Mobile error code
     *
     * @param integer $value error code
     * @return string error message, or null if the error code was not recognized
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
     * Checks whether or not the user agent is mobile by a given user agent string.
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
     * Checks whether or not the user agent is DoCoMo by a given user agent string.
     *
     * @param string $userAgent
     * @return boolean
     * @since Method available since Release 0.31.0
     */
    function isDoCoMo($userAgent = null)
    {
        if (is_null($userAgent)) {
            $userAgent = @$_SERVER['HTTP_USER_AGENT'];
        }

        if (preg_match('!^DoCoMo!', $userAgent)) {
            return true;
        }

        return false;
    }

    // }}}
    // {{{ isEZweb()

    /**
     * Checks whether or not the user agent is EZweb by a given user agent string.
     *
     * @param string $userAgent
     * @return boolean
     * @since Method available since Release 0.31.0
     */
    function isEZweb($userAgent = null)
    {
        if (is_null($userAgent)) {
            $userAgent = @$_SERVER['HTTP_USER_AGENT'];
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
     * Checks whether or not the user agent is SoftBank by a given user agent string.
     *
     * @param string $userAgent
     * @return boolean
     * @since Method available since Release 0.31.0
     */
    function isSoftBank($userAgent = null)
    {
        if (is_null($userAgent)) {
            $userAgent = @$_SERVER['HTTP_USER_AGENT'];
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
     * Checks whether or not the user agent is Willcom by a given user agent string.
     *
     * @param string $userAgent
     * @return boolean
     * @since Method available since Release 0.31.0
     */
    function isWillcom($userAgent = null)
    {
        if (is_null($userAgent)) {
            $userAgent = @$_SERVER['HTTP_USER_AGENT'];
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
