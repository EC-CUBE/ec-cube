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
<!--{foreach key=key item=val from=$arrHidden}-->  
<input type="hidden" name="<!--{$key}-->" value="<!--{$val|escape}-->" />
<!--{/foreach}-->
<input type="hidden" name="mode" value="template" />
<div id="mail" class="contents-main">
  <h2>配信設定：配信内容設定</h2>
  <div class="message">
    メール配信設定が完了しました。指定時刻にメール配信が始まります。<br />
    配信履歴にて配信履歴がご覧いただけます。<br />
    <a href="./<!--{$smarty.const.DIR_INDEX_URL}-->">→続けて設定する</a>
  </div>
  <div class="btn">
    <button type="button" name="subm02" onClick="return fnInsertValAndSubmit( document.form1, 'mode', 'regist_back', '' )"><span>テンプレート設定画面へ戻る</span></button>
  　<button type="button" name="subm03" onClick="return fnInsertValAndSubmit( document.form1, 'mode', 'regist_complete', '' )" <!--{$list_data.template_id|sfGetEnabled}-->><span>配信を予約する</span></button>
  </div>
</div>
</form>

<form name="form2" id="form2" method="post" action="./preview.php" target="_blank">
<input type="hidden" name="subject" value="<!--{$list_data.subject|escape}-->" />
<input type="hidden" name="body" value="<!--{$list_data.body|escape}-->" />
<div id="mail2" class="contents-main">
  <h2>HTMLメール作成</h2>
  <div class="message">
    メール配信設定が完了しました。指定時刻にメール配信が始まります。<br />
    配信履歴にて配信履歴がご覧いただけます。<br />
    <a href="./<!--{$smarty.const.DIR_INDEX_URL}-->">→続けて設定する</a>
  </div>
</div>
</form>
