<!--{*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2014 LOCKON CO.,LTD. All Rights Reserved.
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
        <li><a rel="external" href="<!--{$smarty.const.HTTPS_URL}-->mypage/login.php"><img src="<!--{$TPL_URLPATH}-->img/button/btn_footer_mypage_off.png" alt="MYページ" width="75" height="50" /></a></li>
        <!--{if $smarty.const.OPTION_FAVORITE_PRODUCT == 1}-->
            <li><a rel="external" href="<!--{$smarty.const.HTTPS_URL}-->mypage/favorite.php"><img src="<!--{$TPL_URLPATH}-->img/button/btn_footer_favorite_off.png" alt="お気に入り" width="75" height="50" /></a></li>
        <!--{/if}-->
    <!--{else}-->
        <li><a data-transition="slideup" href="<!--{$smarty.const.HTTPS_URL}-->mypage/login.php"><img src="<!--{$TPL_URLPATH}-->img/button/btn_footer_mypage_off.png" alt="MYページ" width="75" height="50" /></a></li>
        <li><a data-transition="slideup" href="<!--{$smarty.const.HTTPS_URL}-->mypage/login.php"><img src="<!--{$TPL_URLPATH}-->img/button/btn_footer_favorite_off.png" alt="お気に入り" width="75" height="50" /></a></li>
    <!--{/if}-->
    <li><a rel="external" href="<!--{$smarty.const.CART_URL|h}-->"><img src="<!--{$TPL_URLPATH}-->img/button/btn_footer_cart_off.png" alt="カゴの中を見る" width="75" height="50" /></a></li>
    <li><a rel="external" href="<!--{$smarty.const.TOP_URL}-->"><img src="<!--{$TPL_URLPATH}-->img/button/btn_footer_toppage_off.png" alt="トップページへ" width="75" height="50" /></a></li>
</ul>
