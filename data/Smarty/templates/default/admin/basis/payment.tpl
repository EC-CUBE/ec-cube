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
<!--★★メインコンテンツ★★-->
<table width="878" border="0" cellspacing="0" cellpadding="0" summary=" ">
<form name="form1" id="form1" method="post" action="<!--{$smarty.server.PHP_SELF|escape}-->">
<input type="hidden" name="mode" value="edit">
<input type="hidden" name="payment_id" value="<!--{$tpl_payment_id}-->">
	<tr valign="top">
		<td background="<!--{$TPL_DIR}-->img/contents/navi_bg.gif" height="402">
			<!--▼SUB NAVI-->
			<!--{include file=$tpl_subnavi}-->
			<!--▲SUB NAVI-->
		</td>
		<td class="mainbg">
			<!--▼登録テーブルここから-->
			<table width="737" border="0" cellspacing="0" cellpadding="0" summary=" ">
				<!--メインエリア-->
				<tr>
					<td align="center">
						<table width="706" border="0" cellspacing="0" cellpadding="0" summary=" ">
							<tr><td height="14"></td></tr>
							<tr>
								<td colspan="3"><img src="<!--{$TPL_DIR}-->img/contents/main_top.jpg" width="706" height="14" alt=""></td>
							</tr>
							<tr>
								<td background="<!--{$TPL_DIR}-->img/contents/main_left.jpg"><img src="<!--{$TPL_DIR}-->img/common/_.gif" width="14" height="1" alt=""></td>
								<td bgcolor="#cccccc">
								<table width="678" border="0" cellspacing="0" cellpadding="0" summary=" ">
									<tr>
										<td colspan="3"><img src="<!--{$TPL_DIR}-->img/contents/contents_title_top.gif" width="678" height="7" alt=""></td>
									</tr>
									<tr>
										<td background="<!--{$TPL_DIR}-->img/contents/contents_title_left_bg.gif"><img src="<!--{$TPL_DIR}-->img/contents/contents_title_left.gif" width="22" height="12" alt=""></td>
										<td bgcolor="#636469" width="638" class="fs14n"><span class="white"><!--コンテンツタイトル-->支払方法設定</span></td>
										<td background="<!--{$TPL_DIR}-->img/contents/contents_title_right_bg.gif"><img src="<!--{$TPL_DIR}-->img/common/_.gif" width="18" height="1" alt=""></td>
									</tr>
									<tr>
										<td colspan="3"><img src="<!--{$TPL_DIR}-->img/contents/contents_title_bottom.gif" width="678" height="7" alt=""></td>
									</tr>
									<tr>
										<td colspan="3"><img src="<!--{$TPL_DIR}-->img/contents/main_bar.jpg" width="678" height="10" alt=""></td>
									</tr>
								</table>

								<table width="678" border="0" cellspacing="1" cellpadding="8" summary=" ">
									<tr align="center" bgcolor="#f2f1ec" class="fs12n">
										<td width="134">支払方法</td>
										<td width="69">手数料（円）</td>
										<td width="124">利用条件</td>
										<td width="84">配送サービス</td>
										<td width="44">編集</td>
										<td width="44">削除</td>
										<td width="69">移動</td>
									</tr>
									<!--{section name=cnt loop=$arrPaymentListFree}-->
									<tr bgcolor="#ffffff" class="fs12n">
										<td><!--{$arrPaymentListFree[cnt].payment_method|escape}--></td>
										<!--{if $arrPaymentListFree[cnt].charge_flg == 2}-->
											<td align="center">-</td>
										<!--{else}-->
											<td align="right"><!--{$arrPaymentListFree[cnt].charge|escape|number_format}--></td>
										<!--{/if}-->
										<td align="center">
											<table border="0" cellspacing="0" cellpadding="0" summary=" ">
												<tr class="fs12">
													<td align="center" width="80"><!--{if $arrPaymentListFree[cnt].rule > 0}--><!--{$arrPaymentListFree[cnt].rule|escape|number_format}--><!--{else}-->0<!--{/if}-->円</td>
													<td align="center"> 〜 </td>
													<td align="center" width="80"><!--{if $arrPaymentListFree[cnt].upper_rule > 0}--><!--{$arrPaymentListFree[cnt].upper_rule|escape|number_format}-->円<!--{else}-->無制限<!--{/if}--></td>
												</tr>
											</table>
										<td><!--{assign var=key value="`$arrPaymentListFree[cnt].deliv_id`"}--><!--{$arrDelivList[$key]|default:"未登録"}--></td>
										<td align="center"><!--{if $arrPaymentListFree[cnt].fix != 1}--><a href="<!--{$smarty.server.PHP_SELF|escape}-->" onclick="win03('./payment_input.php?mode=pre_edit&payment_id=<!--{$arrPaymentListFree[cnt].payment_id}-->','payment_input','530','400'); return false;">編集</a><!--{else}-->-<!--{/if}--></td>
										<td align="center"><!--{if $arrPaymentListFree[cnt].fix != 1}--><a href="<!--{$smarty.server.PHP_SELF|escape}-->" onclick="fnModeSubmit('delete', 'payment_id', <!--{$arrPaymentListFree[cnt].payment_id}-->); return false;">削除</a><!--{else}-->-<!--{/if}--></td>
										<td align="center">
										<!--{if $smarty.section.cnt.iteration != 1}-->
										<a href="<!--{$smarty.server.PHP_SELF|escape}-->" onclick="fnModeSubmit('up','payment_id', <!--{$arrPaymentListFree[cnt].payment_id}-->); return false;">上へ</a>
										<!--{/if}-->
										<!--{if $smarty.section.cnt.iteration != $smarty.section.cnt.last}-->
										<a href="<!--{$smarty.server.PHP_SELF|escape}-->" onclick="fnModeSubmit('down','payment_id', <!--{$arrPaymentListFree[cnt].payment_id}-->); return false;">下へ</a>
										<!--{/if}-->
										</td>
									</tr>
									<!--{/section}-->
								</table>

								<table width="678" border="0" cellspacing="0" cellpadding="0" summary=" ">
									<tr>
										<td bgcolor="#cccccc"><img src="<!--{$TPL_DIR}-->img/common/_.gif" width="1" height="5" alt=""></td>
										<td><img src="<!--{$TPL_DIR}-->img/contents/tbl_top.gif" width="676" height="7" alt=""></td>
										<td bgcolor="#cccccc"><img src="<!--{$TPL_DIR}-->img/common/_.gif" width="1" height="5" alt=""></td>
									</tr>
									<tr>
										<td bgcolor="#cccccc"><img src="<!--{$TPL_DIR}-->img/common/_.gif" width="1" height="10" alt=""></td>
										<td bgcolor="#e9e7de" align="center">
										<table border="0" cellspacing="0" cellpadding="0" summary=" ">
											<tr>
												<td><input type="button" name="subm2" value="支払方法を追加" onclick="win03('./payment_input.php','payment_input','550','400');" /></td>
											</tr>
										</table>
										</td>
										<td bgcolor="#cccccc"><img src="<!--{$TPL_DIR}-->img/common/_.gif" width="1" height="10" alt=""></td>
									</tr>
									<tr>
										<td colspan="3"><img src="<!--{$TPL_DIR}-->img/contents/tbl_bottom.gif" width="678" height="8" alt=""></td>
									</tr>
								</table>
								</td>
								<td background="<!--{$TPL_DIR}-->img/contents/main_right.jpg"><img src="<!--{$TPL_DIR}-->img/common/_.gif" width="14" height="1" alt=""></td>
							</tr>
							<tr>
								<td colspan="3"><img src="<!--{$TPL_DIR}-->img/contents/main_bottom.jpg" width="706" height="14" alt=""></td>
							</tr>
							<tr><td height="30"></td></tr>
						</table>
					</td>
				</tr>
				<!--メインエリア-->
			</table>
			<!--▲登録テーブルここまで-->
		</td>
	</tr>
</form>
</table>
<!--★★メインコンテンツ★★-->
