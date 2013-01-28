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

<script type="text/javascript">
jQuery(function(){
    $("a.btn-action").click(function(){
        $("form#form1").submit();
        return false;
    });
});
</script>
<form name="form1" id="form1" method="post" action="?">
<input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
<!--{* ▼登録テーブルここから *}-->
<div id="system" class="contents-main">
    <div class="notice">
        <p class="remark"><span class="attention"><!--{t string="tpl_When incorrect settings are applied, it may no longer be possible to access the management screen.<br/>Do not change these settings if you are not familiar with them._01" escape="none"}--></span></p>
        <!--{if $arrErr.all}-->
            <p class="error"><!--{$arrErr.all|h}--></p>
        <!--{/if}-->
    </div>
    <h2><!--{t string="tpl_Management area settings_01"}--></h2>
    <table id="basis-index-admin">
        <tr>
            <th><!--{t string="tpl_Directory name_01"}--></th>
            <td>
                <!--{assign var=key value="admin_dir"}-->
                <span class="attention"><!--{$arrErr[$key]}--></span>
                <!--{$smarty.const.ROOT_URLPATH}--><input type="text" name="<!--{$key}-->" value="<!--{$arrForm[$key]|h}-->" maxlength="<!--{$smarty.const.ID_MAX_LEN}-->" size="40" class="box40" style="<!--{if $arrErr[$key] != ""}-->background-color: <!--{$smarty.const.ERR_COLOR}-->;<!--{/if}-->"/>/
            </td>
        </tr>
        <tr>
            <th><!--{t string="tpl_SSL restrictions_01"}--></th>
            <td>
                <!--{assign var=key value="admin_force_ssl"}-->
                <span class="attention"><!--{$arrErr[$key]}--></span>
                <input type="checkbox" name="<!--{$key}-->" value="1" id="<!--{$key}-->" <!--{if $arrForm[$key] == 1}-->checked="checked"<!--{/if}--><!--{if !$tpl_enable_ssl}--> disabled="disabled"<!--{/if}--> /><label for="<!--{$key}-->"><!--{t string="tpl_Make SSL mandatory._01"}--></label>
            </td>
        </tr>
        <tr>
            <th><!--{t string="tpl_IP restriction_01"}--></th>
            <td>
                <!--{assign var=key value="admin_allow_hosts"}-->
                <span class="attention"><!--{$arrErr[$key]}--></span>
                <textarea name="<!--{$key}-->" cols="60" rows="8" class="area60" style="<!--{$arrErr[$key]|sfGetErrorColor}-->"><!--{"\n"}--><!--{$arrForm[$key]|h}--></textarea>
                <span class="attention"> <!--{t string="tpl_(T_ARG1 characters max)_01" T_ARG1=$smarty.const.LTEXT_LEN}--></span><br />
                
                <span><!--{t string="tpl_* Access to the management area is limited to only connections from a designated IP address._01" escape="none"}--></span><br />
            </td>
        </tr>
    </table>


    <div class="btn-area">
        <ul>
            <li><a class="btn-action" href="#"><span class="btn-next"><!--{t string="tpl_Save and continue_01"}--></span></a></li>
        </ul>
    </div>
</div>
<div style="display: none">
    <div id="maparea">
        <div id="maps" style="width: 300px; height: 300px"></div>
        <a class="btn-normal" href="javascript:;" id="inputPoint"><!--{t string="tpl_Enter this position._01"}--></a>
    </div>
</div>
<!--{* ▲登録テーブルここまで *}-->
</form>
