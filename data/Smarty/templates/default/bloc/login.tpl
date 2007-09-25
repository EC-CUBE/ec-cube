<!--{*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *}-->
<!--▼ログインここから-->
<!--{* FIXME *}-->
<!--{if $smarty.post.url == ""}-->
    <!--{if sfIsHTTPS}-->
        <!--{assign var=url value="https://`$smarty.server.HTTP_HOST``$smarty.server.REQUEST_URI`"}-->
    <!--{else}-->
        <!--{assign var=url value="http://`$smarty.server.HTTP_HOST``$smarty.server.REQUEST_URI`"}-->
    <!--{/if}-->
<!--{else}-->
    <!--{assign var=url value="`$smarty.post.url`"}-->
<!--{/if}-->
<h2>
  <img src="<!--{$TPL_DIR}-->img/side/title_login.jpg" width="166" height="35" alt="ログイン" />
</h2>
  <div id="loginarea">
    <form name="login_form" id="login_form" method="post" action="<!--{$smarty.const.SSL_URL}-->frontparts/login_check.php" onsubmit="return fnCheckLogin('login_form')">
      <input type="hidden" name="mode" value="login" />
      <input type="hidden" name="url" value="<!--{$url|escape}-->" />
      <div id="login">
        <!--{if $tpl_login}-->
        <p>ようこそ<br />
          <!--{$tpl_name1|escape}--> <!--{$tpl_name2|escape}--> 様<br />
          所持ポイント：<span class="price"> <!--{$tpl_user_point|number_format|default:0}--> pt</span>
        </p>
          <!--{if !$tpl_disable_logout}-->
        <p class="btn">
          <a href="<!--{$smarty.server.PHP_SELF|escape}-->" onclick="fnFormModeSubmit('login_form', 'logout', '', ''); return false;">
            <img src="<!--{$TPL_DIR}-->img/header/logout.gif" width="44" height="21" alt="ログアウト" />
          </a>
        </p>
       </div>
          <!--{/if}-->
        <!--{else}-->
        <p><img src="<!--{$TPL_DIR}-->img/side/icon_mail.gif" width="40" height="21" alt="メールアドレス" /><input type="text" name="login_email" class="box96" value="<!--{$tpl_login_email|escape}-->" /></p>
        <p><img src="<!--{$TPL_DIR}-->img/side/icon_pw.gif" width="40" height="22" alt="パスワード" /><input type="password" name="login_pass" class="box96" /></p>
      </div>
        <p class="mini">
          <a href="<!--{$smarty.const.SSL_URL|sfTrimURL}-->/forgot/index.php" onclick="win01('<!--{$smarty.const.SSL_URL|sfTrimURL}-->/forgot/index.php','forget','600','400'); return false;" target="_blank">パスワードを忘れた方はこちら</a>
        </p>
        <p>
          <input type="checkbox" name="login_memory" value="1" <!--{$tpl_login_memory|sfGetChecked:1}--> />
          <img src="<!--{$TPL_DIR}-->img/header/memory.gif" width="18" height="9" alt="記憶" />
        </p>
        <p class="btn">
          <input type="image" onmouseover="chgImgImageSubmit('<!--{$TPL_DIR}-->img/side/button_login_on.gif',this)" onmouseout="chgImgImageSubmit('<!--{$TPL_DIR}-->img/side/button_login.gif',this)" src="<!--{$TPL_DIR}-->img/side/button_login.gif" class="box51" alt="ログイン" name="subm" />
        </p>
        <!--{/if}-->
        <!--ログインフォーム-->
    </form>
  </div>
<!--▲ログインここまで-->
