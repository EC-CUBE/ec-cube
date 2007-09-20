<!--{*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *}-->
<!--現在のカゴの中ここから-->
<table width="166" border="0" cellspacing="0" cellpadding="0" summary=" ">
	<tr>
		<td colspan="3"><img src="<!--{$TPL_DIR}-->img/side/title_cartin.jpg" width="166" height="35" alt="現在のカゴの中"></td>
	</tr>
	<tr>
		<td bgcolor="#cccccc"><img src="<!--{$TPL_DIR}-->img/common/_.gif" width="1" height="10" alt=""></td>
		<td align="center" bgcolor="#ffffff">
		<table width="146" border="0" cellspacing="0" cellpadding="0" summary=" ">
			<tr><td height="5"></td></tr>
			<tr>
				<td class="fs10">商品数：<!--{$arrCartList.0.TotalQuantity|number_format|default:0}-->点</td>
			</tr>
			<tr><td height="10"><img src="<!--{$TPL_DIR}-->img/side/line_146.gif" width="146" height="1" alt=""></td></tr>
			<tr>
				<td class="fs12"><span class="redst">合計：<!--{$arrCartList.0.ProductsTotal|number_format|default:0}-->円</span></td>
			</tr>
			<tr><td height="5"><!--{$arrCartList.0.free_rule}--></td></tr>
			
			<!-- カゴの中に商品がある場合にのみ表示 -->
			<!--{if $arrCartList.0.TotalQuantity > 0 and $arrCartList.0.free_rule > 0}-->
			<tr>
				<td class="fs10">
				<!--{if $arrCartList.0.deliv_free > 0}-->
					送料手数料無料まであと<!--{$arrCartList.0.deliv_free|number_format|default:0}-->円（税込）です。
				<!--{else}-->
					現在、送料は「<span class="redst">無料</span>」です。
				<!--{/if}-->
				</td>
			</tr>
			<tr><td height="10"></td></tr>
			<!--{/if}-->
			<tr>
				<td align="center"><a href="<!--{$smarty.const.URL_DIR}-->cart/index.php" onmouseover="chgImg('<!--{$TPL_DIR}-->img/side/button_cartin_on.gif','button_cartin');" onmouseout="chgImg('<!--{$TPL_DIR}-->img/side/button_cartin.gif','button_cartin');"><img src="<!--{$TPL_DIR}-->img/side/button_cartin.gif" width="87" height="22" alt="カゴの中を見る" border="0" name="button_cartin"></a></td>
			</tr>
		</table>
		</td>
		<td bgcolor="#cccccc"><img src="<!--{$TPL_DIR}-->img/common/_.gif" width="1" height="10" alt=""></td>
	</tr>
	<tr>
		<td colspan="3"><img src="<!--{$TPL_DIR}-->img/side/flame_bottom01.gif" width="166" height="15" alt=""></td>
	</tr>
	<tr><td height="10"></td></tr>
</table>
<!--現在のカゴの中ここまで-->
