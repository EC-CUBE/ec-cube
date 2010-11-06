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
var holidays = <!--{$arrHoliday}-->; // TODO
var regularHolidays = <!--{$arrRegularHoliday}-->; // TODO
$(function() {
    $('#blockCalendar').jCal({
        day: new Date(),
        monthSelect: true,
        dow: ['日', '月', '火', '水', '木', '金', '土'],
        ml: ['1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12'],
        callback: jCalCallback()
    });
    var today = new Date();
    var dToday = $('div[id*=d_' + (today.getMonth() + 1) + '_' + today.getDate() + '_' + today.getFullYear() + ']');
    dToday.addClass('tday');

    $('.jCalMo .day').each(function() {
        var aDays = $(this).attr('id').split('_');
        var hDate = new Date(aDays[3], aDays[1] - 1, aDays[2]);
        var rDay = hDate.getDay();
        for (var r in regularHolidays) {
            if (rDay == regularHolidays[r]) {
                addClassByCal(hDate.getFullYear(), hDate.getMonth() + 1,
                              hDate.getDate(), 'hday');
            }
        }
    });

    for (var m in holidays) {
        for (var d in holidays[m]) {
            addClassByCal(today.getFullYear(), m, holidays[m][d], 'hday')
        }
    }

    function addClassByCal(year, month, date, className) {
        $('div[id*=d_' + month + '_' + date + '_' + year + ']')
            .addClass(className);
    }
});
function jCalCallback (day, days) {
    var daySize = 22;
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
