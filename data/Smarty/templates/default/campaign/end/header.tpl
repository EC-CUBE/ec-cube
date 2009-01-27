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
<link rel="stylesheet" href="<!--{$TPL_DIR}-->css/common.css" type="text/css" media="all" />
<link rel="alternate" type="application/rss+xml" title="RSS" href="<!--{$smarty.const.SITE_URL}-->rss/index.php" />
<script type="text/javascript" src="<!--{$TPL_DIR}-->js/css.js"></script>
<script type="text/javascript" src="<!--{$TPL_DIR}-->js/navi.js"></script>
<script type="text/javascript" src="<!--{$TPL_DIR}-->js/win_op.js"></script>
<script type="text/javascript" src="<!--{$TPL_DIR}-->js/site.js"></script>
<title><!--{$arrSiteInfo.shop_name|escape}-->/<!--{$tpl_title|escape}--></title>
<meta name="author" content="<!--{$arrPageLayout.author|escape}-->" />
<meta name="description" content="<!--{$arrPageLayout.description|escape}-->" />
<meta name="keywords" content="<!--{$arrPageLayout.keyword|escape}-->" />

<!--▼HEADER-->
<div id="header">
    <h1><a href="<!--{$smarty.const.SITE_URL}-->"><em>EC-CUBE CLOTHES STORE</em></a></h1>
    
    <div id="information">
        <ul>
            <li><a href="<!--{$smarty.const.URL_DIR}-->entry/kiyaku.php" onMouseOver="chgImg('<!--{$TPL_DIR}-->img/header/member_on.gif','member');" onMouseOut="chgImg('<!--{$TPL_DIR}-->img/header/member.gif','member');"><img src="<!--{$TPL_DIR}-->img/header/member.gif" width="98" height="26" alt="会員登録" name="member"></a></li
            ><li><a href="<!--{$smarty.const.URL_DIR}-->cart/index.php" onMouseOver="chgImg('<!--{$TPL_DIR}-->img/header/cartin_on.gif','cartin');" onMouseOut="chgImg('<!--{$TPL_DIR}-->img/header/cartin.gif','cartin');"><img src="<!--{$TPL_DIR}-->img/header/cartin.gif" width="120" height="26" alt="買い物かごを確認" name="cartin"></a></li>
        </ul>
    </div>
    
    <div id="mainnavi">

        <ul>
            <li><a href="<!--{$smarty.const.SITE_URL}-->" onMouseOver="chgImg('<!--{$TPL_DIR}-->img/header/home_on.jpg','home');" onMouseOut="chgImg('<!--{$TPL_DIR}-->img/header/home.jpg','home');"><img src="<!--{$TPL_DIR}-->img/header/home.jpg" width="82" height="40" alt="HOME" name="home"></a></li
            ><li><a href="<!--{$smarty.const.URL_DIR}-->abouts/index.php" onMouseOver="chgImg('<!--{$TPL_DIR}-->img/header/about_on.jpg','about');" onMouseOut="chgImg('<!--{$TPL_DIR}-->img/header/about.jpg','about');"><img src="<!--{$TPL_DIR}-->img/header/about.jpg" width="129" height="40" alt="当サイトについて" name="about"></a></li
            ><li><a href="<!--{$smarty.const.SSL_URL}-->mypage/login.php" onMouseOver="chgImg('<!--{$TPL_DIR}-->img/header/mypage_on.jpg','mypage');" onMouseOut="chgImg('<!--{$TPL_DIR}-->img/header/mypage.jpg','mypage');"><img src="<!--{$TPL_DIR}-->img/header/mypage.jpg" width="90" height="40" alt="MYページ" name="mypage"></a></li
            ><li><a href="<!--{$smarty.const.SSL_URL}-->contact/index.php" onMouseOver="chgImg('<!--{$TPL_DIR}-->img/header/contact_on.jpg','contact');" onMouseOut="chgImg('<!--{$TPL_DIR}-->img/header/contact.jpg','contact');"><img src="<!--{$TPL_DIR}-->img/header/contact.jpg" width="106" height="40" alt="お問い合わせ" name="contact"></a></li>
        </ul>
    </div>
</div>
<!--▲HEADER-->
