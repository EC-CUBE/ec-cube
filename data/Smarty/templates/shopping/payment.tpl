<!--{*
 * Copyright(c) 2000-2006 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *}-->
<!--▼CONTENTS-->
<table width="760" border="0" cellspacing="0" cellpadding="0" summary=" ">
	<tr>
		<td align="center" bgcolor="#ffffff">
		<!--▼MAIN ONTENTS-->
		<!--購入手続きの流れ-->
		<table width="700" border="0" cellspacing="0" cellpadding="0" summary=" ">
			<tr>
				<td><img src="<!--{$smarty.const.URL_DIR}-->img/shopping/flow02.gif" width="700" height="36" alt="購入手続きの流れ"></td>
			</tr>
			<tr><td height="15"></td></tr>
		</table>
		<!--購入手続きの流れ-->

		<table width="700" border="0" cellspacing="0" cellpadding="0" summary=" ">
			<tr>
				<td><img src="<!--{$smarty.const.URL_DIR}-->img/shopping/payment_title.jpg" width="700" height="40" alt="お支払い方法、お届け時間等の指定"></td>
			</tr>
			<tr><td height="25"></td></tr>
		</table>
		<table width="670" border="0" cellspacing="0" cellpadding="0" summary=" ">
			<tr>
				<td><img src="<!--{$smarty.const.URL_DIR}-->img/shopping/subtitle01.gif" width="670" height="33" alt="お支払方法の指定"></td>
			</tr>
			<tr><td height="10"></td></tr>
			<tr>
				<td class="fs12n">お支払方法をご選択ください。</td>
			</tr>
			<tr><td height="20"></td></tr>
			<form name="form1" id="form1" method="post" action="<!--{$smarty.server.PHP_SELF}-->">
			<input type="hidden" name="mode" value="confirm">
			<input type="hidden" name="uniqid" value="<!--{$tpl_uniqid}-->">
			<tr><td class="fs12">
				<!--{assign var=key value="payment_id"}-->
				<!--{if $arrErr[$key] != ""}--><span class="redst"><!--{$arrErr[$key]}--></span><!--{/if}-->
			</td></tr>
			<tr>
				<td bgcolor="#cccccc">
				<!--お支払い方法ここから-->
				<table width="670" border="0" cellspacing="1" cellpadding="10" summary=" ">
					<tr bgcolor="#f0f0f0">
						<td width="37" align="center" class="fs12n">選択</td>
						<td width="590" align="center" class="fs12n" colspan="2">お支払方法</td>
					</tr>
					<!--{section name=cnt loop=$arrPayment}-->
					<tr bgcolor="#ffffff">
						<td halign="center" align="center"><input type="radio" id="pay_<!--{$smarty.section.cnt.iteration}-->" name="<!--{$key}-->" onclick="fnModeSubmit('payment', '', '');" value="<!--{$arrPayment[cnt].payment_id}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" <!--{$arrPayment[cnt].payment_id|sfGetChecked:$arrForm[$key].value}--> /></td>
						<td class="fs12n" width="90"><label for="pay_<!--{$smarty.section.cnt.iteration}-->"><!--{$arrPayment[cnt].payment_method|escape}--><!--{if $arrPayment[cnt].note != ""}--><!--{/if}--></label></td>
						<td width="500">
						<!--{if $arrPayment[cnt].payment_image != ""}-->
						<img src="<!--{$smarty.const.IMAGE_SAVE_URL}--><!--{$arrPayment[cnt].payment_image}-->">
						<!--{/if}-->
						</td>
					</tr>
					<!--{/section}-->	
				</table>
				<!--お支払い方法ここまで-->
				</td>
			</tr>
			<tr><td height="40"></td></tr>
			<tr>
				<td><img src="<!--{$smarty.const.URL_DIR}-->img/shopping/subtitle02.gif" width="670" height="33" alt="お届け時間の指定"></td>
			</tr>
			<tr><td height="10"></td></tr>
			<tr>
				<td class="fs12n">ご希望の方は、お届け時間を選択してください。</td>
			</tr>
			<tr><td height="10"></td></tr>
			<tr>
				<td class="fs12n">
				<!--★配達日指定★-->
					<!--{assign var=key value="deliv_date"}-->
					<span class="red"><!--{$arrErr[$key]}--></span>
					<strong>お届け日指定：</strong>&nbsp;
					<!--{if !$arrDelivDate}-->
						ご指定頂けません。
					<!--{else}-->
						<select name="<!--{$key}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->">	
						<option value="" selected="">指定なし</option>
						<!--{html_options options=$arrDelivDate selected=$arrForm[$key].value}-->
						</select>
					<!--{/if}-->
					&nbsp;&nbsp;&nbsp;
					<!--{assign var=key value="deliv_time_id"}-->
					<span class="red"><!--{$arrErr[$key]}--></span>
					<strong>お届け時間指定：</strong>&nbsp;<select name="<!--{$key}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->">	
					<option value="" selected="">指定なし</option>
					<!--{html_options options=$arrDelivTime selected=$arrForm[$key].value}-->
					</select>
				</td>
			</tr>
		
			<tr><td height="40"></td></tr>
			<tr>
				<td><img src="<!--{$smarty.const.URL_DIR}-->img/shopping/subtitle03.gif" width="670" height="33" alt="その他お問い合わせ"></td>
			</tr>
			<tr><td height="10"></td></tr>
			<tr>
				<td class="fs12n">その他お問い合わせ事項がございましたら、こちらにご入力ください。</td>
			</tr>
			<tr><td height="10"></td></tr>
			<tr>
				<td class="fs12"><!--★その他お問い合わせ事項★-->
					<!--{assign var=key value="message"}-->
					<span class="red"><!--{$arrErr[$key]}--></span>
					<textarea name="<!--{$key}-->" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" cols="80" rows="8" class="area80" wrap="head"><!--{$arrForm[$key].value|escape}--></textarea>
					<span class="red"> （<!--{$smarty.const.LTEXT_LEN}-->文字まで）</span>
				</td>
			</tr>
			<tr><td height="20"></td></tr>
			
			<!-- ▼ポイント使用 ここから -->
			<!--{if $tpl_login == 1}-->
			<tr>
				<td><img src="<!--{$smarty.const.URL_DIR}-->img/shopping/subtitle_point.jpg" width="670" height="32" alt="ポイント使用の指定" /></td>
			</tr>
			<tr><td height="10"></td></tr>
			<tr>
				<td class="fs12">
					<span class="redst">1ポイントを1円</span>として使用する事ができます。<br />
					使用する場合は、「ポイントを使用する」にチェックを入れた後、使用するポイントをご記入ください。
				</td>
			</tr>
			<tr><td height="10"></td></tr>
			<tr><td>
				
				<table width="670" cellspacing="3" cellpadding="5" summary=" " bgcolor="#d0d0d0">
					<tr>
						<td bgcolor="#ffffff" align="center">
						<table cellspacing="0" cellpadding="0" summary=" ">
							<tr>
								<td class="fs12" colspan="2"><!--{$objCustomer->getValue('name01')|escape}--> <!--{$objCustomer->getValue('name02')|escape}-->様の、現在の所持ポイントは「<span class="redst"><!--{$tpl_user_point|default:0}-->Pt</span>」です。</td>
							</tr>
							<tr>
								<td class="fs12">今回ご購入合計金額：<span class="redst"><!--{$arrData.subtotal|number_format}-->円</span><span class="red">（送料、手数料を含みません。）</span></td>
							</tr>
						</table>
						<table cellspacing="0" cellpadding="10" summary=" " id="point03">
							<tr>
								<td>
								<table cellspacing="0" cellpadding="0" summary=" ">
									<tr>
										<td class="fs12"><input type="radio" id="point_on" name="point_check" value="1" <!--{$arrForm.point_check.value|sfGetChecked:1}--> onclick="fnCheckInputPoint();" /><label for="point_on">ポイントを使用する</label></td>
									</tr>
									<tr><td height="2"></td></tr>
									<tr>
										<td class="fs12"><span class="indent18">
										<!--{assign var=key value="use_point"}-->
										<span class="red"><!--{$arrErr[$key]}--></span>
										今回のお買い物で、<input type="text" name="<!--{$key}-->" value="<!--{$arrForm[$key].value|default:$tpl_user_point}-->" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" size="6" class="box6" />&nbsp;ポイントを使用する。</span></td>
									</tr>
									<tr>
										<td height="12"><img src="../img/shopping/line02.gif" width="514" height="1" alt="" /></td>
									</tr>
									<tr>
										<td class="fs12"><input type="radio" id="point_off" name="point_check" value="2" <!--{$arrForm.point_check.value|sfGetChecked:2}--> onclick="fnCheckInputPoint();" /><label for="point_off">ポイントを使用しない</label></td>
									</tr>
								</table>
								</td>
							</tr>

						</table>
						</td>
					</tr>
				</table>
				<!--{/if}-->
				</td>
			</tr>
			<tr><td height="20"></td></tr>
			<!-- ▲ポイント使用 ここまで -->			
			
			<tr>
				<td align="center">
					<a href="<!--{$smarty.server.PHP_SELF}-->" onmouseover="chgImg('<!--{$smarty.const.URL_DIR}-->img/common/b_back_on.gif','back03')" onmouseout="chgImg('<!--{$smarty.const.URL_DIR}-->img/common/b_back.gif','back03')" onclick="history.back(); return false;" /><img src="<!--{$smarty.const.URL_DIR}-->img/common/b_back.gif" width="150" height="30" alt="戻る" border="0" name="back03" id="back03" ></a><img src="../img/_.gif" width="20" height="" alt="" />
					<input type="image" onmouseover="chgImgImageSubmit('<!--{$smarty.const.URL_DIR}-->img/common/b_next_on.gif',this)" onmouseout="chgImgImageSubmit('<!--{$smarty.const.URL_DIR}-->img/common/b_next.gif',this)" src="<!--{$smarty.const.URL_DIR}-->img/common/b_next.gif" width="150" height="30" alt="次へ" border="0" name="next" id="next" />
				</td>
			</tr>
			</form>
		</table>
		<!--▲MAIN ONTENTS-->
		</td>
	</tr>
</table>
<!--▲CONTENTS-->
	
	