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
<input type="hidden" name="<!--{$key}-->" value="<!--{$val|h}-->" />
<!--{/foreach}-->
<div id="mail" class="contents-main">
  <table class="form">
    <!--{if $smarty.const.MELMAGA_BATCH_MODE}-->
    <tr>
      <th>配信時間設定<span class="attention"> *</span></th>
      <td>
      <!--{$list_data.send_year}-->年<!--{$list_data.send_month}-->月<!--{$list_data.send_day}-->日
      <!--{$list_data.send_hour}-->時<!--{$list_data.send_minutes}-->分
      </td>
    </tr>
    <!--{/if}-->
    <!--▼インクルードここから-->
    <!--{if $list_data.template_id}-->
    <tr>
      <th>Subject<span class="attention"> *</span></th>
      <td><!--{$list_data.subject|h}--></td>
    </tr>
    <!--{if $list_data.mail_method ne 2}-->
    <tr>
      <td colspan="2"><a href="#" onClick="return document.form2.submit();">HTMLで確認</a></td>
    </tr>
    <!--{/if}-->
    <!--{if $smarty.post.template_mode ne "html_template"}-->
    <tr>
      <th>本文<span class="attention"> *</span><br />（名前差し込み時は {name} といれてください）</th>
      <td><!--{$list_data.body|h|nl2br}--></td>
    </tr>
    <!--{/if}-->
    <!--{/if}-->
    <!--▲インクルードここまで-->
  </table>

  <div class="btn">
    <a class="btn-normal" href="javascript:;" name="subm02" onclick="return fnInsertValAndSubmit( document.form1, 'mode', 'regist_back', '' )"><span>テンプレート設定画面へ戻る</span></a>
    <!--{if $smarty.const.MELMAGA_BATCH_MODE}-->
    <a class="btn-normal" href="javascript:;" name="subm03" onClick="return fnInsertValAndSubmit( document.form1, 'mode', 'regist_complete', '' )" <!--{$list_data.template_id|sfGetEnabled}-->><span>配信を予約する</span></a>
    <!--{else}-->
    <a class="btn-normal" href="javascript:;" name="subm03" onClick="return fnInsertValAndSubmit( document.form1, 'mode', 'regist_complete', '' )" <!--{$list_data.template_id|sfGetEnabled}-->><span>配信する</span></a>
    <!--{/if}-->
  </div>
</div>
<input type="hidden" name="mode" value="template">
</form>
<form name="form2" id="form2" method="post" action="./preview.php" target="_blank">
  <input type="hidden" name="subject" value="<!--{$list_data.subject|h}-->" />
  <input type="hidden" name="body" value="<!--{$list_data.body|h}-->" />
</form>
