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

<form name="form1" id="form1" method="POST" action="?" >
<input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
<input type="hidden" name="mode" value="" />
<input type="hidden" name="status" value="<!--{if $arrForm.status == ""}-->1<!--{else}--><!--{$arrForm.status}--><!--{/if}-->" />
<input type="hidden" name="search_pageno" value="<!--{$tpl_pageno}-->" >
<input type="hidden" name="order_id" value="" />
<div id="order" class="contents-main">
    <h2><!--{t string="tpl_Extraction conditions_01"}--></h2>
        <div class="btn">
        <!--{foreach key=key item=item from=$arrORDERSTATUS}-->
            <a
                class="btn-normal"
                style="padding-right: 1em;"
                <!--{if $key != $SelectedStatus}-->
                    href="javascript:;"
                    onclick="document.form1.search_pageno.value='1'; fnModeSubmit('search','status','<!--{$key}-->' ); return false;"
                <!--{/if}-->
            ><!--{$item}--></a>
        <!--{/foreach}-->
        </div>
    <h2><!--{t string="tpl_Response status change_01"}--></h2>
    <!--{* 登録テーブルここから *}-->
    <!--{if $tpl_linemax > 0}-->
        <div class="btn">
            <select name="change_status">
                <option value="" selected="selected" style="<!--{$Errormes|sfGetErrorColor}-->" ><!--{t string="tpl_Please make a selection_01"}--></option>
                <!--{foreach key=key item=item from=$arrORDERSTATUS}-->
                <!--{if $key ne $SelectedStatus}-->
                <option value="<!--{$key}-->" ><!--{$item}--></option>
                <!--{/if}-->
                <!--{/foreach}-->
                <option value="delete"><!--{t string="tpl_Remove_01"}--></option>
            </select>
            <a class="btn-normal" href="javascript:;" onclick="fnSelectCheckSubmit(); return false;"><span><!--{t string="tpl_Move_01"}--></span></a>
        </div>
        <span class="attention"><!--{t string="tpl_* When T_ARG1 or when changing to delete, restore the inventory count manually._01" T_ARG1=$arrORDERSTATUS[$smarty.const.ORDER_CANCEL]}--></span><br />

        <p class="remark">
            <!--{t string="tpl_T_ARG1 items were found._01" T_ARG1=$tpl_linemax}-->
            <!--{$tpl_strnavi}-->
        </p>

        <table class="list center">
            <col width="5%" />
            <col width="7%" />
            <col width="9%" />
            <col width="15%" />
            <col width="20%" />
            <col width="10%" />
            <col width="10%" />
            <col width="12%" />
            <col width="12%" />
            <tr>
                <th><label for="move_check"><!--{t string="tpl_Selection_01"}--></label> <input type="checkbox" name="move_check" id="move_check" onclick="fnAllCheck(this, 'input[name=move[]]')" /></th>
                <th><!--{t string="tpl_Response status_01"}--></th>
                <th><!--{t string="tpl_Order number_01"}--></th>
                <th><!--{t string="tpl_Date of order receipt_01"}--></th>
                <th><!--{t string="tpl_Name_02"}--></th>
                <th><!--{t string="tpl_Payment method_01"}--></th>
                <th><!--{t string="tpl_Purchase amount(&#36;)_01" escape="none"}--></th>
                <th><!--{t string="tpl_Date of deposit_01"}--></th>
                <th><!--{t string="tpl_Shipment date_01"}--></th>
            </tr>
            <!--{section name=cnt loop=$arrStatus}-->
            <!--{assign var=status value="`$arrStatus[cnt].status`"}-->
            <tr style="background:<!--{$arrORDERSTATUS_COLOR[$status]}-->;">
                <td><input type="checkbox" name="move[]" value="<!--{$arrStatus[cnt].order_id}-->" ></td>
                <td><!--{$arrORDERSTATUS[$status]}--></td>
                <td><a href="#" onclick="fnOpenWindow('./disp.php?order_id=<!--{$arrStatus[cnt].order_id}-->','order_disp','800','900'); return false;" ><!--{$arrStatus[cnt].order_id}--></a></td>
                <td><!--{$arrStatus[cnt].create_date|sfDispDBDate:false}--></td>
                <td><!--{$arrStatus[cnt].order_name01|h}--><!--{$arrStatus[cnt].order_name02|h}--></td>
                <!--{assign var=payment_id value=`$arrStatus[cnt].payment_id`}-->
                <td><!--{$arrStatus[cnt].payment_method|h}--></td>
                <td class="right"><!--{$arrStatus[cnt].total|number_format}--></td>
                <td><!--{if $arrStatus[cnt].payment_date != ""}--><!--{$arrStatus[cnt].payment_date|sfDispDBDate:false}--><!--{else}--><!--{t string="tpl_Not deposited_01"}--><!--{/if}--></td>
                <td><!--{if $arrStatus[cnt].status eq 5}--><!--{$arrStatus[cnt].commit_date|sfDispDBDate:false}--><!--{else}--><!--{t string="tpl_Not shipped_01"}--><!--{/if}--></td>
            </tr>
            <!--{/section}-->
        </table>

        <p><!--{$tpl_strnavi}--></p>

    <!--{elseif $arrStatus != "" & $tpl_linemax == 0}-->
        <div class="message">
            <!--{t string="tpl_No applicable data exists._01"}-->
        </div>
    <!--{/if}-->

    <!--{* 登録テーブルここまで *}-->
</div>
</form>


<script type="text/javascript">
<!--
function fnSelectCheckSubmit(){
    var selectflag = 0;
    var fm = document.form1;

    if (fm.change_status.options[document.form1.change_status.selectedIndex].value == "") {
        selectflag = 1;
    }

    if (selectflag == 1) {
        alert('<!--{t string="tpl_A selection box has not been selected_01"}-->');
        return false;
    }
    var i;
    var checkflag = 0;
    var max = fm["move[]"].length;

    if (max) {
        for (i=0;i<max;i++){
            if(fm["move[]"][i].checked == true) {
                checkflag = 1;
            }
        }
    } else {
        if (fm["move[]"].checked == true) {
            checkflag = 1;
        }
    }

    if (checkflag == 0){
        alert('<!--{t string="tpl_A checkbox has not been selected_01"}-->');
        return false;
    }

    if (selectflag == 0 && checkflag == 1) {
        document.form1.mode.value = 'update';
        document.form1.submit();
    }
}
//-->
</script>
