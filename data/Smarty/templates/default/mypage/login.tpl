<!--{*
 * Copyright (c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *}-->
<!--▼CONTENTS-->
<div id="under02column">
  <div id="under02column_login">
    <h2 class="title">
      <img src="<!--{$TPL_DIR}-->img/login/title.jpg" width="700" height="40" alt="ログイン" />
   </h2>
    <form name="login_mypage" id="login_mypage" method="post" action="./login_check.php" onsubmit="return fnCheckLogin('login_mypage')">
    <input type="hidden" name="mode" value="login" />
   <div class="loginarea">
     <p><img src="<!--{$TPL_DIR}-->img/login/member.gif" width="202" height="16" alt="会員登録がお済みのお客様" /></p>
     <p class="inputtext">会員の方は、登録時に入力されたメールアドレスとパスワードでログインしてください。</p>
       <div class="inputbox">
       <!--{assign var=key value="mypage_login_email"}-->
       <span class="attention"><!--{$arrErr[$key]}--></span>
       <p><img src="<!--{$TPL_DIR}-->img/login/mailadress.gif" width="92" height="13" alt="メールアドレス" />&nbsp;
         <input type="text" name="<!--{$key}-->"
                value="<!--{$tpl_login_email|escape}-->"
                maxlength="<!--{$arrForm[$key].length}-->"
                style="<!--{$arrErr[$key]|sfGetErrorColor}-->; ime-mode: disabled;"
                size="40" class="box300" />
       </p>
       <p class="mini">
         <!--{assign var=key value="mypage_login_memory"}-->
         <input type="checkbox" name="<!--{$key}-->" value="1" <!--{$tpl_login_memory|sfGetChecked:1}--> id="login_memory" />
         <label for="login_memory">会員メールアドレスをコンピューターに記憶させる</label>
       </p>
       <p class="passwd">
         <!--{assign var=key value="mypage_login_pass"}-->
         <span class="attention"><!--{$arrErr[$key]}--></span>
         <img src="<!--{$TPL_DIR}-->img/login/password.gif"
              width="92" height="13" alt="パスワード" />
         &nbsp;<input type="password" name="<!--{$key}-->" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" size="40" class="box300" />
       </p>
     </div>
     <div class="tblareabtn">
      <input type="image" onmouseover="chgImgImageSubmit('<!--{$TPL_DIR}-->img/login/b_login_on.gif',this)" onmouseout="chgImgImageSubmit('<!--{$TPL_DIR}-->img/login/b_login.gif',this)" src="<!--{$TPL_DIR}-->img/login/b_login.gif" alt="ログイン" name="log" id="log" class="box140" />
     </div>
     <p class="inputtext02">
       パスワードを忘れた方は<a href="<!--{$smarty.const.SSL_URL|sfTrimURL}-->/forgot/index.php" onclick="win01('<!--{$smarty.const.SSL_URL|sfTrimURL}-->/forgot/index.php','forget','600','400'); return false;" target="_blank">こちら</a>からパスワードの再発行を行ってください。<br />
      メールアドレスを忘れた方は、お手数ですが、<a href="<!--{$smarty.const.URL_DIR}-->contact/index.php">お問い合わせページ</a>からお問い合わせください。
     </p>
  </div>
  <div class="loginarea">
    <p>
      <img src="<!--{$TPL_DIR}-->img/login/guest.gif" width="247" height="16" alt="まだ会員登録されていないお客様" />
    </p>
    <p class="inputtext">会員登録をすると便利なMyページをご利用いただけます。<br />
      また、ログインするだけで、毎回お名前や住所などを入力することなくスムーズにお買い物をお楽しみいただけます。
    </p>
    <div class="inputbox02">
      <a href="<!--{$smarty.const.URL_DIR}-->entry/kiyaku.php" onmouseover="chgImg('<!--{$TPL_DIR}-->img/login/b_gotoentry_on.gif','b_gotoentry');" onmouseout="chgImg('<!--{$TPL_DIR}-->img/login/b_gotoentry.gif','b_gotoentry');">
        <img src="<!--{$TPL_DIR}-->img/login/b_gotoentry.gif" width="130" height="30" alt="会員登録をする" border="0" name="b_gotoentry" />
      </a>
    </div>
  </div>
</form>
</div>
</div>
<!--▲CONTENTS-->
