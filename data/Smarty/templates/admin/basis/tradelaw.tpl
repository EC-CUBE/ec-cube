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
<input type="hidden" name="mode" value="<!--{$tpl_mode}-->" />
<div id="basis" class="contents-main">
    <table class="form">
        <tr>
            <th><!--{t string="tpl_Distributor<span class='attention'> *</span>_01" escape="none"}--></th>
            <td>
            <!--{assign var=key value="law_company"}-->
            <!--{if $arrErr[$key]}-->
            <span class="attention"><!--{$arrErr[$key]}--></span>
            <!--{/if}-->
            <input type="text" name="<!--{$arrForm[$key].keyname}-->" value="<!--{$arrForm[$key].value|h}-->" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" size="60" class="box60" />
			<span class="attention"> <!--{t string="tpl_(T_ARG1 characters max)_01" T_ARG1=$arrForm[$key].length}--></span>
		</td>
        </tr>
        <tr>
            <th><!--{t string="tpl_Operation director<span class='attention'> *</span>_01" escape="none"}--></th>
            <td>
            <!--{assign var=key value="law_manager"}-->
            <!--{if $arrErr[$key]}-->
            <span class="attention"><!--{$arrErr[$key]}--></span>
            <!--{/if}-->
            <input type="text" name="<!--{$arrForm[$key].keyname}-->" value="<!--{$arrForm[$key].value|h}-->" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" size="60" class="box60" /><span class="attention"> <!--{t string="tpl_(T_ARG1 characters max)_01" T_ARG1=$arrForm[$key].length}--></span></td>
        </tr>
        <tr>
            <th><!--{t string="tpl_Postal code_01" escape="none"}--></th>
            <td>
            <!--{* <!--{assign var=key1 value="law_zip01"}--> *}-->
            <!--{* <!--{assign var=key2 value="law_zip02"}--> *}-->
            <!--{assign var=key value="law_zipcode"}-->

            <!--{*
            <!--{if $arrErr[$key1] || $arrErr[$key2]}--> 
            <span class="attention"><!--{$arrErr[$key1]}--></span>
            <span class="attention"><!--{$arrErr[$key2]}--></span>
            <!--{/if}-->
            *}-->
            <!--{if $arrErr[$key]}-->
            <span class="attention"><!--{$arrErr[$key]}--></span>
            <!--{/if}-->
            
            <!--{t string="tpl_Postal code mark_01"}-->
            <!--{*
            <input type="text" name="<!--{$arrForm[$key1].keyname}-->" value="<!--{$arrForm[$key1].value|h}-->" maxlength="<!--{$arrForm[$key1].length}-->" style="<!--{$arrErr[$key1]|sfGetErrorColor}-->"    size="6" class="box6" />
            -
            <input type="text"    name="<!--{$arrForm[$key2].keyname}-->" value="<!--{$arrForm[$key2].value|h}-->" maxlength="<!--{$arrForm[$key2].length}-->" style="<!--{$arrErr[$key2]|sfGetErrorColor}-->"    size="6" class="box6" />
            <a class="btn-normal" href="javascript:;" name="address_input" onclick="fnCallAddress('<!--{$smarty.const.INPUT_ZIP_URLPATH}-->', 'law_zip01', 'law_zip02', 'law_pref', 'law_addr01'); return false;"><!--{t string="tpl_Location finder_01"}--></a>
            *}-->
            <input type="text" name="<!--{$arrForm[$key].keyname}-->" value="<!--{$arrForm[$key].value|h}-->" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->"  size="15" class="box10" />
            </td>
        </tr>
        <tr>
            <th><!--{t string="tpl_Location<span class='attention'> *</span>_01" escape="none"}--></th>
            <td>
                <!--{assign var=key value="law_pref"}-->
                <!--{if $arrErr[$key]}-->
                    <span class="attention"><!--{$arrErr[$key]}--></span>
                    <!--{/if}-->
                    <!--{*
                    <select class="top" name="<!--{$arrForm[$key].keyname}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->">
                    <option value="" selected="selected"><!--{t string="tpl_Select a prefecture_01"}--></option>
                    <!--{html_options options=$arrPref selected=$arrForm[$key].value}-->
                    </select><br />
                    *}-->
                    <!--{assign var=key value="law_addr01"}-->
                    <!--{if $arrErr[$key]}-->
                    <span class="attention"><!--{$arrErr[$key]}--></span>
                    <!--{/if}-->
                    <input type="text" name="<!--{$arrForm[$key].keyname}-->" value="<!--{$arrForm[$key].value|h}-->" size="60" class="box60" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" /><span class="attention"> <!--{t string="tpl_(T_ARG1 characters max)_01" T_ARG1=$arrForm[$key].length}--></span>
                    <br />
                    <!--{$smarty.const.SAMPLE_ADDRESS1}--><br />
                    <!--{assign var=key value="law_addr02"}-->
                    <!--{if $arrErr[$key]}-->
                    <span class="attention"><!--{$arrErr[$key]}--></span>
                    <!--{/if}-->
                    <input type="text" name="<!--{$arrForm[$key].keyname}-->" value="<!--{$arrForm[$key].value|h}-->" size="60" class="box60" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" /><span class="attention"> <!--{t string="tpl_(T_ARG1 characters max)_01" T_ARG1=$arrForm[$key].length}--></span>
                    <br />
                    <!--{$smarty.const.SAMPLE_ADDRESS2}-->
            </td>
        </tr>
        <tr>
            <th><!--{t string="tpl_Phone Number<span class='attention'> *</span>_01" escape="none"}--></th>
            <td>
                <!--{assign var=key1 value="law_tel01"}-->
                <!--{assign var=key2 value="law_tel02"}-->
                <!--{assign var=key3 value="law_tel03"}-->
                <!--{if $arrErr[$key1] || $arrErr[$key2] || $arrErr[$key3]}-->
                    <span class="attention"><!--{$arrErr[$key1]}--></span>
                    <span class="attention"><!--{$arrErr[$key2]}--></span>
                    <span class="attention"><!--{$arrErr[$key3]}--></span>
                <!--{/if}-->
                <input type="text" name="<!--{$arrForm[$key1].keyname}-->" value="<!--{$arrForm[$key1].value|h}-->" maxlength="<!--{$arrForm[$key1].length}-->" style="<!--{$arrErr[$key1]|sfGetErrorColor}-->" size="6" class="box6" /> -
                <input type="text" name="<!--{$arrForm[$key2].keyname}-->" value="<!--{$arrForm[$key2].value|h}-->" maxlength="<!--{$arrForm[$key2].length}-->" style="<!--{$arrErr[$key2]|sfGetErrorColor}-->"    size="6" class="box6" /> -
                <input type="text" name="<!--{$arrForm[$key3].keyname}-->" value="<!--{$arrForm[$key3].value|h}-->" maxlength="<!--{$arrForm[$key3].length}-->" style="<!--{$arrErr[$key3]|sfGetErrorColor}-->" size="6" class="box6" />
            </td>
        </tr>
        <tr>
            <th><!--{t string="tpl_FAX_01"}--></th>
            <td>
                <!--{assign var=key1 value="law_fax01"}-->
                <!--{assign var=key2 value="law_fax02"}-->
                <!--{assign var=key3 value="law_fax03"}-->
                <!--{if $arrErr[$key1] || $arrErr[$key2] || $arrErr[$key3]}-->
                    <span class="attention"><!--{$arrErr[$key1]}--></span>
                    <span class="attention"><!--{$arrErr[$key2]}--></span>
                    <span class="attention"><!--{$arrErr[$key3]}--></span>
                <!--{/if}-->
                <input type="text" name="<!--{$arrForm[$key1].keyname}-->" value="<!--{$arrForm[$key1].value|h}-->" maxlength="<!--{$arrForm[$key1].length}-->" style="<!--{$arrErr[$key1]|sfGetErrorColor}-->" size="6" class="box6" /> -
                <input type="text" name="<!--{$arrForm[$key2].keyname}-->" value="<!--{$arrForm[$key2].value|h}-->" maxlength="<!--{$arrForm[$key2].length}-->" style="<!--{$arrErr[$key2]|sfGetErrorColor}-->" size="6" class="box6" /> -
                <input type="text" name="<!--{$arrForm[$key3].keyname}-->" value="<!--{$arrForm[$key3].value|h}-->" maxlength="<!--{$arrForm[$key3].length}-->" style="<!--{$arrErr[$key3]|sfGetErrorColor}-->" size="6" class="box6" />
            </td>
        </tr>
        <tr>
            <th><!--{t string="tpl_E-mail address<span class='attention'> *</span>_01" escape="none"}--></th>
            <td>
            <!--{assign var=key value="law_email"}-->
            <span class="attention"><!--{$arrErr[$key]}--></span>
            <input type="text" name="<!--{$arrForm[$key].keyname}-->" value="<!--{$arrForm[$key].value|h}-->" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" size="60" class="box60" /><span class="attention"> <!--{t string="tpl_(T_ARG1 characters max)_01" T_ARG1=$arrForm[$key].length}--></span>
            </td>
        </tr>
        <tr>
            <th><!--{t string="tpl_URL<span class='attention'> *</span>_01" escape="none"}--></th>
            <td>
            <!--{assign var=key value="law_url"}-->
            <span class="attention"><!--{$arrErr[$key]}--></span>
            <input type="text" name="<!--{$arrForm[$key].keyname}-->" value="<!--{$arrForm[$key].value|h}-->" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" size="60" class="box60" /><span class="attention"> <!--{t string="tpl_(T_ARG1 characters max)_01" T_ARG1=$arrForm[$key].length}--></span>
            </td>
        </tr>
        <tr>
            <th><!--{t string="tpl_Necessary fees other than for the product cost<span class='attention'> *</span>_01" escape="none"}--></th>
            <td>
            <!--{assign var=key value="law_term01"}-->
            <span class="attention"><!--{$arrErr[$key]}--></span>
            <textarea name="<!--{$arrForm[$key].keyname}-->" maxlength="<!--{$arrForm[$key].length}-->" cols="60" rows="8" class="area60" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" ><!--{$arrForm[$key].value|h}--></textarea><span class="attention"> <!--{t string="tpl_(T_ARG1 characters max)_01" T_ARG1=$arrForm[$key].length}--></span></td>
        </tr>
        <tr>
            <th><!--{t string="tpl_Order method<span class='attention'> *</span>_01" escape="none"}--></th>
            <td>
            <!--{assign var=key value="law_term02"}-->
            <span class="attention"><!--{$arrErr[$key]}--></span>
            <textarea name="<!--{$arrForm[$key].keyname}-->" maxlength="<!--{$arrForm[$key].length}-->" cols="60" rows="8" class="area60" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" ><!--{$arrForm[$key].value|h}--></textarea><span class="attention"> <!--{t string="tpl_(T_ARG1 characters max)_01" T_ARG1=$arrForm[$key].length}--></span></td>
        </tr>
        <tr>
            <th><!--{t string="tpl_Payment method<span class='attention'> *</span>_01" escape="none"}--></th>
            <td>
            <!--{assign var=key value="law_term03"}-->
            <span class="attention"><!--{$arrErr[$key]}--></span>
            <textarea name="<!--{$arrForm[$key].keyname}-->" maxlength="<!--{$arrForm[$key].length}-->" cols="60" rows="8" class="area60" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" ><!--{$arrForm[$key].value|h}--></textarea><span class="attention"> <!--{t string="tpl_(T_ARG1 characters max)_01" T_ARG1=$arrForm[$key].length}--></span></td>
        </tr>
        <tr>
            <th><!--{t string="tpl_Payment deadline<span class='attention'> *</span>_01" escape="none"}--></th>
            <td>
            <!--{assign var=key value="law_term04"}-->
            <span class="attention"><!--{$arrErr[$key]}--></span>
            <textarea name="<!--{$arrForm[$key].keyname}-->" maxlength="<!--{$arrForm[$key].length}-->" cols="60" rows="8" class="area60" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" ><!--{$arrForm[$key].value|h}--></textarea><span class="attention"> <!--{t string="tpl_(T_ARG1 characters max)_01" T_ARG1=$arrForm[$key].length}--></span></td>
        </tr>
        <tr>
            <th><!--{t string="tpl_Delivery period<span class='attention'> *</span>_01" escape="none"}--></th>
            <td>
            <!--{assign var=key value="law_term05"}-->
            <span class="attention"><!--{$arrErr[$key]}--></span>
            <textarea name="<!--{$arrForm[$key].keyname}-->" maxlength="<!--{$arrForm[$key].length}-->" cols="60" rows="8" class="area60" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" ><!--{$arrForm[$key].value|h}--></textarea><span class="attention"> <!--{t string="tpl_(T_ARG1 characters max)_01" T_ARG1=$arrForm[$key].length}--></span></td>
        </tr>
        <tr>
            <th><!--{t string="tpl_Returns and exchanges<span class='attention'> *</span>_01" escape="none"}--></th>
            <td>
            <!--{assign var=key value="law_term06"}-->
            <span class="attention"><!--{$arrErr[$key]}--></span>
            <textarea name="<!--{$arrForm[$key].keyname}-->" maxlength="<!--{$arrForm[$key].length}-->" cols="60" rows="8" class="area60" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" ><!--{$arrForm[$key].value|h}--></textarea><span class="attention"> <!--{t string="tpl_(T_ARG1 characters max)_01" T_ARG1=$arrForm[$key].length}--></span></td>
        </tr>
    </table>

    <div class="btn-area">
        <ul>
            <li><a class="btn-action" href="javascript:;" onclick="fnFormModeSubmit('form1', '<!--{$tpl_mode}-->', '', ''); return false;"><span class="btn-next"><!--{t string="tpl_Save and continue_01"}--></span></a></li>
        </ul>
    </div>
</div>
</form>
