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
    <font color="#ff0000">※新しくパスワードを発行いたしますので、お忘れになったパスワードはご利用できなくなります。</font>
    <br>
    <br>

    <!--{if $errmsg}-->
        <font color="#ff0000"><!--{$errmsg}--></font><br>
    <!--{/if}-->

    <!--{if @$tpl_kara_mail_to != ''}-->
        ■ご登録時のメールアドレスからメールを送れる方は、次のリンクをクリックして空メールを送信してください。<br>
        <br>
        <center><a href="mailto:<!--{$tpl_kara_mail_to|u}-->">メール送信</a></center>

        <br>

        ■メールを送れない方は、ご登録時のメールアドレスとお名前を入力して「次へ」ボタンをクリックしてください。<br>
    <!--{else}-->
        ご登録時のメールアドレスとお名前を入力して「次へ」ボタンをクリックしてください。<br>
    <!--{/if}-->
    <br>

    <form action="?" method="post">
        <input type="hidden" name="mode" value="mail_check">
        <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->">

        メールアドレス：<font color="#FF0000"><!--{$arrErr.email}--></font><br>
        <input type="text" name="email" value="<!--{$arrForm.email|default:$tpl_login_email|h}-->" size="50" istyle="3"><br>
        <br>
        お名前：<font color="#FF0000"><!--{$arrErr.name01}--><!--{$arrErr.name02}--></font><br>
        姓<input type="text" name="name01" value="<!--{$arrForm.name01|h}-->" maxlength="<!--{$smarty.const.STEXT_LEN}-->" istyle="1"><br>
        名<input type="text" name="name02" value="<!--{$arrForm.name02|h}-->" maxlength="<!--{$smarty.const.STEXT_LEN}-->" istyle="1"><br>

        <br>
        <center><input type="submit" value="次へ" name="next"></center>
    </form>
<!--{/strip}-->
