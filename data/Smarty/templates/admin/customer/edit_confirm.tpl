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
                <th><!--{t string="tpl_207"}--></th>
                <td><!--{$arrForm.customer_id|h}--></td>
            </tr>
            <tr>
                <th><!--{t string="tpl_209"}--></th>
                <td><!--{if $arrForm.status == 1}--><!--{t string="tpl_238"}--><!--{else}--><!--{t string="tpl_239"}--><!--{/if}--></td>
            </tr>
            <tr>
                <th><!--{t string="tpl_208"}--></th>
                <td><!--{t string="tpl_240" T_FIELD1=$arrForm.name01|h T_FIELD2=$arrForm.name02|h}--></td>
            </tr>
            <tr>
                <th><!--{t string="tpl_Postal code_01"}--></th>
                <!--{* <td><!--{t string="tpl_106"}--> <!--{$arrForm.zip01|h}--> - <!--{$arrForm.zip02|h}--></td> *}-->
                <td><!--{t string="tpl_106"}--> <!--{$arrForm.zipcode|h}--></td>
            </tr>
            <tr>
                <th><!--{t string="tpl_212"}--></th>
                <td><!--{$arrPref[$arrForm.pref]|h}--><!--{$arrForm.addr01|h}--><!--{$arrForm.addr02|h}--></td>
            </tr>
            <tr>
                <th><!--{t string="tpl_108"}--></th>
                <td><!--{$arrForm.email|h}--></td>
            </tr>
            <tr>
                <th><!--{t string="tpl_213"}--></th>
                <td><!--{$arrForm.email_mobile|h}--></td>
            </tr>
            <tr>
                <th><!--{t string="tpl_241"}--></th>
                <td><!--{$arrForm.tel01|h}--> - <!--{$arrForm.tel02|h}--> - <!--{$arrForm.tel03|h}--></td>
            </tr>
            <tr>
                <th><!--{t string="tpl_FAX_01"}--></th>
                <td><!--{if strlen($arrForm.fax01) > 0}--><!--{$arrForm.fax01|h}--> - <!--{$arrForm.fax02|h}--> - <!--{$arrForm.fax03|h}--><!--{else}--><!--{t string="tpl_242"}--><!--{/if}--></td>
            </tr>
            <tr>
                <th><!--{t string="tpl_215"}--></th>
                <td><!--{$arrSex[$arrForm.sex]|h}--></td>
            </tr>
            <tr>
                <th><!--{t string="tpl_216"}--></th>
                <td><!--{$arrJob[$arrForm.job]|default_t:"tpl_242"|h}--></td>
            </tr>
            <tr>
                <th><!--{t string="tpl_217"}--></th>
                <td>
                    <!--{if strlen($arrForm.year) > 0 && strlen($arrForm.month) > 0 && strlen($arrForm.day) > 0}-->
                        <!--{t string="tpl_726" T_FIELD1=$arrForm.year|h T_FIELD2=$arrForm.month|h T_FIELD3=$arrForm.day|h }-->
                    <!--{else}-->
                        <!--{t string="tpl_242"}-->
                    <!--{/if}-->
                </td>
            </tr>
            <tr>
                <th><!--{t string="tpl_218"}--></th>
                <td><!--{$smarty.const.DEFAULT_PASSWORD}--></td>
            </tr>
            <tr>
                <th><!--{t string="tpl_243"}--></th>
                <td>
                    <!--{t string="tpl_222"}--> <!--{$arrReminder[$arrForm.reminder]|h}--><br />
                    <!--{t string="tpl_223"}--> <!--{$smarty.const.DEFAULT_PASSWORD}-->
                </td>
            </tr>
            <tr>
                <th><!--{t string="tpl_224"}--></th>
                <td><!--{if $arrForm.mailmaga_flg eq 1}--><!--{t string="tpl_244"}--><!--{elseif $arrForm.mailmaga_flg eq 2}--><!--{t string="tpl_245"}--><!--{else}--><!--{t string="tpl_246"}--><!--{/if}--></td>
            </tr>
            <tr>
                <th><!--{t string="tpl_225"}--></th>
                <td><!--{$arrForm.note|h|nl2br|default_t:"tpl_242"}--></td>
            </tr>
            <tr>
                <th><!--{t string="tpl_226"}--></th>
                <td><!--{t string="pt_prefix"}--><!--{$arrForm.point|default:"0"|h}--> <!--{t string="pt_suffix"}--></td>
            </tr>
        </table>
        <div class="btn-area">
            <ul>
                <li><a class="btn-action" href="javascript:;" onclick="func_return(); return false;"><span class="btn-prev"><!--{t string="tpl_247"}--></span></a></li>
                <li><a class="btn-action" href="javascript:;" onclick="fnFormModeSubmit('form1', 'complete', '', ''); return false;"><span class="btn-next"><!--{t string="tpl_Save and continue_01"}--></span></a></li>
            </ul>
        </div>
    </div>
</form>
