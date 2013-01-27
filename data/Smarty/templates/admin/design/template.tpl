<!--{*
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2012 LOCKON CO.,LTD. All Rights Reserved.
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
    var msg    = "<!--{t string="tpl_The template will be changed._01"}-->";

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
<input type="hidden" name="template_code" value="" />
<input type="hidden" name="device_type_id" value="<!--{$device_type_id|h}-->" />
<div id="design" class="contents-main">
    <p class="remark">
        <!--{t string="tpl_Select a template and click the 'Save and Continue' button to,<br />change the designtemplate._01" escape="none"}-->
    </p>

    <!--{if $arrErr.err != ""}-->
        <div class="message">
            <span class="attention"><!--{$arrErr.err}--></span>
        </div>
    <!--{/if}-->

    <table class="list center">
        <col width="5%" />
        <col width="30%" />
        <col width="50%" />
        <col width="10%" />
        <col width="5%" />
        <tr>
            <th><!--{t string="tpl_Selection_01"}--></th>
            <th><!--{t string="tpl_Name_03"}--></th>
            <th><!--{t string="tpl_Saving destination_01"}--></th>
            <th><!--{t string="tpl_Download_01"}--></th>
            <th class="delete"><!--{t string="tpl_Remove_01"}--></th>
        </tr>
        <!--{foreach from=$templates item=tpl}-->
        <!--{assign var=tplcode value=$tpl.template_code}-->
        <tr class="center">
            <td><input type="radio" name="template_code" value="<!--{$tplcode|h}-->" <!--{if $tplcode == $tpl_select}-->checked="checked"<!--{/if}--> /></td>
            <td class="left"><!--{$tpl.template_name|h}--></td>
            <td class="left">data/Smarty/templates/<!--{$tplcode|h}-->/</td>
            <td><span class="icon_confirm"><a href="javascript:;" onClick="fnFormModeSubmit('form2', 'download','template_code','<!--{$tplcode}-->');return false;"><!--{t string="tpl_Download_01"}--></a></span></td>
            <td><span class="icon_delete"><a href="javascript:;" onClick="fnFormModeSubmit('form2', 'delete','template_code','<!--{$tplcode}-->');return false;"><!--{t string="tpl_Remove_01"}--></a></span></td>
        </tr>
        <!--{/foreach}-->
    </table>
    <div class="btn-area">
        <ul>
            <li><a class="btn-action" href="javascript:;" onclick="submitRegister();return false;"><span class="btn-next"><!--{t string="tpl_Save and continue_01"}--></span></a></li>
        </ul>
    </div>
</div>
</form>
<form name="form2" method="post" action="?">
    <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
    <input type="hidden" name="mode" value="" />
    <input type="hidden" name="template_code" value="" />
    <input type="hidden" name="device_type_id" value="<!--{$device_type_id|h}-->" />
</form>
