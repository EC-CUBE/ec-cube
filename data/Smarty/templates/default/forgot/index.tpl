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
<title><!--{$arrSiteInfo.shop_name}-->/パスワードを忘れた方(入力ページ)</title>
</head>

<body onload="preLoadImg('<!--{$smarty.const.URL_DIR}-->')">
<noscript>
 <p>JavaScriptを有効にしてご利用下さい</p>
</noscript>
<div id="windowcolumn">
  <div id="windowarea">
    <h2><img src="<!--{$TPL_DIR}-->img/forget/title.jpg" width="500" height="40" alt="パスワードを忘れた方" /></h2>
    <p>ご登録時のメールアドレスを入力して「次へ」ボタンをクリックしてください。<br />
      <span class="attention">※新しくパスワードを発行いたしますので、お忘れになったパスワードはご利用できなくなります。</span></p>
    <form action="<!--{$smarty.server.PHP_SELF|escape}-->" method="post" name="form1">
      <input type="hidden" name="mode" value="mail_check" />
      <div id="completebox">
        <p>メールアドレス：&nbsp;<!--★メールアドレス入力★--><input type="text" name="email" value="<!--{$tpl_login_email|escape}-->" size="40" class="box300" style="<!--{$errmsg|sfGetErrorColor}-->; ime-mode: disabled;" /></p>
        <span class="attention"><!--{$errmsg}--></span>
      </div>
      <div class="btn">
        <input type="image" onmouseover="chgImgImageSubmit('<!--{$TPL_DIR}-->img/common/b_next_on.gif',this)" onmouseout="chgImgImageSubmit('<!--{$TPL_DIR}-->img/common/b_next.gif',this)" src="<!--{$TPL_DIR}-->img/common/b_next.gif" alt="次へ" class="box150" name="next" id="next" />
      </div>
    </form>
  </div>
</div>
</body>
</html>
