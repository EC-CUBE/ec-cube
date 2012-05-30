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

<nav class="header_navi">
    <!--{if $tpl_login}-->
        <p class="guest">ようこそ <a href="<!--{$smarty.const.HTTPS_URL|sfTrimURL}-->/mypage/login.php" rel="external"><!--{$tpl_name1|h}--> <!--{$tpl_name2|h}-->さん</a></p>
        <!--{if $smarty.const.USE_POINT !== false}-->
            <p>所持ポイント<!--{$tpl_user_point|number_format|default:0}-->pt</p>
        <!--{/if}-->
    <!--{else}-->
        <p class="guest">ようこそ ゲストさん</p>
        <p><a rel="external" href="<!--{$smarty.const.HTTPS_URL|sfTrimURL}-->/entry/kiyaku.php">新規会員登録</a></p>
    <!--{/if}-->
</nav>
