<!--{*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *}-->
<!--▼HEADER-->
<div id="header">
  <h1>
    <a href="<!--{$smarty.const.SITE_URL}-->">
      <em><!--{$arrSiteInfo.shop_name|escape}-->/<!--{$tpl_title|escape}--></em>
    </a>
  </h1>
  <div id="information">
    <ul>
      <li>
        <a href="<!--{$smarty.const.URL_DIR}-->mypage/index.php"
           onmouseover="chgImg('<!--{$TPL_DIR}-->img/header/mypage_on.gif','mypage');"
           onmouseout="chgImg('<!--{$TPL_DIR}-->img/header/mypage.gif','mypage');">
          <img src="<!--{$TPL_DIR}-->img/header/mypage.gif" width="95" height="20" alt="ログイン情報変更" name="mypage" id="mypage" />
        </a>
      </li>
      <li>
        <a href="<!--{$smarty.const.URL_DIR}-->entry/index.php"
           onmouseover="chgImg('<!--{$TPL_DIR}-->img/header/member_on.gif','member');"
           onmouseout="chgImg('<!--{$TPL_DIR}-->img/header/member.gif','member');">
          <img src="<!--{$TPL_DIR}-->img/header/member.gif" width="95" height="20" alt="会員登録" name="member" id="member" />
        </a>
      </li>
      <li>
        <a href="<!--{$smarty.const.URL_DIR}-->cart/index.php"
           onmouseover="chgImg('<!--{$TPL_DIR}-->img/header/cartin_on.gif','cartin');"
           onmouseout="chgImg('<!--{$TPL_DIR}-->img/header/cartin.gif','cartin');">
          <img src="<!--{$TPL_DIR}-->img/header/cartin.gif" width="95" height="20" alt="カゴの中を見る" name="cartin" id="cartin" />
        </a>
      </li>
    </ul>
  </div>
</div>
<!--▲HEADER-->
