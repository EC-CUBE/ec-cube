<!--{*
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
 *}-->

<script>
    function ajaxLogin() {
        var checkLogin = eccube.checkLoginFormInputted('member_form');

        if (checkLogin == false) {
            return false;
        } else {
            var postData = new Object;
            postData['<!--{$smarty.const.TRANSACTION_ID_NAME}-->'] = "<!--{$transactionid}-->";
            postData['mode'] = 'login';
            postData['login_email'] = $('input[type=email]').val();
            postData['login_pass'] = $('input[type=password]').val();

            $.ajax({
                type: "POST",
                url: "<!--{$smarty.const.ROOT_URLPATH}-->shopping/index.php",
                data: postData,
                cache: false,
                dataType: "json",
                error: function(XMLHttpRequest, textStatus, errorThrown){
                    alert(textStatus);
                },
                success: function(result){
                    if (result.success) {
                        location.href = '<!--{$smarty.const.ROOT_URLPATH}-->shopping/' + result.success;
                    } else {
                        alert(result.login_error);
                    }
                }
            });
        }
    }
</script>

<section id="slidewindow">
    <h2 class="title"><!--{$tpl_title|h}--></h2>
    <form name="member_form" id="member_form" method="post" action="javascript:;" onSubmit="return ajaxLogin()">
        <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
        <input type="hidden" name="mode" value="login" />
        <div class="login_area">

        <div class="loginareaBox data-role-none">
        <!--{assign var=key value="login_email"}-->
        <span class="attention"><!--{$arrErr[$key]}--></span>
        <input type="email" name="<!--{$key}-->" value="<!--{$tpl_login_email|h}-->" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" class="mailtextBox data-role-none" placeholder="メールアドレス" />
        <!--{assign var=key value="login_pass"}-->
        <span class="attention"><!--{$arrErr[$key]}--></span>
        <input type="password" name="<!--{$key}-->" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" class="passtextBox data-role-none" placeholder="パスワード" />
        </div>

        <p class="arrowRtxt"><a rel="external" href="<!--{$smarty.const.HTTPS_URL}-->forgot/<!--{$smarty.const.DIR_INDEX_PATH}-->">パスワードを忘れた方</a></p>
        <div class="btn_area">
        <p><input type="submit" value="ログイン" class="btn data-role-none" name="log" id="log" /></p>
        </div>
        </div><!--▲loginarea-->
    </form>
    <form name="member_form2" id="member_form2" method="post" action="<!--{$smarty.const.ROOT_URLPATH}-->shopping/index.php">
        <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
        <input type="hidden" name="mode" value="nonmember" />
        <div class="login_area_btm">
            <nav>
                <ul class="navBox">
                    <li><a rel="external" href="<!--{$smarty.const.ROOT_URLPATH}-->entry/kiyaku.php" class="navBox_link">新規会員登録</a></li>
                    <li><input type="submit" value="会員登録をせずに購入" class="nav_nonmember data-role-none" name="buystep" id="buystep" /></li>
                </ul>
            </nav>
            <p class="message">会員登録をすると便利なMyページをご利用いただけます。</p>
        </div>
    </form>
</section>

<!--{include file= 'frontparts/search_area.tpl'}-->

