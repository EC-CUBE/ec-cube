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
<div id="basis" class="contents-main">
  <table class="list">
    <tr>
      <th>管理タイトル/管理内容</th>
      <th>選択項目</th>
      <th class="edit">編集</th>
    </tr>
    <!--{section name=cnt loop=$arrControlList}-->
    <tr>
      <td><strong><!--{$arrControlList[cnt].control_title|h}--></strong><br /><!--{$arrControlList[cnt].control_text|h}--></td>
      <td align="center">
        <form name="form<!--{$smarty.section.cnt.index}-->" id="form<!--{$smarty.section.cnt.index}-->" method="post" action="?">
        <input type="hidden" name="mode" value="edit" />
        <input type="hidden" name="control_id" value="<!--{$arrControlList[cnt].control_id}-->" />
        <select name="control_flg" style="<!--{$arrErr[$key]|sfGetErrorColor}-->">
          <!--{html_options options=$arrControlList[cnt].control_area selected=$arrControlList[cnt].control_flg.value}-->
        </select>
        </form>
      </td>
      <td class="menu">
        <a href="javascript;" onclick="document.form<!--{$smarty.section.cnt.index}-->.submit();">編集</a>
      </td>
    </tr>
    <!--{/section}-->
  </table>
</div>
