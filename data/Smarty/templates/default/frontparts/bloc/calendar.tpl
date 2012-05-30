<!--{*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2012 LOCKON CO.,LTD. All Rights Reserved.
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

<div class="block_outer">
    <div id="calender_area">
    <h2><img src="<!--{$TPL_URLPATH}-->img/title/tit_bloc_calender.gif" alt="カレンダー" /></h2>
        <div class="block_body">
            <!--{section name=num loop=$arrCalendar}-->
                <!--{assign var=arrCal value=`$arrCalendar[num]`}-->
                <!--{section name=cnt loop=$arrCal}-->
                    <!--{if $smarty.section.cnt.first}-->
                        <table>
                            <caption class="month"><!--{$arrCal[cnt].year}-->年<!--{$arrCal[cnt].month}-->月の定休日</caption>
                            <thead><tr><th>日</th><th>月</th><th>火</th><th>水</th><th>木</th><th>金</th><th>土</th></tr></thead>
                    <!--{/if}-->
                    <!--{if $arrCal[cnt].first}-->
                        <tr>
                        <!--{/if}-->
                        <!--{if !$arrCal[cnt].in_month}-->
                            <td></td>
                        <!--{elseif $arrCal[cnt].holiday}-->
                            <td class="off<!--{if $arrCal[cnt].today}--> today<!--{/if}-->"><!--{$arrCal[cnt].day}--></td>
                        <!--{else}-->
                            <td<!--{if $arrCal[cnt].today}--> class="today"<!--{/if}-->><!--{$arrCal[cnt].day}--></td>
                        <!--{/if}-->
                        <!--{if $arrCal[cnt].last}-->
                            </tr>
                    <!--{/if}-->
                <!--{/section}-->
                <!--{if $smarty.section.cnt.last}-->
                    </table>
                <!--{/if}-->
            <!--{/section}-->
            <p class="information">※赤字は休業日です</p>
        </div>

    </div>
</div>
