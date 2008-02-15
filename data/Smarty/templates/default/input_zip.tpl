<!--{printXMLDeclaration}--><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
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
<title><!--{$arrSiteInfo.shop_name|escape}-->/住所検索</title>
<meta name="author" content="<!--{$arrPageLayout.author|escape}-->" />
<meta name="description" content="<!--{$arrPageLayout.description|escape}-->" />
<meta name="keywords" content="<!--{$arrPageLayout.keyword|escape}-->" />
</head>

<body onload="preLoadImg('<!--{$TPL_DIR}-->'); <!--{$tpl_onload}--> <!--{$tpl_start}-->">
<noscript>
  <p>JavaScript を有効にしてご利用下さい.</p>
</noscript>
<!--▼CONTENTS-->
  <div id="zipsearchcolumn">
    <h2><img src="<!--{$TPL_DIR}-->img/common/zip_title.jpg" width="460" height="40" alt="住所検索" /></h2>
    <div id="zipsearcharea">
      <form name="form1" id="form1" method="post" action="<!--{$smarty.const.PHP_SELF|escape}-->">
        <input type="hidden" name="state" value="<!--{$tpl_state}-->" />
        <input type="hidden" name="city" value="<!--{$tpl_city}-->" />
        <input type="hidden" name="town" value="<!--{$tpl_town}-->" />
        <div id="completebox">
          <p><!--{$tpl_message}--></p>
        </div>
      </form>
    </div>
    <div class="btn"><a href="javascript:window.close()" onmouseover="chgImg('<!--{$TPL_DIR}-->img/common/b_close_on.gif','b_close');" onmouseout="chgImg('<!--{$TPL_DIR}-->img/common/b_close.gif','b_close');"><img src="<!--{$TPL_DIR}-->img/common/b_close.gif" width="140" height="30" alt="閉じる" border="0" name="b_close" /></a></div>
<!--▲CONTENTS-->
  </div>
</body>
</html>
