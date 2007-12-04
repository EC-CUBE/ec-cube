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
 * @since      File available since Release 0.20.0
 */

require_once dirname(__FILE__) . '/Common.php';
require_once dirname(__FILE__) . '/Display.php';

// {{{ Net_UserAgent_Mobile_Vodafone

/**
 * Vodafone implementation
 *
 * Net_UserAgent_Mobile_Vodafone is a subclass of
 * {@link Net_UserAgent_Mobile_Common}, which implements Vodafone user agents.
 *
 * SYNOPSIS:
 * <code>
 * require_once 'Net/UserAgent/Mobile.php';
 *
 * $_SERVER['HTTP_USER_AGENT'] = 'J-PHONE/2.0/J-DN02';
 * $agent = &Net_UserAgent_Mobile::factory();
 *
 * printf("Name: %s\n", $agent->getName()); // 'J-PHONE'
 * printf("Version: %s\n", $agent->getVersion()); // 2.0
 * printf("Model: %s\n", $agent->getModel()); // 'J-DN02'
 * if ($agent->isPacketCompliant()) {
 *     print "Packet is compliant.\n"; // false
 * }
 *
 * // only availabe in Java compliant
 * // e.g.) 'J-PHONE/4.0/J-SH51/SNXXXXXXXXX SH/0001a Profile/MIDP-1.0 Configuration/CLDC-1.0 Ext-Profile/JSCL-1.1.0'
 * printf("Serial: %s\n", $agent->getSerialNumber()); // XXXXXXXXX
 * printf("Vendor: %s\n", $agent->getVendor()); // 'SH'
 * printf("Vendor Version: %s\n", $agent->getVendorVersion()); // '0001a'
 *
 * $info = $agent->getJavaInfo();  // array
 * foreach ($info as $key => $value) {
 *     print "$key: $value\n";
 * }
 * </code>
 *
 * @category   Networking
 * @package    Net_UserAgent_Mobile
 * @author     KUBO Atsuhiro <iteman@users.sourceforge.net>
 * @copyright  2003-2007 KUBO Atsuhiro <iteman@users.sourceforge.net>
 * @license    http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version    Release: 0.30.0
 * @link       http://developers.vodafone.jp/dp/tool_dl/web/useragent.php
 * @link       http://developers.vodafone.jp/dp/tool_dl/web/position.php
 * @see        Net_UserAgent_Mobile_Common
 * @since      Class available since Release 0.20.0
 */
