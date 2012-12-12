<!--{*
/*
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
 */
*}-->

<form name="form1" id="form1" method="post" action="?">
<input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
<input type="hidden" name="mode" value="edit" />
<input type="hidden" name="holiday_id" value="<!--{$tpl_holiday_id}-->" />
<div id="basis" class="contents-main">

    <table class="form">
        <tr>
            <th><!--{t string="tpl_022"}--></th>
            <td>
                <!--{if $arrErr.title}--><span class="attention"><!--{$arrErr.title}--></span><!--{/if}-->
                <input type="text" name="title" value="<!--{$arrForm.title|h}-->" maxlength="<!--{$smarty.const.SMTEXT_LEN}-->" style="" size="60" class="box60"/>
                <span class="attention"> <!--{t string="tpl_023" T_FIELD=$smarty.const.SMTEXT_LEN}--></span>
            </td>
        </tr>
        <tr>
            <th><!--{t string="tpl_024_1"}--></th>
            <td>
                <!--{if $arrErr.date || $arrErr.month || $arrErr.day}-->
                <span class="attention"><!--{$arrErr.date}--></span>
                <span class="attention"><!--{$arrErr.month}--></span>
                <span class="attention"><!--{$arrErr.day}--></span>
                <!--{/if}-->
                <select name="month" style="<!--{$arrErr.month|sfGetErrorColor}-->">
                    <option value="">--</option>
                    <!--{html_options options=$arrMonth selected=$arrForm.month}-->
                </select>月
                <select name="day" style="<!--{$arrErr.day|sfGetErrorColor}-->">
                    <option value="">--</option>
                    <!--{html_options options=$arrDay selected=$arrForm.day}-->
                </select>日
                <br />
                <span class="attention"><!--{t string="tpl_025"}--></span>
            </td>
        </tr>
    </table>

    <div class="btn-area">
        <ul>
            <li><a class="btn-action" href="javascript:;" onclick="fnFormModeSubmit('form1', 'edit', '', ''); return false;"><span class="btn-next"><!--{t string="tpl_021"}--></span></a></li>
        </ul>
    </div>

    <table class="list">
        <col width="50%" />
        <col width="20%" />
        <col width="10%" />
        <col width="10%" />
        <col width="20%" />
        <tr>
            <th><!--{t string="tpl_022"}--></th>
            <th><!--{t string="tpl_024"}--></th>
            <th class="edit"><!--{t string="tpl_003"}--></th>
            <th class="delete"><!--{t string="tpl_004"}--></th>
            <th><!--{t string="tpl_005"}--></th>
        </tr>
        <!--{section name=cnt loop=$arrHoliday}-->
        <tr style="background:<!--{if $tpl_holiday_id != $arrHoliday[cnt].holiday_id}-->#ffffff<!--{else}--><!--{$smarty.const.SELECT_RGB}--><!--{/if}-->;">
            <!--{assign var=holiday_id value=$arrHoliday[cnt].holiday_id}-->
            <td><!--{$arrHoliday[cnt].title|h}--></td>
            <td><!--{$arrHoliday[cnt].month|h}-->月<!--{$arrHoliday[cnt].day|h}-->日</td>
            <td class="center">
                <!--{if $tpl_holiday_id != $arrHoliday[cnt].holiday_id}-->
                <a href="?" onclick="fnModeSubmit('pre_edit', 'holiday_id', <!--{$arrHoliday[cnt].holiday_id}-->); return false;"><!--{t string="tpl_003"}--></a>
                <!--{else}-->
                <!--{t string="tpl_026"}-->
                <!--{/if}-->
            </td>
            <td class="center">
                <!--{if $arrClassCatCount[$class_id] > 0}-->
                -
                <!--{else}-->
                <a href="?" onclick="fnModeSubmit('delete', 'holiday_id', <!--{$arrHoliday[cnt].holiday_id}-->); return false;"><!--{t string="tpl_004"}--></a>
                <!--{/if}-->
            </td>
            <td class="center">
                <!--{if $smarty.section.cnt.iteration != 1}-->
                <a href="?" onclick="fnModeSubmit('up', 'holiday_id', <!--{$arrHoliday[cnt].holiday_id}-->); return false;" /><!--{t string="tpl_077"}--></a>
                <!--{/if}-->
                <!--{if $smarty.section.cnt.iteration != $smarty.section.cnt.last}-->
                <a href="?" onclick="fnModeSubmit('down', 'holiday_id', <!--{$arrHoliday[cnt].holiday_id}-->); return false;" /><!--{t string="tpl_078"}--></a>
                <!--{/if}-->
            </td>
        </tr>
        <!--{/section}-->
    </table>

</div>
</form>
