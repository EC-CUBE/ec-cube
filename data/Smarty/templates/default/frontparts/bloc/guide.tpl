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

<div class="block_outer">
    <div id="guide_area" class="block_body">
        <!--{strip}-->
            <ul class="button_like">
                <li>
                    <a href="<!--{$smarty.const.ROOT_URLPATH}-->abouts/<!--{$smarty.const.DIR_INDEX_PATH}-->" class="<!--{if $tpl_page_category == "abouts"}--> selected<!--{/if}-->"
                    >当サイトについて</a></li>
                <li>
                    <a href="<!--{$smarty.const.HTTPS_URL}-->contact/<!--{$smarty.const.DIR_INDEX_PATH}-->" class="<!--{if $tpl_page_category == "contact"}--> selected<!--{/if}-->"
                    >お問い合わせ</a></li>
                <li>
                    <a href="<!--{$smarty.const.ROOT_URLPATH}-->order/<!--{$smarty.const.DIR_INDEX_PATH}-->" class="<!--{if $tpl_page_category == "order"}--> selected<!--{/if}-->"
                    >特定商取引に関する表記</a></li>
                <li>
                    <a href="<!--{$smarty.const.ROOT_URLPATH}-->guide/privacy.php" class="<!--{if $tpl_page_category == "order"}--> selected<!--{/if}-->"
                    >プライバシーポリシー</a></li>
            </ul>
        <!--{/strip}-->
        <div style="height: 0px; overflow: hidden;"></div><!--{* IE6ハック(背景乱れ防止) *}-->
    </div>
</div>
