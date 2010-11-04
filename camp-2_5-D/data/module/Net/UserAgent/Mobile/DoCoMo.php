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
 * @link       http://www.nttdocomo.co.jp/service/imode/make/content/spec/useragent/index.html
 * @link       http://www.nttdocomo.co.jp/service/imode/make/content/browser/browser2/useragent/index.html
 * @since      File available since Release 0.1
 */

require_once dirname(__FILE__) . '/Common.php';
require_once dirname(__FILE__) . '/Display.php';
require_once dirname(__FILE__) . '/../Mobile.php';

// {{{ Net_UserAgent_Mobile_DoCoMo

/**
 * NTT DoCoMo implementation
 *
 * Net_UserAgent_Mobile_DoCoMo is a subclass of {@link Net_UserAgent_Mobile_Common},
 * which implements NTT docomo i-mode user agents.
 *
 * SYNOPSIS:
 * <code>
 * require_once 'Net/UserAgent/Mobile.php';
 *
 * $_SERVER['HTTP_USER_AGENT'] = 'DoCoMo/1.0/P502i/c10';
 * $agent = &Net_UserAgent_Mobile::factory();
 *
 * printf("Name: %s\n", $agent->getName()); // 'DoCoMo'
 * printf("Version: %s\n", $agent->getVersion()); // 1.0
 * printf("HTML version: %s\n", $agent->getHTMLVersion()); // 2.0
 * printf("Model: %s\n", $agent->getModel()); // 'P502i'
 * printf("Cache: %dk\n", $agent->getCacheSize()); // 10
 * if ($agent->isFOMA()) {
 *     print "FOMA\n";             // false
 * }
 * printf("Vendor: %s\n", $agent->getVendor()); // 'P'
 * printf("Series: %s\n", $agent->getSeries()); // '502i'
 *
 * // only available with <form utn>
 * // e.g.) 'DoCoMo/1.0/P503i/c10/serNMABH200331';
 * printf("Serial: %s\n", $agent->getSerialNumber()); // 'NMABH200331'
 *
 * // e.g.) 'DoCoMo/2.0 N2001(c10;ser0123456789abcde;icc01234567890123456789)';
 * printf("Serial: %s\n", $agent->getSerialNumber()); // '0123456789abcde'
 * printf("Card ID: %s\n", $agent->getCardID()); // '01234567890123456789'
 *
 * // e.g.) 'DoCoMo/1.0/P502i (Google CHTML Proxy/1.0)'
 * printf("Comment: %s\n", $agent->getComment()); // 'Google CHTML Proxy/1.0'
 *
 * // e.g.) 'DoCoMo/1.0/D505i/c20/TB/W20H10'
 * printf("Status: %s\n", $agent->getStatus()); // 'TB'
 *
 * // only available in eggy/M-stage
 * // e.g.) 'DoCoMo/1.0/eggy/c300/s32/kPHS-K'
 * printf("Bandwidth: %dkbps\n", $agent->getBandwidth()); // 32
 * </code>
 *
 * @category   Networking
 * @package    Net_UserAgent_Mobile
 * @author     KUBO Atsuhiro <kubo@iteman.jp>
 * @copyright  2003-2009 KUBO Atsuhiro <kubo@iteman.jp>
 * @license    http://www.opensource.org/licenses/bsd-license.php  New BSD License
 * @version    Release: 1.0.0
 * @link       http://www.nttdocomo.co.jp/service/imode/make/content/spec/useragent/index.html
 * @link       http://www.nttdocomo.co.jp/service/imode/make/content/browser/browser2/useragent/index.html
 * @since      Class available since Release 0.1
 */
