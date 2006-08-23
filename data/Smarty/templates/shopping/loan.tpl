<!--▼CONTENTS-->
<table width="780" cellspacing="0" cellpadding="0" summary=" ">
	<tr>
		<td bgcolor="#cccccc"><img src="../img/_.gif" width="1" height="18" alt="" /></td>
		<td bgcolor="#ffffff" colspan="3"><img src="../img/_.gif" width="778" height="1" alt="" /></td>
		<td bgcolor="#cccccc"><img src="../img/_.gif" width="1" height="18" alt="" /></td>		
	</tr>

	<tr>
		<td bgcolor="#cccccc"><img src="../img/_.gif" width="1" height="10" alt="" /></td>
		<td bgcolor="#ffffff"><img src="../img/_.gif" width="10" height="1" alt="" /></td>
		<td><img src="../img/shopping/flow05.gif" width="758" height="78" alt="お買い物の流れ" /></td>
		<td bgcolor="#ffffff"><img src="../img/_.gif" width="10" height="1" alt="" /></td>
		<td bgcolor="#cccccc"><img src="../img/_.gif" width="1" height="10" alt="" /></td>		
	</tr>
</table>
<table width="780" cellspacing="0" cellpadding="0" summary=" ">
	<form name="form1" id="form1" method="POST" action="<!--{$tpl_homeaddr}--><!--{$tpl_simulate}-->">
		<input type="hidden" name="mode" value="">
		<!--{* 加盟店番号(必須) *}-->
		<input type="hidden" name="store" value="<!--{$tpl_storecode}-->">
		<!--{* 支払い総額(必須) *}-->
		<input type="hidden" name="amount" value="<!--{$tpl_amount}-->">
		<!--{* 戻り先(必須) *}-->
		<input type="hidden" name="returnurl" value="<!--{$tpl_returnurl}-->">
		<!--{* 呼び出し区分(必須) *}-->
		<input type="hidden" name="continue" value="<!--{$tpl_continue}-->">
		<!--{* 役務有無区分(必須) *}-->
		<input type="hidden" name="Labor" value="<!--{$tpl_labor}-->">
		
		<!--{* 取り扱い番号 *}-->
		<input type="hidden" name="tranno" value="<!--{$tpl_tranno}-->">
		<!--{* 結果応答 *}-->
		<input type="hidden" name="result" value="<!--{$tpl_result}-->">
		<input type="hidden" name="cancelurl" value="<!--{$tpl_cancelurl}-->">
		
		<!--{* 購入品目 *}-->
		<!--{section name=cnt loop=$arrProductsClass}-->
		<input type="hidden" name="item<!--{$smarty.section.cnt.iteration}-->" value="<!--{$arrProductsClass[cnt].name}-->">
		<input type="hidden" name="item<!--{$smarty.section.cnt.iteration}-->count" value="<!--{$arrProductsClass[cnt].quantity}-->">
		<input type="hidden" name="item<!--{$smarty.section.cnt.iteration}-->amount" value="<!--{$arrProductsClass[cnt].uniq_price|sfPreTax:$arrInfo.tax:$arrInfo.tax_rule}-->">
		<!--{/section}-->	<tr>
		<td bgcolor="#cccccc" width="1"><img src="../img/_.gif" width="1" height="10" alt="" /></td>
		<td bgcolor="#ffffff" width="9"><img src="../img/_.gif" width="39" height="1" alt="" /></td>
		<td bgcolor="#ffffff" align="left"> 
		<!--▼MAIN CONTENTS-->
		<table cellspacing="0" cellpadding="0" summary=" " id="containerfull">
			<tr><td height="20"></td></tr>
			<tr valign="top">
				<!--▼CONTENTS-->
				<td align="center">

				<div id="maintitle"><img src="../img/shopping/loan_title.jpg" width="700" height="40" alt="ショッピングローン決済" /></div>
				<table cellspacing="0" cellpadding="0" summary=" " id="ichi">
					<tr>
						<td>
						<table cellspacing="0" cellpadding="0" summary=" " id="comp">
							<tr><td height="40" class="fs14st"><span class="red"><!--{$tpl_message}--></span></td></tr>
							<tr>
								<td class="fs12">これよりショッピングローン決済ページへ移動します。<br />

								よろしければ「<span class="redst">ショッピングローン決済へ</span>」ボタンをクリックしてください。</td>
							</tr>
							<tr><td height="15"></td></tr>
							<tr>
								<td class="fs12">ショッピングローン決済の詳しい説明については<a href="https://cf.ufit.ne.jp/dotcredit/guide/guide.asp?store=<!--{$tpl_storecode}-->" target="_blank">こちら</a>をご覧ください。</td>
							</tr>
							<tr><td height="40"></td></tr>
						</table>
						</td>
					</tr>
				</table>
				<div id="button">
				<a href="<!--{$smarty.server.PHP_SELF}-->" onmouseover="chgImg('/img/button/back03_on.gif','back03')" onmouseout="chgImg('/img/button/back03.gif','back03')" onclick="history.back(); return false;" /><img src="/img/button/back03.gif" width="110" height="30" alt="戻る" border="0" name="back03" id="back03" ></a><img src="/img/_.gif" width="20" height="" alt="" /><input type="image" onmouseover="chgImgImageSubmit('/img/shopping/loan_on.gif',this)" onmouseout="chgImgImageSubmit('/img/shopping/loan.gif',this)" src="/img/shopping/loan.gif" width="194" height="30" alt="ショッピングローン決済へ" border="0" name="send_button" id="send_button" />
				</div>
				</form>
				
				<table cellspacing="0" cellpadding="0" summary=" " id="verisign">
					<tr>
						<td><script src=https://seal.verisign.com/getseal?host_name=secure.tokado.jp&size=S&use_flash=YES&use_transparent=NO&lang=ja></script></td>
						<td><img src="../img/_.gif" width="10" height="1" alt="" /></td>
						<td class="fs10">トーカ堂インターネットショッピングでは、通信の安全性を確保するセキュリティモードを設定しています。「暗号化(SSL)」を選択すると、送受信するデータが暗号化され、漏洩の危険性が低くなります。また、日本ベリサイン社によって通信サーバが認証されるため、なりすましなどによるID・パスワードの盗用の可能性も低減できます。</td>
					</tr>
				</table>
				</td>

				<!--▲ONTENTS-->	
			</tr>
		</table>
		<!--▲MAIN CONTENTS-->
		</td>
		<td bgcolor="#ffffff" width="10"><img src="../img/_.gif" width="39" height="1" alt="" /></td>
		<td bgcolor="#cccccc" width="1"><img src="../img/_.gif" width="1" height="10" alt="" />
<!-- EBiS start -->
<script type="text/javascript">
if ( location.protocol == 'http:' ){ 
	strServerName = 'http://daikoku.ebis.ne.jp'; 
} else { 
	strServerName = 'https://secure2.ebis.ne.jp/ver3';
}
cid = 'tqYg3k6U'; pid = 'shopping_loan'; m1id=''; a1id=''; o1id=''; o2id=''; o3id=''; o4id=''; o5id='';
document.write("<scr" + "ipt type=\"text\/javascript\" src=\"" + strServerName + "\/ebis_tag.php?cid=" + cid + "&pid=" + pid + "&m1id=" + m1id + "&a1id=" + a1id + "&o1id=" + o1id + "&o2id=" + o2id + "&o3id=" + o3id + "&o4id=" + o4id + "&o5id=" + o5id + "\"><\/scr" + "ipt>");
</script>
<!-- EBiS end -->
		</td>
	</tr>
</table>
<!--▲CONTENTS-->

