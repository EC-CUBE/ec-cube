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

<!--{include file="`$smarty.const.TEMPLATE_REALDIR`popup_header.tpl" subtitle="パスワードを忘れた方(完了ページ)"}-->

<div id="window_area">
    <h2 class="title">パスワードを忘れた方</h2>
    <p class="information">パスワードの発行が完了いたしました。ログインには下記のパスワードをご利用ください。<br />
        ※下記パスワードは、MYページの「会員登録内容変更」よりご変更いただけます。</p>
    <form action="?" method="post" name="form1">
        <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
        <div id="forgot">
            <!--{if $smarty.const.FORGOT_MAIL != 1}-->
                    <p><span class="attention"><!--{$arrForm.new_password}--></span></p>
            <!--{else}-->
            <p><span class="attention">ご登録メールアドレスに送付致しました。</span></p>
            <!--{/if}-->
        </div>
        <div class="btn_area">
            <ul>
                <li><a href="javascript:window.close()" onmouseover="chgImg('<!--{$TPL_URLPATH}-->img/button/btn_close_on.jpg','close');" onmouseout="chgImg('<!--{$TPL_URLPATH}-->img/button/btn_close.jpg','close');"><img src="<!--{$TPL_URLPATH}-->img/button/btn_close.jpg" alt="閉じる" name="close" id="close" /></a></li>
            </ul>
        </div>
    </form>
</div>

<!--{include file="`$smarty.const.TEMPLATE_REALDIR`popup_footer.tpl"}-->