class Net_UserAgent_Mobile_DoCoMo extends Net_UserAgent_Mobile_Common
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
     * status of the cache (TC, TB, TD, TJ)
     * @var string
     */
    var $_status;

    /**
     * bandwidth like 32 as kilobytes unit
     * @var integer
     */
    var $_bandwidth;

    /**
     * hardware unique serial number
     * @var string
     */
    var $_serialNumber;

    /**
     * whether it's FOMA or not
     * @var boolean
     */
    var $_isFOMA = false;

    /**
     * FOMA Card ID (20 digit alphanumeric)
     * @var string
     */
    var $_cardID;

    /**
     * comment on user agent string like 'Google Proxy'
     * @var string
     */
    var $_comment;

    /**
     * cache size as killobytes unit
     * @var integer
     */
    var $_cacheSize;

    /**
     * width and height of the display
     * @var string
     */
    var $_displayBytes;

    /**
     * The model names which have GPS capability.
     *
     * @var array
     * @since Property available since Release 1.0.0RC1
     */
    var $_gpsModels = array('F884i',
                            'F801i',
                            'F905iBiz',
                            'SO905iCS',
                            'N905iBiz',
                            'N905imyu',
                            'SO905i',
                            'F905i',
                            'P905i',
                            'N905i',
                            'D905i',
                            'SH905i',
                            'P904i',
                            'D904i',
                            'F904i',
                            'N904i',
                            'SH904i',
                            'F883iESS',
                            'F883iES',
                            'F903iBSC',
                            'SO903i',
                            'F903i',
                            'D903i',
                            'N903i',
                            'P903i',
                            'SH903i',
                            'SA800i',
                            'SA702i',
                            'SA700iS',
                            'F505iGPS',
                            'F661i',
                            'F884iES',
                            'N906iL',
                            'P906i',
                            'SO906i',
                            'SH906i',
                            'N906imyu',
                            'F906i',
                            'N906i',
                            'F01A',
                            'F03A',
                            'F06A',
                            'F05A',
                            'P01A',
                            'P02A',
                            'SH01A',
                            'SH02A',
                            'SH03A',
                            'SH04A',
                            'N01A',
                            'N02A',
                            'P07A3',
                            'N06A3',
                            'N08A3',
                            'P08A3',
                            'P09A3',
                            'N09A3',
                            'F09A3',
                            'SH05A3',
                            'SH06A3',
                            'SH07A3'
                            );

    /**
     * The HTML versions which maps models to HTML versions.
     *
     * @var array
     * @since Property available since Release 1.0.0RC1
     */
    var $_htmlVersions = array(
        'D501i' => '1.0',
        'F501i' => '1.0',
        'N501i' => '1.0',
        'P501i' => '1.0',
        'D502i' => '2.0',
        'F502i' => '2.0',
        'N502i' => '2.0',
        'P502i' => '2.0',
        'NM502i' => '2.0',
        'SO502i' => '2.0',
        'F502it' => '2.0',
        'N502it' => '2.0',
        'SO502iWM' => '2.0',
        'SH821i' => '2.0',
        'N821i' => '2.0',
        'P821i' => '2.0',
        'D209i' => '2.0',
        'ER209i' => '2.0',
        'F209i' => '2.0',
        'KO209i' => '2.0',
        'N209i' => '2.0',
        'P209i' => '2.0',
        'P209iS' => '2.0',
        'R209i' => '2.0',
        'P651ps' => '2.0',
        'R691i' => '2.0',
        'F210i' => '2.0',
        'N210i' => '2.0',
        'P210i' => '2.0',
        'KO210i' => '2.0',
        'F671i' => '2.0',
        'D210i' => '3.0',
        'SO210i' => '3.0',
        'F503i' => '3.0',
        'F503iS' => '3.0',
        'P503i' => '3.0',
        'P503iS' => '3.0',
        'N503i' => '3.0',
        'N503iS' => '3.0',
        'SO503i' => '3.0',
        'SO503iS' => '3.0',
        'D503i' => '3.0',
        'D503iS' => '3.0',
        'F211i' => '3.0',
        'D211i' => '3.0',
        'N211i' => '3.0',
        'N211iS' => '3.0',
        'P211i' => '3.0',
        'P211iS' => '3.0',
        'SO211i' => '3.0',
        'R211i' => '3.0',
        'SH251i' => '3.0',
        'SH251iS' => '3.0',
        'R692i' => '3.0',
        'N2001' => '3.0',
        'N2002' => '3.0',
        'P2002' => '3.0',
        'D2101V' => '3.0',
        'P2101V' => '3.0',
        'SH2101V' => '3.0',
        'T2101V' => '3.0',
        'D504i' => '4.0',
        'F504i' => '4.0',
        'F504iS' => '4.0',
        'N504i' => '4.0',
        'N504iS' => '4.0',
        'SO504i' => '4.0',
        'P504i' => '4.0',
        'P504iS' => '4.0',
        'D251i' => '4.0',
        'D251iS' => '4.0',
        'F251i' => '4.0',
        'N251i' => '4.0',
        'N251iS' => '4.0',
        'P251iS' => '4.0',
        'F671iS' => '4.0',
        'F212i' => '4.0',
        'SO212i' => '4.0',
        'F661i' => '4.0',
        'F672i' => '4.0',
        'SO213i' => '4.0',
        'SO213iS' => '4.0',
        'SO213iWR' => '4.0',
        'F2051' => '4.0',
        'N2051' => '4.0',
        'P2102V' => '4.0',
        'F2102V' => '4.0',
        'N2102V' => '4.0',
        'N2701' => '4.0',
        'NM850iG' => '4.0',
        'NM705i' => '4.0',
        'NM706i' => '4.0',
        'D505i' => '5.0',
        'SO505i' => '5.0',
        'SH505i' => '5.0',
        'N505i' => '5.0',
        'F505i' => '5.0',
        'P505i' => '5.0',
        'D505iS' => '5.0',
        'P505iS' => '5.0',
        'N505iS' => '5.0',
        'SO505iS' => '5.0',
        'SH505iS' => '5.0',
        'F505iGPS' => '5.0',
        'D252i' => '5.0',
        'SH252i' => '5.0',
        'P252i' => '5.0',
        'N252i' => '5.0',
        'P252iS' => '5.0',
        'D506i' => '5.0',
        'F506i' => '5.0',
        'N506i' => '5.0',
        'P506iC' => '5.0',
        'SH506iC' => '5.0',
        'SO506iC' => '5.0',
        'N506iS' => '5.0',
        'SO506i' => '5.0',
        'SO506iS' => '5.0',
        'N506iS2' => '5.0',
        'D253i' => '5.0',
        'N253i' => '5.0',
        'P253i' => '5.0',
        'D253iWM' => '5.0',
        'P253iS' => '5.0',
        'P213i' => '5.0',
        'F900i' => '5.0',
        'N900i' => '5.0',
        'P900i' => '5.0',
        'SH900i' => '5.0',
        'F900iT' => '5.0',
        'P900iV' => '5.0',
        'N900iS' => '5.0',
        'D900i' => '5.0',
        'F900iC' => '5.0',
        'N900iL' => '5.0',
        'N900iG' => '5.0',
        'F880iES' => '5.0',
        'SH901iC' => '5.0',
        'F901iC' => '5.0',
        'N901iC' => '5.0',
        'D901i' => '5.0',
        'P901i' => '5.0',
        'SH901iS' => '5.0',
        'F901iS' => '5.0',
        'D901iS' => '5.0',
        'P901iS' => '5.0',
        'N901iS' => '5.0',
        'P901iTV' => '5.0',
        'F700i' => '5.0',
        'SH700i' => '5.0',
        'N700i' => '5.0',
        'P700i' => '5.0',
        'F700iS' => '5.0',
        'SH700iS' => '5.0',
        'SA700iS' => '5.0',
        'SH851i' => '5.0',
        'P851i' => '5.0',
        'F881iES' => '5.0',
        'D701i' => '5.0',
        'N701i' => '5.0',
        'P701iD' => '5.0',
        'D701iWM' => '5.0',
        'N701iECO' => '5.0',
        'SA800i' => '5.0',
        'L600i' => '5.0',
        'N600i' => '5.0',
        'L601i' => '5.0',
        'M702iS' => '5.0',
        'M702iG' => '5.0',
        'L602i' => '5.0',
        'F902i' => '6.0',
        'D902i' => '6.0',
        'N902i' => '6.0',
        'P902i' => '6.0',
        'SH902i' => '6.0',
        'SO902i' => '6.0',
        'SH902iS' => '6.0',
        'P902iS' => '6.0',
        'N902iS' => '6.0',
        'D902iS' => '6.0',
        'F902iS' => '6.0',
        'SO902iWP+' => '6.0',
        'SH902iSL' => '6.0',
        'N902iX' => '6.0',
        'N902iL' => '6.0',
        'P702i' => '6.0',
        'N702iD' => '6.0',
        'F702iD' => '6.0',
        'SH702iD' => '6.0',
        'D702i' => '6.0',
        'SO702i' => '6.0',
        'D702iBCL' => '6.0',
        'SA702i' => '6.0',
        'SH702iS' => '6.0',
        'N702iS' => '6.0',
        'P702iD' => '6.0',
        'D702iF' => '6.0',
        'D851iWM' => '6.0',
        'F882iES' => '6.0',
        'N601i' => '6.0',
        'D800iDS' => '6.0',
        'P703imyu' => '6.0',
        'F883i' => '6.0',
        'F883iS' => '6.0',
        'P704imyu' => '6.0',
        'L704i' => '6.0',
        'L705i' => '6.0',
        'L705iX' => '6.0',
        'L852i' => '6.0',
        'L706ie' => '6.0',
        'L01A' => '6.0',
        'L03A' => '6.0',
        'SH903i' => '7.0',
        'P903i' => '7.0',
        'N903i' => '7.0',
        'D903i' => '7.0',
        'F903i' => '7.0',
        'SO903i' => '7.0',
        'D903iTV' => '7.0',
        'F903iX' => '7.0',
        'P903iTV' => '7.0',
        'SH903iTV' => '7.0',
        'F903iBSC' => '7.0',
        'P903iX' => '7.0',
        'SO903iTV' => '7.0',
        'N703iD' => '7.0',
        'F703i' => '7.0',
        'P703i' => '7.0',
        'D703i' => '7.0',
        'SH703i' => '7.0',
        'N703imyu' => '7.0',
        'SO703i' => '7.0',
        'SH904i' => '7.0',
        'N904i' => '7.0',
        'F904i' => '7.0',
        'D904i' => '7.0',
        'P904i' => '7.0',
        'SO704i' => '7.0',
        'F704i' => '7.0',
        'N704imyu' => '7.0',
        'SH704i' => '7.0',
        'D704i' => '7.0',
        'P704i' => '7.0',
        'F883iES' => '7.0',
        'F883iESS' => '7.0',
        'F801i' => '7.0',
        'F705i' => '7.0',
        'D705i' => '7.0',
        'D705imyu' => '7.0',
        'SH705i' => '7.0',
        'SH705i2' => '7.0',
        'SH706ie' => '7.0',
        'F05A' => '7.0',
        'SH905i' => '7.1',
        'D905i' => '7.1',
        'N905i' => '7.1',
        'P905i' => '7.1',
        'F905i' => '7.1',
        'SO905i' => '7.1',
        'N905imyu' => '7.1',
        'N905iBiz' => '7.1',
        'SH905iTV' => '7.1',
        'SO905iCS' => '7.1',
        'F905iBiz' => '7.1',
        'P905iTV' => '7.1',
        'P705i' => '7.1',
        'N705i' => '7.1',
        'N705imyu' => '7.1',
        'P705imyu' => '7.1',
        'SO705i' => '7.1',
        'P705iCL' => '7.1',
        'F884i' => '7.1',
        'F884iES' => '7.1',
        'N906iL' => '7.1',
        'N706i' => '7.1',
        'SO706i' => '7.1',
        'P706imyu' => '7.1',
        'N706ie' => '7.1',
        'N706i2' => '7.1',
        'N03A' => '7.1',
        'N05A' => '7.1',
        'F07A' => '7.1',
        'P906i' => '7.2',
        'SO906i' => '7.2',
        'SH906i' => '7.2',
        'N906imyu' => '7.2',
        'F906i' => '7.2',
        'N906i' => '7.2',
        'SH906iTV' => '7.2',
        'F706i' => '7.2',
        'SH706i' => '7.2',
        'P706ie' => '7.2',
        'SH706iw' => '7.2',
        'F01A' => '7.2',
        'F02A' => '7.2',
        'F03A' => '7.2',
        'F04A' => '7.2',
        'F06A' => '7.2',
        'P01A' => '7.2',
        'P02A' => '7.2',
        'P03A' => '7.2',
        'P04A' => '7.2',
        'P05A' => '7.2',
        'P06A' => '7.2',
        'SH01A' => '7.2',
        'SH02A' => '7.2',
        'SH03A' => '7.2',
        'SH04A' => '7.2',
        'N01A' => '7.2',
        'N02A' => '7.2',
        'N04A' => '7.2',
        'P10A' => '7.2',
                               );

    /**#@-*/

    /**#@+
     * @access public
     */

    // }}}
    // {{{ isDoCoMo()

    /**
     * returns true
     *
     * @return boolean
     */
    function isDoCoMo()
    {
        return true;
    }

    // }}}
    // {{{ parse()

    /**
     * Parses HTTP_USER_AGENT string.
     *
     * @param string $userAgent User-Agent string
     * @throws Net_UserAgent_Mobile_Error
     */
    function parse($userAgent)
    {
        @list($main, $foma_or_comment) = explode(' ', $userAgent, 2);

        if ($foma_or_comment
            && preg_match('/^\((.*)\)$/', $foma_or_comment, $matches)
            ) {

            // DoCoMo/1.0/P209is (Google CHTML Proxy/1.0)
            $this->_comment = $matches[1];
            $result = $this->_parseMain($main);
        } elseif ($foma_or_comment) {

            // DoCoMo/2.0 N2001(c10;ser0123456789abcde;icc01234567890123456789)
            $this->_isFOMA = true;
            @list($this->name, $this->version) = explode('/', $main);
            $result = $this->_parseFOMA($foma_or_comment);
        } else {

            // DoCoMo/1.0/R692i/c10
            $result = $this->_parseMain($main);
        }

        if (Net_UserAgent_Mobile::isError($result)) {
            return $result;
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
        include_once dirname(__FILE__) . '/DoCoMo/ScreenInfo.php';

        $screenInfo = &Net_UserAgent_Mobile_DoCoMo_ScreenInfo::singleton();
        $display = $screenInfo->get($this->getModel());
        if (!is_null($this->_displayBytes)) {
            @list($widthBytes, $heightBytes) = explode('*', $this->_displayBytes);
            $display['width_bytes']  = $widthBytes;
            $display['height_bytes'] = $heightBytes;
        }

        return new Net_UserAgent_Mobile_Display($display);
    }

    // }}}
    // {{{ getHTMLVersion()

    /**
     * Gets the HTML version like '3.0'. Returns null if unknown.
     *
     * @return string
     */
    function getHTMLVersion()
    {
        return @$this->_htmlVersions[ $this->getModel() ];
    }

    // }}}
    // {{{ getCacheSize()

    /**
     * returns cache size as kilobytes unit. returns 5 if unknown.
     *
     * @return integer
     */
    function getCacheSize()
    {
        if ($this->_cacheSize) {
            return $this->_cacheSize;
        }

        return 5;
    }

    // }}}
    // {{{ getSeries()

    /**
     * returns series name like '502i'. returns null if unknown.
     *
     * @return string
     */
    function getSeries()
    {
        if (preg_match('/(\d{4})/', $this->_rawModel)) {
            return 'FOMA';
        }

        if (preg_match('/(\d{3}i)/', $this->_rawModel, $matches)) {
            return $matches[1];
        }

        if ($this->_rawModel == 'P651ps') {
            return '651';
        }
    }

    // }}}
    // {{{ getVendor()

    /**
     * returns vender code like 'SO' for Sony. returns null if unknown.
     *
     * @return string
     */
    function getVendor()
    {
        if (preg_match('/([A-Z]+)\d/', $this->_rawModel, $matches)) {
            return $matches[1];
        }
    }

    // }}}
    // {{{ getStatus()

    /**
     * returns status like "TB", "TC", "TD" or "TJ", which means:
     * 
     * TB | Browsers
     * TC | Browsers with image off (only Available in HTML 5.0)
     * TD | Fetching JAR
     * TJ | i-Appli
     *
     * @return string
     */
    function getStatus()
    {
        return $this->_status;
    }

    // }}}
    // {{{ getBandwidth()

    /**
     * returns bandwidth like 32 as killobytes unit. Only vailable in eggy,
     * returns null otherwise.
     *
     * @return integer
     */
    function getBandwidth()
    {
        return $this->_bandwidth;
    }

    // }}}
    // {{{ getSerialNumber()

    /**
     * returns hardware unique serial number (15 digit in FOMA, 11 digit
     * otherwise alphanumeric). Only available with form utn attribute.
     * returns null otherwise.
     *
     * @return string
     */
    function getSerialNumber()
    {
        return $this->_serialNumber;
    }

    // }}}
    // {{{ isFOMA()

    /**
     * retuns whether it's FOMA or not
     *
     * @return boolean
     */
    function isFOMA()
    {
        return $this->_isFOMA;
    }

    // }}}
    // {{{ getComment()

    /**
     * returns comment on user agent string like 'Google Proxy'. returns null
     * otherwise.
     *
     * @return string
     */
    function getComment()
    {
        return $this->_comment;
    }

    // }}}
    // {{{ getCardID()

    /**
     * returns FOMA Card ID (20 digit alphanumeric). Only available in FOMA
     * with <form utn> attribute. returns null otherwise.
     *
     * @return string
     */ 
    function getCardID()
    {
        return $this->_cardID;
    }

    // }}}
    // {{{ isGPS()

    /**
     * Returns whether a user agent is a GPS model or not.
     *
     * @return boolean
     */ 
    function isGPS()
    {
        return in_array($this->_rawModel, $this->_gpsModels);
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
        return 'I';
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
        return 'DoCoMo';
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
        if (array_key_exists('HTTP_X_DCMGUID', $_SERVER)) {
            return $_SERVER['HTTP_X_DCMGUID'];
        }
    }

    // }}}
    // {{{ getBrowserVersion()

    /**
     * Gets the i-mode browser version.
     *
     * @return string
     * @since Method available since Release 1.0.0RC3
     */
    function getBrowserVersion()
    {
        return $this->getCacheSize() == 500 ? '2.0' : '1.0';
    }

    /**#@-*/

    /**#@+
     * @access private
     */

    // }}}
    // {{{ _parseMain()

    /**
     * parse main part of HTTP_USER_AGENT string (not FOMA)
     *
     * @param string $main main part of HTTP_USER_AGENT string
     * @throws Net_UserAgent_Mobile_Error
     */ 
    function _parseMain($main)
    {
        @list($this->name, $this->version, $this->_rawModel, $cache, $rest) =
            explode('/', $main, 5);
        if ($this->_rawModel == 'SH505i2') {
            $this->_model = 'SH505i';
        }

        if ($cache) {
            if (!preg_match('/^c(\d+)$/', $cache, $matches)) {
                return $this->noMatch();
            }
            $this->_cacheSize = (integer)$matches[1];
        }

        if ($rest) {
            $rest = explode('/', $rest);
            foreach ($rest as $value) {
                if (preg_match('/^ser(\w{11})$/', $value, $matches)) {
                    $this->_serialNumber = $matches[1];
                    continue;
                }
                if (preg_match('/^(T[CDBJ])$/', $value, $matches)) {
                    $this->_status = $matches[1];
                    continue;
                }
                if (preg_match('/^s(\d+)$/', $value, $matches)) {
                    $this->_bandwidth = (integer)$matches[1];
                    continue;
                }
                if (preg_match('/^W(\d+)H(\d+)$/', $value, $matches)) {
                    $this->_displayBytes = "{$matches[1]}*{$matches[2]}";
                    continue;
                }
            }
        }
    }

    // }}}
    // {{{ _parseFOMA()

    /**
     * parse main part of HTTP_USER_AGENT string (FOMA)
     *
     * @param string $foma main part of HTTP_USER_AGENT string
     * @throws Net_UserAgent_Mobile_Error
     */ 
    function _parseFOMA($foma)
    {
        if (!preg_match('/^([^(\s]+)/', $foma, $matches)) {
            return $this->noMatch();
        }

        $this->_rawModel = $matches[1];
        if ($this->_rawModel == 'MST_v_SH2101V') {
            $this->_model = 'SH2101V';
        }

        if (preg_match('/^[^(\s]+\s?\(([^)]+)\)(?:\(([^)]+)\))?$/', $foma, $matches)) {
            if (preg_match('/^compatible/', $matches[1])) { // The user-agent is DoCoMo compatible.
                $this->_comment = $matches[1];
                return;
            }

            if (count($matches) == 3) {
                if (preg_match('/^compatible/', $matches[2])) { // The user-agent is DoCoMo compatible.
                    $this->_comment = $matches[2];
                }
            }

            $rest = explode(';', $matches[1]);
            foreach ($rest as $value) {
                if (preg_match('/^c(\d+)$/', $value, $matches)) {
                    $this->_cacheSize = (integer)$matches[1];
                    continue;
                }
                if (preg_match('/^ser(\w{15})$/', $value, $matches)) {
                    $this->_serialNumber = $matches[1];
                    continue;
                }
                if (preg_match('/^([A-Z]+)$/', $value, $matches)) {
                    $this->_status = $matches[1];
                    continue;
                }
                if (preg_match('/^icc(\w{20})?$/', $value, $matches)) {
                    if (count($matches) == 2) {
                        $this->_cardID = $matches[1];
                    }
                    continue;
                }
                if (preg_match('/^W(\d+)H(\d+)$/', $value, $matches)) {
                    $this->_displayBytes = "{$matches[1]}*{$matches[2]}";
                    continue;
                }
                return $this->noMatch();
            }
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
