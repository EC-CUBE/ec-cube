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

<!--{strip}-->
    <!--{if !$tpl_valid_phone_id}-->
        <!--▼会員登録がお済みでないお客様-->
        ■初めてご注文の方<br>
        (新規ご登録)<br>
        <form name="member_form" id="member_form" method="post" action="<!--{$smarty.const.ROOT_URLPATH}-->entry/kiyaku.php">
            <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->">
            <div align="center"><input type="submit" value="新規登録"></div><br>
        </form>
        <!--▲まだ会員登録されていないお客様-->
    <!--{/if}-->

    <!--▼会員登録がお済みのお客様-->
    <form name="member_form" id="member_form" method="post" action="?">
        <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->">
        <input type="hidden" name="mode" value="login">
        <!--{if !$tpl_valid_phone_id}-->
            ■以前にご注文された方<br>
            (モバイル又はPCでご登録済み)<br>
            ▼メールアドレス<br>
            <!--{assign var=key value="login_email"}-->
            <font color="#FF0000"><!--{$arrErr[$key]}--></font>
            <input type="text" name="<!--{$key}-->" value="<!--{$tpl_login_email|h}-->" size="40" istyle="3"><br>
        <!--{else}-->
            <input type="hidden" name="login_email" value="dummy">
        <!--{/if}-->
        ▼パスワード<br>
        <!--{assign var=key value="login_pass"}--><font color="#FF0000"><!--{$arrErr[$key]}--></font>
        <input type="password" name="<!--{$key}-->" size="40" istyle="3"><br>
        <center><input type="submit" value="送信" name="log"></center><br>
        <a href="<!--{$smarty.const.ROOT_URLPATH}-->forgot/<!--{$smarty.const.DIR_INDEX_PATH}-->">パスワードをお忘れの方はこちら</a><br>
    </form>
    <!--▲会員登録がお済のお客様-->
<!--{/strip}-->
