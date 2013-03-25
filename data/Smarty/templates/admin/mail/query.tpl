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

<!--{include file="`$smarty.const.TEMPLATE_ADMIN_REALDIR`admin_popup_header.tpl"}-->

<script type="text/javascript">
<!--
self.moveTo(20,20);self.focus();
//-->
</script>

<form name="form1" id="form1" method="post" action="?">
<input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
<div id="mail" class="contents-main">
    <h2><!--{t string="tpl_Delivery conditions_01"}--></h2>

    <table class="form">
        <tr>
            <th><!--{t string="tpl_Member ID_01"}--></th>
            <td><!--{$arrSearchData.search_customer_id|default_t:"tpl_(Not designated)_01"|h}--></td>
        </tr>
        <tr>
            <th><!--{t string="tpl_Name_02"}--></th>
            <td><!--{$arrSearchData.search_name|default_t:"tpl_(Not designated)_01"|h}--></td>
        </tr>
        <tr>
            <th><!--{t string="tpl_Gender_01"}--></th>
            <td>
            <!--{assign var=key value="search_sex"}-->
            <!--{if is_array($arrSearchData[$key])}-->
                <!--{foreach item=item from=$arrSearchData[$key]}-->
                    <!--{$arrSex[$item]|h}-->
                <!--{/foreach}-->
            <!--{else}--><!--{t string="tpl_(Not designated)_01"}--><!--{/if}-->
            </td>
        </tr>
        <tr>
            <th><!--{t string="tpl_Birth month_01"}--></th>
            <td><!--{if $arrSearchData.search_birth_month}--><!--{t string="tpl_T_ARG1_02" T_ARG1=$arrSearchData.search_birth_month|h}--><!--{else}--><!--{t string="tpl_(Not designated)_01"}--><!--{/if}--></td>
        </tr>
        <tr>
            <th><!--{t string="tpl_Birthday_01"}--></th>
            <td>
            <!--{if $arrSearchData.search_b_start_year}-->
                <!--{t string="tpl_s1YearT_ARG2MonthT_ARG3Day_01" T_ARG1=$arrSearchData.search_b_start_year T_ARG2=$arrSearchData.search_b_start_month T_ARG3=$arrSearchData.search_b_start_day}-->&nbsp;<!--{t string="-"}-->
                <!--{if $arrSearchData.search_b_end_year}-->&nbsp;<!--{t string="tpl_s1YearT_ARG2MonthT_ARG3Day_01" T_ARG1=$arrSearchData.search_b_end_year T_ARG2=$arrSearchData.search_b_end_month T_ARG3=$arrSearchData.search_b_end_day}--><!--{/if}-->
            <!--{else}--><!--{t string="tpl_(Not designated)_01"}--><!--{/if}-->
            </td>
        </tr>
        <tr>
            <th><!--{t string="tpl_E-mail address_01"}--></th>
            <td><!--{$arrSearchData.search_email|default_t:"tpl_(Not designated)_01"|h}--></td>
        </tr>
        <tr>
            <th><!--{t string="tpl_Mobile e-mail address_01"}--></th>
            <td><!--{$arrSearchData.search_email_mobile|default_t:"tpl_(Not designated)_01"|h}--></td>
        </tr>
        <tr>
            <th><!--{t string="tpl_Telephone number_01"}--></th>
            <td><!--{$arrSearchData.search_tel|default_t:"tpl_(Not designated)_01"|h}--></td>
        </tr>
        <tr>
            <th><!--{t string="tpl_Occupation_02"}--></th>
            <td>
            <!--{assign var=key value="search_job"}-->
            <!--{if is_array($arrSearchData[$key])}-->
                <!--{foreach item=item from=$arrSearchData[$key]}-->
                    <!--{$arrJob[$item]|h}-->
                <!--{/foreach}-->
            <!--{else}--><!--{t string="tpl_(Not designated)_01"}--><!--{/if}-->
            </td>
        </tr>
        <tr>
            <th><!--{t string="tpl_Purchase amount_01"}--></th>
            <td>
                <!--{if $arrSearchData.search_buy_total_from == null}--><!--{t string="tpl_(Not designated)_01"}--><!--{else}--><!--{t string="tpl_&#36; T_ARG1_01" escape="none" T_ARG1=$arrSearchData.search_buy_total_from|h}--><!--{/if}--> <!--{t string="-"}-->
                <!--{if $arrSearchData.search_buy_total_to == null}--><!--{t string="tpl_(Not designated)_01"}--><!--{else}--><!--{t string="tpl_&#36; T_ARG1_01" escape="none" T_ARG1=$arrSearchData.search_buy_total_to|h}--><!--{/if}-->
            </td>
        </tr>
        <tr>
            <th><!--{t string="tpl_Purchase frequency_01"}--></th>
            <td>
                <!--{if $arrSearchData.search_buy_times_from == null}--><!--{t string="tpl_(Not designated)_01"}--><!--{else}--><!--{t string="times_prefix"}--><!--{$arrSearchData.search_buy_times_from|h}--><!--{t string="times_suffix"}--><!--{/if}-->
                <!--{t string="-"}-->
                <!--{if $arrSearchData.search_buy_times_to == null}--><!--{t string="tpl_(Not designated)_01"}--><!--{else}--><!--{t string="times_prefix"}--><!--{$arrSearchData.search_buy_times_to|h}--><!--{t string="times_suffix"}--><!--{/if}-->
            </td>
        </tr>
        <tr>
            <th><!--{t string="tpl_Registration/update date_01"}--></th>
            <td>
            <!--{if $arrSearchData.search_start_year}-->
                <!--{t string="tpl_s1YearT_ARG2MonthT_ARG3Day_01" T_ARG1=$arrSearchData.search_start_year T_ARG2=$arrSearchData.search_start_month T_ARG3=$arrSearchData.search_start_day}-->&nbsp;<!--{t string="-"}-->
            
            <!--{if $arrSearchData.search_end_year}-->&nbsp;<!--{t string="tpl_s1YearT_ARG2MonthT_ARG3Day_01" T_ARG1=$arrSearchData.search_end_year T_ARG2=$arrSearchData.search_end_month T_ARG3=$arrSearchData.search_end_day}--><!--{/if}-->
            <!--{else}--><!--{t string="tpl_(Not designated)_01"}--><!--{/if}-->
            </td>
        </tr>
        <tr>
            <th><!--{t string="tpl_Final purchase date_01"}--></th>
            <td>
            <!--{if $arrSearchData.search_buy_start_year}-->
                <!--{t string="tpl_s1YearT_ARG2MonthT_ARG3Day_01" T_ARG1=$arrSearchData.search_buy_start_year T_ARG2=$arrSearchData.search_buy_start_month T_ARG3=$arrSearchData.search_buy_start_day}-->&nbsp;<!--{t string="-"}-->
                <!--{if $arrSearchData.search_buy_end_year}-->&nbsp;<!--{t string="tpl_s1YearT_ARG2MonthT_ARG3Day_01" T_ARG1=$arrSearchData.search_buy_end_year T_ARG2=$arrSearchData.search_buy_end_month T_ARG3=$arrSearchData.search_buy_end_day}--><!--{/if}-->
            <!--{else}--><!--{t string="tpl_(Not designated)_01"}--><!--{/if}-->
            </td>
        </tr>
        <tr>
            <th><!--{t string="tpl_Purchased product name_01"}--></th>
            <td><!--{$arrSearchData.search_buy_product_name|default_t:"tpl_(Not designated)_01"|h}--></td>
        </tr>
        <tr>
            <th><!--{t string="tpl_Purchased product code_01"}--></th>
            <td><!--{$arrSearchData.search_buy_product_code|default_t:"tpl_(Not designated)_01"|h}--></td>
        </tr>
        <tr>
            <th><!--{t string="tpl_Category_01"}--></th>
            <td>
            <!--{if $arrSearchData.search_category_id}-->
                <!--{if $arrCatList[$arrSearchData.search_category_id]}-->
                    <!--{$arrCatList[$arrSearchData.search_category_id]|h}-->
                <!--{else}--><!--{t string="tpl_(Categories already deleted)_01"}--><!--{/if}-->
            <!--{else}--><!--{t string="tpl_(Not designated)_01"}--><!--{/if}-->
            </td>
        </tr>
        <tr>
            <th><!--{t string="tpl_Delivery format_01"}--></th>
            <td><!--{$arrHtmlmail[$arrSearchData.search_htmlmail]|default_t:"tpl_(Not designated)_01"|h}--></td>
        </tr>
        <tr>
            <th><!--{t string="tpl_Type of delivery e-mail address_01"}--></th>
            <td><!--{$arrMailType[$arrSearchData.search_mail_type]|default_t:"tpl_(Not designated)_01"|h}--></td>
        </tr>
    </table>

    <div class="btn-area">
        <ul>
            <li><a class="btn-action" href="javascript:;" onclick="window.close(); return false;"><span class="btn-next"><!--{t string="tpl_Close the window._01"}--></span></a></li>
        </ul>
    </div>
</div>
</form>

<!--{include file="`$smarty.const.TEMPLATE_ADMIN_REALDIR`admin_popup_footer.tpl"}-->
