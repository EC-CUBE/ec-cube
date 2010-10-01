<!--{*
/*
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
 */
*}-->
<div id="mynavarea">
    <!--{strip}-->
        <ul class="button_like">
            <li><a href="./<!--{$smarty.const.DIR_INDEX_URL}-->" class="<!--{if $tpl_mypageno == 'index'}--> selected<!--{/if}-->">
                購入履歴一覧</a></li>
            <!--{if $smarty.const.OPTION_FAVOFITE_PRODUCT == 1}-->
                <!--{if $tpl_login}-->
                    <li><a href="favorite.php" class="<!--{if $tpl_mypageno == 'favorite'}--> selected<!--{/if}-->">
                           お気に入り一覧</a></li>
                <!--{else}-->
                       <!--{* 退会時、TOPページへ *}-->
                    <li><a href="/index.php" class="<!--{if $tpl_mypageno == 'favorite'}--> selected<!--{/if}-->">
                           お気に入り一覧</a></li>
                <!--{/if}-->
            <!--{/if}-->
            <li><a href="change.php" class="<!--{if $tpl_mypageno == 'change'}--> selected<!--{/if}-->">
                会員登録内容変更</a></li>
            <li><a href="delivery.php" class="<!--{if $tpl_mypageno == 'delivery'}--> selected<!--{/if}-->">
                お届け先追加・変更</a></li>
            <li><a href="refusal.php" class="<!--{if $tpl_mypageno == 'refusal'}--> selected<!--{/if}-->">
                退会手続き</a></li>
        </ul>
        
        <!--▼現在のポイント-->
        <!--{if $point_disp !== false}-->
            <ul>
                 <li>ようこそ <br />
                     <!--{$CustomerName1|escape}--> <!--{$CustomerName2|escape}-->様
                     <!--{if $smarty.const.USE_POINT !== false}-->
                         <br />現在の所持ポイントは<em><!--{$CustomerPoint|number_format|escape|default:"0"}-->pt</em>です。
                     <!--{/if}-->
                 </li>
            </ul>
        <!--{/if}-->
        <!--▲現在のポイント-->
    <!--{/strip}-->
</div>
<!--▲NAVI-->
