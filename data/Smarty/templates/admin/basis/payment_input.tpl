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

<form name="form1" id="form1" method="post" action="./payment_input.php" enctype="multipart/form-data">
<input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
<input type="hidden" name="mode" value="edit" />
<input type="hidden" name="payment_id" value="<!--{$tpl_payment_id}-->" />
<input type="hidden" name="image_key" value="" />
<input type="hidden" name="fix" value="<!--{$arrForm.fix.value}-->" />
<!--{foreach key=key item=item from=$arrHidden}-->
<input type="hidden" name="<!--{$key}-->" value="<!--{$item|h}-->" />
<!--{/foreach}-->
<input type="hidden" name="charge_flg" value="<!--{$charge_flg}-->" />
<div id="basis" class="contents-main">
    <h2>支払方法登録・編集</h2>

        <table class="form">
        <col width="20%" />
        <col width="80%" />
            <tr>
                <th>支払方法<span class="attention"> *</span></th>
                <td>
                    <!--{assign var=key value="payment_method"}-->
                    <span class="attention"><!--{$arrErr[$key]}--></span>
                    <input type="text" name="<!--{$arrForm[$key].keyname}-->" value="<!--{$arrForm[$key].value|h}-->" size="30" class="box30" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" />
                </td>
            </tr>
            <tr>
                <th>手数料<span class="attention"> *</span></th>
                <td>
                    <!--{if $charge_flg == 2}-->
                        設定できません
                    <!--{else}-->
                        <!--{assign var=key value="charge"}-->
                        <span class="attention"><!--{$arrErr[$key]}--></span>
                        <input type="text" name="<!--{$arrForm[$key].keyname}-->" value="<!--{$arrForm[$key].value|h}-->" size="10" class="box10" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" />
                        円
                    <!--{/if}-->
                </td>
            </tr>
            <tr>
                <th>利用条件(円)</th>
                <td>
                    <!--{assign var=key_from value="rule_max"}-->
                    <!--{assign var=key_to value="upper_rule"}-->
                    <span class="attention"><!--{$arrErr[$key_from]}--></span>
                    <span class="attention"><!--{$arrErr[$key_to]}--></span>
                    <input type="text" name="<!--{$arrForm[$key_from].keyname}-->" value="<!--{$arrForm[$key_from].value|h}-->" size="10" class="box10" maxlength="<!--{$arrForm[$key_from].length}-->" style="<!--{$arrErr[$key_from]|sfGetErrorColor}-->" />
                    円
                    ～
                    <input type="text" name="<!--{$arrForm[$key_to].keyname}-->" value="<!--{$arrForm[$key_to].value|h}-->" size="10" class="box10" maxlength="<!--{$arrForm[$key_to].length}-->" style="<!--{$arrErr[$key_to]|sfGetErrorColor}-->" />
                    円
                </td>
            </tr>
            <tr>
                <th>ロゴ画像</th>
                <td>
                    <!--{assign var=key value="payment_image"}-->
                    <span class="attention"><!--{$arrErr[$key]}--></span>
                    <!--{if $arrFile[$key].filepath != ""}-->
                    <img src="<!--{$arrFile[$key].filepath}-->" alt="<!--{$arrForm.name|h}-->">　<br /><a href="" onclick="fnModeSubmit('delete_image', 'image_key', '<!--{$key}-->'); return false;">[画像の取り消し]</a><br />
                    <!--{/if}-->
                    <input type="file" name="<!--{$key}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" />
                    <a class="btn-normal" href="javascript:;" name="btn" onclick="fnModeSubmit('upload_image', 'image_key', '<!--{$key}-->'); return false;">アップロード</a>
                </td>
            </tr>
        </table>

    <div class="btn-area">
        <ul>
            <li><a class="btn-action" href="javascript:;" onclick="location.href='./payment.php';"><span class="btn-prev">前のページに戻る</span></a></li>
            <li><a class="btn-action" href="javascript:;" onclick="fnFormModeSubmit('form1', 'edit', '', ''); return false;"><span class="btn-next">この内容で登録する</span></a></li>
        </ul>
    </div>
</div>
</form>
