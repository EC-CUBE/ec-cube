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
<!--{*

XXX: ヘッダーとフッターでwrapの設定が違うのは疑問。それぞれの良し悪しはともかく、統一を。

*}-->
<div id="design" class="contents-main">
  <!--{* ▼ヘッダー編集ここから *}-->
  <h2>ヘッダー編集</h2>
  <!--{* プレビューここから *}-->
  <!--{ if $header_prev == "on"}-->
  <dic id="design-header-preview">
    <!--{if $browser_type == 1 }-->
      <div style="zoom:0.8"><!--{include file="`$smarty.const.HTML_PATH`user_data/include/preview/header.tpl"}--></div>
    <!--{ else }-->
      <span class="attention"><strong>プレビューはIEでのみ表示されます。</strong></span>
    <!--{ /if }-->
  </div>
  <!--{ /if }-->
  <!--{* プレビューここまで *}-->
    
  <form name="form_header" id="form_header" method="post" action="?" >
  <input type="hidden" name="mode" value="" />
  <input type="hidden" name="division" value="header" />
  <input type="hidden" name="header_row" value="<!--{$header_row}-->" />
  <input type="hidden" name="browser_type" value="" />
  <input type="hidden" name="device_type_id" value="<!--{$device_type_id|escape}-->" />

    <textarea name="header" rows="<!--{$header_row}-->" wrap="off" style="width: 100%;"><!--{$header_data|smarty:nodefaults}--></textarea>
    <div class="btn">
      <button type="button" onClick="ChangeSize(this, header, 50, 13, header_row)"><span><!--{if $header_row > 13}-->縮小<!--{else}-->拡大<!--{/if}--></span></button>
    </div>
    <div class="btn">
      <button type='button' name='subm' onclick="fnFormModeSubmit('form_header','confirm','','');"><span>登録</span></button>
      <button type='button' name='preview' onclick="lfnSetBrowser('form_header', 'browser_type'); fnFormModeSubmit('form_header','preview','','');"><span>プレビュー</span></button>
    </div>
  </form>
  <!--{* ▲ヘッダー編集ここまで *}-->

  <!--{* ▼フッター編集ここから *}-->
  <h2>フッター編集</h2>
  <!--{ if $footer_prev == "on"}-->
  <div id="design-footer-preview">
    <!--{if $browser_type == 1 }-->
      <div style="zoom:0.8"><!--{include file="`$smarty.const.HTML_PATH`/user_data/include/preview/footer.tpl"}--></div>
    <!--{ else }-->
      <span class="attention"><strong>プレビューはIEでのみ表示されます。</strong></span>
    <!--{ /if }-->
  </div>
  <!--{ /if }-->

  <form name="form_footer" id="form_footer" method="post" action="?" >
  <input type="hidden" name="mode" value="" />
  <input type="hidden" name="division" value="footer" />
  <input type="hidden" name="footer_row" value=<!--{$footer_row}--> />
  <input type="hidden" name="browser_type" value="" />
  <input type="hidden" name="device_type_id" value="<!--{$device_type_id|escape}-->" />

    <textarea name="footer" rows="<!--{$footer_row}-->" style="width: 100%;"><!--{$footer_data|smarty:nodefaults}--></textarea>
    <div class="btn">
      <button type="button" onClick="ChangeSize(this, footer, 50, 13, footer_row)"><span><!--{if $footer_row > 13}-->縮小<!--{else}-->拡大<!--{/if}--></span></button>
    </div>
    <div class="btn">
      <button type='button' name='subm' onclick="fnFormModeSubmit('form_footer','confirm','','');"><span>登録</span></button>
      <button type='button' name='preview' onclick="lfnSetBrowser('form_footer', 'browser_type'); fnFormModeSubmit('form_footer','preview','','');"><span>プレビュー</span></button>
    </div>
  </form>
  <!--{* ▲フッター編集ここまで *}-->

<script type="text/javascript">
  /* ブラウザの種類をセットする */
  function lfnSetBrowser(form, item){
    browser_type = 0;
    if(navigator.userAgent.indexOf("MSIE") >= 0){
        browser_type = 1;
    }
    else if(navigator.userAgent.indexOf("Gecko/") >= 0){
        browser_type = 2;
    }
    
    document[form][item].value=browser_type;
  }

</script>
