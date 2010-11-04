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
<form name="form1" id="form1" method="post" action="?" >
<input type="hidden" name="mode" value="" />
<input type="hidden" name="campaign_id" value="<!--{$arrForm.campaign_id}-->" />
<input type="hidden" name="status" value="<!--{$arrForm.status}-->" />
<input type="hidden" name="header_row" value="" />
<input type="hidden" name="contents_row" value="" />
<input type="hidden" name="footer_row" value="" />
<div id="admin-contents" class="contents-main">
  <h2><!--{$tpl_campaign_title}--></h2>

  <!--{* ▼ヘッダー編集ここから *}-->
  <h3>ヘッダー編集</h3>
  <div id="campaign-design-header">
    <textarea name="header" cols="90" rows="<!--{$header_row}-->"><!--{$header_data|smarty:nodefaults}--></textarea>
    <div class="btn">
        <button type="button" onClick="ChangeSize(this, header, 50, 13, header_row)"><span><!--{if $header_row > 13}-->小さくする<!--{else}-->大きくする<!--{/if}--></span></button>
    </div>
  </div>
  <!--{* ▲ヘッダー編集ここまで *}-->

  <!--{* ▼コンテンツ編集ここから *}-->
  <h3>コンテンツ編集</h3>
  <div id="campaign-design-contents">
    <textarea name="contents" cols="90" rows="<!--{$contents_row}-->"><!--{$contents_data|smarty:nodefaults}--></textarea>
    <div class="btn">
      <button type="button" onclick="win03('./campaign_create_tag.php?campaign_id=<!--{$arrForm.campaign_id}-->', 'search', '550', '500');"><span>商品設定</span></button>
      <button type="button" onClick="ChangeSize(this, contents, 50, 13, contents_row)"><span><!--{if $contents_row > 13}-->小さくする<!--{else}-->大きくする<!--{/if}--></span></button>
    </div>
  </div>
  <!--{* ▲コンテンツ編集ここまで *}-->

  <!--{* ▼フッター編集ここから *}-->
  <h3>フッター編集</h3>
  <div id="campaign-design-footer">
    <textarea name="footer" cols="90" rows="<!--{$footer_row}-->"><!--{$footer_data|smarty:nodefaults}--></textarea>
    <div class="btn">
      <button type="button" onClick="ChangeSize(this, footer, 50, 13, footer_row)"><span><!--{if $footer_row > 13}-->小さくする<!--{else}-->大きくする<!--{/if}--></span></button>
    </div>
  </div>
  <!--{* ▲フッター編集ここまで *}-->

  <div class="btn">
    <button type="button" onclick="fnFormModeSubmit('form1', 'return', '', ''); return false;"><span>一覧ページへ戻る</span></button>
    <button type="button" onclick="fnFormModeSubmit('form1', 'regist', '', ''); return false;"><span>登録する</span></button>
    <button type="button" onclick="fnFormModeSubmit('form1', 'preview', '', ''); return false;"><span>プレビュー</span></button>
  </div>

</div>
</form>

<script type="text/javascript">
  /* テキストエリアの大きさを変更する */
  function ChangeSize(button, TextArea, Max, Min, row_tmp){
    if(TextArea.rows <= Min){
      TextArea.rows=Max; button.value="小さくする"; row_tmp.value=Max;
    }else{
      TextArea.rows =Min; button.value="大きくする"; row_tmp.value=Min;
    }
  }
  
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
