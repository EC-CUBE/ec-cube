<!--{*
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
 *}-->
<!--▼HEADER-->
<div id="header">
  <h1>
    <a href="<!--{$smarty.const.URL_SITE_TOP}-->">
      <em><!--{$arrSiteInfo.shop_name|escape}-->/<!--{$tpl_title|escape}--></em></a>
  </h1>
  <div id="information">
    <ul>
      <li>
        <a href="<!--{$smarty.const.SSL_URL}-->mypage/login.php"
           onmouseover="chgImg('<!--{$TPL_DIR}-->img/header/mypage_on.gif','mypage');"
           onmouseout="chgImg('<!--{$TPL_DIR}-->img/header/mypage.gif','mypage');">
          <img src="<!--{$TPL_DIR}-->img/header/mypage.gif" width="95" height="20" alt="MYページ" name="mypage" id="mypage" /></a>
      </li>
      <li>
        <a href="<!--{$smarty.const.URL_DIR}-->entry/kiyaku.php"
           onmouseover="chgImg('<!--{$TPL_DIR}-->img/header/member_on.gif','member');"
           onmouseout="chgImg('<!--{$TPL_DIR}-->img/header/member.gif','member');">
          <img src="<!--{$TPL_DIR}-->img/header/member.gif" width="95" height="20" alt="会員登録" name="member" id="member" /></a>
      </li>
      <li>
        <a href="<!--{$smarty.const.URL_CART_TOP}-->"
           onmouseover="chgImg('<!--{$TPL_DIR}-->img/header/cartin_on.gif','cartin');"
           onmouseout="chgImg('<!--{$TPL_DIR}-->img/header/cartin.gif','cartin');">
          <img src="<!--{$TPL_DIR}-->img/header/cartin.gif" width="95" height="20" alt="カゴの中を見る" name="cartin" id="cartin" /></a>
      </li>
    </ul>
  </div>
</div>
<!--▲HEADER-->
