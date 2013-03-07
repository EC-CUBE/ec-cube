<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */

/**
 * PHP versions 4 and 5
 *
 * Copyright (c) 2008-2009 KUBO Atsuhiro <kubo@iteman.jp>,
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
 * @copyright  2008-2009 KUBO Atsuhiro <kubo@iteman.jp>
 * @license    http://www.opensource.org/licenses/bsd-license.php  New BSD License
 * @version    CVS: $Id: ScreenInfo.php,v 1.2 2009/06/23 08:06:58 kuboa Exp $
 * @link       http://www.nttdocomo.co.jp/service/imode/make/content/spec/screen_area/index.html
 * @since      File available since Release 1.0.0RC1
 */

// {{{ GLOBALS

$GLOBALS['NET_USERAGENT_MOBILE_DoCoMo_ScreenInfo_Instance'] = null;

// }}}
// {{{ Net_UserAgent_Mobile_DoCoMo_ScreenInfo

/**
 * The screen information class for DoCoMo.
 *
 * @category   Networking
 * @package    Net_UserAgent_Mobile
 * @author     KUBO Atsuhiro <kubo@iteman.jp>
 * @copyright  2008-2009 KUBO Atsuhiro <kubo@iteman.jp>
 * @license    http://www.opensource.org/licenses/bsd-license.php  New BSD License
 * @version    Release: 1.0.0
 * @link       http://www.nttdocomo.co.jp/service/imode/make/content/spec/screen_area/index.html
 * @since      Class available since Release 1.0.0RC1
 */
class Net_UserAgent_Mobile_DoCoMo_ScreenInfo
{

    // {{{ properties

    /**#@+
     * @access public
     */

    /**#@-*/

    /**#@+
     * @access private
     */

