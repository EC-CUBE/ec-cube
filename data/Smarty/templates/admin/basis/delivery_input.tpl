<!--{*
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2014 LOCKON CO.,LTD. All Rights Reserved.
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

<form name="form1" id="form1" method="post" action="">
    <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
    <input type="hidden" name="mode" value="edit" />
    <!--{assign var=key value="deliv_id"}-->
    <input type="hidden" name="<!--{$arrForm[$key].keyname}-->" value="<!--{$arrForm[$key].value|h}-->" />
    <div id="basis" class="contents-main">
        <h2>配送方法登録</h2>

        <table>
            <tr>
                <th>配送業者名<span class="attention"> *</span></th>
                <td colspan="3">
                <!--{assign var=key value="name"}-->
                <span class="attention"><!--{$arrErr[$key]}--></span>
                <input type="text" name="<!--{$arrForm[$key].keyname}-->" value="<!--{$arrForm[$key].value|h}-->" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" size="60" class="box60" /></td>
            </tr>
            <tr>
                <th>名称<span class="attention"> *</span></th>
                <td colspan="3">
                <!--{assign var=key value="service_name"}-->
                <span class="attention"><!--{$arrErr[$key]}--></span>
                <input type="text" name="<!--{$arrForm[$key].keyname}-->" value="<!--{$arrForm[$key].value|h}-->" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" size="60" class="box60" /></td>
            </tr>
            <tr>
                <th>説明</th>
                <td colspan="3">
                <!--{assign var=key value="remark"}-->
                <span class="attention"><!--{$arrErr[$key]}--></span>
                <textarea name="<!--{$arrForm[$key].keyname}-->" cols="60" rows="8" class="area60" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->"><!--{"\n"}--><!--{$arrForm[$key].value|h}--></textarea></td>
            </tr>
            <tr>
                <th>伝票No.URL</th>
                <td colspan="3">
                <!--{assign var=key value="confirm_url"}-->
                <span class="attention"><!--{$arrErr[$key]}--></span>
                <input type="text" name="<!--{$arrForm[$key].keyname}-->" value="<!--{$arrForm[$key].value|h}-->" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" size="60" class="box60" /></td>
            </tr>
            <!--{section name=cnt loop=$smarty.const.DELIVTIME_MAX}-->
            <!--{assign var=type value="`$smarty.section.cnt.index%2`"}-->
            <!--{assign var=keyno value="`$smarty.section.cnt.iteration`"}-->
            <!--{assign var=key value="deliv_time`$smarty.section.cnt.iteration`"}-->
            <!--{assign var=key_next value="deliv_time`$smarty.section.cnt.iteration+1`"}-->
            <!--{if $type == 0}-->
                <!--{if $arrErr[$key] != "" || $arrErr[$key_next] != ""}-->
                <tr>
                    <td colspan="4"><span class="attention"><!--{$arrErr[$key]}--><!--{$arrErr[$key_next]}--></span></td>
                </tr>
                <!--{/if}-->
                <tr>
                <th>お届け時間<!--{$keyno}--></th>
                <!--{if $smarty.section.cnt.last}-->
                <!--{assign var=colspan value="3"}-->
                <!--{else}-->
                <!--{assign var=colspan value="1"}-->
                <!--{/if}-->
                <td colspan="<!--{$colspan}-->">
                <input type="text" name="<!--{$arrForm[$key].keyname}-->" value="<!--{$arrForm[$key].value|h}-->" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" size="20" class="box20" /></td>
            <!--{else}-->
                <th>お届け時間<!--{$keyno}--></th>
                <td><input type="text" name="<!--{$arrForm[$key].keyname}-->" value="<!--{$arrForm[$key].value|h}-->" maxlength="<!--{$arrForm[$key].length}-->" size="20" class="box20" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" /> </td>
                </tr>
            <!--{/if}-->
            <!--{/section}-->

        </table>

        <h2>取扱商品種別</h2>
        <!--{assign var=key value="product_type_id"}-->
        <table>
            <tr>
                <th>商品種別 <span class="attention">*</span></th>
                <td>
                    <span class="attention"><!--{$arrErr[$key]}--></span>
                    <span style="<!--{$arrErr[$key]|sfGetErrorColor}-->">
                    <!--{html_radios name=$key options=$arrProductType selected=$arrForm[$key].value separator='&nbsp;&nbsp;'}-->
                    </span>
                </td>
            </tr>
        </table>

        <h2>取扱支払方法</h2>
        <!--{assign var=key value="payment_ids"}-->
        <table>
            <tr>
                <th>支払方法 <span class="attention">*</span></th>
                <td>
                    <span class="attention"><!--{$arrErr[$key]}--></span>
                    <span style="<!--{$arrErr[$key]|sfGetErrorColor}-->">
                    <!--{html_checkboxes name=$key options=$arrPayments selected=$arrForm[$key].value separator='&nbsp;&nbsp;'}-->
                    </span>
                </td>
            </tr>
        </table>

        <!--{if $smarty.const.INPUT_DELIV_FEE}-->
        <h2>配送料登録</h2>
        <div class="btn">※全国一律送料 <input type="text" name="fee_all" class="box10" /> 円に設定する　<a class="btn-normal" href="javascript:;" onclick="eccube.setDelivFee(<!--{$smarty.const.DELIVFEE_MAX}-->); return false;"><span>反映</span></a></div>
        <table>
            <!--{section name=cnt loop=$smarty.const.DELIVFEE_MAX}-->
            <!--{assign var=type value="`$smarty.section.cnt.index%2`"}-->
            <!--{assign var=keyno value="`$smarty.section.cnt.iteration`"}-->
            <!--{assign var=key value="fee`$smarty.section.cnt.iteration`"}-->
            <!--{assign var=key_next value="fee`$smarty.section.cnt.iteration+1`"}-->

            <!--{if $type == 0}-->
                <tr>
                <th><!--{$arrPref[$keyno]}--> <span class="attention">*</span></th>
                <!--{if $smarty.section.cnt.last}-->
                <!--{assign var=colspan value="3"}-->
                <!--{else}-->
                <!--{assign var=colspan value="1"}-->
                <!--{/if}-->
                <td width="247" colspan="<!--{$colspan}-->">
                <!--{if $arrErr[$key] != ""}-->
                <span class="attention"><!--{$arrErr[$key]}--></span>
                <!--{/if}-->
                <input type="text" name="<!--{$arrForm[$key].keyname}-->" value="<!--{$arrForm[$key].value|h}-->" maxlength="<!--{$arrForm[$key].length}-->" size="20" class="box20" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" /> 円</td>
            <!--{else}-->
                <th><!--{$arrPref[$keyno]}--> <span class="attention">*</span></th>
                <td width="248">
                <!--{if $arrErr[$key] != ""}-->
                <span class="attention"><!--{$arrErr[$key]}--></span>
                <!--{/if}-->
                <input type="text" name="<!--{$arrForm[$key].keyname}-->" value="<!--{$arrForm[$key].value|h}-->" maxlength="<!--{$arrForm[$key].length}-->" size="20" class="box20" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" /> 円</td>
                </tr>
            <!--{/if}-->
            <!--{/section}-->
            </tr>
        </table>
        <!--{/if}-->

        <div class="btn-area">
            <ul>
                <li><a class="btn-action" href="javascript:;" onclick="location.href='./delivery.php';"><span class="btn-prev">前のページに戻る</span></a></li>
                <li><a class="btn-action" href="javascript:;" onclick="eccube.fnFormModeSubmit('form1', 'edit', '', ''); return false;"><span class="btn-next">この内容で登録する</span></a></li>
            </ul>
        </div>
    </div>
</form>
