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
<script type="text/javascript">
<!--
function doPreview(){
  document.form_edit.mode.value="preview"
  document.form_edit.target = "_blank";
  document.form_edit.submit();
}

function fnTargetSelf(){
  document.form_edit.target = "_self";
}

browser_type = 0;
if(navigator.userAgent.indexOf("MSIE") >= 0){
    browser_type = 1;
}
else if(navigator.userAgent.indexOf("Mozilla") >= 0){
    browser_type = 2;
}
//-->
</script>


<form name="form_edit" id="form_edit" method="post" action="?" >
<input type="hidden" name="mode" value="" />
<input type="hidden" name="page_id" value="<!--{$page_id}-->" />
<input type="hidden" name="device_type_id" value="<!--{$device_type_id|escape}-->" />

  <!--{if $arrErr.page_id_err != ""}-->
  <div class="message">
    <span class="attention"><!--{$arrErr.page_id_err}--></span>
  </div>
  <!--{/if}-->
  <table>
    <tr>
      <th>名称</th>
      <td>
  <!--{if $arrPageData.edit_flg == 2}-->
      <!--{$arrPageData.page_name|escape}--><input type="hidden" name="page_name" value="<!--{$arrPageData.page_name|escape}-->" />
  <!--{else}-->
        <input type="text" name="page_name" value="<!--{$arrPageData.page_name|escape}-->" maxlength="<!--{$smarty.const.STEXT_LEN}-->" style="<!--{if $arrErr.page_name != ""}-->background-color: <!--{$smarty.const.ERR_COLOR}-->;<!--{/if}-->" size="60" class="box60" /><span class="attention"> (上限<!--{$smarty.const.STEXT_LEN}-->文字)</span>
  <!--{/if}-->
  <!--{ if $arrErr.page_name != "" }-->
        <div class="message">
          <span class="attention"><!--{$arrErr.page_name}--></span>
        </div>
  <!--{/if}-->
     </td>
   <tr>
     <th>URL</th>
     <td>
   <!--{if $arrPageData.edit_flg == 2}-->
      <!--{$smarty.const.SITE_URL|escape}--><!--{$arrPageData.url|escape}-->
      <input type="hidden" name="url" value="<!--{$arrPageData.filename|escape}-->" />
    <!--{else}-->
      <!--{$user_URL|escape}--><input type="text" name="url" value="<!--{$arrPageData.directory|escape}--><!--{$arrPageData.filename|escape}-->" maxlength="<!--{$smarty.const.STEXT_LEN}-->" style="<!--{if $arrErr.url != ""}-->background-color: <!--{$smarty.const.ERR_COLOR}-->;<!--{/if}--> ime-mode: disabled;" size="40" class="box40" />.php<span class="attention"> (上限<!--{$smarty.const.STEXT_LEN}-->文字)</span>
    <!--{/if}-->
    <!--{ if $arrErr.url != "" }-->
      <div class="attention">
        <span class="attention"><!--{$arrErr.url}--></span>
      </div>
    <!--{/if}-->
     </td>
   </tr>
   <tr>
     <td colspan="2">

  <label for="header-chk"><input type="checkbox" name="header_chk" id="header-chk" <!--{$arrPageData.header_chk}--> />共通のヘッダーを使用する</label>&nbsp;
  <label for="footer-chk"><input type="checkbox" name="footer_chk" id="footer-chk" <!--{$arrPageData.footer_chk}--> />共通のフッターを使用する</label>
  <div>
    <textarea name="tpl_data" rows=<!--{$text_row}--> style="width: 100%;"><!--{$arrPageData.tpl_data|escape|smarty:nodefaults}--></textarea>
    <input type="hidden" name="html_area_row" value="<!--{$text_row}-->" />
    <a class="btn-normal" href="javascript:;" onclick="ChangeSize(this, tpl_data, 50, 13, html_area_row)"><span><!--{if $text_row > 13}-->縮小<!--{else}-->拡大<!--{/if}--></span></a>
  </div>
      </td>
    </tr>
  </table>

  <div class="btn">
    <a class='button' href="javascript:;" name='subm' onclick="fnTargetSelf(); fnFormModeSubmit('form_edit','confirm','','');"><span>登録する</span></a>
    <a class='button' href="javascript:;" name='preview' onclick="doPreview(); "><span>プレビュー</span></a>
  </div>


  <h2>編集可能ページ一覧</h2>
  <table class="list center">
    <tr>
      <th>名称</th>
      <th>レイアウト</th>
      <th><strong>ページ詳細</strong></th>
      <th>削除</th>
    </tr>
    <!--{foreach key=key item=item from=$arrPageList}-->
    <tr style="<!--{if $item.page_id == $page_id}-->background-color: <!--{$smarty.const.SELECT_RGB}-->;<!--{/if}-->">
      <td>
        <!--{$item.page_name}-->
      </td>
      <td>
        <a href="./<!--{$smarty.const.DIR_INDEX_URL}-->?page_id=<!--{$item.page_id}-->&amp;device_type_id=<!--{$item.device_type_id}-->" >編集</a>
      </td>
      <td>
        <!--{if $item.filename|strlen >= 1}-->
          <a href="?page_id=<!--{$item.page_id}-->&amp;device_type_id=<!--{$item.device_type_id}-->">編集</a>
        <!--{/if}-->
      </td>
      <td>
        <!--{if $item.edit_flg == 1}-->
          <a href="?" onclick="fnTargetSelf(); fnFormModeSubmit('form_edit','delete','page_id',this.name.substr(3));">削除</a>
          <input type="hidden" value="<!--{$item.page_id}-->" name="del_id<!--{$item.page_id}-->" />
        <!--{/if}-->
      </td>
    </tr>
    <!--{/foreach}-->
  </table>
  <div class="btn addnew">
    <a class="btn-normal" href="<!--{$smarty.server.PHP_SELF|escape}-->"><span>ページを新規入力</span></a>
  </div>

</form>
