<!--{*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2012 LOCKON CO.,LTD. All Rights Reserved.
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

<ul class="footer_navi">
    <!--{if $tpl_login}-->
        <li><a rel="external" href="<!--{$smarty.const.HTTPS_URL|sfTrimURL}-->/mypage/login.php"><img src="<!--{$TPL_URLPATH}-->img/button/btn_footer_mypage_off.png" alt="MY page" width="75" height="34" /><br />MY page</a></li>
        <li><a rel="external" href="<!--{$smarty.const.HTTPS_URL|sfTrimURL}-->/mypage/favorite.php"><img src="<!--{$TPL_URLPATH}-->img/button/btn_footer_favorite_off.png" alt="Favorites" width="75" height="34" /><br />Favorites</a></li>
    <!--{else}-->
        <li><a data-transition="slideup" href="<!--{$smarty.const.HTTPS_URL|sfTrimURL}-->/mypage/login.php"><img src="<!--{$TPL_URLPATH}-->img/button/btn_footer_mypage_off.png" alt="MY page" width="75" height="34" /><br />MY page</a></li>
        <li><a data-transition="slideup" href="<!--{$smarty.const.HTTPS_URL|sfTrimURL}-->/mypage/login.php"><img src="<!--{$TPL_URLPATH}-->img/button/btn_footer_favorite_off.png" alt="Favorites" width="75" height="34" /><br />Favorites</a></li>
    <!--{/if}-->
    <li><a rel="external" href="<!--{$smarty.const.CART_URLPATH|h}-->"><img src="<!--{$TPL_URLPATH}-->img/button/btn_footer_cart_off.png" alt="View Cart" width="75" height="34" /><br />View Cart</a></li>
    <li><a rel="external" href="<!--{$smarty.const.ROOT_URLPATH}-->"><img src="<!--{$TPL_URLPATH}-->img/button/btn_footer_toppage_off.png" alt="Home" width="75" height="34" /><br />Home</a></li>
</ul>
