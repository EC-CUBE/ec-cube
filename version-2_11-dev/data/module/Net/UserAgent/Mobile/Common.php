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

require_once dirname(__FILE__) . '/Error.php';
require_once dirname(__FILE__) . '/../../../PEAR.php';

// {{{ Net_UserAgent_Mobile_Common

/**
 * Base class that is extended by each user agents implementor
 *
 * Net_UserAgent_Mobile_Common is a class for mobile user agent
 * abstraction layer on Net_UserAgent_Mobile.
 *
 * @category   Networking
 * @package    Net_UserAgent_Mobile
 * @author     KUBO Atsuhiro <kubo@iteman.jp>
 * @copyright  2003-2009 KUBO Atsuhiro <kubo@iteman.jp>
 * @license    http://www.opensource.org/licenses/bsd-license.php  New BSD License
 * @version    Release: 1.0.0
 * @since      Class available since Release 0.1
 */
class Net_UserAgent_Mobile_Common
{

    // {{{ properties

    /**#@+
     * @access public
     */

    /**
     * User-Agent name like 'DoCoMo'
     * @var string
     */
    var $name;

    /**
     * User-Agent version number like '1.0'
     * @var string
     */
    var $version;

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
     * {@link Net_UserAgent_Mobile_Error} object for error handling in the constructor
     * @var object
     **/
    var $_error;

    /**
     * The User-Agent string.
     * @var string
     * @since Property available since Release 0.31.0
     **/
    var $_userAgent;

    /**
     * The model name of the user agent.
     *
     * @var string
     * @since Property available since Release 0.31.0
     */
    var $_model;

    /**
     * The raw model name of the user agent.
     *
     * @var string
     * @since Property available since Release 0.31.0
     */
    var $_rawModel;

    /**#@-*/

    /**#@+
     * @access public
     */

    // }}}
    // {{{ constructor

    /**
     * constructor
     *
     * @param string $userAgent User-Agent string
     */
    function Net_UserAgent_Mobile_Common($userAgent)
    {
        $this->_userAgent = $userAgent;

        $result = $this->parse($userAgent);
        if (PEAR::isError($result)) {
            $this->_error = &$result;
        }
    }

    // }}}
    // {{{ getError

    /**
     * Gets a Net_UserAgent_Mobile_Error object.
     *
     * @param object {@link Net_UserAgent_Mobile_Error} object when setting an error
     * @return Net_UserAgent_Mobile_Error
     * @since Method available since Release 1.0.0RC2
     */
    function &getError()
    {
        if (is_null($this->_error)) {
            $return = null;
            return $return;
        }

        return $this->_error;
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
        return $this->_userAgent;
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
        return @$_SERVER[ 'HTTP_' . str_replace('-', '_', $header) ];
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
     * @return Net_UserAgent_Mobile_Display
     */
    function getDisplay()
    {
        if (is_null($this->_display)) {
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
     * @throws Net_UserAgent_Mobile_Error
     */
    function noMatch()
    {
        return PEAR::raiseError($this->getUserAgent() . ': might be new variants. Please contact the author of Net_UserAgent_Mobile!',
                                NET_USERAGENT_MOBILE_ERROR_NOMATCH,
                                null,
                                null,
                                null,
                                'Net_UserAgent_Mobile_Error'
                                );
    }

    // }}}
    // {{{ parse()

    /**
     * Parses HTTP_USER_AGENT string.
     *
     * @param string $userAgent User-Agent string
     * @abstract
     */
    function parse($userAgent) {}

    // }}}
    // {{{ makeDisplay()

    /**
     * create a new Net_UserAgent_Mobile_Display class instance (should be
     * implemented in subclasses)
     *
     * @return Net_UserAgent_Mobile_Display
     * @abstract
     */
    function makeDisplay() {}

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

    // }}}
    // {{{ isSoftBank()

    /**
     * Returns whether the agent is SoftBank or not.
     *
     * @return boolean
     * @since Method available since Release 0.31.0
     */
    function isSoftBank()
    {
        return false;
    }

    // }}}
    // {{{ isWillcom()

    /**
     * Returns whether the agent is Willcom or not.
     *
     * @return boolean
     * @since Method available since Release 0.31.0
     */
    function isWillcom()
    {
        return false;
    }

    // }}}
    // {{{ isSmartphone()

    /**
     * Returns whether the agent is Smartphone or not.
     *
     * @return boolean
     * @since Method available since Release 0.31.0
     */
    function isSmartphone()
    {
        return false;
    }


    // }}}
    // {{{ getModel()

    /**
     * Returns the model name of the user agent.
     *
     * @return string
     * @since Method available since Release 0.31.0
     */
    function getModel()
    {
        if (is_null($this->_model)) {
            return $this->_rawModel;
        } else {
            return $this->_model;
        }
    }

    // }}}
    // {{{ getRawModel()

    /**
     * Returns the raw model name of the user agent.
     *
     * @return string
     * @since Method available since Release 0.31.0
     */
    function getRawModel()
    {
        return $this->_rawModel;
    }

    // }}}
    // {{{ getUID()

    /**
     * Gets the UID of a subscriber.
     *
     * @return string
     * @since Method available since Release 1.0.0RC1
     */
    function getUID() {}

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
