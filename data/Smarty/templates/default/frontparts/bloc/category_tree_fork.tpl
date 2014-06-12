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

<!--{strip}-->
    <ul <!--{if $treeID != ""}-->id="<!--{$treeID}-->"<!--{/if}--> style="<!--{if !$display}-->display: none;<!--{/if}-->">
        <!--{foreach from=$children item=child}-->
            <li class="level<!--{$child.level}--><!--{if in_array($child.category_id, $tpl_category_id)}--> onmark<!--{/if}-->">
                <p><a href="<!--{$smarty.const.ROOT_URLPATH}-->products/list.php?category_id=<!--{$child.category_id}-->"<!--{if in_array($child.category_id, $tpl_category_id)}--> class="onlink"<!--{/if}-->><!--{$child.category_name|h}-->(<!--{$child.product_count|default:0}-->)</a></p>
                <!--{if in_array($child.category_id, $arrParentID)}-->
                    <!--{assign var=disp_child value=1}-->
                <!--{else}-->
                    <!--{assign var=disp_child value=0}-->
                <!--{/if}-->
                <!--{if isset($child.children|smarty:nodefaults)}-->
                    <!--{include file="`$smarty.const.TEMPLATE_REALDIR`frontparts/bloc/category_tree_fork.tpl" children=$child.children display=$disp_child}-->
                <!--{/if}-->
            </li>
        <!--{/foreach}-->
    </ul>
<!--{/strip}-->
