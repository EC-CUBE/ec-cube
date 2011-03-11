<!--{*
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
 *}-->
<!--{include file="`$smarty.const.SMARTPHONE_TEMPLATE_REALDIR`popup_header.tpl" subtitle="パスワードを忘れた方(入力ページ)"}-->

  <div id="windowarea">
    <h2>パスワードを忘れた方</h2>
    <p>ご登録時のメールアドレスを入力して「次へ」ボタンをクリックしてください。<br />
      <span class="mini"><em>※新しくパスワードを発行いたしますので、お忘れになったパスワードはご利用できなくなります。</em></span></p>
    <form action="?" method="post" name="form1">
      <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
      <input type="hidden" name="mode" value="mail_check" />



<table class="entryform">
<tr>
<th>メールアドレス</th>
<td><!--★メールアドレス入力★--><input type="email" name="email" value="<!--{$tpl_login_email|h}-->" size="40" class="box300" style="<!--{$errmsg|sfGetErrorColor}-->; ime-mode: disabled;" />
</td>
</tr>
<tr>
<th>お名前</th>
<td><span class="attention"><!--{$arrErr.name01}--><!--{$arrErr.name02}--></span>
姓&nbsp;<input type="text" class="box120" name="name01" value="<!--{$arrForm.name01|default:''|h}-->" maxlength="<!--{$smarty.const.STEXT_LEN}-->" style="<!--{$arrErr.name01|sfGetErrorColor}-->; ime-mode: active;" />　

名&nbsp;<input type="text" class="box120" name="name02" value="<!--{$arrForm.name02|default:''|h}-->" maxlength="<!--{$smarty.const.STEXT_LEN}-->" style="<!--{$arrErr.name02|sfGetErrorColor}-->; ime-mode: active;" />
<br />
        <span class="attention"><!--{$errmsg}--></span>
</td></tr></table>

      <div class="btn">
        <input class="spbtn spbtn-shopping" type="submit" value="次へ" />
      </div>
    </form>
  </div>
<br />
<!--{include file="`$smarty.const.SMARTPHONE_TEMPLATE_REALDIR`popup_footer.tpl"}-->
