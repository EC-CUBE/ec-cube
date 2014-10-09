<!--{*
/*
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
 */
*}-->

<!--{strip}-->
    <!--{1|numeric_emoji}-->商品カテゴリ<br>
    <!--{section name=cnt loop=$arrCat}-->
        <!--{assign var=disp_name value="`$arrCat[cnt].category_name`"}-->
        <!--{if $arrCat[cnt].has_children}-->
            <!--{assign var=path value="`$smarty.const.ROOT_URLPATH`products/category_list.php"}-->
        <!--{else}-->
            <!--{assign var=path value="`$smarty.const.ROOT_URLPATH`products/list.php"}-->
        <!--{/if}-->
        　<font color="<!--{cycle values="#000000,#880000,#8888ff,#88ff88,#ff0000"}-->">■</font><a href="<!--{$path}-->?category_id=<!--{$arrCat[cnt].category_id}-->"><!--{$disp_name|sfCutString:10:false|h}--></a><br>
    <!--{/section}-->
<!--{/strip}-->
