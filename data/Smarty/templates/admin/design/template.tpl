<!--{*
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2011 LOCKON CO.,LTD. All Rights Reserved.
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
<script type="text/javascript"><!--
function submitRegister() {
    var form = document.form1;
    var msg    = "テンプレートを変更します。";

    if (window.confirm(msg)) {
        form['mode'].value = 'register';
        form.submit();
    }
}
// -->
</script>

<form name="form1" method="post" action="?">
<input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
<input type="hidden" name="mode" value="" />
<input type="hidden" name="template_code_temp" value="" />
<input type="hidden" name="device_type_id" value="<!--{$device_type_id|h}-->" />
<div id="design" class="contents-main">
    <p class="remark">
        テンプレートを選択し、「この内容で登録する」ボタンを押すと、<br />
        選択したテンプレートへデザインを変更することが出来ます。
    </p>
    <table class="list center">
        <colgroup width="5%">
        <colgroup width="30%">
        <colgroup width="50%">
        <colgroup width="10%">
        <colgroup width="5%">
        <tr>
            <th>選択</th>
            <th>名前</th>
            <th>保存先</th>
            <th>ダウンロード</th>
            <th class="delete">削除</th>
        </tr>
        <!--{foreach from=$templates item=tpl}-->
        <!--{assign var=tplcode value=$tpl.template_code}-->
        <tr class="center">
            <td><input type="radio" name="template_code" value="<!--{$tplcode|h}-->" <!--{if $tplcode == $tpl_select}-->checked<!--{/if}--> /></td>
            <td class="left"><!--{$tpl.template_name|h}--></td>
            <td class="left">data/Smarty/templates/<!--{$tplcode|h}-->/</td>
            <td><span class="icon_confirm"><a href="#" onClick="fnModeSubmit('download','template_code_temp','<!--{$tplcode}-->');return false;">ダウンロード</a></span></td>
            <td><span class="icon_delete"><a href="#" onClick="fnModeSubmit('delete','template_code_temp','<!--{$tplcode}-->');return false;">削除</a></span></td>
        </tr>
        <!--{/foreach}-->
    </table>
    <div class="btn-area">
        <ul>
            <li><a class="btn-action" href="javascript:;" onclick="submitRegister();return false;"><span class="btn-next">この内容で登録する</span></a></li>
        </ul>
    </div>
</div>
</form>
