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

<form name="index_form"  method="post" action="?"> 
  <input type="hidden" name="mode" value="confirm" />
      <table class="list">
      <tr>
        <th>インデックス</th>
        <th>テーブル名</th>
        <th>カラム名</th>
        <th>説明</th>
      </tr>

      
 <!--{section name=cnt loop=$arrForm}-->
  
    <tr>
    <td class="center"><input type="checkbox" name="indexflag_new[]" value="<!--{$smarty.section.cnt.index}-->" <!--{if $arrForm[cnt].indexflag == "1"}-->checked<!--{/if}--> /></td>
    <td><!--{$arrForm[cnt].table_name}--></td>
    <td><!--{$arrForm[cnt].column_name}--></td>
    <td><!--{$arrForm[cnt].recommend_comment}--></td>
    </tr>
<input type="hidden" name="table_name[]" value="<!--{$arrForm[cnt].table_name}-->" />
<input type="hidden" name="column_name[]" value="<!--{$arrForm[cnt].column_name}-->" />
<input type="hidden" name="indexflag[]" value="<!--{$arrForm[cnt].indexflag}-->" />

<!--{/section}-->
</table>

<a class="btn_normal" href="javascript:;" onclick="fnFormModeSubmit('index_form', 'confirm', '', '');">変更する</a>
</form>
