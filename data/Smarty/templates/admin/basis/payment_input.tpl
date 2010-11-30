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
<!--{include file="`$smarty.const.TEMPLATE_ADMIN_DIR`admin_popup_header.tpl"}-->

<script type="text/javascript">
<!--
self.moveTo(20,20);self.focus();
//-->
</script>

<form name="form1" id="form1" method="post" action="" enctype="multipart/form-data">
<input type="hidden" name="mode" value="edit" />
<input type="hidden" name="payment_id" value="<!--{$tpl_payment_id}-->" />
<input type="hidden" name="image_key" value="" />
<input type="hidden" name="fix" value="<!--{$arrForm.fix.value}-->" />
<!--{foreach key=key item=item from=$arrHidden}-->
<input type="hidden" name="<!--{$key}-->" value="<!--{$item|escape}-->" />
<!--{/foreach}-->
<input type="hidden" name="charge_flg" value="<!--{$charge_flg}-->" />
  <h2>支払方法登録・編集</h2>
  
    <table class="form">
      <tr>
        <th>支払方法<span class="attention"> *</span></th>
        <td>
          <!--{assign var=key value="payment_method"}-->
          <span class="attention"><!--{$arrErr[$key]}--></span>
          <input type="text" name="<!--{$arrForm[$key].keyname}-->" value="<!--{$arrForm[$key].value|escape}-->" size="30" class="box30" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" />
        </td>
      </tr>
      <tr>
        <th>手数料<span class="attention"> *</span></th>
        <td>
          <!--{if $charge_flg == 2}-->
          設定できません
          <!--{else}-->
          <!--{assign var=key value="charge"}-->
          <span class="attention"><!--{$arrErr[$key]}--></span>
          <input type="text" name="<!--{$arrForm[$key].keyname}-->" value="<!--{$arrForm[$key].value|escape}-->" size="10" class="box10" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" />
           円
          <!--{/if}-->
        </td>
      </tr>
      <tr>
        <th>利用条件(円)</th>
        <td>
          <!--{assign var=key_from value="rule"}-->
          <!--{assign var=key_to value="upper_rule"}-->
          <span class="attention"><!--{$arrErr[$key_from]}--></span>
          <span class="attention"><!--{$arrErr[$key_to]}--></span>
          <input type="text" name="<!--{$arrForm[$key_from].keyname}-->" value="<!--{$arrForm[$key_from].value|escape}-->" size="10" class="box10" maxlength="<!--{$arrForm[$key_from].length}-->" style="<!--{$arrErr[$key_from]|sfGetErrorColor}-->" />
           円
           ～ 
          <input type="text" name="<!--{$arrForm[$key_to].keyname}-->" value="<!--{$arrForm[$key_to].value|escape}-->" size="10" class="box10" maxlength="<!--{$arrForm[$key_to].length}-->" style="<!--{$arrErr[$key_to]|sfGetErrorColor}-->" />
           円
        </td>
      </tr>
      <tr>
        <th>ロゴ画像</th>
        <td>
          <!--{assign var=key value="payment_image"}-->
          <span class="attention"><!--{$arrErr[$key]}--></span>
          <!--{if $arrFile[$key].filepath != ""}-->
          <img src="<!--{$arrFile[$key].filepath}-->" alt="<!--{$arrForm.name|escape}-->">　<a href="" onclick="fnModeSubmit('delete_image', 'image_key', '<!--{$key}-->'); return false;">[画像の取り消し]</a><br />
          <!--{/if}-->
          <input type="file" name="<!--{$key}-->" size="25" class="box25" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" />
          <input type="button" name="btn" onclick="fnModeSubmit('upload_image', 'image_key', '<!--{$key}-->')" value="アップロード" />
        </td>
      </tr>
    </table>
  
  <div class="btn"><button type="submit"><span>この内容で登録する</span></button></div>
  
</div>
</form>

<!--{include file="`$smarty.const.TEMPLATE_ADMIN_DIR`admin_popup_footer.tpl"}-->
