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
 * @version    CVS: $Id: EZweb.php,v 1.17 2007/02/20 15:19:07 kuboa Exp $
 * @link       http://www.au.kddi.com/ezfactory/tec/spec/4_4.html
 * @link       http://www.au.kddi.com/ezfactory/tec/spec/new_win/ezkishu.html
 * @see        Net_UserAgent_Mobile_Common
 * @since      File available since Release 0.1.0
 */

require_once(dirname(__FILE__) . '/Common.php');
require_once(dirname(__FILE__) . '/Display.php');

// {{{ Net_UserAgent_Mobile_EZweb

/**
 * EZweb implementation
 *
 * Net_UserAgent_Mobile_EZweb is a subclass of
 * {@link Net_UserAgent_Mobile_Common}, which implements EZweb (WAP1.0/2.0)
 * user agents.
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
 * @author     KUBO Atsuhiro <iteman@users.sourceforge.net>
 * @copyright  2003-2007 KUBO Atsuhiro <iteman@users.sourceforge.net>
 * @license    http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version    Release: 0.30.0
 * @link       http://www.au.kddi.com/ezfactory/tec/spec/4_4.html
 * @link       http://www.au.kddi.com/ezfactory/tec/spec/new_win/ezkishu.html
 * @see        Net_UserAgent_Mobile_Common
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
     * name of the model like 'P502i'
     * @var string
     */
    var $_model = '';

    /**
     * device ID like 'TS21'
     * @var string
     */
    var $_deviceID = '';

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
        $tuka = substr($this->_deviceID, 2, 1);
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
     * parse HTTP_USER_AGENT string
     */
    function parse()
    {
        $agent = $this->getUserAgent();

        if (preg_match('/^KDDI-(.*)/', $agent, $matches)) {

            // KDDI-TS21 UP.Browser/6.0.2.276 (GUI) MMP/1.1
            $this->_xhtmlCompliant = true;
            list($this->_deviceID, $browser, $opt, $this->_serverName) =
                explode(' ', $matches[1], 4);
            list($this->name, $version) = explode('/', $browser);
            $this->version = "$version $opt";
        } else {

            // UP.Browser/3.01-HI01 UP.Link/3.4.5.2
            @list($browser, $this->_serverName, $comment) =
                explode(' ', $agent, 3);
            list($this->name, $software) = explode('/', $browser);
            list($this->version, $this->_deviceID) =
                explode('-', $software);
            if ($comment) {
                $this->_comment =
                    preg_replace('/^\((.*)\)$/', '$1', $comment);
            }
        }

        $this->_model = $this->_deviceID;
    }

    // }}}
    // {{{ makeDisplay()

    /**
     * create a new {@link Net_UserAgent_Mobile_Display} class instance
     *
     * @return object a newly created {@link Net_UserAgent_Mobile_Display}
     *     object
     * @see Net_UserAgent_Mobile_Display
     */
    function makeDisplay()
    {
        @list($width, $height) =
            explode(',', $this->getHeader('x-up-devcap-screenpixels'));
        $screenDepth =
            explode(',', $this->getHeader('x-up-devcap-screendepth'));
        $depth = $screenDepth[0] ? pow(2, (integer)$screenDepth[0]) : 0;
        $color =
            $this->getHeader('x-up-devcap-iscolor') === '1' ? true : false;
        return new Net_UserAgent_Mobile_Display(array(
                                                      'width'  => $width,
                                                      'height' => $height,
                                                      'color'  => $color,
                                                      'depth'  => $depth
                                                      )
                                                );
    }

    // }}}
    // {{{ getModel()

    /**
     * returns name of the model (device ID) like 'TS21'
     *
     * @return string
     */
    function getModel()
    {
        return $this->_model;
    }

    // }}}
    // {{{ getDeviceID()

    /**
     * returns device ID like 'TS21'
     *
     * @return string
     */
    function getDeviceID()
    {
        return $this->_deviceID;
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
        return substr($this->_deviceID, 2, 1) == 3 ? true : false;
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
