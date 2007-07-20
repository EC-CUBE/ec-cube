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
 * @copyright  2003-2007 KUBO Atsuhiro <iteman@users.sourceforge.net>
 * @license    http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version    CVS: $Id$
 * @since      File available since Release 0.1
 */

// {{{ Net_UserAgent_Mobile_Common

/**
 * Base class that is extended by each user agents implementor
 *
 * Net_UserAgent_Mobile_Common is a class for mobile user agent
 * abstraction layer on Net_UserAgent_Mobile.
 *
 * @category   Networking
 * @package    Net_UserAgent_Mobile
 * @author     KUBO Atsuhiro <iteman@users.sourceforge.net>
 * @copyright  2003-2007 KUBO Atsuhiro <iteman@users.sourceforge.net>
 * @license    http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version    Release: 0.30.0
 * @since      Class available since Release 0.1
 */
class Net_UserAgent_Mobile_Common extends PEAR
{

    // {{{ properties

    /**#@+
     * @access public
     */

    /**
     * User-Agent name like 'DoCoMo'
     * @var string
     */
    var $name = '';

    /**
     * User-Agent version number like '1.0'
     * @var string
     */
    var $version = '';

    /**#@-*/

    /**#@+
     * @access private
     */

    /**
     * {@link Net_UserAgent_Mobile_Display} object
     * @var object {@link Net_UserAgent_Mobile_Display}
     */
    var $_display;

    /**
     * Net_UserAgent_Mobile_Request_XXX object
     * @var object {@link Net_UserAgent_Mobile_Request_Env}
     */
    var $_request;

    /**
     * {@link Net_UserAgent_Mobile_Error} object for error handling in the
     *     constructor
     * @var object
     **/
    var $_error = null;

    /**#@-*/

    /**#@+
     * @access public
     */

    // }}}
    // {{{ constructor

    /**
     * constructor
     *
     * @param object $request a {@link Net_UserAgent_Mobile_Request_Env}
     *     object
     */
    function Net_UserAgent_Mobile_Common($request)
    {
        parent::PEAR('Net_UserAgent_Mobile_Error');
        $this->_request = $request;
        if (Net_UserAgent_Mobile::isError($result = $this->parse())) {
            $this->isError($result);
        }
    }

    // }}}
    // {{{ isError

    /**
     * Returns/set an error when the instance couldn't initialize properly
     *
     * @param object {@link Net_UserAgent_Mobile_Error} object when setting
     *     an error
     * @return object {@link Net_UserAgent_Mobile_Error} object
     */
    function &isError($error = null)
    {
        if ($error !== null) {
            $this->_error = &$error;
        }

        return $this->_error;
    }

    // }}}
    // {{{ raiseError()

    /**
     * This method is used to communicate an error and invoke error
     * callbacks etc. Basically a wrapper for PEAR::raiseError without
     * the message string.
     *
     * @param mixed $code integer error code, or a PEAR error object (all
     *     other parameters are ignored if this parameter is an object
     * @param int $mode error mode, see PEAR_Error docs
     * @param mixed $options If error mode is PEAR_ERROR_TRIGGER, this is the
     *     error level (E_USER_NOTICE etc). If error mode is
     *     PEAR_ERROR_CALLBACK, this is the callback function, either as a
     *     function name, or as an array of an object and method name. For
     *     other error modes this parameter is ignored.
     * @param string $userinfo Extra debug information. Defaults to the last
     *     query and native error code.
     * @return object a PEAR error object
     * @see PEAR_Error
     */
    function &raiseError($code = NET_USERAGENT_MOBILE_ERROR, $mode = null,
                         $options = null, $userinfo = null
                         )
    {

        // The error is yet a Net_UserAgent_Mobile error object
        if (is_object($code)) {
            $error = &PEAR::raiseError($code, null, null, null, null, null,
                                       true
                                       );
            return $error;
        }

        $error = &PEAR::raiseError(null, $code, $mode, $options, $userinfo,
                                   'Net_UserAgent_Mobile_Error', true
                                   );
        return $error;
    }

    // }}}
    // {{{ getUserAgent()

