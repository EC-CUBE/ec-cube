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
<!--

function func_return(){
    document.form1.mode.value = "return";
    document.form1.submit();
}

//-->
</script>


<form name="form1" id="form1" method="post" action="?">
    <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
    <input type="hidden" name="mode" value="complete" />

    <!--{foreach from=$arrForm key=key item=item}-->
        <!--{if $key ne "mode" && $key ne "subm" && $key ne $smarty.const.TRANSACTION_ID_NAME}-->
            <input type="hidden" name="<!--{$key|h}-->" value="<!--{$item|h}-->" />
        <!--{/if}-->
    <!--{/foreach}-->

    <!-- 検索条件の保持 -->
    <!--{foreach from=$arrSearchData key="key" item="item"}-->
        <!--{if $key ne "customer_id" && $key ne "mode" && $key ne "edit_customer_id" && $key ne $smarty.const.TRANSACTION_ID_NAME}-->
            <!--{if is_array($item)}-->
                <!--{foreach item=c_item from=$item}-->
                    <input type="hidden" name="search_data[<!--{$key|h}-->][]" value="<!--{$c_item|h}-->" />
                <!--{/foreach}-->
            <!--{else}-->
                <input type="hidden" name="search_data[<!--{$key|h}-->]" value="<!--{$item|h}-->" />
            <!--{/if}-->
        <!--{/if}-->
    <!--{/foreach}-->

    <div id="customer" class="contents-main">
        <table class="form">
            <tr>
                <th><!--{t string="tpl_Member ID_01"}--></th>
                <td><!--{$arrForm.customer_id|h}--></td>
            </tr>
            <tr>
                <th><!--{t string="tpl_Member status_01"}--></th>
                <td><!--{if $arrForm.status == 1}--><!--{t string="tpl_Temporary member_01"}--><!--{else}--><!--{t string="tpl_full member_01"}--><!--{/if}--></td>
            </tr>
            <tr>
                <th><!--{t string="tpl_Name_02"}--></th>
                <td><!--{t string="tpl_T_ARG1 T_ARG2_01" T_ARG1=$arrForm.name01|h T_ARG2=$arrForm.name02|h}--></td>
            </tr>
            <tr>
                <th><!--{t string="tpl_Postal code_01"}--></th>
                <!--{* <td><!--{t string="tpl_Postal code mark_01"}--> <!--{$arrForm.zip01|h}--> - <!--{$arrForm.zip02|h}--></td> *}-->
                <td><!--{t string="tpl_Postal code mark_01"}--> <!--{$arrForm.zipcode|h}--></td>
            </tr>
            <tr>
                <th><!--{t string="tpl_Address_01"}--></th>
                <td><!--{$arrPref[$arrForm.pref]|h}--><!--{$arrForm.addr01|h}--> <!--{$arrForm.addr02|h}--></td>
            </tr>
            <tr>
                <th><!--{t string="tpl_E-mail address_01"}--></th>
                <td><!--{$arrForm.email|h}--></td>
            </tr>
            <tr>
                <th><!--{t string="tpl_Mobile e-mail address_01"}--></th>
                <td><!--{$arrForm.email_mobile|h}--></td>
            </tr>
            <tr>
                <th><!--{t string="tpl_Telephone number_01"}--></th>
                <td><!--{$arrForm.tel01|h}--> - <!--{$arrForm.tel02|h}--> - <!--{$arrForm.tel03|h}--></td>
            </tr>
            <tr>
                <th><!--{t string="tpl_FAX_01"}--></th>
                <td><!--{if strlen($arrForm.fax01) > 0}--><!--{$arrForm.fax01|h}--> - <!--{$arrForm.fax02|h}--> - <!--{$arrForm.fax03|h}--><!--{else}--><!--{t string="tpl_Not registered_01"}--><!--{/if}--></td>
            </tr>
            <tr>
                <th><!--{t string="tpl_Gender_01"}--></th>
                <td><!--{$arrSex[$arrForm.sex]|h}--></td>
            </tr>
            <tr>
                <th><!--{t string="tpl_Occupation_01"}--></th>
                <td><!--{$arrJob[$arrForm.job]|default_t:"tpl_Not registered_01"|h}--></td>
            </tr>
            <tr>
                <th><!--{t string="tpl_Date of birth_01"}--></th>
                <td>
                    <!--{if strlen($arrForm.year) > 0 && strlen($arrForm.month) > 0 && strlen($arrForm.day) > 0}-->
                        <!--{t string="tpl_s1YearT_ARG2MonthT_ARG3Day_01" T_ARG1=$arrForm.year|h T_ARG2=$arrForm.month|h T_ARG3=$arrForm.day|h }-->
                    <!--{else}-->
                        <!--{t string="tpl_Not registered_01"}-->
                    <!--{/if}-->
                </td>
            </tr>
            <tr>
                <th><!--{t string="tpl_Password_01"}--></th>
                <td><!--{$smarty.const.DEFAULT_PASSWORD}--></td>
            </tr>
            <tr>
                <th><!--{t string="tpl_Hint for when you have forgotten your password_01"}--></th>
                <td>
                    <!--{t string="tpl_Question:_01"}--> <!--{$arrReminder[$arrForm.reminder]|h}--><br />
                    <!--{t string="tpl_Answer:_01"}--> <!--{$smarty.const.DEFAULT_PASSWORD}-->
                </td>
            </tr>
            <tr>
                <th><!--{t string="tpl_Mail magazine_01"}--></th>
                <td><!--{if $arrForm.mailmaga_flg eq 1}--><!--{t string="tpl_HTML_01"}--><!--{elseif $arrForm.mailmaga_flg eq 2}--><!--{t string="tpl_Text_01"}--><!--{else}--><!--{t string="tpl_Do not wish to receive"}--><!--{/if}--></td>
            </tr>
            <tr>
                <th><!--{t string="tpl_Memo for SHOP_01"}--></th>
                <td><!--{$arrForm.note|h|nl2br|default_t:"tpl_Not registered_01"}--></td>
            </tr>
            <tr>
                <th><!--{t string="tpl_Points in possession_01"}--></th>
                <td><!--{t string="pt_prefix"}--><!--{$arrForm.point|default:"0"|h}--> <!--{t string="pt_suffix"}--></td>
            </tr>
        </table>
        <div class="btn-area">
            <ul>
                <li><a class="btn-action" href="javascript:;" onclick="func_return(); return false;"><span class="btn-prev"><!--{t string="tpl_Return to the edit screen_01"}--></span></a></li>
                <li><a class="btn-action" href="javascript:;" onclick="fnFormModeSubmit('form1', 'complete', '', ''); return false;"><span class="btn-next"><!--{t string="tpl_Save and continue_01"}--></span></a></li>
            </ul>
        </div>
    </div>
</form>
