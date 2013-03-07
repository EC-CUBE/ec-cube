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

// {{{ Net_UserAgent_Mobile_Display

/**
 * Display information for Net_UserAgent_Mobile
 *
 * Net_UserAgent_Mobile_Display is a class for display information on
 * {@link Net_UserAgent_Mobile}. Handy for image resizing or dispatching.
 *
 * SYNOPSIS:
 * <code>
 * require_once 'Net/UserAgent/Mobile.php';
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
 * If the environment variable DOCOMO_MAP exists, the specified XML data will be used
 * for DoCoMo display information.
 *
 * ex) Please add the following code.
 * $_SERVER['DOCOMO_MAP'] = '/path/to/DoCoMoMap.xml';
 *
 * @category   Networking
 * @package    Net_UserAgent_Mobile
 * @author     KUBO Atsuhiro <kubo@iteman.jp>
 * @copyright  2003-2009 KUBO Atsuhiro <kubo@iteman.jp>
 * @license    http://www.opensource.org/licenses/bsd-license.php  New BSD License
 * @version    Release: 1.0.0
 * @since      Class available since Release 0.1
 */
class Net_UserAgent_Mobile_Display
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

    /**#@+
     * @access public
     */

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
