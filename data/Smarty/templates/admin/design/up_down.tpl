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
function confirmSubmit(mode, msg) {
    var form = document.form1;
    form['mode'].value = mode;
    if (window.confirm(msg)) {
        form.submit();
    } else {
        form['mode'].value = '';
    }
}
//-->
</script>

<form name="form1" method="post" action="?" enctype="multipart/form-data">
<input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
<input type="hidden" name="mode" value="" />
<input type="hidden" name="device_type_id" value="<!--{$device_type_id|h}-->" />
<div id="disign" class="contents-main">
    <p class="remark">
        <!--{t string="tpl_The template package will be uploaded._01" escape="none"}-->
    </p>

    <!--{if $arrErr.err != ""}-->
        <div class="message">
            <span class="attention"><!--{$arrErr.err}--></span>
        </div>
    <!--{/if}-->

    <table>
        <!--{assign var=key value="template_code"}-->
        <tr>
            <th><!--{t string="Template code"}--></th>
            <td>
                <span class="attention"><!--{$arrErr[$key]}--></span>
                <input type="text" name="<!--{$key}-->" value="<!--{$arrForm[$key].value|h}-->" maxlength="" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" class="box54">
            </td>
        </tr>
        <!--{assign var=key value="template_name"}-->
        <tr>
            <th><!--{t string="tpl_Template name_01"}--></th>
            <td>
                <span class="attention"><!--{$arrErr[$key]}--></span>
                <input type="text" name="<!--{$key}-->" value="<!--{$arrForm[$key].value|h}-->" maxlength="" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" class="box54">
            </td>
        </tr>
        <!--{assign var=key value="template_file"}-->
        <tr>
            <th><!--{t string="tpl_Template file_01"}--><br/>
                <span class="attention"><span class="fs14n"><!--{t string="tpl_*  .tar/.tar.gz only_01"}--></span></span>
            </th>
            <td>
                <span class="attention"><!--{$arrErr[$key]}--></span>
                <input type="file" name="<!--{$key}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" class="box54" size="64" <!--{if $arrErr[$key]}-->style="background-color:<!--{$smarty.const.ERR_COLOR|h}-->"<!--{/if}-->>
            </td>
        </tr>
    </table>
    <div class="btn-area">
        <a class="btn-action" href="javascript:;" onclick="fnModeSubmit('upload', '', '');return false;"><span class="btn-next"><!--{t string="tpl_Save and continue_01"}--></span></a>
    </div>
</div>
</form>
