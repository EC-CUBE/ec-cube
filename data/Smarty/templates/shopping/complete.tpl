<!--▼CONTENTS-->
<table width="760" border="0" cellspacing="0" cellpadding="0" summary=" ">
	<tr>
		<td align="center" bgcolor="#ffffff">
		<!--▼MAIN ONTENTS-->
		<!--購入手続きの流れ-->
		<table width="700" border="0" cellspacing="0" cellpadding="0" summary=" ">
			<tr>
				<td><img src="/img/shopping/flow04.gif" width="700" height="36" alt="購入手続きの流れ"></td>
			</tr>
			<tr><td height="15"></td></tr>
		</table>
		<!--購入手続きの流れ-->
			
		<table width="700" border="0" cellspacing="0" cellpadding="0" summary=" ">
			<tr>
				<td><img src="/img/shopping/complete_title.jpg" width="700" height="40" alt="ご注文完了"></td>
			</tr>
			<tr><td height="15"></td></tr>
		</table>
		
		<table width="640" border="0" cellspacing="0" cellpadding="0" summary=" ">
			<tr>
				<td align="center" bgcolor="#cccccc">
				<table width="630" border="0" cellspacing="0" cellpadding="0" summary=" ">
					<tr><td height="5"></td></tr>
					<tr>
						<td align="center" bgcolor="#ffffff">
						
							<!-- ▼コンビに決済の場合には表示 -->
							<!--{if count($arrConv) > 0}-->
							<table cellspacing="0" cellpadding="0" summary=" " id="ichi">
								<tr>
									<td>
									<table cellspacing="0" cellpadding="0" summary=" " id="comp">
										<tr><td height="20"></td></tr>
										<tr>
											<td class="fs12">■コンビニ決済情報<br />
											コンビニの種類：<!--{$arrCONVENIENCE[$arrConv.cv_type]|escape}--><br />
											<!--{if $arrConv.cv_payment_url != ""}-->振込票URL(PC)：<!--{$arrConv.cv_payment_url}--><br /><!--{/if}-->
											<!--{if $arrConv.cv_payment_mobile_url != ""}-->振込票URL(モバイル)：<!--{$arrConv.cv_payment_mobile_url}--><br /><!--{/if}-->
											<!--{if $arrConv.cv_receipt_no != ""}-->振込票番号：<!--{$arrConv.cv_receipt_no}--><br /><!--{/if}-->
											<!--{if $arrConv.cv_company_code != ""}-->企業コード：<!--{$arrConv.cv_company_code}--><br /><!--{/if}-->
											<!--{if $arrConv.cv_order_no != ""}-->受付番号：<!--{$arrConv.cv_order_no}--><br /><!--{/if}-->
											支払期限:<!--{$arrConv.cv_payment_limit}--><br />
											<!--{$arrCONVENIMESSAGE[$arrConv.cv_type]}-->
										</tr>
										<tr><td height="20"></td></tr>
									</table>
									</td>
								</tr>
							</table>
						</td>
					</tr>
					<tr><td height="20"></td></tr>
					<tr>
						<td align="center" bgcolor="#ffffff">
							<!--{/if}-->						
							<!-- ▲コンビに決済の場合には表示 -->
						
							<!--ご注文完了の文章ここから-->
							<table width="590" border="0" cellspacing="0" cellpadding="0" summary=" ">
								<tr><td height="25"></td></tr>
								<tr>
									<td class="fs12"><span class="redst"><!--{$arrInfo.company_name|escape}-->の商品をご購入いただき、ありがとうございました。</span></td>
								</tr>
								<tr><td height="20"></td></tr>
								<tr>
									<td class="fs12">ただいま、ご注文の確認メールをお送りさせていただきました。 <br>
									万一、ご確認メールが届かない場合は、トラブルの可能性もありますので大変お手数ではございますがもう一度お問い合わせいただくか、お電話にてお問い合わせくださいませ。 </td>
								</tr>
								<tr><td height="15"></td></tr>
								<tr>
									<td class="fs12">今後ともご愛顧賜りますようよろしくお願い申し上げます。</td>
								</tr>
								<tr><td height="20"></td></tr>
								<tr>
									<td class="fs12"><!--{$arrInfo.company_name|escape}--><br>
									TEL：<!--{$arrInfo.tel01}-->-<!--{$arrInfo.tel02}-->-<!--{$arrInfo.tel03}--> <!--{if $arrInfo.business_hour != ""}-->（受付時間/<!--{$arrInfo.business_hour}-->）<!--{/if}--><br>
									E-mal：<a href="mailto:<!--{$arrInfo.email02|escape}-->"><!--{$arrInfo.email02|escape}--></a></td>
								</tr>
								<tr><td height="25"></td></tr>
							</table>
							<!--ご注文完了の文章ここまで-->
						</td>
					</tr>
					<tr><td height="5"></td></tr>
				</table>
				</td>
			</tr>
			<tr><td height="20"></td></tr>
			<tr align="center">
				<td><a href="/index.php" onmouseover="chgImg('/img/common/b_toppage_on.gif','b_toppage');" onmouseout="chgImg('/img/common/b_toppage.gif','b_toppage');"><img src="/img/common/b_toppage.gif" width="150" height="30" alt="トップページへ" border="0" name="b_toppage"></a></td>
			</tr>
		</table>
		<!--▲MAIN ONTENTS-->
		</td>
	</tr>
</table>
<!--▲CONTENTS-->
