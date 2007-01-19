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
// $Id: Display.php,v 1.9 2006/11/07 09:25:14 kuboa Exp $
//

/**
 * Display information for Net_UserAgent_Mobile
 *
 * Net_UserAgent_Mobile_Display is a class for display information on
 * {@link Net_UserAgent_Mobile}. Handy for image resizing or dispatching.
 *
 * SYNOPSIS:
 * <code>
 * require_once('Net/UserAgent/Mobile.php');
 *
 * $agent = &Net_UserAgent_Mobile::factory();
 * $display = $agent->getDisplay();
 *
 * $width  = $display->getWidth();
 * $height = $display->getHeight();
 * list($width, $height) = $display->getSize();
 *
 * if ($display->isColor()) {
 *     $depth = $display->getDepth();
 * }
 *
 * // only available in DoCoMo 505i
 * $width_bytes  = $display->getWidthBytes();
 * $height_bytes = $display->getHeightBytes();
 * </code>
 *
 * USING EXTERNAL MAP FILE:
 * If the environment variable DOCOMO_MAP exists, the specified XML data will
 * be used for DoCoMo display information.
 *
 * ex) Please add the following code.
 * $_SERVER['DOCOMO_MAP'] = '/path/to/DoCoMoMap.xml';
 *
 * @package  Net_UserAgent_Mobile
 * @category Networking
 * @author   KUBO Atsuhiro <iteman@users.sourceforge.net>
 * @access   public
 * @version  $Revision: 1.9 $
 */
class Net_UserAgent_Mobile_Display
{

    // {{{ properties

    /**#@+
     * @access private
     */

    /**
     * width of the display
     * @var integer
     */
    var $_width;

    /**
     * height of the display
     * @var integer
     */
    var $_height;

    /**
     * depth of the display
     * @var integer
     */
    var $_depth;

    /**
     * color capability of the display
     * @var boolean
     */
    var $_color;

    /**
     * width (bytes) of the display
     * @var integer
     */
    var $_widthBytes;

    /**
     * height (bytes) of the display
     * @var integer
     */
    var $_heightBytes;

    /**#@-*/

    // }}}
    // {{{ constructor

    /**
     * constructor
     *
     * @param array $data display infomation
     */
    function Net_UserAgent_Mobile_Display($data)
    {
        $this->_width  = (integer)@$data['width'];
        $this->_height = (integer)@$data['height'];
        $this->_depth  = (integer)@$data['depth'];
        $this->_color  = (boolean)@$data['color'];

        $this->_widthBytes  = (integer)@$data['width_bytes'];
        $this->_heightBytes = (integer)@$data['height_bytes'];
    }

    /**#@+
     * @access public
     */

    // }}}
    // {{{ calcSize()

    /**
     * returns width * height of the display
     *
     * @return integer
     */
    function calcSize()
    {
        return $this->_width * $this->_height;
    }

    // }}}
    // {{{ getSize()

    /**
     * returns width with height of the display
     *
     * @return array
     */
    function getSize()
    {
        return array($this->_width, $this->_height);
    }

    // }}}
    // {{{ getWidth()

    /**
     * returns width of the display
     *
     * @return integer
     */
    function getWidth()
    {
        return $this->_width;
    }

    // }}}
    // {{{ getHeight()

    /**
     * returns height of the display
     *
     * @return integer
     */
    function getHeight()
    {
        return $this->_height;
    }

    // }}}
    // {{{ getDepth()

    /**
     * returns depth of the display
     *
     * @return integer
     */
    function getDepth()
    {
        return $this->_depth;
    }

    // }}}
    // {{{ isColor()

    /**
     * returns true if the display has color capability
     *
     * @return boolean
     */
    function isColor()
    {
        return $this->_color;
    }

    // }}}
    // {{{ getWidthBytes()

    /**
     * returns width (bytes) of the display
     *
     * @return integer
     */
    function getWidthBytes()
    {
        return $this->_widthBytes;
    }

    // }}}
    // {{{ getHeightBytes()

    /**
     * returns height (bytes) of the display
     *
     * @return integer
     */
    function getHeightBytes()
    {
        return $this->_heightBytes;
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
