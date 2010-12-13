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
<form name="form_css" method="post" action="?" >
<input type="hidden" name="mode" value="" />
<input type="hidden" name="area_row" value="<!--{$area_row}-->" />
<input type="hidden" name="old_css_name" value="<!--{$old_css_name}-->" />
<div id="design" class="contents-main">

  <!--▼CSS設定ここから-->
  <table class="form">
    <tr>
      <th>CSSファイル名</th>
      <td>
        <!--{ if $arrErr.css_name != "" }--><span class="attention"><!--{$arrErr.css_name}--></span><br /><!--{/if}-->
        <input type="text" name="css_name" value="<!--{$css_name}-->" maxlength="<!--{$smarty.const.STEXT_LEN}-->" style="<!--{if $arrErr.css_name != ""}-->background-color: <!--{$smarty.const.ERR_COLOR}-->;<!--{/if}-->" size="60" class="box60" />.css<span class="attention"> (上限<!--{$smarty.const.STEXT_LEN}-->文字)</span>
      </td>
    </tr>
    <tr>
      <th>CSS内容</th>
      <td>
        <textarea name="css" cols=90 rows=<!--{$area_row}--> align="left" wrap=off style="width: 650px;"><!--{$css_data}--></textarea>
        <input type="hidden" name="area_row" value="<!--{$area_row}-->" />
        <div class="btn">
          <button type="button" onClick="ChangeSize(this, css, 50, 30, area_row)">拡大</button>
        </div>
      </td>
    </tr>
  </table>
  <div class="btn">
    <button type="submit" onclick="fnFormModeSubmit('form_css','confirm','','');"><span>この内容で登録する</span></button>
  </div>
  <!--▲CSS設定　ここまで-->

  <!--▼CSSファイル一覧　ここから-->
  <h2>編集可能CSSファイル</h2>
  <div class="btn addnew">
    <button type="button" onclick="location.href='http://<!--{$smarty.server.HTTP_HOST}--><!--{$smarty.server.PHP_SELF|escape}-->'"><span>CSSを新規入力</span></button>
  </div>
  <table class="list center" id="design-css-list">
    <tr>
      <th class="name">ファイル名</th>
      <th class="menu">編集</th>
      <th class="action">削除</th>
    </tr>
    <!--{if count($arrCSSList) > 0}-->
    <!--{foreach key=key item=item from=$arrCSSList}-->
    <tr>
			<td><!--{$item.file_name}--></td>
      <td style="background:<!--{if $item.css_name == $css_name}--><!--{$smarty.const.SELECT_RGB}--><!--{else}-->#ffffff<!--{/if}-->;">
        <a href="<!--{$smarty.server.PHP_SELF}-->?css_name=<!--{$item.css_name}-->">編集</a>
      </td>
      <td style="background:<!--{if $item.css_name == $css_name}--><!--{$smarty.const.SELECT_RGB}--><!--{else}-->#ffffff<!--{/if}-->;">
        <a href="javascript:;" onclick="fnFormModeSubmit('form_css','delete','css_name','<!--{$item.css_name}-->');">削除</a>
      </td>
    </tr>
    <!--{/foreach}-->
    <!--{else}-->
    <tr>
      <td colspan="2">CSSファイルが存在しません。</td>
    </tr>
    <!--{/if}-->
  </table>
  <!--▲CSSファイル一覧　ここまで-->

</div>
</form>
