<!--{*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2010 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 *}-->
<script type="text/javascript" src="<!--{$TPL_DIR}-->jquery.jCal/jCal.js"></script>
<link rel="stylesheet" href="<!--{$TPL_DIR}-->jquery.jCal/jCal.css" type="text/css" media="screen" />
<!--{* TODO 休日表示などの処理は未実装 *}-->
<script type="text/javascript">//<![CDATA[
$(function() {
    $('#blockCalendar').jCal({
        day: new Date(),
        monthSelect: true,
        dow: ['日', '月', '火', '水', '木', '金', '土'],
        ml: ['1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12'],
        callback: changeCalSize(22)
    });
});
function changeCalSize (daySize) {
    var daySize = (parseInt(daySize) || 30);
    var monthSize = ( daySize + 2 ) * 7;
    var titleSize = monthSize - 35;
    var titleMsgSize = titleSize - 10;
    $('head:first').append(
      '<style>' +
        '.jCalMo .day,.jCalMo .invday,.jCalMo .pday,.jCalMo .aday,.jCalMo .selectedDay,.jCalMo .dow { width:' + daySize + 'px !important; height:' + daySize + 'px !important; }' +
        '.jCalMo .dow { height:auto !important }' +
        '.jCalMo, .jCalMo .jCal { width:' + monthSize + 'px !important; }' +
        '.jCalMo .month { width:' + titleSize + 'px !important; }' +
        '.jCalMo .month span.monthYear { width:' + titleMsgSize * 0.6  + 'px !important; }' +
        '.jCalMo .month span.monthName { width:' + titleMsgSize * 0.4  + 'px !important; }' +
      '</style>');
}
//]]>
</script>
<div id="blockCalendar" class="bloc_outer">
</div>
