<!--{printXMLDeclaration}--><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<!--{*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *}-->
<html xmlns="http://www.w3.org/1999/xhtml" lang="ja" xml:lang="ja">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<!--{$smarty.const.CHAR_CODE}-->" />
<meta http-equiv="Content-Script-Type" content="text/javascript" />
<meta http-equiv="Content-Style-Type" content="text/css" />
<link rel="stylesheet" href="<!--{$smarty.const.URL_DIR}--><!--{$smarty.const.USER_DIR}-->css/common.css" type="text/css" media="all" />
<link rel="alternate" type="application/rss+xml" title="RSS" href="<!--{$smarty.const.SITE_URL}-->/rss/index.php" />
<script type="text/javascript" src="<!--{$TPL_DIR}-->js/css.js"></script>
<script type="text/javascript" src="<!--{$TPL_DIR}-->js/navi.js"></script>
<script type="text/javascript" src="<!--{$TPL_DIR}-->js/win_op.js"></script>
<script type="text/javascript" src="<!--{$TPL_DIR}-->js/site.js"></script>
<meta name="author" content="<!--{$arrPageLayout.author|escape}-->" />
<meta name="description" content="<!--{$arrPageLayout.description|escape}-->" />
<meta name="keywords" content="<!--{$arrPageLayout.keyword|escape}-->" />
<title><!--{$arrSiteInfo.shop_name}-->/パスワードを忘れた方(完了ページ)</title>
</head>

<body onload="preLoadImg('<!--{$smarty.const.URL_DIR}-->')">
<noscript>
 <p>JavaScriptを有効にしてご利用下さい</p>
</noscript>
<div id="windowcolumn">
  <div id="windowarea">
    <h2><img src="<!--{$TPL_DIR}-->img/forget/title.jpg" width="500" height="40" alt="パスワードを忘れた方" /></h2>
    <p>パスワードの発行が完了いたしました。ログインには下記のパスワードをご利用ください。<br />
    ※下記パスワードは、MYページの「会員登録内容変更」よりご変更いただけます。</p>
    <form action="<!--{$smarty.server.PHP_SELF|escape}-->" method="post" name="form1">
      <div id="completebox">
        <p><em><!--{$temp_password}--></em></p>
      </div>
      <div class="btn">
        <a href="javascript:window.close()" onmouseover="chgImg('<!--{$TPL_DIR}-->img/common/b_close_on.gif','close');" onmouseout="chgImg('<!--{$TPL_DIR}-->img/common/b_close.gif','close');"><img src="<!--{$TPL_DIR}-->img/common/b_close.gif" width="150" height="30" alt="閉じる" name="close" id="close" /></a>
      </div>
    </form>
  </div>
</div>
</body>
</html>
