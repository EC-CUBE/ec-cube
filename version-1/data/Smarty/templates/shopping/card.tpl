<!--{*
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
*}-->
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
		<td><img src="../img/shopping/flow04.gif" width="758" height="78" alt="お買い物の流れ" /></td>
		<td bgcolor="#ffffff"><img src="../img/_.gif" width="10" height="1" alt="" /></td>
		<td bgcolor="#cccccc"><img src="../img/_.gif" width="1" height="10" alt="" /></td>		
	</tr>
</table>

<table width="780" cellspacing="0" cellpadding="0" summary=" ">
	<tr>
		<td bgcolor="#cccccc" width="1"><img src="../img/_.gif" width="1" height="10" alt="" /></td>
		<td bgcolor="#ffffff" width="9"><img src="../img/_.gif" width="39" height="1" alt="" /></td>
		<td bgcolor="#ffffff" align="left"> 
		<!--▼MAIN CONTENTS-->
		<table cellspacing="0" cellpadding="0" summary=" " id="containerfull">
			<tr><td height="20"></td></tr>
			<tr valign="top">
				<!--▼CONTENTS-->

				<td>
				<div id="maintitle"><img src="../img/shopping/card_title.jpg" width="700" height="40" alt="クレジットカード決済" /></div>
				<div class="fs12n" id="comment01">下記項目にクレジットカード情報をご入力くださいませ。<br />
				入力後、一番下の「ご注文完了ページへ」ボタンをクリックしてください。</div>
				<form name="form1" id="form1" method="post" action="<!--{$smarty.server.PHP_SELF|escape}-->">
				<input type="hidden" name="mode" value="regist">
				<input type="hidden" name="uniqid" value="<!--{$tpl_uniqid}-->">
				<span class="redst"><!--{$tpl_error}--></span>
				<table cellspacing="1" cellpadding="8" summary=" " id="frame">
					<tr>
						<td class="fs12n" id="left">ご利用可能なカードの種類</td>

						<td id="right"><img src="../img/shopping/card.gif" width="399" height="52" alt="ご利用可能なカードの種類" /></td>
					</tr>
					<tr>
						<td class="fs12n" id="left">カード番号</td>
						<td id="right">
						<table cellspacing="0" cellpadding="0" summary=" ">
							<tr>
								<!--{assign var=key1 value="card_no01"}-->
								<!--{assign var=key2 value="card_no02"}-->
								<!--{assign var=key3 value="card_no03"}-->
								<!--{assign var=key4 value="card_no04"}-->
								<td class="fs12n">
								<span class="red"><!--{$arrErr[$key1]}--></span>
								<span class="red"><!--{$arrErr[$key2]}--></span>
								<span class="red"><!--{$arrErr[$key3]}--></span>
								<span class="red"><!--{$arrErr[$key4]}--></span>
								<input type="text" name="<!--{$key1}-->" value="<!--{$arrForm[$key1].value|escape}-->" maxlength="<!--{$arrForm[$key1].length}-->" style="<!--{$arrErr[$key1]|sfGetErrorColor}-->"  size="6">&nbsp;-&nbsp;
								<input type="text" name="<!--{$key2}-->" value="<!--{$arrForm[$key2].value|escape}-->" maxlength="<!--{$arrForm[$key2].length}-->" style="<!--{$arrErr[$key2]|sfGetErrorColor}-->"  size="6">&nbsp;-&nbsp;
								<input type="text" name="<!--{$key3}-->" value="<!--{$arrForm[$key3].value|escape}-->" maxlength="<!--{$arrForm[$key3].length}-->" style="<!--{$arrErr[$key3]|sfGetErrorColor}-->"  size="6">&nbsp;-&nbsp;
								<input type="text" name="<!--{$key4}-->" value="<!--{$arrForm[$key4].value|escape}-->" maxlength="<!--{$arrForm[$key4].length}-->" style="<!--{$arrErr[$key4]|sfGetErrorColor}-->"  size="6">
								</td>
							</tr>
							<tr><td height="5"></td></tr>
							<tr>
								<td class="fs12n">ご本人名義のカードをご使用ください。<br>
								半角入力&nbsp;例：1234-5678-9012-3456</td>
							</tr>
						</table>

						</td>
					</tr>
					<tr>
						<td class="fs12n" id="left">有効期限</td>
						<td id="right">
						<table cellspacing="0" cellpadding="0" summary=" ">
							<tr>
								<!--{assign var=key1 value="card_month"}-->
								<!--{assign var=key2 value="card_year"}-->
								<td class="fs12n">
								<span class="red"><!--{$arrErr[$key1]}--></span>
								<span class="red"><!--{$arrErr[$key2]}--></span>
								<select name="<!--{$key1}-->" value="<!--{$arrForm[$key1].value|escape}-->" maxlength="<!--{$arrForm[$key1].length}-->" style="<!--{$arrErr[$key1]|sfGetErrorColor}-->" >
								<option value="">--</option>
								<!--{html_options options=$arrMonth selected=$arrForm[$key1].value}-->
								</select>月/
								<select name="<!--{$key2}-->" value="<!--{$arrForm[$key2].value|escape}-->" maxlength="<!--{$arrForm[$key2].length}-->" style="<!--{$arrErr[$key2]|sfGetErrorColor}-->" >
								<option value="">--</option>
								<!--{html_options options=$arrYear selected=$arrForm[$key2].value}-->
								</select>年</td>
							</tr>
							<tr><td height="5"></td></tr>

							<tr>
								<td class="fs12n">カード上は月/年と記述しています。</td>
							</tr>
						</table>
						</td>
					</tr>
					<tr>
						<td class="fs12n" id="left">ローマ字氏名</td>

						<td id="right">
						<table cellspacing="0" cellpadding="0" summary=" ">
							<tr>
								<!--{assign var=key2 value="card_name01"}-->
								<!--{assign var=key1 value="card_name02"}-->								
								<td class="fs12n">
								<span class="red"><!--{$arrErr[$key1]}--></span>
								<span class="red"><!--{$arrErr[$key2]}--></span>
								名&nbsp;<input type="text" name="<!--{$key1}-->" value="<!--{$arrForm[$key1].value|escape}-->" maxlength="<!--{$arrForm[$key1].length}-->" style="<!--{$arrErr[$key1]|sfGetErrorColor}-->" size="20" class="bo20">&nbsp;&nbsp;姓&nbsp;
								<input type="text" name="<!--{$key2}-->" value="<!--{$arrForm[$key2].value|escape}-->" maxlength="<!--{$arrForm[$key2].length}-->" style="<!--{$arrErr[$key2]|sfGetErrorColor}-->" size="20" class="bo20"></td>
							</tr>
							<tr><td height="5"></td></tr>
							<tr>
								<td class="fs12n">半角英字入力　例：TARO YAMADA</td>
							</tr>
						</table>
						</td>
					</tr>
					<tr>
						<td class="fs12n" id="left">お支払い方法</td>

						<td id="right">
						<table cellspacing="0" cellpadding="0" summary=" ">
							<tr>
								<!--{assign var=key value="jpo_info"}-->								
								<td class="fs12n">
								<select name="<!--{$key}-->" value="<!--{$arrForm[$key].value|escape}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" >
								<!--{html_options options=$arrJPO_INFO selected=$arrForm[$key].value}-->
								</select></td>
							</tr>
						</table>
						</td>
					</tr>
					
				</table>
				<div class="red12" id="comment02">※「ご注文完了ページへ」をクリック後、完了ページが表示されるまでお待ちください。</div>
				<div id="button">
				<!--「戻る」「登録」-->
				<a href="<!--{$smarty.server.PHP_SELF|escape}-->" onmouseover="chgImg('/img/button/back03_on.gif','back03')" onmouseout="chgImg('/img/button/back03.gif','back03')" onclick="history.back(); return false;" /><img src="/img/button/back03.gif" width="110" height="30" alt="戻る" border="0" name="back03" id="back03" ></a><img src="../img/_.gif" width="12" height="" alt="" />
				<input type="image" onmouseover="chgImgImageSubmit('../img/shopping/complete_on.gif',this)" onmouseout="chgImgImageSubmit('../img/shopping/complete.gif',this)" src="../img/shopping/complete.gif" width="170" height="30" alt="ご注文完了ページへ" border="0" name="complete" id="complete" />
				</div>
				</form>
				
				<table cellspacing="0" cellpadding="0" summary=" " id="verisign">
					<tr>
						<td><script src=https://seal.verisign.com/getseal?host_name=secure.tokado.jp&size=S&use_flash=YES&use_transparent=NO&lang=ja></script></td>
						<td><img src="../img/_.gif" width="10" height="1" alt="" /></td>
						<td class="fs10">インターネットショッピングでは、通信の安全性を確保するセキュリティモードを設定しています。「暗号化(SSL)」を選択すると、送受信するデータが暗号化され、漏洩の危険性が低くなります。また、日本ベリサイン社によって通信サーバが認証されるため、なりすましなどによるID・パスワードの盗用の可能性も低減できます。</td>
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
cid = 'tqYg3k6U'; pid = 'shopping_card'; m1id=''; a1id=''; o1id=''; o2id=''; o3id=''; o4id=''; o5id='';
document.write("<scr" + "ipt type=\"text\/javascript\" src=\"" + strServerName + "\/ebis_tag.php?cid=" + cid + "&pid=" + pid + "&m1id=" + m1id + "&a1id=" + a1id + "&o1id=" + o1id + "&o2id=" + o2id + "&o3id=" + o3id + "&o4id=" + o4id + "&o5id=" + o5id + "\"><\/scr" + "ipt>");
</script>
<!-- EBiS end -->
		</td>

	</tr>
</table>
<!--▲CONTENTS-->