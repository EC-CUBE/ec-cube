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
    <!--{if $arrErr.deli != ""}-->
        <font color="#ff0000"><!--{$arrErr.deli}--></font>
    <!--{/if}-->

        <!--{section name=cnt loop=$arrAddr}-->
        <form method="post" action="?">
            <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->">
            <input type="hidden" name="uniqid" value="<!--{$tpl_uniqid}-->">
            <input type="hidden" name="deli" value="<!--{$smarty.section.cnt.iteration}-->">
            <input type="hidden" name="mode" value="customer_addr">
            <!--{if $smarty.section.cnt.first}-->
                <input type="hidden" name="other_deliv_id" value="">
                <input type="hidden" name="deliv_check" value="-1">
            <!--{else}-->
                <input type="hidden" name="other_deliv_id" value="<!--{$arrAddr[cnt].other_deliv_id}-->">
                <input type="hidden" name="deliv_check" value="<!--{$arrAddr[cnt].other_deliv_id}-->">
                <br>
            <!--{/if}-->

            <!--{assign var=key1 value=$arrAddr[cnt].pref}-->
            <!--{assign var=key2 value=$arrAddr[cnt].country_id}-->
            ■お届け先<!--{$smarty.section.cnt.iteration}--><br>
            <!--{if $smarty.const.FORM_COUNTRY_ENABLE}-->
            国：<!--{$arrCountry[$key2]|h}--><br>
            <!--{/if}-->
            〒<!--{$arrAddr[cnt].zip01}-->-<!--{$arrAddr[cnt].zip02}--><br>
            <!--{$arrPref[$key1]}--><!--{$arrAddr[cnt].addr01|h}--><br>
            <!--{if $arrAddr[cnt].addr02 != ""}-->
                <!--{$arrAddr[cnt].addr02|h}--><br>
            <!--{/if}-->
            <!--{$arrAddr[cnt].name01}--> <!--{$arrAddr[cnt].name02}--><br>
            <center><input type="submit" value="ここに送る"></center>
        </form>
        <!--{if !$smarty.section.cnt.first}-->
            <!--{* リンクにした方がすっきりしますが、お届け先削除処理がother_deliv_idをPOSTしか受け付けていないので、ボタンで統一しています *}-->
            <!--{* <a href="<!--{$smarty.const.ROOT_URLPATH}-->mypage/delivery_addr.php?page=<!--{$smarty.server.SCRIPT_NAME|h}-->&amp;other_deliv_id=<!--{$arrAddr[cnt].other_deliv_id}-->&amp;uniqid=<!--{$tpl_uniqid}-->">変更</a> *}-->
            <form method="get" action="<!--{$smarty.const.ROOT_URLPATH}-->mypage/delivery_addr.php">
                <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->">
                <input type="hidden" name="page" value="<!--{$smarty.server.SCRIPT_NAME|h}-->">
                <input type="hidden" name="other_deliv_id" value="<!--{$arrAddr[cnt].other_deliv_id}-->">
                <center><input type="submit" value="お届け先情報変更"></center>
            </form>

            <form method="post" action="?">
                <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->">
                <input type="hidden" name="uniqid" value="<!--{$tpl_uniqid}-->">
                <input type="hidden" name="mode" value="delete">
                <input type="hidden" name="other_deliv_id" value="<!--{$arrAddr[cnt].other_deliv_id}-->">
                <center><input type="submit" value="お届け先を削除"></center>
            </form>
        <!--{/if}-->
    <!--{/section}-->

    <br>

    ■新しいお届け先を追加する<br>
    <form method="post" action="<!--{$smarty.const.ROOT_URLPATH}-->mypage/delivery_addr.php">
    <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->">
    <input type="hidden" name="ParentPage" value="<!--{$smarty.const.DELIV_URLPATH}-->">
    <center><input type="submit" value="新規登録"></center>
    </form>

    <!--{if $smarty.const.USE_MULTIPLE_SHIPPING !== false}-->
        <br>

        ■お届け先を複数指定する<br>
        <form method="post" action="?">
            <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->">
            <input type="hidden" name="uniqid" value="<!--{$tpl_uniqid}-->">
            <input type="hidden" name="mode" value="multiple">
            <center><input type="submit" value="複数お届け先"></center>
        </form>
    <!--{/if}-->
<!--{/strip}-->
