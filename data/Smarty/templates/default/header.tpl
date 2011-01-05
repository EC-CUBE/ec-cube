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
    <a href="<!--{$smarty.const.TOP_URL_PATH}-->">
      <em><!--{$arrSiteInfo.shop_name|h}-->/<!--{$tpl_title|h}--></em></a>
  </h1>
  <!--{* ▼HeaderInternal COLUMN*}-->
  <!--{if $arrPageLayout.HeaderInternalNavi|@count > 0}-->
      <div id="headerinternalcolumn">
          <!--{* ▼上ナビ *}-->
          <!--{foreach key=HeaderInternalNaviKey item=HeaderInternalNaviItem from=$arrPageLayout.HeaderInternalNavi}-->
            <!-- ▼<!--{$HeaderInternalNaviItem.bloc_name}--> -->
            <!--{if $HeaderInternalNaviItem.php_path != ""}-->
              <!--{include_php file=$HeaderInternalNaviItem.php_path}-->
            <!--{else}-->
              <!--{include file=$HeaderInternalNaviItem.tpl_path}-->
            <!--{/if}-->
            <!-- ▲<!--{$HeaderInternalNaviItem.bloc_name}--> -->
          <!--{/foreach}-->
          <!--{* ▲上ナビ *}-->
      </div>
  <!--{/if}-->
  <!--{* ▲HeaderInternal COLUMN*}-->
  <div id="information">
    <ul>
      <li>
        <a href="<!--{$smarty.const.HTTPS_URL}-->mypage/login.php"
           onmouseover="chgImg('<!--{$TPL_DIR}-->img/common/btn_header_mypage_on.gif','mypage');"
           onmouseout="chgImg('<!--{$TPL_DIR}-->img/common/btn_header_mypage.gif','mypage');">
          <img src="<!--{$TPL_DIR}-->img/common/btn_header_mypage.gif" width="95" height="20" alt="MYページ" name="mypage" id="mypage" /></a>
      </li>
      <li>
        <a href="<!--{$smarty.const.URL_PATH}-->entry/kiyaku.php"
           onmouseover="chgImg('<!--{$TPL_DIR}-->img/common/btn_header_entry_on.gif','entry');"
           onmouseout="chgImg('<!--{$TPL_DIR}-->img/common/btn_header_entry.gif','entry');">
          <img src="<!--{$TPL_DIR}-->img/common/btn_header_entry.gif" width="95" height="20" alt="会員登録" name="entry" id="entry" /></a>
      </li>
      <li>
        <a href="<!--{$smarty.const.CART_URL_PATH}-->"
           onmouseover="chgImg('<!--{$TPL_DIR}-->img/common/btn_header_cartin_on.gif','cartin');"
           onmouseout="chgImg('<!--{$TPL_DIR}-->img/common/btn_header_cartin.gif','cartin');">
          <img src="<!--{$TPL_DIR}-->img/common/btn_header_cartin.gif" width="95" height="20" alt="カゴの中を見る" name="cartin" id="cartin" /></a>
      </li>
    </ul>
  </div>
</div>
<!--▲HEADER-->
