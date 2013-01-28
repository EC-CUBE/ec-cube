<!--{*
/*
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
 */
*}-->

<nav id="mypage_nav">
    <!--{strip}-->
        <ul>
            <!--{if $tpl_login}-->
                <!--{* 会員状態 *}-->
                <li class="nav_delivadd"><a href="delivery.php" class="<!--{if $tpl_mypageno == 'delivery'}--> selected<!--{/if}-->" rel="external">Delivery options</a></li>
                <li class="nav_change"><a href="change.php" class="<!--{if $tpl_mypageno == 'change'}--> selected<!--{/if}-->" rel="external">Edit member details</a></li>
                <li class="nav_history"><a href="./<!--{$smarty.const.DIR_INDEX_PATH}-->" class="<!--{if $tpl_mypageno == 'index'}--> selected<!--{/if}-->" rel="external">History</a></li>
                <!--{if $smarty.const.OPTION_FAVORITE_PRODUCT == 1}-->
                    <li class="nav_favorite"><a href="favorite.php" class="<!--{if $tpl_mypageno == 'favorite'}--> selected<!--{/if}-->" rel="external">Favorites</a></li>
                <!--{/if}-->
                    <li class="nav_refusal"><a href="refusal.php" class="<!--{if $tpl_mypageno == 'refusal'}--> selected<!--{/if}-->" rel="external">Close account</a></li>
                <!--{else}-->

                <!--{* 退会状態 *}-->
                <li class="nav_delivadd"><a href="<!--{$smarty.const.TOP_URLPATH}-->" class="<!--{if $tpl_mypageno == 'delivery'}--> selected<!--{/if}-->" rel="external">Delivery options</a></li>
                <li class="nav_change"><a href="<!--{$smarty.const.TOP_URLPATH}-->" class="<!--{if $tpl_mypageno == 'change'}--> selected<!--{/if}-->" rel="external">Edit member details</a></li>
                <li class="nav_history"><a href="<!--{$smarty.const.TOP_URLPATH}-->" class="<!--{if $tpl_mypageno == 'index'}--> selected<!--{/if}-->" rel="external">History</a></li>
                <!--{if $smarty.const.OPTION_FAVORITE_PRODUCT == 1}-->
                    <li class="nav_favorite"><a href="<!--{$smarty.const.TOP_URLPATH}-->" class="<!--{if $tpl_mypageno == 'favorite'}--> selected<!--{/if}-->" rel="external">Favorites</a></li>
                <!--{/if}-->
                <li class="nav_refusal"><a href="<!--{$smarty.const.TOP_URLPATH}-->" class="<!--{if $tpl_mypageno == 'refusal'}--> selected<!--{/if}-->" rel="external">Cancel membership</a></li>
            <!--{/if}-->
        </ul>
    <!--{/strip}-->
</nav>
<!--▲NAVI-->
