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
<div id="mail" class="contents-main">
  <table class="form">
    <tr>
      <th>テンプレート選択<span class="attention"> *</span></th>
      <td>
        <!--{if $arrErr.template_id}--><span class="attention"><!--{$arrErr.template_id}--></span><!--{/if}-->
        <select name="template_id" onchange="return fnInsertValAndSubmit( document.form1, 'mode', 'template', '' ) " style="<!--{$arrErr.template_id|sfGetErrorColor}-->">
        <option value="" selected="selected">選択してください</option>
        <!--{html_options options=$arrTemplate selected=$list_data.template_id}-->
        </select>
      </td>
    </tr>
    
    <!--{* バッチモードの場合のみ表示 *}-->
    <!--{if $smarty.const.MELMAGA_BATCH_MODE}-->
    <tr>
      <th>配信時間設定<span class="attention"> *</span></th>
      <td>
        <!--{if $arrErr.send_year || $arrErr.send_month || $arrErr.send_day || $arrErr.send_hour || $arrErr.send_minutes}--><span class="attention"><!--{$arrErr.send_year}--><!--{$arrErr.send_month}--><!--{$arrErr.send_day}--><!--{$arrErr.send_hour}--><!--{$arrErr.send_minutes}--></span><br /><!--{/if}-->
        <select name="send_year" style="<!--{$arrErr.send_year|sfGetErrorColor}-->">
        <!--{html_options options=$arrYear selected=$arrNowDate.year}-->
        </select>年
        <select name="send_month" style="<!--{$arrErr.send_month|sfGetErrorColor}-->">
        <!--{html_options options=$objDate->getMonth() selected=$arrNowDate.month}-->
        </select>月
        <select name="send_day" style="<!--{$arrErr.send_day|sfGetErrorColor}-->">
        <!--{html_options options=$objDate->getDay() selected=$arrNowDate.day}-->
        </select>日
        <select name="send_hour" style="<!--{$arrErr.send_hour|sfGetErrorColor}-->">
        <!--{html_options options=$objDate->getHour() selected=$arrNowDate.hour}-->
        </select>時
        <select name="send_minutes" style="<!--{$arrErr.send_minutes|sfGetErrorColor}-->">
        <!--{html_options options=$objDate->getMinutesInterval() selected=$arrNowDate.minutes}-->
        </select>分
      </td>
    </tr>
    <!--{/if}-->
  </table>

  <!--{if $list_data.template_id}-->
  <table class="form">
    <tr>
      <th>Subject<span class="attention"> *</span></th>
      <td>
        <!--{if $arrErr.subject}--><span class="attention"><!--{$arrErr.subject}--></span><!--{/if}-->
        <input type="text" name="subject" size="65" class="box65" <!--{if $arrErr.subject}--><!--{sfSetErrorStyle}--><!--{/if}--> value="<!--{$list_data.subject|escape}-->" />
      </td>
    </tr>
    <tr>
      <th>本文<span class="attention"> *</span><br />（名前差し込み時は {name} といれてください）</th>
      <td>
        <!--{if $arrErr.body}--><span class="attention"><!--{$arrErr.body}--></span><!--{/if}-->
        <textarea name="body" cols="90" rows="40" class="area90" <!--{if $arrErr.body}--><!--{sfSetErrorStyle}--><!--{/if}-->><!--{$list_data.body|escape}--></textarea>
      </td>
    </tr>
  </table>
  <!--{/if}-->

  <div class="btn">
    <button type="button" onClick="return fnInsertValAndSubmit( document.form1, 'mode', 'back', '' )"><span>検索画面に戻る</span></button>
    <button type="submit" onClick="return fnInsertValAndSubmit( document.form1, 'mode', 'regist_confirm', '' )" ><span>確認ページへ</span></button>
  </div>
</div>
<input type="hidden" name="mode" value="template" />
<input type="hidden" name="mail_method" value="<!--{$list_data.mail_method}-->" />
</form>
