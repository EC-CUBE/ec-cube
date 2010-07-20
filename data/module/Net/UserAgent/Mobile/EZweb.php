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
 * @link       http://www.au.kddi.com/ezfactory/tec/spec/4_4.html
 * @link       http://www.au.kddi.com/ezfactory/tec/spec/new_win/ezkishu.html
 * @since      File available since Release 0.1.0
 */

require_once dirname(__FILE__) . '/Common.php';
require_once dirname(__FILE__) . '/Display.php';

// {{{ Net_UserAgent_Mobile_EZweb

/**
 * EZweb implementation
 *
 * Net_UserAgent_Mobile_EZweb is a subclass of {@link Net_UserAgent_Mobile_Common},
 * which implements EZweb (WAP1.0/2.0) user agents.
 *
 * SYNOPSIS:
 * <code>
 * require_once 'Net/UserAgent/Mobile.php';
 *
 * $_SERVER['HTTP_USER_AGENT'] = 'UP.Browser/3.01-HI02 UP.Link/3.2.1.2';
 * $agent = &Net_UserAgent_Mobile::factory();
 *
 * printf("Name: %s\n", $agent->getName()); // 'UP.Browser'
 * printf("Version: %s\n", $agent->getVersion()); // 3.01
 * printf("DeviceID: %s\n", $agent->getDeviceID()); // 'HI02'
 * printf("Server: %s\n", $agent->getServer()); // 'UP.Link/3.2.1.2'
 *
 * e.g.) 'UP.Browser/3.01-HI02 UP.Link/3.2.1.2 (Google WAP Proxy/1.0)'
 * printf("Comment: %s\n", $agent->getComment()); // 'Google WAP Proxy/1.0'
 *
 * e.g.) 'KDDI-TS21 UP.Browser/6.0.2.276 (GUI) MMP/1.1'
 * if ($agent->isXHTMLCompliant()) {
 *     print "XHTML compliant!\n"; // true
 * }
 * </code>
 *
 * @category   Networking
 * @package    Net_UserAgent_Mobile
 * @author     KUBO Atsuhiro <kubo@iteman.jp>
 * @copyright  2003-2009 KUBO Atsuhiro <kubo@iteman.jp>
 * @license    http://www.opensource.org/licenses/bsd-license.php  New BSD License
 * @version    Release: 1.0.0
 * @link       http://www.au.kddi.com/ezfactory/tec/spec/4_4.html
 * @link       http://www.au.kddi.com/ezfactory/tec/spec/new_win/ezkishu.html
 * @since      Class available since Release 0.1.0
 */
class Net_UserAgent_Mobile_EZweb extends Net_UserAgent_Mobile_Common
{

    // {{{ properties

    /**#@+
     * @access public
     */

    /**#@-*/

    /**#@+
     * @access private
     */

    /**
     * server string like 'UP.Link/3.2.1.2'
     * @var string
     */
    var $_serverName = '';

    /**
     * comment like 'Google WAP Proxy/1.0'
     * @var string
     */
    var $_comment = null;

    /**
     * whether it's XHTML compliant or not
     * @var boolean
     */
    var $_xhtmlCompliant = false;

    /**#@-*/

    /**#@+
     * @access public
     */

    // }}}
    // {{{ isEZweb()

    /**
     * returns true
     *
     * @return boolean
     */
    function isEZweb()
    {
        return true;
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
        $tuka = substr($this->_rawModel, 2, 1);
        if ($this->isWAP2()) {
            if ($tuka == 'U') {
                return true;
            }
        } else {
            if ($tuka == 'T') {
                return true;
            }
        }
        
        return false;
    }

    // }}}
    // {{{ parse()

    /**
     * Parses HTTP_USER_AGENT string.
     *
     * @param string $userAgent User-Agent string
     */
    function parse($userAgent)
    {
        if (preg_match('/^KDDI-(.*)/', $userAgent, $matches)) {

            // KDDI-TS21 UP.Browser/6.0.2.276 (GUI) MMP/1.1
            $this->_xhtmlCompliant = true;
            @list($this->_rawModel, $browser, $opt, $this->_serverName) =
                explode(' ', $matches[1], 4);
            @list($this->name, $version) = explode('/', $browser);
            $this->version = "$version $opt";
        } else {

            // UP.Browser/3.01-HI01 UP.Link/3.4.5.2
            @list($browser, $this->_serverName, $comment) =
                explode(' ', $userAgent, 3);
            @list($this->name, $software) = explode('/', $browser);
            @list($this->version, $this->_rawModel) = explode('-', $software);
            if ($comment) {
                $this->_comment = preg_replace('/^\((.*)\)$/', '$1', $comment);
            }
        }
    }

    // }}}
    // {{{ makeDisplay()

    /**
     * create a new {@link Net_UserAgent_Mobile_Display} class instance
     *
     * @return Net_UserAgent_Mobile_Display
     */
    function makeDisplay()
    {
        @list($width, $height) =
            explode(',', $this->getHeader('X-UP-DEVCAP-SCREENPIXELS'));
        $screenDepth = explode(',', $this->getHeader('X-UP-DEVCAP-SCREENDEPTH'));
        $depth = $screenDepth[0] ? pow(2, (integer)$screenDepth[0]) : 0;
        $color = $this->getHeader('X-UP-DEVCAP-ISCOLOR') === '1' ? true : false;
        return new Net_UserAgent_Mobile_Display(array('width'  => $width,
                                                      'height' => $height,
                                                      'color'  => $color,
                                                      'depth'  => $depth)
                                                );
    }

    // }}}
    // {{{ getDeviceID()

    /**
     * Returns the device ID of the user agent.
     *
     * @return string
     */
    function getDeviceID()
    {
        return $this->_rawModel;
    }

    // }}}
    // {{{ getServer()

    /**
     * returns server string like 'UP.Link/3.2.1.2'
     *
     * @return string
     */
    function getServer()
    {
        return $this->_serverName;
    }

    // }}}
    // {{{ getComment()

    /**
     * returns comment like 'Google WAP Proxy/1.0'. returns null if nothinng.
     *
     * @return boolean
     */
    function getComment()
    {
        return $this->_comment;
    }

    // }}}
    // {{{ isXHTMLCompliant()

    /**
     * returns whether it's XHTML compliant or not
     *
     * @return boolean
     */
    function isXHTMLCompliant()
    {
        return $this->_xhtmlCompliant;
    }

    // }}}
    // {{{ getCarrierShortName()

    /**
     * returns the short name of the carrier
     *
     * @return string
     */
    function getCarrierShortName()
    {
        return 'E';
    }

    // }}}
    // {{{ getCarrierLongName()

    /**
     * returns the long name of the carrier
     *
     * @return string
     */
    function getCarrierLongName()
    {
        return 'EZweb';
    }

    // }}}
    // {{{ isWIN()

    /**
     * Returns whether the agent is CDMA 1X WIN or not.
     *
     * @return boolean
     */
    function isWIN()
    {
        return substr($this->_rawModel, 2, 1) == 3 ? true : false;
    }

    // }}}
    // {{{ getUID()

    /**
     * Gets the UID of a subscriber.
     *
     * @return string
     * @since Method available since Release 1.0.0RC1
     */
    function getUID()
    {
        if (array_key_exists('HTTP_X_UP_SUBNO', $_SERVER)) {
            return $_SERVER['HTTP_X_UP_SUBNO'];
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
