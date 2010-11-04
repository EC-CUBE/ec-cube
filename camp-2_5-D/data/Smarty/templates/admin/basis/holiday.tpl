<!--{*
/*
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
 */
*}-->
<form name="form1" id="form1" method="post" action="?">
<input type="hidden" name="mode" value="edit" />
<input type="hidden" name="holiday_id" value="<!--{$tpl_holiday_id}-->" />
<div id="basis" class="contents-main">
  <h2>定休日登録</h2>

  <table class="form">
    <tr>
      <th>タイトル<span class="attention"> *</span></th>
      <td>
        <!--{if $arrErr.title}--><span class="attention"><!--{$arrErr.title}--></span><!--{/if}-->
        <input type="text" name="title" value="<!--{$arrForm.title|escape}-->" maxlength="<!--{$smarty.const.SMTEXT_LEN}-->" style="" size="60" class="box60"/>
        <span class="attention"> (上限<!--{$smarty.const.SMTEXT_LEN}-->文字)</span>
      </td>
    </tr>
    <tr>
      <th>日付<span class="attention"> *</span></th>
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
        <span class="attention">振替え休日は自動設定されないので、振替え先の日付を設定してください。</span>
      </td>
    </tr>
  </table>

  <div class="btn">
    <button type="submit"><span>この内容で登録する</span></button>
  </div>

  <table class="list">
    <tr>
      <th>タイトル</th>
      <th>日付</th>
      <th>編集</th>
      <th>削除</th>
      <th>移動</th>
    </tr>
    <!--{section name=cnt loop=$arrHoliday}-->
    <tr style="background:<!--{if $tpl_class_id != $arrHoliday[cnt].holiday_id}-->#ffffff<!--{else}--><!--{$smarty.const.SELECT_RGB}--><!--{/if}-->;">
      <!--{assign var=holiday_id value=$arrHoliday[cnt].holiday_id}-->
      <td><!--{$arrHoliday[cnt].title|escape}--></td>
      <td><!--{$arrHoliday[cnt].month|escape}-->月<!--{$arrHoliday[cnt].day|escape}-->日</td>
      <td class="center">
        <!--{if $tpl_holiday_id != $arrHoliday[cnt].holiday_id}-->
        <a href="?" onclick="fnModeSubmit('pre_edit', 'holiday_id', <!--{$arrHoliday[cnt].holiday_id}-->); return false;">編集</a>
        <!--{else}-->
        編集中
        <!--{/if}-->
      </td>
      <td class="center">
        <!--{if $arrClassCatCount[$class_id] > 0}-->
        -
        <!--{else}-->
        <a href="?" onclick="fnModeSubmit('delete', 'holiday_id', <!--{$arrHoliday[cnt].holiday_id}-->); return false;">削除</a>
        <!--{/if}-->
      </td>
      <td class="center">
        <!--{if $smarty.section.cnt.iteration != 1}-->
        <a href="?" onclick="fnModeSubmit('up', 'holiday_id', <!--{$arrHoliday[cnt].holiday_id}-->); return false;" />上へ</a>
        <!--{/if}-->
        <!--{if $smarty.section.cnt.iteration != $smarty.section.cnt.last}-->
        <a href="?" onclick="fnModeSubmit('down', 'holiday_id', <!--{$arrHoliday[cnt].holiday_id}-->); return false;" />下へ</a>
        <!--{/if}-->
      </td>
    </tr>
    <!--{/section}-->
  </table>

</div>
</form>
