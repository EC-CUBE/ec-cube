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
<!--{include file="`$smarty.const.TEMPLATE_ADMIN_REALDIR`admin_popup_header.tpl"}-->

<script type="text/javascript">
<!--
self.moveTo(20,20);self.focus();
//-->
</script>

<form name="form1" id="form1" method="post" action="" onsubmit="return fnRegistMember();">
<input type="hidden" name="mode" value="<!--{$tpl_mode|h}-->">
<input type="hidden" name="member_id" value="<!--{$tpl_member_id|h}-->">
<input type="hidden" name="pageno" value="<!--{$tpl_pageno|h}-->">
<input type="hidden" name="old_login_id" value="<!--{$tpl_old_login_id|h}-->">
<input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid|h}-->">
<h2>メンバー登録/編集</h2>

<table>
  <tr>
    <th>名前</th>
    <td>
      <!--{if $arrErr.name}--><span class="attention"><!--{$arrErr.name}--></span><!--{/if}-->
      <input type="text" name="name" size="30" class="box30" value="<!--{$arrForm.name}-->" maxlength="<!--{$smarty.const.STEXT_LEN}-->" />
      <span class="attention">※必須入力</span>
    </td>
  </tr>
  <tr>
    <th>所属</th>
    <td>
      <!--{if $arrErr.department}--><span class="attention"><!--{$arrErr.department}--></span><!--{/if}-->
      <input type="text" name="department" size="30" class="box30" value="<!--{$arrForm.department}-->" maxlength="<!--{$smarty.const.STEXT_LEN}-->" />
    </td>
  </tr>
  <tr>
    <th>ログインＩＤ</th>
    <td>
      <!--{if $arrErr.login_id}--><span class="attention"><!--{$arrErr.login_id}--></span><!--{/if}-->
      <input type="text" name="login_id" size="20" class="box20"  value="<!--{$arrForm.login_id}-->" maxlength="<!--{$smarty.const.STEXT_LEN}-->"/>
      <span class="attention">※必須入力</span><br />
      ※半角英数字<!--{$smarty.const.ID_MIN_LEN}-->～<!--{$smarty.const.ID_MAX_LEN}-->文字
    </td>
  </tr>
  <tr>
    <th>パスワード</th>
    <td>
      <!--{if $arrErr.password}--><span class="attention"><!--{$arrErr.password}--></span><!--{/if}-->
      <input type="password" name="password" size="20" class="box20" value="<!--{$arrForm.password}-->" onfocus="<!--{$tpl_onfocus}-->" maxlength="<!--{$smarty.const.STEXT_LEN}-->"/>
      <span class="attention">※必須入力</span><br />
      ※半角英数字<!--{$smarty.const.ID_MIN_LEN}-->～<!--{$smarty.const.ID_MAX_LEN}-->文字
  </td>
  </tr>
  <tr>
    <th>権限</th>
    <td>
      <!--{if $arrErr.authority}--><span class="attention"><!--{$arrErr.authority}--></span><!--{/if}-->
      <select name="authority">
        <option value="">選択してください</option>
        <!--{html_options options=$arrAUTHORITY selected=$arrForm.authority}-->
      </select>
      <span class="attention">※必須入力</span>
    </td>
  </tr>
</table>

<div class="btn"><a class="btn-normal" href="javascript:;" onclick="fnFormModeSubmit('form1', '<!--{$tpl_mode|h}-->', '', '');"><span>この内容で登録する</span></a></div>
</form>

<!--{include file="`$smarty.const.TEMPLATE_ADMIN_REALDIR`admin_popup_footer.tpl"}-->
