<!--★★メインコンテンツ★★-->
<table width="878" border="0" cellspacing="0" cellpadding="0" summary=" ">
<form name="form1" id="form1" method="post" action="<!--{$smarty.server.PHP_SELF}-->">
<input type="hidden" name="mode" value="edit">
<input type="hidden" name="payment_id" value="<!--{$tpl_payment_id}-->">
	<tr valign="top">
		<td background="/img/contents/navi_bg.gif" height="402">
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
								<td colspan="3"><img src="/img/contents/main_top.jpg" width="706" height="14" alt=""></td>
							</tr>
							<tr>
								<td background="/img/contents/main_left.jpg"><img src="/img/common/_.gif" width="14" height="1" alt=""></td>
								<td bgcolor="#cccccc">
								<table width="678" border="0" cellspacing="0" cellpadding="0" summary=" ">
									<tr>
										<td colspan="3"><img src="/img/contents/contents_title_top.gif" width="678" height="7" alt=""></td>
									</tr>
									<tr>
										<td background="/img/contents/contents_title_left_bg.gif"><img src="/img/contents/contents_title_left.gif" width="22" height="12" alt=""></td>
										<td bgcolor="#636469" width="638" class="fs14n"><span class="white"><!--コンテンツタイトル-->支払方法設定</span></td>
										<td background="/img/contents/contents_title_right_bg.gif"><img src="/img/common/_.gif" width="18" height="1" alt=""></td>
									</tr>
									<tr>
										<td colspan="3"><img src="/img/contents/contents_title_bottom.gif" width="678" height="7" alt=""></td>
									</tr>
									<tr>
										<td colspan="3"><img src="/img/contents/main_bar.jpg" width="678" height="10" alt=""></td>
									</tr>
								</table>

								<table width="678" border="0" cellspacing="1" cellpadding="8" summary=" ">
									<tr align="center" bgcolor="#f2f1ec" class="fs12n">
										<td width="116">支払方法</td>
										<td width="96">手数料（円）</td>
										<td width="194">利用条件</td>
										<td width="116">配送サービス</td>
										<td width="66">編集</td>
										<td width="66">削除</td>
										<td width="86">移動</td>
									</tr>
									<!--{section name=cnt loop=$arrPaymentListFree}-->
									<tr bgcolor="#ffffff" class="fs12n">
										<td width="116"><!--{$arrPaymentListFree[cnt].payment_method|escape}--></td>
										<td width="96" align="right"><!--{$arrPaymentListFree[cnt].charge|escape|number_format}--></td>
										<td width="194" align="center"><!--{if $arrPaymentListFree[cnt].rule > 0}--><!--{$arrPaymentListFree[cnt].rule|escape|number_format}--><!--{else}-->無制限<!--{/if}--> 〜 <!--{if $arrPaymentListFree[cnt].upper_rule > 0}--><!--{$arrPaymentListFree[cnt].upper_rule|escape|number_format}--><!--{else}-->無制限<!--{/if}--></td>
										<td width="116"><!--{assign var=key value="`$arrPaymentListFree[cnt].deliv_id`"}--><!--{$arrDelivList[$key]|default:"未登録"}--></td>
										<td width="66" align="center"><a href="<!--{$smarty.server.PHP_SELF}-->" onclick="win03('./payment_input.php?mode=pre_edit&payment_id=<!--{$arrPaymentListFree[cnt].payment_id}-->','payment_input','500','420'); return false;">編集</a></td>
										<td width="66" align="center"><a href="<!--{$smarty.server.PHP_SELF}-->" onclick="fnModeSubmit('delete', 'payment_id', <!--{$arrPaymentListFree[cnt].payment_id}-->); return false;">削除</a></td>
										<td width="86" align="center">
										<!--{if $smarty.section.cnt.iteration != 1}-->
										<a href="<!--{$smarty.server.PHP_SELF}-->" onclick="fnModeSubmit('up','payment_id', <!--{$arrPaymentListFree[cnt].payment_id}-->); return false;">上へ</a>
										<!--{/if}-->
										<!--{if $smarty.section.cnt.iteration != $smarty.section.cnt.last}-->
										<a href="<!--{$smarty.server.PHP_SELF}-->" onclick="fnModeSubmit('down','payment_id', <!--{$arrPaymentListFree[cnt].payment_id}-->); return false;">下へ</a>
										<!--{/if}-->
										</td>
									</tr>
									<!--{/section}-->
								</table>

								<table width="678" border="0" cellspacing="0" cellpadding="0" summary=" ">
									<tr>
										<td bgcolor="#cccccc"><img src="/img/common/_.gif" width="1" height="5" alt=""></td>
										<td><img src="/img/contents/tbl_top.gif" width="676" height="7" alt=""></td>
										<td bgcolor="#cccccc"><img src="/img/common/_.gif" width="1" height="5" alt=""></td>
									</tr>
									<tr>
										<td bgcolor="#cccccc"><img src="/img/common/_.gif" width="1" height="10" alt=""></td>
										<td bgcolor="#e9e7de" align="center">
										<table border="0" cellspacing="0" cellpadding="0" summary=" ">
											<tr>
												<td><input type="button" name="subm2" value="支払方法を追加" onclick="win03('./payment_input.php','payment_input','550','400');" /></td>
											</tr>
										</table>
										</td>
										<td bgcolor="#cccccc"><img src="/img/common/_.gif" width="1" height="10" alt=""></td>
									</tr>
									<tr>
										<td colspan="3"><img src="/img/contents/tbl_bottom.gif" width="678" height="8" alt=""></td>
									</tr>
								</table>
								</td>
								<td background="/img/contents/main_right.jpg"><img src="/img/common/_.gif" width="14" height="1" alt=""></td>
							</tr>
							<tr>
								<td colspan="3"><img src="/img/contents/main_bottom.jpg" width="706" height="14" alt=""></td>
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