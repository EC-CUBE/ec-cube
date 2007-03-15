<!--{*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
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
				<td><img src="<!--{$smarty.const.URL_DIR}-->img/shopping/flow04.gif" width="700" height="36" alt="購入手続きの流れ"></td>
			</tr>
			<tr><td height="15"></td></tr>
		</table>
		<!--購入手続きの流れ-->
			
		<table width="700" border="0" cellspacing="0" cellpadding="0" summary=" ">
			<tr>
				<td><img src="<!--{$smarty.const.URL_DIR}-->img/shopping/complete_title.jpg" width="700" height="40" alt="ご注文完了"></td>
			</tr>
			<tr><td height="15"></td></tr>
		</table>
		
		<!-- ▼クレジット(コンビニ)決済 -->
		<!--{if $arrModuleParam.module_id > 0 }-->
		<img src="<!--{$smarty.const.CREDIT_HTTP_ANALYZE_URL}-->?mid=<!--{$arrModuleParam.module_id}-->&tid=<!--{$arrModuleParam.payment_total}-->&pid=<!--{$arrModuleParam.payment_id}-->" width="0px" height="0px" border="0">
		<!--{/if}-->
		<!-- ▲クレジット(コンビニ)決済 -->
		
		<table width="640" border="0" cellspacing="0" cellpadding="0" summary=" ">
			<tr>
				<td align="center" bgcolor="#cccccc">
				<table width="630" border="0" cellspacing="0" cellpadding="0" summary=" ">
					<tr><td height="5"></td></tr>
					<tr>
						<td align="center" bgcolor="#ffffff">
							<!-- ▼その他決済情報を表示する場合は表示 -->
							<!--{if $arrOther.title.value }-->
							<table  width="590" cellspacing="0" cellpadding="0" summary=" ">
								<tr>
									<td>
									<table cellspacing="0" cellpadding="0" summary=" " id="comp">
										<tr><td height="20"></td></tr>
										<tr>
											<td class="fs12">■<!--{$arrOther.title.name}-->情報<br />
											<!--{foreach key=key item=item from=$arrOther}-->
											<!--{if $key != "title"}--><!--{if $item.name != ""}--><!--{$item.name}-->：<!--{/if}--><!--{$item.value|nl2br}--><br/><!--{/if}-->
											<!--{/foreach}-->
										</tr>
									</table>
									</td>
								</tr>
							</table>
						</td>
					</tr>
					<tr><td height="5"></td></tr>
					<tr>
						<td align="center" bgcolor="#ffffff">
							<!--{/if}-->						
							<!-- ▲コンビに決済の場合には表示 -->
						
							<!--ご注文完了の文章ここから-->
							<table width="590" border="0" cellspacing="0" cellpadding="0" summary=" ">
								<tr><td height="25"></td></tr>
								<tr>
									<td class="fs12"><span class="redst"><!--{$arrInfo.shop_name|escape}-->の商品をご購入いただき、ありがとうございました。</span></td>
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
									<td class="fs12"><!--{$arrInfo.shop_name|escape}--><br>
									TEL：<!--{$arrInfo.tel01}-->-<!--{$arrInfo.tel02}-->-<!--{$arrInfo.tel03}--> <!--{if $arrInfo.business_hour != ""}-->（受付時間/<!--{$arrInfo.business_hour}-->）<!--{/if}--><br>
									E-mail：<a href="mailto:<!--{$arrInfo.email02|escape}-->"><!--{$arrInfo.email02|escape}--></a></td>
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
				<td>
					<!--{if $is_campaign}-->
					<a href="<!--{$smarty.const.CAMPAIGN_URL}--><!--{$campaign_dir}-->/index.php" onmouseover="chgImg('<!--{$smarty.const.URL_DIR}-->img/common/b_toppage_on.gif','b_toppage');" onmouseout="chgImg('<!--{$smarty.const.URL_DIR}-->img/common/b_toppage.gif','b_toppage');"><img src="<!--{$smarty.const.URL_DIR}-->img/common/b_toppage.gif" width="150" height="30" alt="トップページへ" border="0" name="b_toppage"></a>
					<!--{else}-->
					<a href="<!--{$smarty.const.URL_DIR}-->index.php" onmouseover="chgImg('<!--{$smarty.const.URL_DIR}-->img/common/b_toppage_on.gif','b_toppage');" onmouseout="chgImg('<!--{$smarty.const.URL_DIR}-->img/common/b_toppage.gif','b_toppage');"><img src="<!--{$smarty.const.URL_DIR}-->img/common/b_toppage.gif" width="150" height="30" alt="トップページへ" border="0" name="b_toppage"></a>
					<!--{/if}-->
				</td>
			</tr>
		</table>
		<!--▲MAIN ONTENTS-->
		</td>
	</tr>
</table>
<!--▲CONTENTS-->
