<!--{*
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2010 LOCKON CO.,LTD. All Rights Reserved.
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
<!--▼CONTENTS-->
<div id="under02column">
  <div id="under02column_login">
    <h2 class="title"><!--{$tpl_title|h}--></h2>
    <form name="login_mypage" id="login_mypage" method="post" action="<!--{$smarty.const.HTTPS_URL}-->frontparts/login_check.php" onsubmit="return fnCheckLogin('login_mypage')">
    <input type="hidden" name="mode" value="login" />
            <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
            <input type="hidden" name="url" value="<!--{$smarty.server.PHP_SELF|h}-->" />    
   <div class="loginarea">
     <h3>会員登録がお済みのお客様</h3>
     <p class="inputtext">会員の方は、登録時に入力されたメールアドレスとパスワードでログインしてください。</p>
       <div class="inputbox">
       <!--{assign var=key value="login_email"}-->
       <span class="attention"><!--{$arrErr[$key]}--></span>
       <p>メールアドレス:&nbsp;
         <input type="text" name="<!--{$key}-->"
                value="<!--{$tpl_login_email|h}-->"
                maxlength="<!--{$arrForm[$key].length}-->"
                style="<!--{$arrErr[$key]|sfGetErrorColor}-->; ime-mode: disabled;"
                size="40" class="box300" />
       </p>
       <p class="mini">
         <!--{assign var=key value="login_memory"}-->
         <input type="checkbox" name="<!--{$key}-->" value="1" <!--{$tpl_login_memory|sfGetChecked:1}--> id="login_memory" />
         <label for="login_memory">メールアドレスを記憶させる</label>
       </p>
       <p class="passwd">
         <!--{assign var=key value="login_pass"}-->
         <span class="attention"><!--{$arrErr[$key]}--></span>
         パスワード:&nbsp;
         <input type="password" name="<!--{$key}-->" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" size="40" class="box300" />
       </p>
     </div>
     <div class="tblareabtn">
      <input type="submit" value="ログイン" class="spbtn spbtn-shopping" width="130" height="30" alt="ログイン" name="log" id="log" />
     </div>
     <p class="inputtext02">
       パスワードを忘れた方は<a href="<!--{$smarty.const.HTTPS_URL|sfTrimURL}-->/forgot/<!--{$smarty.const.DIR_INDEX_PATH}-->" onclick="win01('<!--{$smarty.const.HTTPS_URL|sfTrimURL}-->/forgot/<!--{$smarty.const.DIR_INDEX_PATH}-->','forget','600','460'); return false;" target="_blank">こちら</a>からパスワードの再発行を行ってください。<br />
      メールアドレスを忘れた方は、お手数ですが、<a href="<!--{$smarty.const.ROOT_URLPATH}-->contact/<!--{$smarty.const.DIR_INDEX_PATH}-->">お問い合わせページ</a>からお問い合わせください。
     </p>
  </div>
  <div class="loginarea">
    <h3>まだ会員登録されていないお客様</h3>
    <p class="inputtext">会員登録をすると便利なMyページをご利用いただけます。<br />
      また、ログインするだけで、毎回お名前や住所などを入力することなくスムーズにお買い物をお楽しみいただけます。
    </p>
    <div class="inputbox02">
        <a href="<!--{$smarty.const.ROOT_URLPATH}-->entry/kiyaku.php" class="spbtn spbtn-medeum">
                    会員登録をする</a>&nbsp;
    </div>
  </div>
</form>
</div>
</div>
<!--▲CONTENTS-->
