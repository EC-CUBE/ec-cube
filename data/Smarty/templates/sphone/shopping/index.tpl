<!--{*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2011 LOCKON CO.,LTD. All Rights Reserved.
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
<!--▼CONTENTS-->
<section id="slidewindow">
<div data-role="header">
<div class="title_box clearfix">
<h2><!--{$tpl_title|h}--></h2><a href="#" data-role="button" data-rel="back" data-icon="delete" data-iconpos="notext" class="ui-btn-right" data-theme="d"><span class="ui-btn-text">close</span></a>
</div>
</div>
<form name="member_form" id="member_form" method="post" action="<!--{$smarty.const.HTTP_URL}-->shopping/index.php" onSubmit="return fnCheckLogin('member_form')">
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

    <p class="arrowRtxt"><a rel="external" href="<!--{$smarty.const.HTTPS_URL|sfTrimURL}-->/forgot/<!--{$smarty.const.DIR_INDEX_PATH}-->">パスワードを忘れた方</a></p>
    <div class="btn_area">
    <p><input type="submit" value="ログイン" class="btn data-role-none" name="log" id="log" /></p>
    </div>
    </div><!--▲loginarea-->
</form>
<form name="member_form2" id="member_form2" method="post" action="<!--{$smarty.const.HTTP_URL}-->shopping/index.php">
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
<!--▼検索バー -->
<section id="search_area">
<form method="get" action="<!--{$smarty.const.ROOT_URLPATH}-->products/list.php">
<input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
<input type="search" name="name" id="search" value="" placeholder="キーワードを入力" class="searchbox" >
</form>
</section>
<!--▲検索バー -->
<!--▲CONTENTS-->
