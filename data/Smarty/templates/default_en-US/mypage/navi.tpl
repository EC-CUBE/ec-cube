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

<div id="mynavi_area">
    <!--{strip}-->
        <ul class="mynavi_list clearfix">

            <!--{* 会員状態 *}-->
            <!--{if $tpl_login}-->
                <li><a href="./<!--{$smarty.const.DIR_INDEX_PATH}-->" class="<!--{if $tpl_mypageno == 'index'}--> selected<!--{/if}-->">
                    Purchase history</a></li>
                <!--{if $smarty.const.OPTION_FAVORITE_PRODUCT == 1}-->
                    <li><a href="favorite.php" class="<!--{if $tpl_mypageno == 'favorite'}--> selected<!--{/if}-->">
                        Favorites</a></li>
                <!--{/if}-->
                <li><a href="change.php" class="<!--{if $tpl_mypageno == 'change'}--> selected<!--{/if}-->">
                    Edit registration details</a></li>
                <li><a href="delivery.php" class="<!--{if $tpl_mypageno == 'delivery'}--> selected<!--{/if}-->">
                    Delivery options</a></li>
                <li><a href="refusal.php" class="<!--{if $tpl_mypageno == 'refusal'}--> selected<!--{/if}-->">
                    Cancel membership</a></li>

            <!--{* 退会状態 *}-->
            <!--{else}-->
                <li><a href="<!--{$smarty.const.TOP_URLPATH}-->" class="<!--{if $tpl_mypageno == 'index'}--> selected<!--{/if}-->">
                    Purchase history</a></li>
                <!--{if $smarty.const.OPTION_FAVORITE_PRODUCT == 1}-->
                    <li><a href="<!--{$smarty.const.TOP_URLPATH}-->" class="<!--{if $tpl_mypageno == 'favorite'}--> selected<!--{/if}-->">
                        Favorites</a></li>
                <!--{/if}-->
                <li><a href="<!--{$smarty.const.TOP_URLPATH}-->" class="<!--{if $tpl_mypageno == 'change'}--> selected<!--{/if}-->">
                    Edit registration details</a></li>
                <li><a href="<!--{$smarty.const.TOP_URLPATH}-->" class="<!--{if $tpl_mypageno == 'delivery'}--> selected<!--{/if}-->">
                    Delivery options</a></li>
                <li><a href="<!--{$smarty.const.TOP_URLPATH}-->" class="<!--{if $tpl_mypageno == 'refusal'}--> selected<!--{/if}-->">
                    Cancel membership</a></li>
            <!--{/if}-->
        </ul>

        <!--▼現在のポイント-->
        <!--{if $point_disp !== false}-->
            <div class="point_announce clearfix">
                <p>Welcome&nbsp;/&nbsp;
                    <span class="user_name"><!--{$CustomerName1|h}--> <!--{$CustomerName2|h}--></span>
                    <!--{if $smarty.const.USE_POINT !== false}-->&nbsp;
                        You currently have&nbsp;<span class="point st"><!--{$CustomerPoint|number_format|default:"0"|h}--> pts</span>
                    <!--{/if}-->
                </p>
            </div>
        <!--{/if}-->
        <!--▲現在のポイント-->
    <!--{/strip}-->

</div>
<!--▲NAVI-->