    /**
     * returns User-Agent string
     *
     * @return string
     */
    function getUserAgent()
    {
        return $this->getHeader('User-Agent');
    }

    // }}}
    // {{{ getHeader()

    /**
     * returns a specified HTTP header
     *
     * @param string $header
     * @return string
     */
    function getHeader($header)
    {
        return $this->_request->get($header);
    }

    // }}}
    // {{{ getName()

    /**
     * returns User-Agent name like 'DoCoMo'
     *
     * @return string
     */
    function getName()
    {
        return $this->name;
    }

    // }}}
    // {{{ getDisplay()

    /**
     * returns {@link Net_UserAgent_Mobile_Disply} object
     *
     * @return object a {@link Net_UserAgent_Mobile_Display} object, or a
     *     PEAR error object on error
     * @see Net_UserAgent_Mobile_Display
     */
    function getDisplay()
    {
        if (!is_object($this->_display)) {
            $this->_display = $this->makeDisplay();
        }
        return $this->_display;
    }

    // }}}
    // {{{ getVersion()

    /**
     * returns User-Agent version number like '1.0'
     *
     * @return string
     */
    function getVersion()
    {
        return $this->version;
    }

    // }}}
    // {{{ noMatch()

    /**
     * generates a warning message for new variants
     *
     * @return object a PEAR error object
     */
    function noMatch()
    {
        return $this->raiseError(NET_USERAGENT_MOBILE_ERROR_NOMATCH, null,
                                 null, $this->getUserAgent() .
                                 ': might be new variants. Please contact the author of Net_UserAgent_Mobile!'
                                 );
    }

    // }}}
    // {{{ parse()

    /**
     * parse HTTP_USER_AGENT string (should be implemented in subclasses)
     *
     * @abstract
     */
    function parse()
    {
        die();
    }

    // }}}
    // {{{ makeDisplay()

    /**
     * create a new Net_UserAgent_Mobile_Display class instance (should be
     * implemented in subclasses)
     *
     * @abstract
     */
    function makeDisplay()
    {
        die();
    }

    // }}}
    // {{{ isDoCoMo()

    /**
     * returns true if the agent is DoCoMo
     *
     * @return boolean
     */
    function isDoCoMo()
    {
        return false;
    }

    // }}}
    // {{{ isJPhone()

    /**
     * returns true if the agent is J-PHONE
     *
     * @return boolean
     */
    function isJPhone()
    {
        return false;
    }

    // }}}
    // {{{ isVodafone()

    /**
     * returns true if the agent is Vodafone
     *
     * @return boolean
     */
    function isVodafone()
    {
        return false;
    }

    // }}}
    // {{{ isEZweb()

    /**
     * returns true if the agent is EZweb
     *
     * @return boolean
     */
    function isEZweb()
    {
        return false;
    }

    // }}}
    // {{{ isAirHPhone()

    /**
     * returns true if the agent is AirH"PHONE
     *
     * @return boolean
     */
    function isAirHPhone()
    {
        return false;
    }

    // }}}
    // {{{ isNonMobile()

    /**
     * returns true if the agent is NonMobile
     *
     * @return boolean
     */
    function isNonMobile()
    {
        return false;
    }

    // }}}
    // {{{ isTUKa()

    /**
     * returns true if the agent is TU-Ka
     *
     * @return boolean
     */
    function isTUKa()
    {
        return false;
    }

    // }}}
    // {{{ isWAP1()

    /**
     * returns true if the agent can speak WAP1 protocol
     *
     * @return boolean
     */
    function isWAP1()
    {
        return $this->isEZweb() && !$this->isWAP2();
    }

    // }}}
    // {{{ isWAP2()

    /**
     * returns true if the agent can speak WAP2 protocol
     *
     * @return boolean
     */
    function isWAP2()
    {
        return $this->isEZweb() && $this->isXHTMLCompliant();
    }

    // }}}
    // {{{ getCarrierShortName()

    /**
     * returns the short name of the carrier
     *
     * @abstract
     */
    function getCarrierShortName()
    {
        die();
    }

    // }}}
    // {{{ getCarrierLongName()

    /**
     * returns the long name of the carrier
     *
     * @abstract
     */
    function getCarrierLongName()
    {
        die();
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
?>
