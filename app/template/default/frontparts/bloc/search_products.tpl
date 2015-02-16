<!--{*
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2015 LOCKON CO.,LTD. All Rights Reserved.
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
<aside id="block_search" class="block_outer">
	<div class="block_inner">
		<h2 class="title">検索条件</h2>
		<div class="block_body">
			<!--検索フォーム-->
			<form name="search_form" id="search_form" method="get" action="<!--{$smarty.const.ROOT_URLPATH}-->products/list.php">
				<input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
				<dl class="formlist">
					<dt>商品カテゴリから選ぶ</dt>
					<dd>
						<input type="hidden" name="mode" value="search" />
						<select name="category_id" class="w96p">
						<option label="全ての商品" value="">全ての商品</option>
						<!--{html_options options=$arrCatList selected=$category_id}-->
						</select>
					</dd>
				</dl>
				<dl class="formlist">
				<!--{if $arrMakerList}-->
					<dt>メーカーから選ぶ</dt>
					<dd>
						<select name="maker_id" class="w96p">
						<option label="全てのメーカー" value="">全てのメーカー</option>
						<!--{html_options options=$arrMakerList selected=$maker_id}-->
						</select>
					</dd>
				</dl>
				<dl class="formlist">
				<!--{/if}-->
					<dt>商品名を入力</dt>
					<dd><input type="text" name="name" class="w96p" maxlength="50" value="<!--{$smarty.get.name|h}-->" /></dd>
				</dl>
				<div class="ecbtn"><input type="submit" class="btn btn-success" value="検索" name="search" /></div>
			</form>
		</div>
	</div>
</aside>
<!--{/strip}-->
