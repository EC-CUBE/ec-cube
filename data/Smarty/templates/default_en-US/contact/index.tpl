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
    <h2 class="title"><!--{$tpl_title|h}--></h2>

    <div id="undercolumn_contact">

        <p>Depending on the inquiry, it may take some time to provide a response.<br />
        Please note that on holidays, your request will be handled the next business day or afterwards.</p>

        <form name="form1" method="post" action="?">
        <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
        <input type="hidden" name="mode" value="confirm" />

        <table summary="Inquiry">
            <tr>
                <th>Name<span class="attention">*</span></th>
                <td>
                    <span class="attention"><!--{$arrErr.name01}--><!--{$arrErr.name02}--></span>
                    Last name&nbsp;<input type="text" class="box120" name="name01" value="<!--{$arrForm.name01.value|default:$arrData.name01|h}-->" maxlength="<!--{$smarty.const.STEXT_LEN}-->" style="<!--{$arrErr.name01|sfGetErrorColor}-->;" /> 
                    First name&nbsp;<input type="text" class="box120" name="name02" value="<!--{$arrForm.name02.value|default:$arrData.name02|h}-->" maxlength="<!--{$smarty.const.STEXT_LEN}-->" style="<!--{$arrErr.name02|sfGetErrorColor}-->;" />
                </td>
            </tr
            <tr>
                <th>Postal code</th>
                <td>
                    <!--{* <span class="attention"><!--{$arrErr.zip01}--><!--{$arrErr.zip02}--></span> *}-->
                    <span class="attention"><!--{$arrErr.zipcode}--></span>

                    <p class="top">
                        &nbsp;
                        <!--{* <input type="text" name="zip01" class="box60" value="<!--{$arrForm.zip01.value|default:$arrData.zip01|h}-->" maxlength="<!--{$smarty.const.ZIP01_LEN}-->" style="<!--{$arrErr.zip01|sfGetErrorColor}-->; ime-mode: disabled;" />&nbsp;-&nbsp; *}-->
                        <!--{* <input type="text" name="zip02" class="box60" value="<!--{$arrForm.zip02.value|default:$arrData.zip02|h}-->" maxlength="<!--{$smarty.const.ZIP02_LEN}-->" style="<!--{$arrErr.zip02|sfGetErrorColor}-->; ime-mode: disabled;" /> *}-->
                        <input type="text" name="zipcode" class="box60" value="<!--{$arrForm.zipcode.value|default:$arrData.zipcode|h}-->" maxlength="<!--{$smarty.const.ZIPCODE_LEN}-->" style="<!--{$arrErr.zipcode|sfGetErrorColor}-->; ime-mode: disabled;" /> 
                    </p>
                    
                    <!--{*
                    <p class="zipimg">
                        <a class="bt01" href="javascript:fnCallAddress('<!--{$smarty.const.INPUT_ZIP_URLPATH}-->', 'zip01', 'zip02', 'pref', 'addr01');">Automatic address input</a>
                        <span class="mini">&nbsp;Click after inputting the postal code.</span>
                    </p>
                    *}-->
                </td>
            </tr>
            <tr>
                <th>Address</th>
                <td>
                    <span class="attention"><!--{$arrErr.addr01}--><!--{$arrErr.addr02}--></span>

                    <p>
                        <input type="text" class="box380" name="addr01" value="<!--{$arrForm.addr01.value|default:$arrData.addr01|h}-->" style="<!--{$arrErr.addr01|sfGetErrorColor}-->;" /><br />
                        <!--{$smarty.const.SAMPLE_ADDRESS1}-->
                    </p>

                    <p>
                        <input type="text" class="box380" name="addr02" value="<!--{$arrForm.addr02.value|default:$arrData.addr02|h}-->" style="<!--{$arrErr.addr02|sfGetErrorColor}-->;" /><br />
                        <!--{$smarty.const.SAMPLE_ADDRESS2}-->
                    </p>

                    <p class="mini"><span class="attention">Separate the address into two fields. Make sure to write down the building name.</span></p>
                </td>
            </tr>
            <tr>
                <th>Phone number</th>
                <td>
                    <span class="attention"><!--{$arrErr.tel01}--><!--{$arrErr.tel02}--><!--{$arrErr.tel03}--></span>
                    <input type="text" class="box60" name="tel01" value="<!--{$arrForm.tel01.value|default:$arrData.tel01|h}-->" maxlength="<!--{$smarty.const.TEL_ITEM_LEN}-->" style="<!--{$arrErr.tel01|sfGetErrorColor}-->; ime-mode: disabled;" />&nbsp;-&nbsp;
                    <input type="text" class="box60" name="tel02" value="<!--{$arrForm.tel02.value|default:$arrData.tel02|h}-->" maxlength="<!--{$smarty.const.TEL_ITEM_LEN}-->" style="<!--{$arrErr.tel02|sfGetErrorColor}-->; ime-mode: disabled;" />&nbsp;-&nbsp;
                    <input type="text" class="box60" name="tel03" value="<!--{$arrForm.tel03.value|default:$arrData.tel03|h}-->" maxlength="<!--{$smarty.const.TEL_ITEM_LEN}-->" style="<!--{$arrErr.tel03|sfGetErrorColor}-->; ime-mode: disabled;" />
                </td>
            </tr>
            <tr>
                <th>E-mail address<span class="attention">*</span></th>
                <td>
                    <span class="attention"><!--{$arrErr.email}--><!--{$arrErr.email02}--></span>
                    <input type="text" class="box380 top" name="email" value="<!--{$arrForm.email.value|default:$arrData.email|h}-->" style="<!--{$arrErr.email|sfGetErrorColor}-->; ime-mode: disabled;" /><br />
                    <!--{* ログインしていれば入力済みにする *}-->
                    <!--{if $smarty.server.REQUEST_METHOD != 'POST' && $smarty.session.customer}-->
                    <!--{assign var=email02 value=$arrData.email}-->
                    <!--{/if}-->
                    <input type="text" class="box380" name="email02" value="<!--{$arrForm.email02.value|default:$email02|h}-->" style="<!--{$arrErr.email02|sfGetErrorColor}-->; ime-mode: disabled;" /><br />
                    <p class="mini"><span class="attention">Input twice for confirmation</span></p>
                </td>
            </tr>
            <tr>
                <th>Details of inquiry<span class="attention">*</span><br />
                <span class="mini">(<!--{$smarty.const.MLTEXT_LEN}--> characters or less)</span></th>
                <td>
                    <span class="attention"><!--{$arrErr.contents}--></span>
                    <textarea name="contents" class="box380" cols="60" rows="20" style="<!--{$arrErr.contents.value|h|sfGetErrorColor}-->;"><!--{"\n"}--><!--{$arrForm.contents.value|h}--></textarea>
                    <p class="mini attention">* Make sure to include the "Order number" for inquiries related to orders.</p>
                </td>
            </tr>
        </table>

        <div class="btn_area">
            <ul>
                <li>
					<button class="bt02">Confirm</button>
                </li>
            </ul>
        </div>

        </form>
    </div>
</div>
