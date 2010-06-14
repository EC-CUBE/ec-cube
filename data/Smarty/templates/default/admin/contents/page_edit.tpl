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
<input type="hidden" name="mode" value="edit" style="<!--{$arrErr[$key]|sfGetErrorColor}-->">
<div id="" class="contents-main">
  <h2>ページ編集</h2>
  <table>
    <tr >
      <th width="200">ページ選択<span class="attention"> *</span></td>
      <td width="507">
      <!--{assign var=key value="page"}-->
      <span class="attention"><!--{$arrErr[$key]}--></span>
      <select name="<!--{$key}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" onchange="fnModeSubmit('select','','');">
      <option value="">選択してください</option>
      <!--{html_options options=$arrPageList selected=$arrForm[$key].value}-->
      </select>
      </td>
    </tr>
  </table>
          
  <table>
    <tr><td colspan="3"><img src="<!--{$TPL_DIR}-->img/contents/main_bar.jpg" height="10" alt=""></td></tr>
  </table>
            
  <table>
    <!--{assign var=key value="template"}-->
    <tr>
      <th colspan="2">テンプレート<span class="attention">（上限<!--{$arrForm[$key].length}-->文字）</span></td>
    </tr>
    <tr>
      <td colspan="2">
      <span class="attention"><!--{$arrErr[$key]}--></span>
      <textarea name="<!--{$key}-->" cols="90" rows="40" class="area90" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" ><!--{$arrForm[$key].value|escape}--></textarea>
      </td>
    </tr>
  </table>

            <input type="button" name="subm1" onclick="fnModeSubmit('preview','','');" value="プレビュー" />
            <button type="submit"><span>この内容で登録する</span></button>
</div>
</form>
