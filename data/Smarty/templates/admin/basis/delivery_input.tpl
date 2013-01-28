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

<form name="form1" id="form1" method="post" action="">
<input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
<input type="hidden" name="mode" value="edit" />
<!--{assign var=key value="deliv_id"}-->
<input type="hidden" name="<!--{$arrForm[$key].keyname}-->" value="<!--{$arrForm[$key].value|h}-->" />
<div id="basis" class="contents-main">
    <h2><!--{t string="tpl_Delivery method registration_01"}--></h2>

    <table>
        <tr>
            <th><!--{t string="tpl_Delivery company name<span class='attention'> *</span>_01" escape="none"}--></th>
            <td colspan="3">
            <!--{assign var=key value="name"}-->
            <span class="attention"><!--{$arrErr[$key]}--></span>
            <input type="text" name="<!--{$arrForm[$key].keyname}-->" value="<!--{$arrForm[$key].value|h}-->" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" size="60" class="box60" /></td>
        </tr>
        <tr>
            <th><!--{t string="tpl_Name<span class='attention'> *</span>_01" escape="none"}--></th>
            <td colspan="3">
            <!--{assign var=key value="service_name"}-->
            <span class="attention"><!--{$arrErr[$key]}--></span>
            <input type="text" name="<!--{$arrForm[$key].keyname}-->" value="<!--{$arrForm[$key].value|h}-->" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" size="60" class="box60" /></td>
        </tr>
        <tr>
            <th><!--{t string="tpl_Explanation_01"}--></th>
            <td colspan="3">
            <!--{assign var=key value="remark"}-->
            <span class="attention"><!--{$arrErr[$key]}--></span>
            <textarea name="<!--{$arrForm[$key].keyname}-->" cols="60" rows="8" class="area60" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->"><!--{"\n"}--><!--{$arrForm[$key].value|h}--></textarea></td>
        </tr>
        <tr>
            <th><!--{t string="tpl_Voucher No. URL_01"}--></th>
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
            <th><!--{t string="tpl_Delivery time T_ARG1_01" T_ARG1=$keyno}--></th>
            <!--{if $smarty.section.cnt.last}-->
            <!--{assign var=colspan value="3"}-->
            <!--{else}-->
            <!--{assign var=colspan value="1"}-->
            <!--{/if}-->
            <td colspan="<!--{$colspan}-->">
            <input type="text" name="<!--{$arrForm[$key].keyname}-->" value="<!--{$arrForm[$key].value|h}-->" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" size="20" class="box20" /></td>
        <!--{else}-->
            <th><!--{t string="tpl_Delivery time T_ARG1_01" T_ARG1=$keyno}--></th>
            <td><input type="text" name="<!--{$arrForm[$key].keyname}-->" value="<!--{$arrForm[$key].value|h}-->" maxlength="<!--{$arrForm[$key].length}-->" size="20" class="box20" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" /> </td>
            </tr>
        <!--{/if}-->
        <!--{/section}-->

    </table>

    <h2><!--{t string="tpl_Available product types_01"}--></h2>
    <!--{assign var=key value="product_type_id"}-->
    <table>
        <tr>
            <th><!--{t string="tpl_Product type_01"}--></th>
            <td><span class="attention"><!--{$arrErr[$key]}--></span><!--{html_radios name=$key options=$arrProductType selected=$arrForm[$key].value separator='&nbsp;&nbsp;'}--></td>
        </tr>
    </table>

    <h2><!--{t string="tpl_Available payment methods_01"}--></h2>
    <!--{assign var=key value="payment_ids"}-->
    <table>
        <tr>
            <th><!--{t string="tpl_Payment method_01"}--></th>
            <td>
                <span class="attention"><!--{$arrErr[$key]}--></span>
                <!--{html_checkboxes name=$key options=$arrPayments selected=$arrForm[$key].value separator='&nbsp;&nbsp;'}-->
            </td>
        </tr>
    </table>
    <!--{*
    <!--{if $smarty.const.INPUT_DELIV_FEE}-->
    <h2><!--{t string="tpl_Delivery charge registration_01"}--></h2>
    <div class="btn">
        <!--{t string="tpl_*  Uniform shipping rates(&#036;):_01"}--> <input type='text' name='fee_all' class='box10' />　<a class="btn-normal" href="javascript:;" onclick="fnSetDelivFee(<!--{$smarty.const.DELIVFEE_MAX}-->); return false;"><span><!--{t string="tpl_Calculate_01"}--></span></a>
    </div>
    <table>
        <!--{section name=cnt loop=$smarty.const.DELIVFEE_MAX}-->
        <!--{assign var=type value="`$smarty.section.cnt.index%2`"}-->
        <!--{assign var=keyno value="`$smarty.section.cnt.iteration`"}-->
        <!--{assign var=key value="fee`$smarty.section.cnt.iteration`"}-->
        <!--{assign var=key_next value="fee`$smarty.section.cnt.iteration+1`"}-->

        <!--{if $type == 0}-->
            <!--{if $arrErr[$key] != "" || $arrErr[$key_next] != ""}-->
            <tr>
                <td colspan="4"><span class="attention"><!--{$arrErr[$key]}--><!--{$arrErr[$key_next]}--></span></td>
            </tr>
            <!--{/if}-->
            <tr>
            <th><!--{$arrPref[$keyno]}--></th>
            <!--{if $smarty.section.cnt.last}-->
            <!--{assign var=colspan value="3"}-->
            <!--{else}-->
            <!--{assign var=colspan value="1"}-->
            <!--{/if}-->
            <td width="247" colspan="<!--{$colspan}-->">
            <!--{t string="currency_prefix"}--> <input type="text" name="<!--{$arrForm[$key].keyname}-->" value="<!--{$arrForm[$key].value|h}-->" maxlength="<!--{$arrForm[$key].length}-->" size="20" class="box20" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" /> <!--{t string="currency_suffix"}--></td>
        <!--{else}-->
            <th><!--{$arrPref[$keyno]}--></th>
            <td width="248"><!--{t string="currency_prefix"}--><input type="text" name="<!--{$arrForm[$key].keyname}-->" value="<!--{$arrForm[$key].value|h}-->" maxlength="<!--{$arrForm[$key].length}-->" size="20" class="box20" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" /> <!--{t string="currency_suffix"}--></td>
            </tr>
        <!--{/if}-->
        <!--{/section}-->
    </table>
    <!--{/if}-->
    *}-->
    <div class="btn-area">
        <ul>
            <li><a class="btn-action" href="javascript:;" onclick="location.href='./delivery.php';"><span class="btn-prev"><!--{t string="tpl_Return to previous page_01"}--></span></a></li>
            <li><a class="btn-action" href="javascript:;" onclick="fnFormModeSubmit('form1', 'edit', '', ''); return false;"><span class="btn-next"><!--{t string="tpl_Save and continue_01"}--></span></a></li>
        </ul>
    </div>
</div>
</form>
