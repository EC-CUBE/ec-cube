<!--{*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
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
<div id="under02column">
  <div id="undercolumn_cart">
    <h2>キャンペーン開催中</h2>
    <p>キャンペーン開催中です</p>
  </div>
</div>

<!--{*ログインフォーム*}-->
<!--{*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
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
<div id="under02column">
 <div id="under02title"><h2><!--★タイトル★-->ログイン</h2></div>
  <div id="under02column_login">
    <form name="login_mypage" id="login_mypage" method="post" action="./login_check.php" onsubmit="return fnCheckLogin('login_mypage')">
    <input type="hidden" name="mode" value="login" />
    
    <div class="loginarea">
      <div class="totalloginarea">
        <div class="totalloginblock">
          <p><em>会員登録がお済みのお客様</em></p><br />
          <p class="inputtext">会員の方は、登録時に入力されたメールアドレスとパスワードでログインしてください。</p>
        </div>
        <div class="inputbox">
        <!--{assign var=key value="mypage_login_email"}-->
        <span class="attention"><!--{$arrErr[$key]}--></span>
        <p><img src="<!--{$TPL_DIR}-->img/login/mailadress.gif" width="94" height="10" alt="メールアドレス">&nbsp;
          <input type="text" name="<!--{$key}-->"
          value="<!--{$tpl_login_email|escape}-->"
          maxlength="<!--{$arrForm[$key].length}-->"
          style="<!--{$arrErr[$key]|sfGetErrorColor}-->; ime-mode: disabled;"
          size="40" class="box300" />
        <span class="mini">
          <!--{assign var=key value="mypage_login_memory"}-->
          <input type="checkbox" name="<!--{$key}-->" value="1" <!--{$tpl_login_memory|sfGetChecked:1}--> id="login_memory" />
          <label for="login_memory">会員メールアドレスをコンピューターに記憶させる</label>
        </p>
        <p class="passwd">
        <!--{assign var=key value="mypage_login_pass"}-->
         <span class="attention"><!--{$arrErr[$key]}--></span>
         <img src="<!--{$TPL_DIR}-->img/login/password.gif"
              width="94" height="11" alt="パスワード" />
         &nbsp;<input type="password" name="<!--{$key}-->" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" size="40" class="box300" />
        </p>
        </div>
      </div>
    
        <div class="tblareabtn">
          <input type="image" onMouseover="chgImgImageSubmit('<!--{$TPL_DIR}-->img/login/b_login_on.jpg',this)" onMouseout="chgImgImageSubmit('<!--{$TPL_DIR}-->img/login/b_login.jpg',this)" src="<!--{$TPL_DIR}-->img/login/b_login.jpg" width="128" height="32" alt="ログイン" name="log" id="log" class="box140">
        </div>
    
        <p class="inputtext02">
                  ・パスワードを忘れた方は<a href="<!--{$smarty.const.SSL_URL}-->forget/index.php" onClick="win01('<!--{$smarty.const.SSL_URL|sfTrimURL}-->/forgot/index.php','forget','600','400'); return false;" target="_blank">こちら</a>からパスワードの再発行を行ってください。<br />
                  ・メールアドレスを忘れた方は、お手数ですが、<a href="<!--{$smarty.const.SSL_URL}-->contact/index.php">お問い合わせページ</a>からお問い合わせください。
        </p>
    </div>

    <div class="loginarea">
        <div class="totalloginarea">
                <div class="totalloginblock02">
                    <p><em>まだ会員登録されていないお客様</em><br />
                    会員登録をすると便利なMyページをご利用いただけます。<br />
                    また、ログインするだけで、毎回お名前や住所などを入力することなくスムーズにお買い物をお楽しみいただけます。</p>
                </div>
         </div>
            
         <div class="inputbox02">
             <a href="<!--{$smarty.const.SITE_URL}-->entry/kiyaku.php" onMouseOver="chgImg('<!--{$TPL_DIR}-->img/login/b_gotoentry_on.jpg','b_gotoentry');" onMouseOut="chgImg('<!--{$TPL_DIR}-->img/login/b_gotoentry.jpg','b_gotoentry');"><img src="<!--{$TPL_DIR}-->img/login/b_gotoentry.jpg" width="128" height="32" alt="新規会員登録" border="0" name="b_gotoentry"></a>　
         </div>
        </div>
    </div>
</form>
</div>
</div>