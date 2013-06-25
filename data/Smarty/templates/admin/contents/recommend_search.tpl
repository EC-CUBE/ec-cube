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

function func_submit( id ){
    var fm = window.opener.document.form<!--{$rank|h}-->;
    fm.product_id.value = id;
    fm.mode.value = 'set_item';
    fm.rank.value = '<!--{$rank|h}-->';
    fm.submit();
    window.close();
    return false;
}
//-->
</script>

<!--▼検索フォーム-->
<form name="form1" id="form1" method="post" action="#">
<input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
<input name="mode" type="hidden" value="search" />
<input name="search_pageno" type="hidden" value="" />
    <table class="form">
        <col width="20%" />
        <col width="80%" />
        <tr>
            <th><!--{t string="tpl_Category_01"}--></th>
            <td>
                <select name="search_category_id">
                    <option value="" selected="selected"><!--{t string="tpl_Please make a selection_01"}--></option>
                    <!--{html_options options=$arrCatList selected=$arrForm.search_category_id}-->
                </select>
            </td>
        </tr>
        <tr>
            <th><!--{t string="tpl_Product code_01"}--></th>
            <td><input type="text" name="search_product_code" value="<!--{$arrForm.search_product_code}-->" size="35" class="box35" /></td>
        </tr>
        <tr>
            <th><!--{t string="tpl_Product name_01"}--></th>
            <td><input type="text" name="search_name" value="<!--{$arrForm.search_name}-->" size="35" class="box35" /></td>
        </tr>
    </table>
    <div class="btn-area">
        <ul>
            <li><a class="btn-action" href="javascript:;" onclick="fnFormModeSubmit('form1', 'search', '', ''); return false;"><span class="btn-next"><!--{t string="tpl_Search_01"}--></span></a></li>
        </ul>
    </div>
    <!--{* ▼検索結果表示 *}-->
    <!--{if is_numeric($tpl_linemax)}-->
    <p><!--{t string="tpl_T_ARG1 items were found._01" T_ARG1=$tpl_linemax}--></p>
    <!--{$tpl_strnavi}-->

    <table class="list">
        <col width="15%" />
        <col width="12.5%" />
        <col width="60%" />
        <col width="12.5%" />
        <tr>
            <th><!--{t string="tpl_Product image_01"}--></th>
            <th><!--{t string="tpl_Product code_01"}--></th>
            <th><!--{t string="tpl_Product name_01"}--></th>
            <th><!--{t string="tpl_Confirm_01"}--></th>
        </tr>

        <!--{foreach name=loop from=$arrProducts item=arr}-->
        <!--▼商品<!--{$smarty.foreach.loop.iteration}-->-->
        <tr>
            <td class="center">
                <img src="<!--{$smarty.const.ROOT_URLPATH}-->resize_image.php?image=<!--{$arr.main_list_image|sfNoImageMainList|h}-->&width=65&height=65" alt="" />
            </td>
            <td>
                <!--{assign var=codemin value=`$arr.product_code_min`}-->
                <!--{assign var=codemax value=`$arr.product_code_max`}-->
                <!--{* 商品コード *}-->
                <!--{if $codemin != $codemax}-->
                    <!--{$codemin|h}--><!--{t string="-"}--><!--{$codemax|h}-->
                <!--{else}-->
                    <!--{$codemin|h}-->
                <!--{/if}-->
            </td>
            <td><!--{$arr.name|h}--></td>
            <td class="center"><a href="" onClick="return func_submit(<!--{$arr.product_id}-->)"><!--{t string="tpl_Confirm_01"}--></a></td>
        </tr>
        <!--▲商品<!--{$smarty.foreach.loop.iteration}-->-->    
        <!--{/foreach}-->
        <!--{if !$tpl_linemax>0}-->
        <tr>
            <td colspan="4"><!--{t string="tpl_There is no product registered_01"}--></td>
        </tr>
        <!--{/if}-->
        
    </table>
    <!--{/if}-->
    <!--{* ▲検索結果表示 *}-->

</form>

<!--{include file="`$smarty.const.TEMPLATE_ADMIN_REALDIR`admin_popup_footer.tpl"}-->
