<!--{*
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
 *}-->

<div id="undercolumn">
    <div id="undercolumn_customer">
        <div class="flow_area">
			<ol>
			<li class="active"><span>&gt; STEP1</span><br />Delivery destination</li>
			<li class="large"><span>&gt; STEP2</span><br />Payment method and delivery time</li>
			<li><span>&gt; STEP3</span><br />Confirmation</li>
			<li class="last"><span>&gt; STEP4</span><br />Order complete</li>
			</ol>
		</div>
        <h2 class="title"><!--{$tpl_title|h}--></h2>

        <div class="information">
            <p>Input the items below. "<span class="attention">*</span>" indicates that the item is required.<br />
                <!--{if $smarty.const.USE_MULTIPLE_SHIPPING !== false}-->
                    When finished, please click the "Send only to the destination above" button or the "Send to multiple destinations" at the bottom of the page.<br/>
                <!--{else}-->
                    When finished, click the "Next" button at the bottom of the page.
                <!--{/if}-->
            </p>
        </div>

        <form name="form1" id="form1" method="post" action="?">
        <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
        <input type="hidden" name="mode" value="nonmember_confirm" />
        <input type="hidden" name="uniqid" value="<!--{$tpl_uniqid}-->" />
        <table summary=" ">
            <tr>
                <th>Name<span class="attention">*</span></th>
                <td>
                    <!--{assign var=key1 value="order_name01"}-->
                    <!--{assign var=key2 value="order_name02"}-->
                    <span class="attention"><!--{$arrErr[$key1]}--><!--{$arrErr[$key2]}--></span>
                    Last name&nbsp;<input type="text" name="<!--{$key1}-->" value="<!--{$arrForm[$key1].value|h}-->" maxlength="<!--{$arrForm[$key1].length}-->" style="<!--{$arrErr[$key1]|sfGetErrorColor}-->;" class="box120" />&nbsp;
                    First name&nbsp;<input type="text" name="<!--{$key2}-->" value="<!--{$arrForm[$key2].value|h}-->" maxlength="<!--{$arrForm[$key2].length}-->" style="<!--{$arrErr[$key2]|sfGetErrorColor}-->;" class="box120" />
                </td>
            </tr>
            <tr>
                <th>Postal code</th>
                <td>
                    <!--{* <!--{assign var=key1 value="order_zip01"}--> *}-->
                    <!--{* <!--{assign var=key2 value="order_zip02"}--> *}-->
                    <!--{assign var=key1 value="order_zipcode"}-->

                    <!--{* <span class="attention"><!--{$arrErr[$key1]}--><!--{$arrErr[$key2]}--></span> *}-->
                    <span class="attention"><!--{$arrErr[$key1]}--></span>

                    <p class="top">
                        <!--{* &nbsp;<input type="text" name="<!--{$key1}-->" value="<!--{$arrForm[$key1].value|h}-->" maxlength="<!--{$arrForm[$key1].length}-->" style="<!--{$arrErr[$key1]|sfGetErrorColor}-->; ime-mode: disabled;" class="box60" />&nbsp;-&nbsp;    <input type="text"    name="<!--{$key2}-->" value="<!--{$arrForm[$key2].value|h}-->" maxlength="<!--{$arrForm[$key2].length}-->" style="<!--{$arrErr[$key2]|sfGetErrorColor}-->; ime-mode: disabled;" class="box60" /> *}-->
                        &nbsp;<input type="text" name="<!--{$key1}-->" value="<!--{$arrForm[$key1].value|h}-->" maxlength="<!--{$arrForm[$key1].length}-->" style="<!--{$arrErr[$key1]|sfGetErrorColor}-->; ime-mode: disabled;" class="box60" /> 
                    </p>

                    <!--{*
                    <p class="zipimg"><a class="bt01" href="<!--{$smarty.const.ROOT_URLPATH}-->address/<!--{$smarty.const.DIR_INDEX_PATH}-->" onclick="fnCallAddress('<!--{$smarty.const.INPUT_ZIP_URLPATH}-->', 'order_zip01', 'order_zip02', 'order_pref', 'order_addr01'); return false;" target="_blank">Automatic address input</a>
                        <span class="mini">&nbsp;Click after inputting the postal code.</span></p>
                    *}-->
                </td>
            </tr>
            <tr>
                <th>Address<span class="attention">*</span></th>
                <td>
                    <span class="attention"><!--{$arrErr.order_addr01}--><!--{$arrErr.order_addr02}--></span>
                    <p>
                        <!--{assign var=key value="order_addr01"}-->
                        <input type="text" name="<!--{$key}-->" value="<!--{$arrForm[$key].value|h}-->" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->;" class="box380" /><br />
                        <!--{$smarty.const.SAMPLE_ADDRESS1}--></p>
                    <p>
                        <!--{assign var=key value="order_addr02"}-->
                        <input type="text" name="<!--{$key}-->" value="<!--{$arrForm[$key].value|h}-->" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->;" class="box380" /><br />
                        <!--{$smarty.const.SAMPLE_ADDRESS2}--></p>
                    <p class="mini"><span class="attention">Separate the address into two fields. Make sure to write down the building name.</span></p></td>
            </tr>
            <tr>
                <th>Phone number<span class="attention">*</span></th>
                <td>
                    <!--{assign var=key1 value="order_tel01"}-->
                    <!--{assign var=key2 value="order_tel02"}-->
                    <!--{assign var=key3 value="order_tel03"}-->
                    <span class="attention"><!--{$arrErr[$key1]}--></span>
                    <span class="attention"><!--{$arrErr[$key2]}--></span>
                    <span class="attention"><!--{$arrErr[$key3]}--></span>
                    <input type="text" name="<!--{$key1}-->" value="<!--{$arrForm[$key1].value|h}-->" maxlength="<!--{$arrForm[$key1].length}-->" style="<!--{$arrErr[$key1]|sfGetErrorColor}-->; ime-mode: disabled;" class="box60" /> -
                    <input type="text" name="<!--{$key2}-->" value="<!--{$arrForm[$key2].value|h}-->" maxlength="<!--{$arrForm[$key2].length}-->" style="<!--{$arrErr[$key2]|sfGetErrorColor}-->; ime-mode: disabled;"    class="box60" /> -
                    <input type="text" name="<!--{$key3}-->" value="<!--{$arrForm[$key3].value|h}-->" maxlength="<!--{$arrForm[$key3].length}-->" style="<!--{$arrErr[$key3]|sfGetErrorColor}-->; ime-mode: disabled;" class="box60" />
                </td>
            </tr>
            <tr>
                <th>FAX</th>
                <td>
                    <!--{assign var=key1 value="order_fax01"}-->
                    <!--{assign var=key2 value="order_fax02"}-->
                    <!--{assign var=key3 value="order_fax03"}-->
                    <span class="attention"><!--{$arrErr[$key1]}--></span>
                    <span class="attention"><!--{$arrErr[$key2]}--></span>
                    <span class="attention"><!--{$arrErr[$key3]}--></span>
                    <input type="text" name="<!--{$key1}-->" value="<!--{$arrForm[$key1].value|h}-->" maxlength="<!--{$arrForm[$key1].length}-->" style="<!--{$arrErr[$key1]|sfGetErrorColor}-->; ime-mode: disabled;" class="box60" /> -
                    <input type="text" name="<!--{$key2}-->" value="<!--{$arrForm[$key2].value|h}-->" maxlength="<!--{$arrForm[$key2].length}-->" style="<!--{$arrErr[$key2]|sfGetErrorColor}-->; ime-mode: disabled;"    class="box60" /> -
                    <input type="text" name="<!--{$key3}-->" value="<!--{$arrForm[$key3].value|h}-->" maxlength="<!--{$arrForm[$key3].length}-->" style="<!--{$arrErr[$key3]|sfGetErrorColor}-->; ime-mode: disabled;" class="box60" />
                </td>
            </tr>
            <tr>
                <th>E-mail address<span class="attention">*</span></th>
                <td>
                    <!--{assign var=key value="order_email"}-->
                    <span class="attention"><!--{$arrErr[$key]}--></span>
                    <input type="text" name="<!--{$key}-->" value="<!--{$arrForm[$key].value|h}-->" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->; ime-mode: disabled;" class="box380 top" /><br />
                    <!--{assign var=key value="order_email02"}-->
                    <span class="attention"><!--{$arrErr[$key]}--></span>
                    <input type="text" name="<!--{$key}-->" value="<!--{$arrForm[$key].value|h}-->" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->; ime-mode: disabled;" class="box380" /><br />
                    <p class="mini"><span class="attention">Input twice for confirmation</span></p>
                </td>
            </tr>
            <tr>
                <th>Gender<span class="attention">*</span></th>
                <td>
                    <!--{assign var=key value="order_sex"}-->
                    <!--{if $arrErr[$key]}-->
                        <div class="attention"><!--{$arrErr[$key]}--></div>
                    <!--{/if}-->
                    <span style="<!--{$arrErr[$key]|sfGetErrorColor}-->">
                        <!--{html_radios name="$key" options=$arrSex selected=$arrForm[$key].value style="$err" label_ids=true}-->
                    </span>
                </td>
            </tr>
            <tr>
                <th>Occupation</th>
                <td>
                    <!--{assign var=key value="order_job"}-->
                    <!--{if $arrErr[$key]}-->
                        <!--{assign var=err value="background-color: `$smarty.const.ERR_COLOR`"}-->
                    <!--{/if}-->
                    <select name="<!--{$key}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->">
                        <option value="">Please make a selection</option>
                        <!--{html_options options=$arrJob selected=$arrForm[$key].value}-->
                    </select>
                </td>
            </tr>
            <tr>
                <th>Date of birth</th>
                <td>
                    <!--{assign var=errBirth value="`$arrErr.year``$arrErr.month``$arrErr.day`"}-->
                    <span class="attention"><!--{$errBirth}--></span>
                    <select name="year" style="<!--{$errBirth|sfGetErrorColor}-->">
                        <!--{html_options options=$arrYear selected=$arrForm.year.value|default:''}-->
                    </select>Year
                    <select name="month" style="<!--{$errBirth|sfGetErrorColor}-->">
                        <!--{html_options options=$arrMonth selected=$arrForm.month.value|default:''}-->
                    </select>Month
                    <select name="day" style="<!--{$errBirth|sfGetErrorColor}-->">
                        <!--{html_options options=$arrDay selected=$arrForm.day.value|default:''}-->
                    </select>day
                </td>
            </tr>
            <tr>
                <th colspan="2">
                <!--{assign var=key value="deliv_check"}-->
                <input type="checkbox" name="<!--{$key}-->" value="1" onclick="fnCheckInputDeliv();" <!--{$arrForm[$key].value|sfGetChecked:1}--> id="deliv_label" />
                <label for="deliv_label"><span class="attention">Delivery destination</span> *Can be omitted if the destination is the same as the address above.</label>
                </th>
            </tr>
            <tr>
                <th>Name<span class="attention">*</span></th>
                <td>
                    <!--{assign var=key1 value="shipping_name01"}-->
                    <!--{assign var=key2 value="shipping_name02"}-->
                    <span class="attention"><!--{$arrErr[$key1]}--><!--{$arrErr[$key2]}--></span>
                    Last name&nbsp;<input type="text" name="<!--{$key1}-->" value="<!--{$arrForm[$key1].value|h}-->" maxlength="<!--{$arrForm[$key1].length}-->" style="<!--{$arrErr[$key1]|sfGetErrorColor}-->;" class="box120" />&nbsp;
                    First name&nbsp;<input type="text" name="<!--{$key2}-->" value="<!--{$arrForm[$key2].value|h}-->" maxlength="<!--{$arrForm[$key2].length}-->" style="<!--{$arrErr[$key2]|sfGetErrorColor}-->;" class="box120" />
                </td>
            </tr>
            <tr>
                <th>Postal code<span class="attention">*</span></th>
                <td>
                    <!--{* <!--{assign var=key1 value="shipping_zip01"}--> *}-->
                    <!--{* <!--{assign var=key2 value="shipping_zip02"}--> *}-->
                    <!--{assign var=key1 value="shipping_zipcode"}-->

                    <!--{* <span class="attention"><!--{$arrErr[$key1]}--><!--{$arrErr[$key2]}--></span> *}-->
                    <span class="attention"><!--{$arrErr[$key1]}--></span>

                    <p class="top">
                        <!--{* &nbsp;<input type="text" name="<!--{$key1}-->" value="<!--{$arrForm[$key1].value|h}-->" maxlength="<!--{$arrForm[$key1].length}-->" style="<!--{$arrErr[$key1]|sfGetErrorColor}-->; ime-mode: disabled;"    class="box60" />&nbsp;-&nbsp;    <input type="text"    name="<!--{$key2}-->" value="<!--{$arrForm[$key2].value|h}-->" maxlength="<!--{$arrForm[$key2].length}-->" style="<!--{$arrErr[$key2]|sfGetErrorColor}-->; ime-mode: disabled;" class="box60" /> *}-->
                        &nbsp;<input type="text" name="<!--{$key1}-->" value="<!--{$arrForm[$key1].value|h}-->" maxlength="<!--{$arrForm[$key1].length}-->" style="<!--{$arrErr[$key1]|sfGetErrorColor}-->; ime-mode: disabled;"    class="box60" /> 
                    </p>

                    <!--{*
                    <p class="zipimg"><a class="bt01" href="<!--{$smarty.const.ROOT_URLPATH}-->address/<!--{$smarty.const.DIR_INDEX_PATH}-->" onclick="fnCallAddress('<!--{$smarty.const.INPUT_ZIP_URLPATH}-->', 'shipping_zip01', 'shipping_zip02', 'shipping_pref', 'shipping_addr01'); return false;" target="_blank">Automatic address input</a>
                        <span class="mini">&nbsp;Click after inputting the postal code.</span></p>
                    *}-->
                </td>
            </tr>
            <tr>
                <th>Address<span class="attention">*</span></th>
                <td>
                    <span class="attention"><!--{$arrErr.shipping_addr01}--><!--{$arrErr.shipping_addr02}--></span>
                    <p>
                        <!--{assign var=key value="shipping_addr01"}-->
                        <input type="text" name="<!--{$key}-->" value="<!--{$arrForm[$key].value|h}-->" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->;" class="box380" /><br />
                        <!--{$smarty.const.SAMPLE_ADDRESS1}--></p>
                    <p>
                        <!--{assign var=key value="shipping_addr02"}-->
                        <input type="text" name="<!--{$key}-->" value="<!--{$arrForm[$key].value|h}-->" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->;" class="box380" /><br />
                        <!--{$smarty.const.SAMPLE_ADDRESS2}--></p>
                    <p class="mini"><span class="attention">Separate the address into two fields. Make sure to write down the building name.</span></p>

                </td>
            </tr>
            <tr>
                <th>Phone number<span class="attention">*</span></th>
                <td>
                    <!--{assign var=key1 value="shipping_tel01"}-->
                    <!--{assign var=key2 value="shipping_tel02"}-->
                    <!--{assign var=key3 value="shipping_tel03"}-->
                    <span class="attention"><!--{$arrErr[$key1]}--></span>
                    <span class="attention"><!--{$arrErr[$key2]}--></span>
                    <span class="attention"><!--{$arrErr[$key3]}--></span>
                    <input type="text" name="<!--{$key1}-->" value="<!--{$arrForm[$key1].value|h}-->" maxlength="<!--{$arrForm[$key1].length}-->" style="<!--{$arrErr[$key1]|sfGetErrorColor}-->; ime-mode: disabled;" class="box60" /> -
                    <input type="text" name="<!--{$key2}-->" value="<!--{$arrForm[$key2].value|h}-->" maxlength="<!--{$arrForm[$key2].length}-->" style="<!--{$arrErr[$key2]|sfGetErrorColor}-->; ime-mode: disabled;"    class="box60" /> -
                    <input type="text" name="<!--{$key3}-->" value="<!--{$arrForm[$key3].value|h}-->" maxlength="<!--{$arrForm[$key3].length}-->" style="<!--{$arrErr[$key3]|sfGetErrorColor}-->; ime-mode: disabled;" class="box60" />
                </td>
            </tr>
        </table>

        <!--{if $smarty.const.USE_MULTIPLE_SHIPPING !== false}-->
            <p class="alignC">Will you be sending this product multiple destinations?</p>
        <!--{/if}-->
        <div class="btn_area">
            <ul>
                <!--{if $smarty.const.USE_MULTIPLE_SHIPPING !== false}-->
                    <li><button class="bt02">Send only to the destination above</button> </li>
                    <li>
                    <a class="bt02" href="javascript:;" onclick="fnModeSubmit('multiple', '', ''); return false">Send to multiple destinations</a>
                    </li>
                <!--{else}-->
                    <li><button class="bt02">Next</button>
                    </li>
                <!--{/if}-->
            </ul>
        </div>
        </form>
    </div>
</div>
