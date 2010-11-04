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
<input type="hidden" name="mode" value="register" />
<input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
<div id="ownersstore" class="contents-main">

  <!--入力項目ここから-->
  <p><span class="attention">※認証キーは<a href="<!--{$smarty.const.OSTORE_URL}-->" target="_blank">EC-CUBEオーナーズストア</a>で取得できます。</span></p>
  <table class="form">
    <tr>
      <th>認証キーの設定</th>
      <td>
        <!--{assign var="key" value="public_key"}-->
        <!--{if $arrErr[$key]}--><span class="attention"><!--{$arrErr[$key]}--></span><!--{/if}-->
        <textarea name="<!--{$key}-->" cols="60" rows="8" class="area60" maxlength="<!--{$smarty.const.MTEXT_LEN}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->"><!--{$arrForm[$key]|escape}--></textarea>
      </td>
    </tr>
  </table>
  <!--入力項目ここまで-->
  
  <!--登録ボタンここから-->
  <div class="btn">
    <button type="submit"><span>この内容で登録する</span></button>
  </div>
  <!--登録ボタンここまで-->
  
</div>
</form>
