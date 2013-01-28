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

<form name="form1" id="form1" method="post" action="?">
<input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
<input type="hidden" name="mode" value="register" />

<div id="ownersstore" class="contents-main">

    <!--入力項目ここから-->
    <p class="remark"><!--{t string="tpl_<span class='attention'>* The authentication key can be obtained from <a href='T_ARG1' target='_blank'>the EC-CUBE Owners Store</a>.</span>_01" escape="none" T_ARG1=$smarty.const.OSTORE_URL}--></p>
    <table class="form">
        <tr>
            <th><!--{t string="tpl_Authentication key settings._01"}--></th>
            <td>
                <!--{assign var="key" value="public_key"}-->
                <!--{if $arrErr[$key]}--><span class="attention"><!--{$arrErr[$key]}--></span><!--{/if}-->
                <textarea name="<!--{$key}-->" cols="60" rows="8" class="area60" maxlength="<!--{$smarty.const.MTEXT_LEN}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->"><!--{"\n"}--><!--{$arrForm[$key]|h}--></textarea>
            </td>
        </tr>
    </table>
    <!--入力項目ここまで-->

    <!--登録ボタンここから-->
    <div class="btn-area">
        <ul>
            <li><a class="btn-action" href="javascript:;" onclick="fnFormModeSubmit('form1', 'register', '', ''); return false;"><span class="btn-next"><!--{t string="tpl_Save and continue_01"}--></span></a></li>
        </ul>
    </div>
    <!--登録ボタンここまで-->

</div>
</form>
