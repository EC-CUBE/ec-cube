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
<article id="article_shopping" class="undercolumn">
	<p class="flow_area">
		<img src="<!--{$TPL_URLPATH}-->img/picture/img_flow_01.jpg" alt="購入手続きの流れ" />
	</p>
	<h1 class="title"><!--{$tpl_title|h}--></h1>
	<p class="information">各商品のお届け先を選択してください。<br />（※数量の合計は、カゴの中の数量と合わせてください。）</p>
	<!--{if $tpl_addrmax < $smarty.const.DELIV_ADDR_MAX}-->
		<p>一覧にご希望の住所が無い場合は、「新しいお届け先を追加する」より追加登録してください。</p>
	<!--{/if}-->
	<p class="mini attention">※最大<!--{$smarty.const.DELIV_ADDR_MAX|h}-->件まで登録できます。</p>

	<!--{if $tpl_addrmax < $smarty.const.DELIV_ADDR_MAX}-->
		<p class="addbtn">
			<a href="<!--{$smarty.const.ROOT_URLPATH}-->mypage/delivery_addr.php" class="btn btn-default btn-xs" onclick="eccube.openWindow('<!--{$smarty.const.ROOT_URLPATH}-->mypage/delivery_addr.php?page=<!--{$smarty.server.SCRIPT_NAME|h}-->','new_deiv','600','640'); return false;">
				新しいお届け先を追加する
			</a>
		</p>
	<!--{/if}-->
	<form name="form1" id="form1" method="post" action="?">
		<input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
		<input type="hidden" name="uniqid" value="<!--{$tpl_uniqid}-->" />
		<input type="hidden" name="line_of_num" value="<!--{$arrForm.line_of_num.value}-->" />
		<input type="hidden" name="mode" value="confirm" />
		<table summary="商品情報">
			<!--{section name=line loop=$arrForm.line_of_num.value}-->
				<tr>
					<th class="alignC">商品</th>
				</tr>
				<!--{assign var=index value=$smarty.section.line.index}-->
				<tr>
					<td>
						<a
							<!--{if $arrForm.main_image[$index]|strlen >= 1}--> href="<!--{$smarty.const.IMAGE_SAVE_URLPATH}--><!--{$arrForm.main_image.value[$index]|sfNoImageMainList|h}-->" class="expansion" target="_blank"
							<!--{/if}-->
						>
							<img src="<!--{$smarty.const.IMAGE_SAVE_URLPATH}--><!--{$arrForm.main_list_image.value[$index]|sfNoImageMainList|h}-->" style="max-width: 65px;max-height: 65px;" alt="<!--{$arrForm.name.value[$index]|h}-->" class="fr" />
						</a>
						<!--{* 商品名 *}--><strong><!--{$arrForm.name.value[$index]|h}--></strong>
						<!--{if $arrForm.classcategory_name1.value[$index] != ""}-->
							<br /><!--{$arrForm.class_name1.value[$index]|h}-->：<!--{$arrForm.classcategory_name1.value[$index]|h}-->
						<!--{/if}-->
						<!--{if $arrForm.classcategory_name2.value[$index] != ""}-->
							<br /><!--{$arrForm.class_name2.value[$index]|h}-->：<!--{$arrForm.classcategory_name2.value[$index]|h}-->
						<!--{/if}-->
					</td>
				</tr><tr>
					<td>
						<!--{assign var=key value="quantity"}-->
						<!--{if $arrErr[$key][$index] != ''}-->
							<span class="attention"><!--{$arrErr[$key][$index]}--></span>
						<!--{/if}-->
						<!--{$arrForm.price_inctax.value[$index]|n2s}-->円 x
						<input type="text" name="<!--{$key}-->[<!--{$index}-->]" value="<!--{$arrForm[$key].value[$index]|h}-->" class="box40" style="<!--{$arrErr[$key][$index]|sfGetErrorColor}-->" maxlength="<!--{$arrForm[$key].length}-->" />
						 （料金x個数）
					</td>
				</tr><tr>
					<td>
						<input type="hidden" name="cart_no[<!--{$index}-->]" value="<!--{$index}-->" />
						<!--{assign var=key value="product_class_id"}-->
						<input type="hidden" name="<!--{$key}-->[<!--{$index}-->]" value="<!--{$arrForm[$key].value[$index]|h}-->" />
						<!--{assign var=key value="name"}-->
						<input type="hidden" name="<!--{$key}-->[<!--{$index}-->]" value="<!--{$arrForm[$key].value[$index]|h}-->" />
						<!--{assign var=key value="class_name1"}-->
						<input type="hidden" name="<!--{$key}-->[<!--{$index}-->]" value="<!--{$arrForm[$key].value[$index]|h}-->" />
						<!--{assign var=key value="class_name2"}-->
						<input type="hidden" name="<!--{$key}-->[<!--{$index}-->]" value="<!--{$arrForm[$key].value[$index]|h}-->" />
						<!--{assign var=key value="classcategory_name1"}-->
						<input type="hidden" name="<!--{$key}-->[<!--{$index}-->]" value="<!--{$arrForm[$key].value[$index]|h}-->" />
						<!--{assign var=key value="classcategory_name2"}-->
						<input type="hidden" name="<!--{$key}-->[<!--{$index}-->]" value="<!--{$arrForm[$key].value[$index]|h}-->" />
						<!--{assign var=key value="main_image"}-->
						<input type="hidden" name="<!--{$key}-->[<!--{$index}-->]" value="<!--{$arrForm[$key].value[$index]|h}-->" />
						<!--{assign var=key value="main_list_image"}-->
						<input type="hidden" name="<!--{$key}-->[<!--{$index}-->]" value="<!--{$arrForm[$key].value[$index]|h}-->" />
						<!--{assign var=key value="price"}-->
						<input type="hidden" name="<!--{$key}-->[<!--{$index}-->]" value="<!--{$arrForm[$key].value[$index]|h}-->" />
						<!--{assign var=key value="price_inctax"}-->
						<input type="hidden" name="<!--{$key}-->[<!--{$index}-->]" value="<!--{$arrForm[$key].value[$index]|h}-->" />
						<!--{assign var=key value="shipping"}-->
						<!--{if strlen($arrErr[$key][$index]) >= 1}-->
							<div class="attention"><!--{$arrErr[$key][$index]}--></div>
						<!--{/if}-->
						<select name="<!--{$key}-->[<!--{$index}-->]" style="<!--{$arrErr[$key][$index]|sfGetErrorColor}-->" class="w50p">
							<!--{html_options options=$addrs selected=$arrForm[$key].value[$index]}-->
						</select> （お届け先）
					</td>
				</tr>
			<!--{/section}-->
		</table>
		<div class="btn_area">
			<ul>
				<li>
					<a href="<!--{$smarty.const.CART_URL}-->" class="btn btn-default" name="back03" id="back03">戻る</a>
				</li>
				<li>
					<input type="submit" class="btn btn-success" value="選択したお届け先に送る" name="send_button" id="send_button" />
				</li>
			</ul>
		</div>
	</form>
</article>
