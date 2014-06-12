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

<!--{strip}-->
    <form method="post" action="<!--{$smarty.const.MOBILE_SHOPPING_PAYMENT_URLPATH}-->">
        <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->">
        <input type="hidden" name="mode" value="select_deliv">
        <input type="hidden" name="uniqid" value="<!--{$tpl_uniqid}-->">
        ■配送方法 <font color="#FF0000">*</font><br>
        <!--{assign var=key value="deliv_id"}-->
        <!--{if $arrErr[$key] != ""}-->
            <font color="#FF0000"><!--{$arrErr[$key]}--></font>
        <!--{/if}-->
        <!--{section name=cnt loop=$arrDeliv}-->
            <input type="radio" name="<!--{$key}-->" value="<!--{$arrDeliv[cnt].deliv_id}-->" <!--{$arrDeliv[cnt].deliv_id|sfGetChecked:$arrForm[$key].value}-->>
            <!--{$arrDeliv[cnt].name|h}-->
            <br>
        <!--{/section}-->
        <br>

        <center><input type="submit" value="次へ"></center>
    </form>

    <form action="<!--{$tpl_back_url|h}-->" method="get">
        <!--{if $is_multiple}-->
            <input type="hidden" name="from" value="multiple">
        <!--{/if}-->
        <center><input type="submit" name="return" value="戻る"></center>
    </form>
<!--{/strip}-->
