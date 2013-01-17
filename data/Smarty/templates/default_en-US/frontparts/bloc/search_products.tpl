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
    <div id="search_area">
    <h2><img src="<!--{$TPL_URLPATH}-->img/title/icon_bloc_search.gif" alt="" /><span class="title">Search</span></h2>
        <div class="block_body">
            <!--検索フォーム-->
            <form name="search_form" id="search_form" method="get" action="<!--{$smarty.const.ROOT_URLPATH}-->products/list.php">
            <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
            <dl class="formlist">
                <dt>Product categories</dt>
                <dd><input type="hidden" name="mode" value="search" />
                <select name="category_id" class="box145">
                    <option label="All products" value="">All products</option>
                    <!--{html_options options=$arrCatList selected=$category_id}-->
                </select>
                </dd>
            </dl>
            <dl class="formlist">
                <!--{if $arrMakerList}-->
                <dt>Manufacturers</dt>
                <dd><select name="maker_id" class="box145">
                    <option label="All manufacturers" value="">All manufacturers</option>
                    <!--{html_options options=$arrMakerList selected=$maker_id}-->
                </select>
                </dd>
            </dl>
            <dl class="formlist">
                <!--{/if}-->
                <dt>Product name</dt>
                <dd><input type="text" name="name" class="box140" maxlength="50" value="<!--{$smarty.get.name|h}-->" /></dd>
            </dl>
            <p class="btn">
				<button class="bt02" name="search">Search</button>
			</p>
            </form>
        </div>
    </div>
</div>