    var $_data = array(

                       // i-mode compliant HTML 1.0
                       'D501I' => array(
                                        'width'  => 96,
                                        'height' => 72,
                                        'depth'  => 2,
                                        'color'  => 0
                                        ),
                       'F501I' => array(
                                        'width'  => 112,
                                        'height' => 84,
                                        'depth'  => 2,
                                        'color'  => 0
                                        ),
                       'N501I' => array(
                                        'width'  => 118,
                                        'height' => 128,
                                        'depth'  => 2,
                                        'color'  => 0
                                        ),
                       'P501I' => array(
                                        'width'  => 96,
                                        'height' => 120,
                                        'depth'  => 2,
                                        'color'  => 0
                                        ),

                       // i-mode compliant HTML 2.0
                       'D502I' => array(
                                        'width'  => 96,
                                        'height' => 90,
                                        'depth'  => 256,
                                        'color'  => 1
                                        ),
                       'F502I' => array(
                                        'width'  => 96,
                                        'height' => 91,
                                        'depth'  => 256,
                                        'color'  => 1
                                        ),
                       'N502I' => array(
                                        'width'  => 118,
                                        'height' => 128,
                                        'depth'  => 4,
                                        'color'  => 0
                                        ),
                       'P502I' => array(
                                        'width'  => 96,
                                        'height' => 117,
                                        'depth'  => 4,
                                        'color'  => 0
                                        ),
                       'NM502I' => array(
                                         'width'  => 111,
                                         'height' => 106,
                                         'depth'  => 2,
                                         'color'  => 0
                                         ),
                       'SO502I' => array(
                                         'width'  => 120,
                                         'height' => 120,
                                         'depth'  => 4,
                                         'color'  => 0
                                         ),
                       'F502IT' => array(
                                         'width'  => 96,
                                         'height' => 91,
                                         'depth'  => 256,
                                         'color'  => 1
                                         ),
                       'N502IT' => array(
                                         'width'  => 118,
                                         'height' => 128,
                                         'depth'  => 256,
                                         'color'  => 1
                                         ),
                       'SO502IWM' => array(
                                           'width'  => 120,
                                           'height' => 113,
                                           'depth'  => 256,
                                           'color'  => 1
                                           ),
                       'SH821I' => array(
                                         'width'  => 96,
                                         'height' => 78,
                                         'depth'  => 256,
                                         'color'  => 1
                                         ),
                       'N821I' => array(
                                        'width'  => 118,
                                        'height' => 128,
                                        'depth'  => 4,
                                        'color'  => 0
                                        ),
                       'P821I' => array(
                                        'width'  => 118,
                                        'height' => 128,
                                        'depth'  => 4,
                                        'color'  => 0
                                        ),
                       'D209I' => array(
                                        'width'  => 96,
                                        'height' => 90,
                                        'depth'  => 256,
                                        'color'  => 1
                                        ),
                       'ER209I' => array(
                                         'width'  => 120,
                                         'height' => 72,
                                         'depth'  => 2,
                                         'color'  => 0
                                         ),
                       'F209I' => array(
                                        'width'  => 96,
                                        'height' => 91,
                                        'depth'  => 256,
                                        'color'  => 1
                                        ),
                       'KO209I' => array(
                                         'width'  => 96,
                                         'height' => 96,
                                         'depth'  => 256,
                                         'color'  => 1
                                         ),
                       'N209I' => array(
                                        'width'  => 108,
                                        'height' => 82,
                                        'depth'  => 4,
                                        'color'  => 0
                                        ),
                       'P209I' => array(
                                        'width'  => 96,
                                        'height' => 87,
                                        'depth'  => 4,
                                        'color'  => 0
                                        ),
                       'P209IS' => array(
                                         'width'  => 96,
                                         'height' => 87,
                                         'depth'  => 256,
                                         'color'  => 1
                                         ),
                       'R209I' => array(
                                        'width'  => 96,
                                        'height' => 72,
                                        'depth'  => 4,
                                        'color'  => 0
                                        ),
                       'P651PS' => array(
                                         'width'  => 96,
                                         'height' => 87,
                                         'depth'  => 4,
                                         'color'  => 0
                                         ),
                       'R691I' => array(
                                        'width'  => 96,
                                        'height' => 72,
                                        'depth'  => 4,
                                        'color'  => 0
                                        ),
                       'F671I' => array(
                                        'width'  => 120,
                                        'height' => 126,
                                        'depth'  => 256,
                                        'color'  => 1
                                        ),
                       'F210I' => array(
                                        'width'  => 96,
                                        'height' => 113,
                                        'depth'  => 256,
                                        'color'  => 1
                                        ),
                       'N210I' => array(
                                        'width'  => 118,
                                        'height' => 113,
                                        'depth'  => 256,
                                        'color'  => 1
                                        ),
                       'P210I' => array(
                                        'width'  => 96,
                                        'height' => 91,
                                        'depth'  => 256,
                                        'color'  => 1
                                        ),
                       'KO210I' => array(
                                         'width'  => 96,
                                         'height' => 96,
                                         'depth'  => 256,
                                         'color'  => 1
                                         ),

                       // i-mode compliant HTML 3.0
                       'F503I' => array(
                                        'width'  => 120,
                                        'height' => 130,
                                        'depth'  => 256,
                                        'color'  => 1
                                        ),
                       'F503IS' => array(
                                         'width'  => 120,
                                         'height' => 130,
                                         'depth'  => 4096,
                                         'color'  => 1
                                         ),
                       'P503I' => array(
                                        'width'  => 120,
                                        'height' => 130,
                                        'depth'  => 256,
                                        'color'  => 1
                                        ),
                       'P503IS' => array(
                                         'width'  => 120,
                                         'height' => 130,
                                         'depth'  => 256,
                                         'color'  => 1
                                         ),
                       'N503I' => array(
                                        'width'  => 118,
                                        'height' => 128,
                                        'depth'  => 4096,
                                        'color'  => 1
                                        ),
                       'N503IS' => array(
                                         'width'  => 118,
                                         'height' => 128,
                                         'depth'  => 4096,
                                         'color'  => 1
                                         ),
                       'SO503I' => array(
                                         'width'  => 120,
                                         'height' => 113,
                                         'depth'  => 65536,
                                         'color'  => 1
                                         ),
                       'SO503IS' => array(
                                          'width'  => 120,
                                          'height' => 113,
                                          'depth'  => 65536,
                                          'color'  => 1
                                          ),
                       'D503I' => array(
                                        'width'  => 132,
                                        'height' => 126,
                                        'depth'  => 4096,
                                        'color'  => 1
                                        ),
                       'D503IS' => array(
                                         'width'  => 132,
                                         'height' => 126,
                                         'depth'  => 4096,
                                         'color'  => 1
                                         ),
                       'D210I' => array(
                                        'width'  => 96,
                                        'height' => 91,
                                        'depth'  => 256,
                                        'color'  => 1
                                        ),
                       'SO210I' => array(
                                         'width'  => 120,
                                         'height' => 113,
                                         'depth'  => 256,
                                         'color'  => 1
                                         ),
                       'F211I' => array(
                                        'width'  => 96,
                                        'height' => 113,
                                        'depth'  => 4096,
                                        'color'  => 1
                                        ),
                       'D211I' => array(
                                        'width'  => 100,
                                        'height' => 91,
                                        'depth'  => 4096,
                                        'color'  => 1
                                        ),
                       'N211I' => array(
                                        'width'  => 118,
                                        'height' => 128,
                                        'depth'  => 4096,
                                        'color'  => 1
                                        ),
                       'N211IS' => array(
                                         'width'  => 118,
                                         'height' => 128,
                                         'depth'  => 4096,
                                         'color'  => 1
                                         ),
                       'P211I' => array(
                                        'width'  => 120,
                                        'height' => 130,
                                        'depth'  => 65536,
                                        'color'  => 1
                                        ),
                       'P211IS' => array(
                                         'width'  => 120,
                                         'height' => 130,
                                         'depth'  => 65536,
                                         'color'  => 1
                                         ),
                       'SO211I' => array(
                                         'width'  => 120,
                                         'height' => 112,
                                         'depth'  => 4096,
                                         'color'  => 1
                                         ),
                       'R211I' => array(
                                        'width'  => 96,
                                        'height' => 98,
                                        'depth'  => 4096,
                                        'color'  => 1
                                        ),
                       'SH251I' => array(
                                         'width'  => 120,
                                         'height' => 130,
                                         'depth'  => 65536,
                                         'color'  => 1
                                         ),
                       'SH251IS' => array(
                                          'width'  => 176,
                                          'height' => 187,
                                          'depth'  => 65536,
                                          'color'  => 1
                                          ),
                       'R692I' => array(
                                        'width'  => 96,
                                        'height' => 98,
                                        'depth'  => 4096,
                                        'color'  => 1
                                        ),

                       // i-mode compliant HTML 3.0
                       // (FOMA 2001/2002/2101V)
                       'N2001' => array(
                                        'width'  => 118,
                                        'height' => 128,
                                        'depth'  => 4096,
                                        'color'  => 1
                                        ),
                       'N2002' => array(
                                        'width'  => 118,
                                        'height' => 128,
                                        'depth'  => 65536,
                                        'color'  => 1
                                        ),
                       'P2002' => array(
                                        'width'  => 118,
                                        'height' => 128,
                                        'depth'  => 65536,
                                        'color'  => 1
                                        ),
                       'D2101V' => array(
                                         'width'  => 120,
                                         'height' => 130,
                                         'depth'  => 262144,
                                         'color'  => 1
                                         ),
                       'P2101V' => array(
                                         'width'  => 163,
                                         'height' => 182,
                                         'depth'  => 262144,
                                         'color'  => 1
                                         ),
                       'SH2101V' => array(
                                          'width'  => 800,
                                          'height' => 600,
                                          'depth'  => 65536,
                                          'color'  => 1
                                          ),
                       'T2101V' => array(
                                         'width'  => 176,
                                         'height' => 144,
                                         'depth'  => 262144,
                                         'color'  => 1
                                         ),

                       // i-mode compliant HTML 4.0
                       'D504I' => array(
                                        'width'  => 132,
                                        'height' => 144,
                                        'depth'  => 262144,
                                        'color'  => 1
                                        ),
                       'F504I' => array(
                                        'width'  => 132,
                                        'height' => 136,
                                        'depth'  => 65536,
                                        'color'  => 1
                                        ),
                       'F504IS' => array(
                                         'width'  => 132,
                                         'height' => 136,
                                         'depth'  => 65536,
                                         'color'  => 1
                                         ),
                       'N504I' => array(
                                        'width'  => 160,
                                        'height' => 180,
                                        'depth'  => 65536,
                                        'color'  => 1
                                        ),
                       'N504IS' => array(
                                         'width'  => 160,
                                         'height' => 180,
                                         'depth'  => 65536,
                                         'color'  => 1
                                         ),
                       'SO504I' => array(
                                         'width'  => 120,
                                         'height' => 112,
                                         'depth'  => 65536,
                                         'color'  => 1
                                         ),
                       'P504I' => array(
                                        'width'  => 132,
                                        'height' => 144,
                                        'depth'  => 65536,
                                        'color'  => 1
                                        ),
                       'P504IS' => array(
                                         'width'  => 132,
                                         'height' => 144,
                                         'depth'  => 65536,
                                         'color'  => 1
                                         ),
                       'D251I' => array(
                                        'width'  => 132,
                                        'height' => 144,
                                        'depth'  => 262144,
                                        'color'  => 1
                                        ),
                       'D251IS' => array(
                                         'width'  => 132,
                                         'height' => 144,
                                         'depth'  => 262144,
                                         'color'  => 1
                                         ),
                       'F251I' => array(
                                        'width'  => 132,
                                        'height' => 140,
                                        'depth'  => 65536,
                                        'color'  => 1
                                        ),
                       'N251I' => array(
                                        'width'  => 132,
                                        'height' => 140,
                                        'depth'  => 65536,
                                        'color'  => 1
                                        ),
                       'N251IS' => array(
                                         'width'  => 132,
                                         'height' => 140,
                                         'depth'  => 65536,
                                         'color'  => 1
                                         ),
                       'P251IS' => array(
                                         'width'  => 132,
                                         'height' => 144,
                                         'depth'  => 65536,
                                         'color'  => 1
                                         ),
                       'F671IS' => array(
                                         'width'  => 160,
                                         'height' => 120,
                                         'depth'  => 65536,
                                         'color'  => 1
                                         ),
                       'F212I' => array(
                                        'width'  => 132,
                                        'height' => 136,
                                        'depth'  => 65536,
                                        'color'  => 1
                                        ),
                       'SO212I' => array(
                                         'width'  => 120,
                                         'height' => 112,
                                         'depth'  => 65536,
                                         'color'  => 1
                                         ),
                       'F661I' => array(
                                        'width'  => 132,
                                        'height' => 136,
                                        'depth'  => 65536,
                                        'color'  => 1
                                        ),
                       'F672I' => array(
                                        'width'  => 160,
                                        'height' => 120,
                                        'depth'  => 65536,
                                        'color'  => 1
                                        ),
                       'SO213I' => array(
                                         'width'  => 120,
                                         'height' => 112,
                                         'depth'  => 65536,
                                         'color'  => 1
                                         ),
                       'SO213IS' => array(
                                          'width'  => 120,
                                          'height' => 112,
                                          'depth'  => 65536,
                                          'color'  => 1
                                          ),
                       'SO213IWR' => array(
                                           'width'  => 120,
                                           'height' => 112,
                                           'depth'  => 65536,
                                           'color'  => 1
                                           ),

                       // i-mode compliant HTML 4.0
                       // (FOMA 2051/2102V/2701 etc.)
                       'F2051' => array(
                                        'width'  => 176,
                                        'height' => 182,
                                        'depth'  => 65536,
                                        'color'  => 1
                                        ),
                       'N2051' => array(
                                        'width'  => 176,
                                        'height' => 198,
                                        'depth'  => 65536,
                                        'color'  => 1
                                        ),
                       'P2102V' => array(
                                         'width'  => 176,
                                         'height' => 198,
                                         'depth'  => 262144,
                                         'color'  => 1
                                         ),
                       'P2102V' => array(
                                         'width'  => 176,
                                         'height' => 198,
                                         'depth'  => 262144,
                                         'color'  => 1
                                         ),
                       'F2102V' => array(
                                         'width'  => 176,
                                         'height' => 182,
                                         'depth'  => 65536,
                                         'color'  => 1
                                         ),
                       'N2102V' => array(
                                         'width'  => 176,
                                         'height' => 198,
                                         'depth'  => 65536,
                                         'color'  => 1
                                         ),
                       'N2701' => array(
                                        'width'  => 176,
                                        'height' => 198,
                                        'depth'  => 65536,
                                        'color'  => 1
                                        ),
                       'NM850IG' => array(
                                          'width'  => 176,
                                          'height' => 144,
                                          'depth'  => 65536,
                                          'color'  => 1
                                          ),
                       'NM705I' => array(
                                         'width'  => 231,
                                         'height' => 235,
                                         'depth'  => 16777216,
                                         'color'  => 1
                                         ),
                       'NM706I' => array(
                                         'width'  => 231,
                                         'height' => 235,
                                         'depth'  => 16777216,
                                         'color'  => 1
                                         ),

                       // i-mode compliant HTML 5.0 (505i etc.)
                       'D505I' => array(
                                        'width'  => 240,
                                        'height' => 270,
                                        'depth'  => 262144,
                                        'color'  => 1
                                        ),
                       'SO505I' => array(
                                         'width'  => 256,
                                         'height' => 240,
                                         'depth'  => 262144,
                                         'color'  => 1
                                         ),
                       'SH505I' => array(
                                         'width'  => 240,
                                         'height' => 252,
                                         'depth'  => 262144,
                                         'color'  => 1
                                         ),
                       'N505I' => array(
                                        'width'  => 240,
                                        'height' => 270,
                                        'depth'  => 262144,
                                        'color'  => 1
                                        ),
                       'F505I' => array(
                                        'width'  => 240,
                                        'height' => 268,
                                        'depth'  => 262144,
                                        'color'  => 1
                                        ),
                       'P505I' => array(
                                        'width'  => 240,
                                        'height' => 266,
                                        'depth'  => 65536,
                                        'color'  => 1
                                        ),
                       'D505IS' => array(
                                         'width'  => 240,
                                         'height' => 270,
                                         'depth'  => 262144,
                                         'color'  => 1
                                         ),
                       'P505IS' => array(
                                         'width'  => 240,
                                         'height' => 266,
                                         'depth'  => 65536,
                                         'color'  => 1
                                         ),
                       'N505IS' => array(
                                         'width'  => 240,
                                         'height' => 270,
                                         'depth'  => 262144,
                                         'color'  => 1
                                         ),
                       'SO505IS' => array(
                                          'width'  => 240,
                                          'height' => 256,
                                          'depth'  => 262144,
                                          'color'  => 1
                                          ),
                       'SH505IS' => array(
                                          'width'  => 240,
                                          'height' => 252,
                                          'depth'  => 262144,
                                          'color'  => 1
                                          ),
                       'F505IGPS' => array(
                                           'width'  => 240,
                                           'height' => 268,
                                           'depth'  => 262144,
                                           'color'  => 1
                                           ),
                       'D252I' => array(
                                        'width'  => 176,
                                        'height' => 198,
                                        'depth'  => 262144,
                                        'color'  => 1
                                        ),
                       'SH252I' => array(
                                         'width'  => 240,
                                         'height' => 252,
                                         'depth'  => 262144,
                                         'color'  => 1
                                         ),
                       'P252I' => array(
                                        'width'  => 132,
                                        'height' => 144,
                                        'depth'  => 65536,
                                        'color'  => 1
                                        ),
                       'N252I' => array(
                                        'width'  => 132,
                                        'height' => 140,
                                        'depth'  => 65536,
                                        'color'  => 1
                                        ),
                       'P252IS' => array(
                                         'width'  => 132,
                                         'height' => 144,
                                         'depth'  => 65536,
                                         'color'  => 1
                                         ),
                       'D506I' => array(
                                        'width'  => 240,
                                        'height' => 270,
                                        'depth'  => 262144,
                                        'color'  => 1
                                        ),
                       'F506I' => array(
                                        'width'  => 240,
                                        'height' => 268,
                                        'depth'  => 262144,
                                        'color'  => 1
                                        ),
                       'N506I' => array(
                                        'width'  => 240,
                                        'height' => 295,
                                        'depth'  => 262144,
                                        'color'  => 1
                                        ),
                       'P506IC' => array(
                                         'width'  => 240,
                                         'height' => 266,
                                         'depth'  => 65536,
                                         'color'  => 1
                                         ),
                       'SH506IC' => array(
                                          'width'  => 240,
                                          'height' => 252,
                                          'depth'  => 262144,
                                          'color'  => 1
                                          ),
                       'SO506IC' => array(
                                          'width'  => 240,
                                          'height' => 256,
                                          'depth'  => 262144,
                                          'color'  => 1
                                          ),
                       'N506IS' => array(
                                         'width'  => 240,
                                         'height' => 295,
                                         'depth'  => 262144,
                                         'color'  => 1
                                         ),
                       'SO506I' => array(
                                         'width'  => 240,
                                         'height' => 256,
                                         'depth'  => 262144,
                                         'color'  => 1
                                         ),
                       'SO506IS' => array(
                                          'width'  => 240,
                                          'height' => 256,
                                          'depth'  => 262144,
                                          'color'  => 1
                                          ),
                       'N506IS2' => array(
                                          'width'  => 240,
                                          'height' => 295,
                                          'depth'  => 262144,
                                          'color'  => 1
                                          ),
                       'P506ICII' => array(
                                           'width'  => 240,
                                           'height' => 266,
                                           'depth'  => 65536,
                                           'color'  => 1
                                           ),
                       'D253I' => array(
                                        'width'  => 176,
                                        'height' => 198,
                                        'depth'  => 262144,
                                        'color'  => 1
                                        ),
                       'N253I' => array(
                                        'width'  => 160,
                                        'height' => 180,
                                        'depth'  => 65536,
                                        'color'  => 1
                                        ),
                       'P253I' => array(
                                        'width'  => 132,
                                        'height' => 144,
                                        'depth'  => 65536,
                                        'color'  => 1
                                        ),
                       'D253IWM' => array(
                                          'width'  => 220,
                                          'height' => 144,
                                          'depth'  => 262144,
                                          'color'  => 1
                                          ),
                       'P253IS' => array(
                                         'width'  => 132,
                                         'height' => 144,
                                         'depth'  => 65536,
                                         'color'  => 1
                                         ),
                       'P213I' => array(
                                        'width'  => 132,
                                        'height' => 144,
                                        'depth'  => 65536,
                                        'color'  => 1
                                        ),

                       // i-mode compliant HTML 5.0
                       // (FOMA 900i etc.)
                       'F900I' => array(
                                        'width'  => 230,
                                        'height' => 240,
                                        'depth'  => 262144,
                                        'color'  => 1
                                        ),
                       'N900I' => array(
                                        'width'  => 240,
                                        'height' => 269,
                                        'depth'  => 65536,
                                        'color'  => 1
                                        ),
                       'P900I' => array(
                                        'width'  => 240,
                                        'height' => 266,
                                        'depth'  => 65536,
                                        'color'  => 1
                                        ),
                       'SH900I' => array(
                                         'width'  => 240,
                                         'height' => 252,
                                         'depth'  => 262144,
                                         'color'  => 1
                                         ),
                       'F900IT' => array(
                                         'width'  => 230,
                                         'height' => 240,
                                         'depth'  => 262144,
                                         'color'  => 1
                                         ),
                       'P900IV' => array(
                                         'width'  => 240,
                                         'height' => 266,
                                         'depth'  => 262144,
                                         'color'  => 1
                                         ),
                       'N900IS' => array(
                                         'width'  => 240,
                                         'height' => 269,
                                         'depth'  => 65536,
                                         'color'  => 1
                                         ),
                       'D900I' => array(
                                        'width'  => 240,
                                        'height' => 270,
                                        'depth'  => 262144,
                                        'color'  => 1
                                        ),
                       'F900IC' => array(
                                         'width'  => 230,
                                         'height' => 240,
                                         'depth'  => 262144,
                                         'color'  => 1
                                         ),
                       'F880IES' => array(
                                          'width'  => 240,
                                          'height' => 256,
                                          'depth'  => 65536,
                                          'color'  => 1
                                          ),
                       'N900IL' => array(
                                         'width'  => 240,
                                         'height' => 269,
                                         'depth'  => 65536,
                                         'color'  => 1
                                         ),
                       'N900IG' => array(
                                         'width'  => 240,
                                         'height' => 269,
                                         'depth'  => 65536,
                                         'color'  => 1
                                         ),
                       'SH901IC' => array(
                                          'width'  => 240,
                                          'height' => 252,
                                          'depth'  => 262144,
                                          'color'  => 1
                                          ),
                       'F901IC' => array(
                                         'width'  => 230,
                                         'height' => 240,
                                         'depth'  => 262144,
                                         'color'  => 1
                                         ),
                       'N901IC' => array(
                                         'width'  => 240,
                                         'height' => 270,
                                         'depth'  => 65536,
                                         'color'  => 1
                                         ),
                       'D901I' => array(
                                        'width'  => 230,
                                        'height' => 240,
                                        'depth'  => 262144,
                                        'color'  => 1
                                        ),
                       'P901I' => array(
                                        'width'  => 240,
                                        'height' => 270,
                                        'depth'  => 65536,
                                        'color'  => 1
                                        ),
                       'F700I' => array(
                                        'width'  => 230,
                                        'height' => 240,
                                        'depth'  => 262144,
                                        'color'  => 1
                                        ),
                       'SH700I' => array(
                                         'width'  => 240,
                                         'height' => 252,
                                         'depth'  => 262144,
                                         'color'  => 1
                                         ),
                       'N700I' => array(
                                        'width'  => 240,
                                        'height' => 270,
                                        'depth'  => 65536,
                                        'color'  => 1
                                        ),
                       'P700I' => array(
                                        'width'  => 240,
                                        'height' => 270,
                                        'depth'  => 65536,
                                        'color'  => 1
                                        ),
                       'F700IS' => array(
                                         'width'  => 230,
                                         'height' => 240,
                                         'depth'  => 262144,
                                         'color'  => 1
                                         ),
                       'SH700IS' => array(
                                          'width'  => 240,
                                          'height' => 252,
                                          'depth'  => 262144,
                                          'color'  => 1
                                          ),
                       'SA700IS' => array(
                                          'width'  => 240,
                                          'height' => 252,
                                          'depth'  => 65536,
                                          'color'  => 1
                                          ),

                       'SH901IS' => array(
                                          'width'  => 240,
                                          'height' => 252,
                                          'depth'  => 262144,
                                          'color'  => 1
                                          ),
                       'F901IS' => array(
                                         'width'  => 230,
                                         'height' => 240,
                                         'depth'  => 262144,
                                         'color'  => 1
                                         ),
                       'D901IS' => array(
                                         'width'  => 230,
                                         'height' => 240,
                                         'depth'  => 262144,
                                         'color'  => 1
                                         ),
                       'P901IS' => array(
                                         'width'  => 240,
                                         'height' => 270,
                                         'depth'  => 65536,
                                         'color'  => 1
                                         ),
                       'N901IS' => array(
                                         'width'  => 240,
                                         'height' => 270,
                                         'depth'  => 65536,
                                         'color'  => 1
                                         ),
                       'P901ITV' => array(
                                          'width'  => 240,
                                          'height' => 270,
                                          'depth'  => 65536,
                                          'color'  => 1
                                          ),
                       'SH851I' => array(
                                         'width'  => 240,
                                         'height' => 252,
                                         'depth'  => 262144,
                                         'color'  => 1
                                         ),
                       'P851I' => array(
                                        'width'  => 240,
                                        'height' => 270,
                                        'depth'  => 65536,
                                        'color'  => 1
                                        ),
                       'F881IES' => array(
                                          'width'  => 240,
                                          'height' => 256,
                                          'depth'  => 65536,
                                          'color'  => 1
                                          ),
                       'D701I' => array(
                                        'width'  => 230,
                                        'height' => 240,
                                        'depth'  => 65536,
                                        'color'  => 1
                                        ),
                       'N701I' => array(
                                        'width'  => 240,
                                        'height' => 270,
                                        'depth'  => 65536,
                                        'color'  => 1
                                        ),
                       'P701ID' => array(
                                         'width'  => 240,
                                         'height' => 270,
                                         'depth'  => 65536,
                                         'color'  => 1
                                         ),
                       'D701IWM' => array(
                                          'width'  => 230,
                                          'height' => 240,
                                          'depth'  => 65536,
                                          'color'  => 1
                                          ),
                       'N701IECO' => array(
                                           'width'  => 240,
                                           'height' => 270,
                                           'depth'  => 65536,
                                           'color'  => 1
                                           ),
                       'SA800I' => array(
                                         'width'  => 240,
                                         'height' => 252,
                                         'depth'  => 65536,
                                         'color'  => 1
                                         ),
                       'L600I' => array(
                                        'width'  => 170,
                                        'height' => 189,
                                        'depth'  => 65536,
                                        'color'  => 1
                                        ),
                       'N600I' => array(
                                        'width'  => 176,
                                        'height' => 180,
                                        'depth'  => 65536,
                                        'color'  => 1
                                        ),
                       'L601I' => array(
                                        'width'  => 170,
                                        'height' => 189,
                                        'depth'  => 65536,
                                        'color'  => 1
                                        ),
                       'M702IS' => array(
                                         'width'  => 240,
                                         'height' => 267,
                                         'depth'  => 262144,
                                         'color'  => 1
                                         ),
                       'M702IG' => array(
                                         'width'  => 240,
                                         'height' => 267,
                                         'depth'  => 262144,
                                         'color'  => 1
                                         ),
                       'L602I' => array(
                                        'width'  => 170,
                                        'height' => 189,
                                        'depth'  => 65536,
                                        'color'  => 1
                                        ),

                       // i-mode compliant HTML 6.0
                       // (FOMA 902i etc.)
                       'F902I' => array(
                                        'width'  => 230,
                                        'height' => 240,
                                        'depth'  => 262144,
                                        'color'  => 1
                                        ),
                       'D902I' => array(
                                        'width'  => 230,
                                        'height' => 320,
                                        'depth'  => 262144,
                                        'color'  => 1
                                        ),
                       'N902I' => array(
                                        'width'  => 240,
                                        'height' => 270,
                                        'depth'  => 262144,
                                        'color'  => 1
                                        ),
                       'P902I' => array(
                                        'width'  => 240,
                                        'height' => 270,
                                        'depth'  => 262144,
                                        'color'  => 1
                                        ),
                       'SH902I' => array(
                                         'width'  => 240,
                                         'height' => 240,
                                         'depth'  => 262144,
                                         'color'  => 1
                                         ),
                       'SO902I' => array(
                                         'width'  => 240,
                                         'height' => 256,
                                         'depth'  => 262144,
                                         'color'  => 1
                                         ),
                       'SH902IS' => array(
                                          'width'  => 240,
                                          'height' => 240,
                                          'depth'  => 262144,
                                          'color'  => 1
                                          ),
                       'P902IS' => array(
                                         'width'  => 240,
                                         'height' => 270,
                                         'depth'  => 262144,
                                         'color'  => 1
                                         ),
                       'N902IS' => array(
                                         'width'  => 240,
                                         'height' => 270,
                                         'depth'  => 262144,
                                         'color'  => 1
                                         ),
                       'D902IS' => array(
                                         'width'  => 230,
                                         'height' => 320,
                                         'depth'  => 262144,
                                         'color'  => 1
                                         ),
                       'F902IS' => array(
                                         'width'  => 230,
                                         'height' => 240,
                                         'depth'  => 262144,
                                         'color'  => 1
                                         ),
                       'SO902IWP+' => array(
                                            'width'  => 240,
                                            'height' => 256,
                                            'depth'  => 262144,
                                            'color'  => 1
                                            ),
                       'SH902ISL' => array(
                                           'width'  => 240,
                                           'height' => 240,
                                           'depth'  => 262144,
                                           'color'  => 1
                                           ),
                       'N902IX' => array(
                                         'width'  => 240,
                                         'height' => 270,
                                         'depth'  => 262144,
                                         'color'  => 1
                                         ),
                       'N902IL' => array(
                                         'width'  => 240,
                                         'height' => 270,
                                         'depth'  => 262144,
                                         'color'  => 1
                                         ),
                       'P702I' => array(
                                        'width'  => 240,
                                        'height' => 270,
                                        'depth'  => 262144,
                                        'color'  => 1
                                        ),
                       'N702ID' => array(
                                         'width'  => 240,
                                         'height' => 270,
                                         'depth'  => 65536,
                                         'color'  => 1
                                         ),
                       'F702ID' => array(
                                         'width'  => 230,
                                         'height' => 240,
                                         'depth'  => 262144,
                                         'color'  => 1
                                         ),
                       'SH702ID' => array(
                                          'width'  => 240,
                                          'height' => 240,
                                          'depth'  => 262144,
                                          'color'  => 1
                                          ),
                       'D702I' => array(
                                        'width'  => 230,
                                        'height' => 240,
                                        'depth'  => 262144,
                                        'color'  => 1
                                        ),
                       'SO702I' => array(
                                         'width'  => 240,
                                         'height' => 256,
                                         'depth'  => 262144,
                                         'color'  => 1
                                         ),
                       'D702IBCL' => array(
                                           'width'  => 230,
                                           'height' => 240,
                                           'depth'  => 262144,
                                           'color'  => 1
                                           ),
                       'SA702I' => array(
                                         'width'  => 240,
                                         'height' => 252,
                                         'depth'  => 65536,
                                         'color'  => 1
                                         ),
                       'SH702IS' => array(
                                          'width'  => 240,
                                          'height' => 240,
                                          'depth'  => 262144,
                                          'color'  => 1
                                          ),
                       'N702IS' => array(
                                         'width'  => 240,
                                         'height' => 270,
                                         'depth'  => 65536,
                                         'color'  => 1
                                         ),
                       'P702ID' => array(
                                         'width'  => 240,
                                         'height' => 270,
                                         'depth'  => 262144,
                                         'color'  => 1
                                         ),
                       'D702IF' => array(
                                         'width'  => 230,
                                         'height' => 240,
                                         'depth'  => 262144,
                                         'color'  => 1
                                         ),
                       'D851IWM' => array(
                                          'width'  => 230,
                                          'height' => 320,
                                          'depth'  => 262144,
                                          'color'  => 1
                                          ),
                       'F882IES' => array(
                                          'width'  => 240,
                                          'height' => 256,
                                          'depth'  => 65536,
                                          'color'  => 1
                                          ),
                       'N601I' => array(
                                        'width'  => 240,
                                        'height' => 270,
                                        'depth'  => 65536,
                                        'color'  => 1
                                        ),
                       'D800IDS' => array(
                                          'width'  => 230,
                                          'height' => 240,
                                          'depth'  => 262144,
                                          'color'  => 1
                                          ),
                       'P703IMYU' => array(
                                           'width'  => 240,
                                           'height' => 270,
                                           'depth'  => 262144,
                                           'color'  => 1
                                           ),
                       'F883I' => array(
                                        'width'  => 240,
                                        'height' => 256,
                                        'depth'  => 65536,
                                        'color'  => 1
                                        ),
                       'F883IS' => array(
                                         'width'  => 240,
                                         'height' => 256,
                                         'depth'  => 65536,
                                         'color'  => 1
                                         ),
                       'P704IMYU' => array(
                                           'width'  => 240,
                                           'height' => 270,
                                           'depth'  => 262144,
                                           'color'  => 1
                                           ),
                       'L704I' => array(
                                        'width'  => 240,
                                        'height' => 280,
                                        'depth'  => 262144,
                                        'color'  => 1
                                        ),
                       'L705I' => array(
                                        'width'  => 240,
                                        'height' => 280,
                                        'depth'  => 262144,
                                        'color'  => 1
                                        ),
                       'L705IX' => array(
                                         'width'  => 240,
                                         'height' => 280,
                                         'depth'  => 262144,
                                         'color'  => 1
                                         ),
                       'L852I' => array(
                                        'width'  => 240,
                                        'height' => 298,
                                        'depth'  => 65536,
                                        'color'  => 1
                                        ),
                       'L706IE' => array(
                                         'width'  => 240,
                                         'height' => 280,
                                         'depth'  => 262144,
                                         'color'  => 1
                                         ),
                       'L01A' => array(
                                       'width'  => 240,
                                       'height' => 350,
                                       'depth'  => 262144,
                                       'color'  => 1
                                       ),
                       'L03A' => array(
                                       'width'  => 240,
                                       'height' => 280,
                                       'depth'  => 262144,
                                       'color'  => 1
                                       ),

                       // i-mode compliant HTML 7.0
                       // (FOMA 903i etc.)
                       'SH903I' => array(
                                         'width'  => 240,
                                         'height' => 320,
                                         'depth'  => 262144,
                                         'color'  => 1
                                         ),
                       'P903I' => array(
                                        'width'  => 240,
                                        'height' => 270,
                                        'depth'  => 262144,
                                        'color'  => 1
                                        ),
                       'N903I' => array(
                                        'width'  => 240,
                                        'height' => 270,
                                        'depth'  => 262144,
                                        'color'  => 1
                                        ),
                       'D903I' => array(
                                        'width'  => 230,
                                        'height' => 320,
                                        'depth'  => 262144,
                                        'color'  => 1
                                        ),
                       'F903I' => array(
                                        'width'  => 230,
                                        'height' => 240,
                                        'depth'  => 262144,
                                        'color'  => 1
                                        ),
                       'SO903I' => array(
                                         'width'  => 240,
                                         'height' => 368,
                                         'depth'  => 262144,
                                         'color'  => 1
                                         ),
                       'D903ITV' => array(
                                          'width'  => 230,
                                          'height' => 320,
                                          'depth'  => 262144,
                                          'color'  => 1
                                          ),
                       'F903IX' => array(
                                         'width'  => 230,
                                         'height' => 240,
                                         'depth'  => 262144,
                                         'color'  => 1
                                         ),
                       'P903ITV' => array(
                                          'width'  => 240,
                                          'height' => 350,
                                          'depth'  => 262144,
                                          'color'  => 1
                                          ),
                       'SH903ITV' => array(
                                           'width'  => 240,
                                           'height' => 320,
                                           'depth'  => 262144,
                                           'color'  => 1
                                           ),
                       'F903IBSC' => array(
                                           'width'  => 230,
                                           'height' => 240,
                                           'depth'  => 262144,
                                           'color'  => 1
                                           ),
                       'P903IX' => array(
                                         'width'  => 240,
                                         'height' => 270,
                                         'depth'  => 262144,
                                         'color'  => 1
                                         ),
                       'SO903ITV' => array(
                                           'width'  => 240,
                                           'height' => 368,
                                           'depth'  => 262144,
                                           'color'  => 1
                                           ),
                       'N703ID' => array(
                                         'width'  => 240,
                                         'height' => 270,
                                         'depth'  => 262144,
                                         'color'  => 1
                                         ),
                       'F703I' => array(
                                        'width'  => 230,
                                        'height' => 240,
                                        'depth'  => 262144,
                                        'color'  => 1
                                        ),
                       'P703I' => array(
                                        'width'  => 240,
                                        'height' => 270,
                                        'depth'  => 262144,
                                        'color'  => 1
                                        ),
                       'D703I' => array(
                                        'width'  => 230,
                                        'height' => 240,
                                        'depth'  => 262144,
                                        'color'  => 1
                                        ),
                       'SH703I' => array(
                                         'width'  => 240,
                                         'height' => 240,
                                         'depth'  => 262144,
                                         'color'  => 1
                                         ),
                       'SH703I' => array(
                                         'width'  => 240,
                                         'height' => 240,
                                         'depth'  => 262144,
                                         'color'  => 1
                                         ),
                       'N703IMYU' => array(
                                           'width'  => 240,
                                           'height' => 270,
                                           'depth'  => 262144,
                                           'color'  => 1
                                           ),
                       'SO703I' => array(
                                         'width'  => 240,
                                         'height' => 368,
                                         'depth'  => 262144,
                                         'color'  => 1
                                         ),
                       'P904I' => array(
                                        'width'  => 240,
                                        'height' => 350,
                                        'depth'  => 262144,
                                        'color'  => 1
                                        ),
                       'D904I' => array(
                                        'width'  => 240,
                                        'height' => 320,
                                        'depth'  => 262144,
                                        'color'  => 1
                                        ),
                       'F904I' => array(
                                        'width'  => 240,
                                        'height' => 352,
                                        'depth'  => 262144,
                                        'color'  => 1
                                        ),
                       'N904I' => array(
                                        'width'  => 240,
                                        'height' => 352,
                                        'depth'  => 262144,
                                        'color'  => 1
                                        ),
                       'SH904I' => array(
                                         'width'  => 240,
                                         'height' => 320,
                                         'depth'  => 262144,
                                         'color'  => 1
                                         ),
                       'P704I' => array(
                                        'width'  => 240,
                                        'height' => 270,
                                        'depth'  => 262144,
                                        'color'  => 1
                                        ),
                       'D704I' => array(
                                        'width'  => 230,
                                        'height' => 240,
                                        'depth'  => 262144,
                                        'color'  => 1
                                        ),
                       'SH704I' => array(
                                         'width'  => 240,
                                         'height' => 320,
                                         'depth'  => 262144,
                                         'color'  => 1
                                         ),
                       'N704IMYU' => array(
                                           'width'  => 240,
                                           'height' => 270,
                                           'depth'  => 262144,
                                           'color'  => 1
                                           ),
                       'F704I' => array(
                                        'width'  => 230,
                                        'height' => 240,
                                        'depth'  => 262144,
                                        'color'  => 1
                                        ),
                       'SO704I' => array(
                                         'width'  => 240,
                                         'height' => 368,
                                         'depth'  => 262144,
                                         'color'  => 1
                                         ),
                       'F883IES' => array(
                                          'width'  => 240,
                                          'height' => 256,
                                          'depth'  => 65536,
                                          'color'  => 1
                                          ),
                       'F883IESS' => array(
                                           'width'  => 240,
                                           'height' => 256,
                                           'depth'  => 65536,
                                           'color'  => 1
                                           ),
                       'F801I' => array(
                                        'width'  => 240,
                                        'height' => 352,
                                        'depth'  => 65536,
                                        'color'  => 1
                                        ),
                       'F705I' => array(
                                        'width'  => 240,
                                        'height' => 352,
                                        'depth'  => 262144,
                                        'color'  => 1
                                        ),
                       'D705I' => array(
                                        'width'  => 240,
                                        'height' => 320,
                                        'depth'  => 262144,
                                        'color'  => 1
                                        ),
                       'D705IMYU' => array(
                                           'width'  => 240,
                                           'height' => 240,
                                           'depth'  => 262144,
                                           'color'  => 1
                                           ),
                       'SH705I' => array(
                                         'width'  => 240,
                                         'height' => 320,
                                         'depth'  => 262144,
                                         'color'  => 1
                                         ),
                       'SH705I2' => array(
                                          'width'  => 240,
                                          'height' => 320,
                                          'depth'  => 262144,
                                          'color'  => 1
                                          ),
                       'SH706IE' => array(
                                          'width'  => 240,
                                          'height' => 320,
                                          'depth'  => 262144,
                                          'color'  => 1
                                          ),
                       'F05A' => array(
                                       'width'  => 240,
                                       'height' => 352,
                                       'depth'  => 65536,
                                       'color'  => 1
                                       ),
 
                       // i-mode compliant HTML 7.1
                       // (FOMA 905i etc.)
                       'SH905I' => array(
                                         'width'  => 240,
                                         'height' => 320,
                                         'depth'  => 16777216,
                                         'color'  => 1
                                         ),
                       'D905I' => array(
                                        'width'  => 240,
                                        'height' => 352,
                                        'depth'  => 262144,
                                        'color'  => 1
                                        ),
                       'N905I' => array(
                                        'width'  => 240,
                                        'height' => 320,
                                        'depth'  => 262144,
                                        'color'  => 1
                                        ),
                       'P905I' => array(
                                        'width'  => 240,
                                        'height' => 350,
                                        'depth'  => 262144,
                                        'color'  => 1
                                        ),
                       'F905I' => array(
                                        'width'  => 240,
                                        'height' => 352,
                                        'depth'  => 16777216,
                                        'color'  => 1
                                        ),
                       'SO905I' => array(
                                         'width'  => 240,
                                         'height' => 368,
                                         'depth'  => 16777216,
                                         'color'  => 1
                                         ),
                       'N905IMYU' => array(
                                           'width'  => 240,
                                           'height' => 320,
                                           'depth'  => 262144,
                                           'color'  => 1
                                           ),
                       'N905IBIZ' => array(
                                           'width'  => 240,
                                           'height' => 320,
                                           'depth'  => 262144,
                                           'color'  => 1
                                           ),
                       'SH905ITV' => array(
                                           'width'  => 240,
                                           'height' => 320,
                                           'depth'  => 16777216,
                                           'color'  => 1
                                           ),
                       'SO905ICS' => array(
                                           'width'  => 240,
                                           'height' => 368,
                                           'depth'  => 16777216,
                                           'color'  => 1
                                           ),
                       'F905IBIZ' => array(
                                           'width'  => 240,
                                           'height' => 352,
                                           'depth'  => 16777216,
                                           'color'  => 1
                                           ),
                       'P905ITV' => array(
                                          'width'  => 240,
                                          'height' => 350,
                                          'depth'  => 262144,
                                          'color'  => 1
                                          ),
                       'P705I' => array(
                                        'width'  => 240,
                                        'height' => 350,
                                        'depth'  => 262144,
                                        'color'  => 1
                                        ),
                       'N705I' => array(
                                        'width'  => 240,
                                        'height' => 320,
                                        'depth'  => 262144,
                                        'color'  => 1
                                        ),
                       'N705IMYU' => array(
                                           'width'  => 240,
                                           'height' => 320,
                                           'depth'  => 262144,
                                           'color'  => 1
                                           ),
                       'P705IMYU' => array(
                                           'width'  => 240,
                                           'height' => 350,
                                           'depth'  => 262144,
                                           'color'  => 1
                                           ),
                       'SO705I' => array(
                                         'width'  => 240,
                                         'height' => 320,
                                         'depth'  => 262144,
                                         'color'  => 1
                                         ),
                       'P705ICL' => array(
                                          'width'  => 240,
                                          'height' => 350,
                                          'depth'  => 262144,
                                          'color'  => 1
                                          ),
                       'F884I' => array(
                                        'width'  => 240,
                                        'height' => 364,
                                        'depth'  => 262144,
                                        'color'  => 1
                                        ),
                       'F884IES' => array(
                                          'width'  => 240,
                                          'height' => 282,
                                          'depth'  => 262144,
                                          'color'  => 1
                                          ),
                       'N906IL' => array(
                                         'width'  => 240,
                                         'height' => 320,
                                         'depth'  => 262144,
                                         'color'  => 1
                                         ),
                       'N706I' => array(
                                        'width'  => 240,
                                        'height' => 320,
                                        'depth'  => 262144,
                                        'color'  => 1
                                        ),
                       'SO706I' => array(
                                         'width'  => 240,
                                         'height' => 320,
                                         'depth'  => 262144,
                                         'color'  => 1
                                         ),
                       'P706IMYU' => array(
                                           'width'  => 240,
                                           'height' => 350,
                                           'depth'  => 262144,
                                           'color'  => 1
                                           ),
                       'N706IE' => array(
                                         'width'  => 240,
                                         'height' => 320,
                                         'depth'  => 262144,
                                         'color'  => 1
                                         ),
                       'N706I2' => array(
                                         'width'  => 240,
                                         'height' => 320,
                                         'depth'  => 262144,
                                         'color'  => 1
                                         ),
                       'N03A' => array(
                                       'width'  => 240,
                                       'height' => 320,
                                       'depth'  => 262144,
                                       'color'  => 1
                                       ),
                       'N05A' => array(
                                       'width'  => 240,
                                       'height' => 320,
                                       'depth'  => 262144,
                                       'color'  => 1
                                       ),
                       'F07A' => array(
                                       'width'  => 240,
                                       'height' => 256,
                                       'depth'  => 262144,
                                       'color'  => 1
                                       ),

                       // i-mode compliant HTML 7.2
                       // (FOMA 906i etc.)
                       'P906I' => array(
                                        'width'  => 240,
                                        'height' => 350,
                                        'depth'  => 262144,
                                        'color'  => 1
                                        ),
                       'SO906I' => array(
                                         'width'  => 240,
                                         'height' => 368,
                                         'depth'  => 16777216,
                                         'color'  => 1
                                         ),
                       'SH906I' => array(
                                         'width'  => 240,
                                         'height' => 320,
                                         'depth'  => 16777216,
                                         'color'  => 1
                                         ),
                       'N906IMYU' => array(
                                           'width'  => 240,
                                           'height' => 320,
                                           'depth'  => 262144,
                                           'color'  => 1
                                           ),
                       'F906I' => array(
                                        'width'  => 240,
                                        'height' => 352,
                                        'depth'  => 16777216,
                                        'color'  => 1
                                        ),
                       'N906I' => array(
                                        'width'  => 240,
                                        'height' => 320,
                                        'depth'  => 262144,
                                        'color'  => 1
                                        ),
                       'SH906ITV' => array(
                                           'width'  => 240,
                                           'height' => 320,
                                           'depth'  => 16777216,
                                           'color'  => 1
                                           ),
                       'F706I' => array(
                                        'width'  => 240,
                                        'height' => 352,
                                        'depth'  => 262144,
                                        'color'  => 1
                                        ),
                       'SH706I' => array(
                                         'width'  => 240,
                                         'height' => 320,
                                         'depth'  => 16777216,
                                         'color'  => 1
                                         ),
                       'P706IE' => array(
                                         'width'  => 240,
                                         'height' => 350,
                                         'depth'  => 262144,
                                         'color'  => 1
                                         ),
                       'SH706IW' => array(
                                          'width'  => 240,
                                          'height' => 320,
                                          'depth'  => 16777216,
                                          'color'  => 1
                                          ),
                       'F01A' => array(
                                       'width'  => 240,
                                       'height' => 352,
                                       'depth'  => 16777216,
                                       'color'  => 1
                                       ),
                       'F02A' => array(
                                       'width'  => 240,
                                       'height' => 352,
                                       'depth'  => 262144,
                                       'color'  => 1
                                       ),
                       'F03A' => array(
                                       'width'  => 240,
                                       'height' => 352,
                                       'depth'  => 16777216,
                                       'color'  => 1
                                       ),
                       'F04A' => array(
                                       'width'  => 240,
                                       'height' => 352,
                                       'depth'  => 262144,
                                       'color'  => 1
                                       ),
                       'F06A' => array(
                                       'width'  => 240,
                                       'height' => 352,
                                       'depth'  => 16777216,
                                       'color'  => 1
                                       ), 
                       'P01A' => array(
                                       'width'  => 240,
                                       'height' => 350,
                                       'depth'  => 262144,
                                       'color'  => 1
                                       ),
                       'P02A' => array(
                                       'width'  => 240,
                                       'height' => 350,
                                       'depth'  => 262144,
                                       'color'  => 1
                                       ),
                       'P03A' => array(
                                       'width'  => 240,
                                       'height' => 350,
                                       'depth'  => 262144,
                                       'color'  => 1
                                       ),
                       'P04A' => array(
                                       'width'  => 240,
                                       'height' => 350,
                                       'depth'  => 262144,
                                       'color'  => 1
                                       ),
                       'P05A' => array(
                                       'width'  => 240,
                                       'height' => 350,
                                       'depth'  => 262144,
                                       'color'  => 1
                                       ),
                       'P06A' => array(
                                       'width'  => 240,
                                       'height' => 350,
                                       'depth'  => 262144,
                                       'color'  => 1
                                       ),
                       'P10A' => array(
                                       'width'  => 240,
                                       'height' => 350,
                                       'depth'  => 262144,
                                       'color'  => 1
                                       ),
                       'SH01A' => array(
                                        'width'  => 240,
                                        'height' => 320,
                                        'depth'  => 16777216,
                                        'color'  => 1
                                        ),
                       'SH02A' => array(
                                        'width'  => 240,
                                        'height' => 320,
                                        'depth'  => 16777216,
                                        'color'  => 1
                                        ),
                       'SH03A' => array(
                                        'width'  => 240,
                                        'height' => 320,
                                        'depth'  => 16777216,
                                        'color'  => 1
                                        ),
                       'SH04A' => array(
                                        'width'  => 240,
                                        'height' => 320,
                                        'depth'  => 16777216,
                                        'color'  => 1
                                        ),
                       'N01A' => array(
                                       'width'  => 240,
                                       'height' => 320,
                                       'depth'  => 262144,
                                       'color'  => 1
                                       ),
                       'N02A' => array(
                                       'width'  => 240,
                                       'height' => 320,
                                       'depth'  => 262144,
                                       'color'  => 1
                                       ),
                       'N04A' => array(
                                       'width'  => 240,
                                       'height' => 320,
                                       'depth'  => 262144,
                                       'color'  => 1
                                       ),

                       // i-mode browser 2.0
                       'P07A3' => array(
                                        'width'  => 480,
                                        'height' => 662,
                                        'depth'  => 262144,
                                        'color'  => 1
                                        ),
                       'P08A3' => array(
                                        'width'  => 480,
                                        'height' => 662,
                                        'depth'  => 262144,
                                        'color'  => 1
                                        ),
                       'P09A3' => array(
                                        'width'  => 480,
                                        'height' => 662,
                                        'depth'  => 262144,
                                        'color'  => 1
                                        ),
                       'N06A3' => array(
                                        'width'  => 480,
                                        'height' => 640,
                                        'depth'  => 262144,
                                        'color'  => 1
                                        ),
                       'N08A3' => array(
                                        'width'  => 480,
                                        'height' => 640,
                                        'depth'  => 262144,
                                        'color'  => 1
                                        ),
                       'N09A3' => array(
                                        'width'  => 480,
                                        'height' => 640,
                                        'depth'  => 262144,
                                        'color'  => 1
                                        ),
                       'F08A3' => array(
                                        'width'  => 480,
                                        'height' => 648,
                                        'depth'  => 262144,
                                        'color'  => 1
                                        ),
                       'F09A3' => array(
                                        'width'  => 480,
                                        'height' => 648,
                                        'depth'  => 16777216,
                                        'color'  => 1
                                        ),
                       'SH05A3' => array(
                                         'width'  => 480,
                                         'height' => 592,
                                         'depth'  => 16777216,
                                         'color'  => 1
                                         ),
                       'SH06A3' => array(
                                         'width'  => 480,
                                         'height' => 592,
                                         'depth'  => 16777216,
                                         'color'  => 1
                                         ),
                       'SH07A3' => array(
                                         'width'  => 480,
                                         'height' => 592,
                                         'depth'  => 16777216,
                                         'color'  => 1
                                         )
                       );

