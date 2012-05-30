<!--{*
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
 *}-->

<section id="windowcolumn">
    <h2 class="title">パスワードを忘れた方</h2>
    <div class="intro">
        <p>パスワードの発行が完了いたしました。ログインには下記のパスワードをご利用ください。</p>
    </div>
    <form action="?" method="post" name="form1">
        <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />

        <div class="window_area clearfix">
            <!--{if $smarty.const.FORGOT_MAIL != 1}-->
                <input id="completebox" type="text" value="<!--{$arrForm.new_password}-->" readonly="readonly" />
            <!--{else}-->
                <p  class="attention">ご登録メールアドレスに送付致しました。</p>
            <!--{/if}-->
            <hr />
            <p>※パスワードは、MYページの「会員登録内容変更」よりご変更いただけます。</p>
        </div>

        <div class="btn_area">
            <p><a rel="external" href="<!--{$smarty.const.HTTPS_URL|sfTrimURL}-->/mypage/login.php" class="btn_sub btn_close">ログイン画面へ</a></p>
        </div>
    </form>
</section>
