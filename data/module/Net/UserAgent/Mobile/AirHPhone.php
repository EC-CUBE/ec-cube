<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */
// +----------------------------------------------------------------------+
// | PHP version 4                                                        |
// +----------------------------------------------------------------------+
// | Copyright (c) 1997-2004 The PHP Group                                |
// +----------------------------------------------------------------------+
// | This source file is subject to version 3.0 of the PHP license,       |
// | that is bundled with this package in the file LICENSE, and is        |
// | available through the world-wide-web at the following url:           |
// | http://www.php.net/license/3_0.txt.                                  |
// | If you did not receive a copy of the PHP license and are unable to   |
// | obtain it through the world-wide-web, please send a note to          |
// | license@php.net so we can mail you a copy immediately.               |
// +----------------------------------------------------------------------+
// | Authors: KUBO Atsuhiro <iteman@users.sourceforge.net>                            |
// +----------------------------------------------------------------------+
//
// $Id: AirHPhone.php,v 1.9 2006/11/07 09:25:14 kuboa Exp $
//

require_once(dirname(__FILE__) . '/Common.php');
require_once(dirname(__FILE__) . '/Display.php');

/**
 * AirH"PHONE implementation
 *
 * Net_UserAgent_Mobile_AirHPhone is a subclass of
 * {@link Net_UserAgent_Mobile_Common}, which implements DDI POCKET's
 * AirH"PHONE user agents.
 *
 * SYNOPSIS:
 * <code>
 * require_once('Net/UserAgent/Mobile.php');
 *
 * $_SERVER['HTTP_USER_AGENT'] =
 *     'Mozilla/3.0(DDIPOCKET;JRC/AH-J3001V,AH-J3002V/1.0/0100/c50)CNF/2.0';
 * $agent = &Net_UserAgent_Mobile::factory();
 *
 * printf("Name: %s\n", $agent->getName()); // 'DDIPOCKET'
 * printf("Verdor: %s\n", $agent->getVendor()); // 'JRC'
 * printf("Model: %s\n", $agent->getModel()); // 'AH-J3001V,AH-J3002V'
 * printf("Model Version: %s\n", $agent->getModelVersion()); // '1.0'
 * printf("Browser Version: %s\n", $agent->getBrowserVersion()); // '0100'
 * printf("Cache Size: %s\n", $agent->getCacheSize()); // 50
 * </code>
 *
 * @package  Net_UserAgent_Mobile
 * @category Networking
 * @author   KUBO Atsuhiro <iteman@users.sourceforge.net>
 * @access   public
 * @version  $Revision: 1.9 $
 * @see      Net_UserAgent_Mobile_Common
 * @link     http://www.ddipocket.co.jp/airh_phone/i_hp.html
 */
class Net_UserAgent_Mobile_AirHPhone extends Net_UserAgent_Mobile_Common
{

    // {{{ properties

    /**
     * User-Agent name
     * @var string
     * @access public
     */
    var $name = 'WILLCOM';

    /**#@+
     * @access private
     */

    /**
     * vendor name
     * @var string
     */
    var $_vendor;

    /**
     * model name
     * @var string
     */
    var $_model;

    /**
     * version number of the model
     * @var string
     */
    var $_modelVersion;

    /**
     * version number of the browser
     * @var string
     */
    var $_browserVersion;

    /**
     * cache size as killobytes unit
     * @var integer
     */
    var $_cacheSize;

    /**#@-*/

    /**#@+
     * @access public
     */

    // }}}
    // {{{ isAirHPhone()

    /**
     * returns true
     *
     * @return boolean
     */
    function isAirHPhone()
    {
        return true;
    }

    // }}}
    // {{{ parse()

    /**
     * parse HTTP_USER_AGENT string
     */
    function parse()
    {
        $agent = $this->getUserAgent();
        if (preg_match('!^Mozilla/3\.0\((?:DDIPOCKET|WILLCOM);(.*)\)!',
                       $agent, $matches)
            ) {
            list($this->_vendor, $this->_model, $this->_modelVersion,
                 $this->_browserVersion, $cache) =
                explode('/', $matches[1]);
            if (!preg_match('/^[Cc](\d+)/', $cache, $matches)) {
                return $this->noMatch();
            }
            $this->_cacheSize = (integer)$matches[1];
        } else {
            $this->noMatch();
        }
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
        return new Net_UserAgent_Mobile_Display(null);
    }

    // }}}
    // {{{ getVendor()

    /**
     * returns vendor name
     *
     * @return string
     */
    function getVendor()
    {
        return $this->_vendor;
    }

    // }}}
    // {{{ getModel()

    /**
     * returns model name. Note that model names are separated with ','.
     *
     * @return string
     */
    function getModel()
    {
        return $this->_model;
    }

    // }}}
    // {{{ getModelVersion()

    /**
     * returns version number of the model
     *
     * @return string
     */
    function getModelVersion()
    {
        return $this->_modelVersion;
    }

    // }}}
    // {{{ getBrowserVersion()

    /**
     * returns version number of the browser
     *
     * @return string
     */
    function getBrowserVersion()
    {
        return $this->_browserVersion;
    }

    // }}}
    // {{{ getCacheSize()

    /**
     * returns cache size as killobytes unit
     *
     * @return integer
     */
    function getCacheSize()
    {
        return $this->_cacheSize;
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
        return 'H';
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
        return 'AirH';
    }

    /**#@-*/
}

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
