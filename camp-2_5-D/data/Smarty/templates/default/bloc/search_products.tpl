<!--{*
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
 *}-->
<div class="bloc_outer">
    <h2><img src="<!--{$TPL_DIR}-->img/bloc/search_products/title_icon.gif" width="20" height="20" alt="*" class="title_icon" />
        検索条件</h2>
    <div id="searcharea" class="bloc_body">
        <!--検索フォーム-->
        <form name="search_form" id="search_form" method="get" action="<!--{$smarty.const.URL_DIR}-->products/list.php">

            <p><img src="<!--{$TPL_DIR}-->img/side/search_cat.gif" width="104" height="10" alt="商品カテゴリから選ぶ" />
                <input type="hidden" name="mode" value="search" />
                <select name="category_id" class="box142">
                    <option label="すべての商品" value="">全ての商品</option>
                    <!--{html_options options=$arrCatList selected=$category_id}-->
                </select>
            </p>
            <!--{if $arrMakerList}-->
            <p>メーカーから選ぶ
                <select name="maker_id" class="box142">
                    <option label="すべてのメーカー" value="">すべてのメーカー</option>
                    <!--{html_options options=$arrMakerList selected=$maker_id}-->
                </select>
            </p>
            <!--{/if}-->
            <p><img src="<!--{$TPL_DIR}-->img/side/search_name.gif" width="66" height="10" alt="商品名を入力" /></p>
            <p><input type="text" name="name" class="box142" maxlength="50" value="<!--{$smarty.get.name|escape}-->" /></p>
            <p class="btn"><input type="image" onmouseover="chgImgImageSubmit('<!--{$TPL_DIR}-->img/side/button_search_on.gif',this)" onmouseout="chgImgImageSubmit('<!--{$TPL_DIR}-->img/side/button_search.gif',this)" src="<!--{$TPL_DIR}-->img/side/button_search.gif" class="box51" alt="検索" name="search" /></p>
        </form>
    </div>
</div>
