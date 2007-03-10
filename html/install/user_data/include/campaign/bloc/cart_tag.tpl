<!--{*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *}-->
<div id="cart_tag_<{assign_product_id}>">
<!--{assign var=id value=$arrProducts[<{assign_product_id}>].product_id}-->
<!--▼買い物かご-->
<table width="420" border="0" cellspacing="0" cellpadding="0" summary=" ">
	<tr><td height=5></td></tr>
	<tr valign="top" align="right" id="price">
		<td id="right" colspan=2>
			<table cellspacing="0" cellpadding="0" summary=" " id="price">
				<tr>
					<td align="center">
					<table width="285" cellspacing="0" cellpadding="0" summary=" ">
						<!--{if $tpl_classcat_find1[$id]}-->
						<!--{assign var=class1 value=classcategory_id`$id`_1}-->
						<!--{assign var=class2 value=classcategory_id`$id`_2}-->
						<tr><td colspan="2" height="10" align="center" class="fs12"><span class="redst"><!--{if $arrErr[$class1] != ""}-->※ <!--{$tpl_class_name1[$id]}-->を入力して下さい。<!--{/if}--></span></td></tr>
						<tr>
							<td align="right" class="fs12st"><!--{$tpl_class_name1[$id]|escape}-->： </td>
							<td>
								<select name="<!--{$class1}-->" style="<!--{$arrErr[$class1]|sfGetErrorColor}-->" onchange="lnSetSelect('<!--{$class1}-->', '<!--{$class2}-->', '<!--{$id}-->','');">
								<option value="">選択してください</option>
								<!--{html_options options=$arrClassCat1[$id] selected=$arrForm[$class1]}-->
								</select>
							</td>
						</tr>
						<!--{/if}-->
						<!--{if $tpl_classcat_find2[$id]}-->
						<tr><td colspan="2" height="5" align="center" class="fs12"><span class="redst"><!--{if $arrErr[$class2] != ""}-->※ <!--{$tpl_class_name2[$id]}-->を入力して下さい。<!--{/if}--></span></td></tr>
						<tr>
							<td align="right" class="fs12st"><!--{$tpl_class_name2[$id]|escape}-->： </td>
							<td>
								<select name="<!--{$class2}-->" style="<!--{$arrErr[$class2]|sfGetErrorColor}-->">
								<option value="">選択してください</option>
								</select>
							</td>
						</tr>
						<!--{/if}-->
						<!--{assign var=quantity value=quantity`$id`}-->		
						<tr><td colspan="2" height="10" align="center" class="fs12"><span class="redst"><!--{$arrErr[$quantity]}--></span></td></tr>
						<tr>
							<td align="right" width="115" class="fs12st">個数： 
								<!--{if $arrErr.quantity != ""}--><br/><span class="redst"><!--{$arrErr.quantity}--></span><!--{/if}-->
								<input type="text" name="<!--{$quantity}-->" size="3" class="box3" value="<!--{$arrForm[$quantity]|default:1}-->" maxlength=<!--{$smarty.const.INT_LEN}--> style="<!--{$arrErr[$quantity]|sfGetErrorColor}-->" >
							</td>
							<td width="170" align="center">
								<a href="" onclick="fnChangeAction('<!--{$smarty.server.REQUEST_URI|escape}-->#product<!--{$id}-->'); fnModeSubmit('cart','product_id','<!--{$id}-->'); return false;" onmouseover="chgImg('<!--{$smarty.const.URL_DIR}-->img/products/b_cartin_on.gif','cart<!--{$id}-->');" onmouseout="chgImg('<!--{$smarty.const.URL_DIR}-->img/products/b_cartin.gif','cart<!--{$id}-->');"><img src="<!--{$smarty.const.URL_DIR}-->img/products/b_cartin.gif" width="115" height="25" alt="カゴに入れる" name="cart<!--{$id}-->" id="cart<!--{$id}-->" /></a>
							</td>
						</tr>
						<tr><td height="10"></td></tr>
					</table>
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>
</div>