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
<form name="form1" id="form1" method="post" action="./product_class.php">
<input type="hidden" name="mode" value="" />
<input type="hidden" name="product_id" value="" />
	<div id="complete">
		<div class="complete-top"></div>
		<div class="contents">
			<div class="message">
				登録が完了致しました。
			</div>
		</div>
		<div class="btn-area-top"></div>
		<div class="btn-area">
			<ul>
				<li><a class="btn-action" href="./product.php"><span class="btn-next">続けて登録を行う</span></a></li>
				<li><a class="btn-action" href="?" onclick="fnModeSubmit('pre_edit', 'product_id', '<!--{$arrForm.product_id}-->'); return false;"><span class="btn-next">この商品の規格を登録する</span></a></li>
			</ul>
		</div>
		<div class="btn-area-bottom"></div>
	</div>
</form>



<!--{* オペビルダー用 *}-->
<!--{if "sfViewAdminOpe"|function_exists === TRUE}-->
<!--{include file=`$smarty.const.MODULE_REALDIR`mdl_opebuilder/admin_ope_view.tpl}-->
<!--{/if}-->