class Net_UserAgent_Mobile_Vodafone extends Net_UserAgent_Mobile_Common
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
     * name of the model like 'J-DN02'
     * @var string
     */
    var $_model = '';

    /**
     * whether the agent is packet connection complicant or not
     * @var boolean
     */
    var $_packetCompliant = false;

    /**
     * terminal unique serial number
     * @var string
     */
    var $_serialNumber = null;

    /**
     * vendor code like 'SH'
     * @var string
     */
    var $_vendor = '';

    /**
     * vendor version like '0001a'
     * @var string
     */
    var $_vendorVersion = null;

    /**
     * Java profiles
     * @var array
     */
    var $_javaInfo = array();

    /**
     * whether the agent is 3G
     * @var boolean
     */
    var $_is3G = true;

    /**
     * the name of the mobile phone
     * @var string
     */
    var $_msname = '';

    /**#@-*/

    /**#@+
     * @access public
     */

    // }}}
    // {{{ isJPhone()

    /**
     * returns true
     *
     * @return boolean
     */
    function isJPhone()
    {
        return true;
    }

    // }}}
    // {{{ isVodafone()

    /**
     * returns true
     *
     * @return boolean
     */
    function isVodafone()
    {
        return true;
    }

    // }}}
    // {{{ parse()

    /**
     * parse HTTP_USER_AGENT string
     *
     * @return mixed void, or a PEAR error object on error
     */
    function parse()
    {
        $agent = explode(' ', $this->getUserAgent());
        preg_match('!^(?:(SoftBank|Vodafone|J-PHONE)/\d\.\d|MOT-)!',
                   $agent[0], $matches
                   );
        if (count($matches) > 1) {
            $carrier = $matches[1];
        } else {
            $carrier = 'Motorola';
        }

        switch ($carrier) {
        case 'Vodafone':
        case 'SoftBank':
            $result = $this->_parseVodafone($agent);
            break;
        case 'J-PHONE':
            $result = $this->_parseJphone($agent);
            break;
        case 'Motorola':
            $result = $this->_parseMotorola($agent);
            break;
        }

        if (Net_UserAgent_Mobile::isError($result)) {
            return $result;
        }

        $this->_msname = $this->getHeader('x-jphone-msname');
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
            explode('*', $this->getHeader('x-jphone-display'));
        $color = false;
        $depth = 0;
        if ($color_string = $this->getHeader('x-jphone-color')) {
            preg_match('!^([CG])(\d+)$!', $color_string, $matches);
            $color = $matches[1] === 'C' ? true : false;
            $depth = $matches[2];
        }
        return new Net_UserAgent_Mobile_Display(array(
                                                      'width'  => $width,
                                                      'height' => $height,
                                                      'depth'  => $depth,
                                                      'color'  => $color)
                                                );
    }

    // }}}
    // {{{ getModel()

    /**
     * returns name of the model like 'J-DN02'
     *
     * @return string
     */
    function getModel()
    {
        return $this->_model;
    }

    // }}}
    // {{{ isPacketCompliant()

    /**
     * returns whether the agent is packet connection complicant or not
     *
     * @return boolean
     */
    function isPacketCompliant()
    {
        return $this->_packetCompliant;
    }

    // }}}
    // {{{ getSerialNumber()

    /**
     * return terminal unique serial number. returns null if user forbids to
     * send his/her serial number.
     *
     * @return string
     */
    function getSerialNumber()
    {
        return $this->_serialNumber;
    }

    // }}}
    // {{{ getVendor()

    /**
     * returns vendor code like 'SH'
     *
     * @return string
     */
    function getVendor()
    {
        return $this->_vendor;
    }

    // }}}
    // {{{ getVendorVersion()

    /**
     * returns vendor version like '0001a'. returns null if unknown.
     *
     * @return string
     */
    function getVendorVersion()
    {
        return $this->_vendorVersion;
    }

    // }}}
    // {{{ getJavaInfo()

    /**
     * returns array of Java profiles
     *
     * Array structure is something like:
     *
     * - 'Profile'       => 'MIDP-1.0',
     * - 'Configuration' => 'CLDC-1.0',
     * - 'Ext-Profile'   => 'JSCL-1.1.0'
     *
     * @return array
     */
    function getJavaInfo()
    {
        return $this->_javaInfo;
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
        return 'V';
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
        return 'Vodafone';
    }

    // }}}
    // {{{ isTypeC()

    /**
     * returns true if the type is C
     *
     * @return boolean
     */
    function isTypeC()
    {
        if ($this->_is3G || !preg_match('!^[32]\.!', $this->version)) {
            return false;
        }

        return true;
    }

    // }}}
    // {{{ isTypeP()

    /**
     * returns true if the type is P
     *
     * @return boolean
     */
    function isTypeP()
    {
        if ($this->_is3G || !preg_match('!^4\.!', $this->version)) {
            return false;
        }

        return true;
    }

    // }}}
    // {{{ isTypeW()

    /**
     * returns true if the type is W
     *
     * @return boolean
     */
    function isTypeW()
    {
        if ($this->_is3G || !preg_match('!^5\.!', $this->version)) {
            return false;
        }

        return true;
    }

    // }}}
    // {{{ isType3GC()

    /**
     * returns true if the type is 3GC
     *
     * @return boolean
     */
    function isType3GC()
    {
        return $this->_is3G;
    }

    // }}}
    // {{{ getMsname()

    /**
     * returns the name of the mobile phone
     *
     * @return string the name of the mobile phone
     */
    function getMsname()
    {
        return $this->_msname;
    }

    /**#@-*/

    /**#@+
     * @access private
     */

    // }}}
    // {{{ _parseVodafone()

    /**
     * parse HTTP_USER_AGENT string for the Vodafone 3G aegnt
     *
     * @param array $agent parts of the User-Agent string
     * @return mixed void, or a PEAR error object on error
     */
    function _parseVodafone(&$agent)
    {
        $count = count($agent);
        $this->_packetCompliant = true;

        // Vodafone/1.0/V702NK/NKJ001 Series60/2.6 Nokia6630/2.39.148 Profile/MIDP-2.0 Configuration/CLDC-1.1
        // Vodafone/1.0/V702NK/NKJ001/SN123456789012345 Series60/2.6 Nokia6630/2.39.148 Profile/MIDP-2.0 Configuration/CLDC-1.1
        // Vodafone/1.0/V802SE/SEJ001/SN123456789012345 Browser/SEMC-Browser/4.1 Profile/MIDP-2.0 Configuration/CLDC-1.1
        @list($this->name, $this->version, $this->_model, $modelVersion,
              $serialNumber) = explode('/', $agent[0]);
        if ($serialNumber) {
            if (!preg_match('!^SN(.+)!', $serialNumber, $matches)) {
                return $this->noMatch();
            }
            $this->_serialNumber = $matches[1];
        }

        if (!preg_match('!^([a-z]+)((?:[a-z]|\d){4})$!i', $modelVersion, $matches)) {
            return $this->noMatch();
        }

        $this->_vendor = $matches[1];
        $this->_vendorVersion = $matches[2];

        for ($i = 2; $i < $count; ++$i) {
            list($key, $value) = explode('/', $agent[$i]);
            $this->_javaInfo[$key] = $value;
        }
    }

    // }}}
    // {{{ _parseJphone()

    /**
     * parse HTTP_USER_AGENT string for the ancient agent
     *
     * @param array $agent parts of the User-Agent string
     * @return mixed void, or a PEAR error object on error
     */
    function _parseJphone(&$agent)
    {
        $count = count($agent);
        $this->_is3G = false;

        if ($count > 1) {

            // J-PHONE/4.0/J-SH51/SNJSHA3029293 SH/0001aa Profile/MIDP-1.0 Configuration/CLDC-1.0 Ext-Profile/JSCL-1.1.0
            $this->_packetCompliant = true;
            @list($this->name, $this->version, $this->_model,
                  $serialNumber) = explode('/', $agent[0]);
            if ($serialNumber) {
                if (!preg_match('!^SN(.+)!', $serialNumber, $matches)) {
                    return $this->noMatch();
                }
                $this->_serialNumber = $matches[1];
            }
            list($this->_vendor, $this->_vendorVersion) =
                explode('/', $agent[1]);
            for ($i = 2; $i < $count; ++$i) {
                list($key, $value) = explode('/', $agent[$i]);
                $this->_javaInfo[$key] = $value;
            }
        } else {

            // J-PHONE/2.0/J-DN02
            @list($this->name, $this->version, $model) =
                explode('/', $agent[0]);
            $this->_model  = (string)$model;
            if ($this->_model) {
                if (preg_match('!V\d+([A-Z]+)!', $this->_model, $matches)) {
                    $this->_vendor = $matches[1];
                } elseif (preg_match('!J-([A-Z]+)!', $this->_model, $matches)) {
                    $this->_vendor = $matches[1];
                }
            }
        }
    }

    // }}}
    // {{{ _parseMotorola()

    /**
     * parse HTTP_USER_AGENT string for the Motorola 3G aegnt
     *
     * @param array $agent parts of the User-Agent string
     * @return mixed void, or a PEAR error object on error
     */
    function _parseMotorola(&$agent)
    {
        $count = count($agent);
        $this->_packetCompliant = true;
        $this->_vendor = 'MOT';

        // MOT-V980/80.2F.2E. MIB/2.2.1 Profile/MIDP-2.0 Configuration/CLDC-1.1
        list($name, $this->_vendorVersion) = explode('/', $agent[0]);
        $this->_model = substr(strrchr($name, '-'), 1);

        for ($i = 2; $i < $count; ++$i) {
            list($key, $value) = explode('/', $agent[$i]);
            $this->_javaInfo[$key] = $value;
        }
    }

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