    /**#@-*/

    /**#@+
     * @access public
     */

    // }}}
    // {{{ singleton()

    /**
     * Returns the Net_UserAgent_Mobile_DoCoMo_Screen instance if it exists. If it
     * not exists, a new instance of Net_UserAgent_Mobile_DoCoMo_Screen will be
     * created and returned.
     *
     * @return Net_UserAgent_Mobile_DoCoMo_ScreenInfo
     * @static
     */
    function &singleton()
    {
        if (@is_null($GLOBALS['NET_USERAGENT_MOBILE_DoCoMo_ScreenInfo_Instance'])) {
            $GLOBALS['NET_USERAGENT_MOBILE_DoCoMo_ScreenInfo_Instance'] = &new Net_UserAgent_Mobile_DoCoMo_ScreenInfo();
        }

        return $GLOBALS['NET_USERAGENT_MOBILE_DoCoMo_ScreenInfo_Instance'];
    }

    // }}}
    // {{{ get()

    /**
     * Gets the screen information of a given model.
     *
     * @param string $model
     * @return array
     */
    function get($model)
    {
        return $this->_data[ strtoupper($model) ];
    }

    /**#@-*/

    /**#@+
     * @access private
     */

    // }}}
    // {{{ constructor

    /**
     * Creates the screen information by a given XML file if DOCOMO_MAP environment
     * variable exists.
     */
    function Net_UserAgent_Mobile_DoCoMo_ScreenInfo()
    {
        if (!array_key_exists('DOCOMO_MAP', $_SERVER)) {
            return;
        }

        // using the specified XML data
        do {
            if (!function_exists('xml_parser_create')
                || !is_readable($_SERVER['DOCOMO_MAP'])
                ) {
                break;
            }

            $xml = file_get_contents($_SERVER['DOCOMO_MAP']);
            if ($xml === false) {
                break;
            }

            $parser = xml_parser_create();
            if ($parser === false) {
                break;
            }

            xml_parse_into_struct($parser, $xml, $values, $indexes);
            if (!xml_parser_free($parser)) {
                break;
            }

            if (array_key_exists('OPT', $indexes)) {
                unset($indexes['OPT']);
            }

            $data = array();
            foreach ($indexes as $modelName => $modelIndexes) {
                $data[$modelName] = array();
                foreach ($values[ $modelIndexes[0] ]['attributes']
                         as $attributeName => $attributeValue
                         ) {
                    $data[$modelName][ strtolower($attributeName) ] = $attributeValue;
                }
            }

            $this->_data = $data;
        } while (false);
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
