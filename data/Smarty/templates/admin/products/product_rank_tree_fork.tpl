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

<ul <!--{if $treeID != ""}-->id="<!--{$treeID}-->"<!--{/if}--> style="<!--{if !$display}-->display: none;<!--{/if}-->">
    <!--{foreach from=$children item=child}-->
        <li class="level<!--{$child.level}-->">
            <!--{* カテゴリ名表示 *}-->
            <!--{assign var=disp_name value="`$child.category_id`.`$child.category_name`"}-->
            <!--{if $child.level != $smarty.const.LEVEL_MAX}-->
                <a href="?" onclick="eccube.setModeAndSubmit('tree', 'parent_category_id', <!--{$child.category_id}-->); return false;">
                <!--{if $arrForm.parent_category_id == $child.category_id}-->
                    <img src="<!--{$TPL_URLPATH}-->img/contents/folder_open.gif" alt="フォルダ" />
                <!--{else}-->
                    <img src="<!--{$TPL_URLPATH}-->img/contents/folder_close.gif" alt="フォルダ" />
                <!--{/if}-->
                <!--{$disp_name|sfCutString:10:false|h}--></a>
            <!--{else}-->
                <img src="<!--{$TPL_URLPATH}-->img/contents/folder_close.gif" alt="フォルダ" />
                <!--{$disp_name|sfCutString:10:false|h}-->
            <!--{/if}-->
            <!--{if in_array($child.category_id, $arrParentID)}-->
                <!--{assign var=disp_child value=1}-->
            <!--{else}-->
                <!--{assign var=disp_child value=0}-->
            <!--{/if}-->
            <!--{if isset($child.children|smarty:nodefaults)}-->
                <!--{include file="`$smarty.const.TEMPLATE_ADMIN_REALDIR`products/product_rank_tree_fork.tpl" children=$child.children treeID="f`$child.category_id`" display=$disp_child}-->
            <!--{/if}-->
        </li>
    <!--{/foreach}-->
</ul>