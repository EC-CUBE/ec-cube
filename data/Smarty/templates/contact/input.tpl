<!--▼CONTENTS-->
<table width="780" cellspacing="0" cellpadding="0" summary=" ">
	<tr>
		<td bgcolor="#cccccc" width="1"><img src="../img/_.gif" width="1" height="10" alt="" /></td>
		<td bgcolor="#ffffff" width="9"><img src="../img/_.gif" width="9" height="1" alt="" /></td>
		<td bgcolor="#ffffff" align="left"> 
		<!--▼MAIN CONTENTS-->
		<!--パンクズ-->
		<div id="pan"><span class="fs12n"><a href="../index.php">トップページ</a> ＞ <span class="redst">お問い合わせ（入力ページ）</span></span></div>
		<!--パンクズ-->
		<table cellspacing="0" cellpadding="0" summary=" " id="container">
			<tr><td height="10"></td></tr>
			<tr valign="top">
				<!--▼LEFT CONTENTS-->
				<td id="left">
				<!--▼バナー--><!--{include file=$tpl_banner}--><!--▲バナー-->
				
				<!--▼商品検索-->
					<!--{include_php file=$tpl_search_products_php}-->
				<!--▲商品検索-->
								
				<!--▼左ナビ-->
					<!--{include file=$tpl_leftnavi}-->
				<!--▲左ナビ-->
								
				</td>
				<!--▲LEFT CONTENTS-->
				
				<!--▼RIGHT CONTENTS-->
				<td id="right">
				<div id="maintitle"><img src="../img/right_contact/title.jpg" width="570" height="40" alt="お問い合わせ" /></div>
				<div id="comment" class="fs12">下記項目にご入力ください。「<span class="asterisk">※</span>」印は入力必須項目です。<br />
				入力後、一番下の「確認ページへ」ボタンをクリックしてください。<br />　<br />
				<!--{$name|escape}-->様</span></div>
				
				<form action="<!--{$smarty.server.PHP_SELF}-->" method="post" name="form1">
				<input type="hidden" name="mode" value="confirm">
				<input type="hidden" name="name" value="<!--{$name|escape}-->">
				<input type="hidden" name="kana" value="<!--{$kana|escape}-->">
				<input type="hidden" name="customer_id" value="<!--{$customer_id}-->">
				
				<table cellspacing="1" cellpadding="10" summary=" " id="frame">
					<tr class="fs12n">
						<td id="left"><span class="asterisk">※</span>お問い合わせの種類</td>
						<td id="right">
						<!--{assign var=key value="question"}-->
						<span class="red"><!--{$arrErr[$key]}--></span>
							<select name="<!--{$key}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->">
								<option value="" selected>選択してください</option>
								<!--{html_options options=$arrContact selected=$arrForm[$key]}-->
							</select>
						</td>
					</tr>
					<tr>
						<td class="fs12" id="left"><span class="asterisk">※</span>お問い合わせ内容<br />
						<span class="indent12">（全角1000文字以内）</span></td>
						<td id="right" class="fs12n"><!--{assign var=key value="contents"}-->
							<span class="red"><!--{$arrErr[$key]}--></span>
							<textarea name="contents" cols="45" rows="8" wrap="physical" style="<!--{$arrErr[$key]|sfGetErrorColor}-->"><!--{$arrForm[$key]|escape}--></textarea>
						</td>
					</tr>
				</table>
				
				<!--{assign var=key value="method"}-->
				<div id="comment02"><span class="red12">質問への返答方法をお選びくださいませ。</span>
				<!--{if $arrErr[$key]}--><br /><span class="red12"><!--{$arrErr[$key]}--></span><!--{/if}-->
				</div>			
				<table cellspacing="1" cellpadding="10" summary=" " id="frame">
					<tr>
						<td class="fs12n" id="left"><input type="radio" name="method" value="1" onClick="setInputArea(this.value)" <!--{if $arrForm.method eq "1"}-->checked<!--{/if}--> />メールでの回答を希望</td>
						<td id="right">
						<table cellspacing="0" cellpadding="0" summary=" ">
			
							<tr>
								<td class="fs12n">▼返答先メールアドレス<br><span class="red"><!--{$arrErr.email}--><!--{$arrErr.email02}--></span></td>
							</tr>
							<tr><td height="5"></td></tr>
							<tr>
								<td><input type="text" name="email" value="<!--{$arrForm.email|escape}-->" style="<!--{$arrErr.email|sfGetErrorColor}-->" size="40" class="box40" /></td>
							</tr>
							<tr><td height="2"></td></tr>
							<tr>
								<td><input type="text" name="email02" value="<!--{$arrForm.email02|escape}-->" style="<!--{$arrErr.email02|sfGetErrorColor}-->" size="40" class="box40" /></td>
							</tr>
							<tr><td height="2"></td></tr>
							<tr>
								<td class="fs10"><span class="red">確認のために2度入力してください。</span><br />
								携帯電話のメールアドレスをご使用の場合はドメイン着信設定にご注意ください。<br />
								こちらからのご返答メールが届かないことがございます。</td>
							</tr>
						</table>
						</td>
					</tr>
					<tr>
						<td class="fs12n" id="left"><input type="radio" name="method" value="2" onClick="setInputArea(this.value)" <!--{if $arrForm.method eq "2"}-->checked<!--{/if}-->>お電話での回答を希望</td>
						<td id="right">
						<table cellspacing="0" cellpadding="0" summary=" ">
							<tr>
								<td class="fs12n">▼返答先お電話番号<br><span class="red"><!--{$arrErr.tel01}--><!--{$arrErr.tel02}--><!--{$arrErr.tel03}--></span></td>
							</tr>
							<tr><td height="5"></td></tr>
							<tr>
								<td class="fs12n">
									<input type="text" name="tel01" value="<!--{$arrForm.tel01|escape}-->" style="<!--{$arrErr.tel01|sfGetErrorColor}-->" size="6" />&nbsp;-&nbsp;
									<input type="text" name="tel02" value="<!--{$arrForm.tel02|escape}-->" style="<!--{$arrErr.tel02|sfGetErrorColor}-->" size="6" />&nbsp;-&nbsp;
									<input type="text" name="tel03" value="<!--{$arrForm.tel03|escape}-->" style="<!--{$arrErr.tel03|sfGetErrorColor}-->" size="6" />
								</td>
							</tr>
							<tr><td height="15" class="fs12n"></td></tr>
							<tr>
								<td class="fs12n">▼ご連絡時間はいつごろが宜しいでしょうか？</td>
							</tr>
							<tr><td height="5"></td></tr>
							<tr>
								<td class="fs12n">
								<!--{assign var=key value="contact_time"}-->
								<span class="red"><!--{$arrErr[$key]}--></span>
								<select name="<!--{$key}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->">
								<!--{html_options options=$arrContactTime selected=$arrForm[$key]}-->
								</select></td>
							</tr>
							<tr><td height="15"></td></tr>
							<tr>
								<td class="fs12n">▼細かい時間指定がございましたら、ご記入ください。<span class="red">(1000文字以内)</span></td>
							</tr>
							<tr><td height="2"></td></tr>
							<tr>
								<td class="red10">当社の営業時間は9:30〜17:00（土・日・祝祭日休み）です。<br />
								当社の営業時間内でご指定をお願いいたします。</td>
							</tr>
							<tr><td height="5"></td></tr>
							<tr>
								<td class="fs12n">
								<!--{assign var=key value="message"}-->
								<span class="red"><!--{$arrErr[$key]}--></span>
								<textarea name="<!--{$key}-->"  cols="46" rows="8" class="area46" wrap="physical" style="<!--{$arrErr[$key]|sfGetErrorColor}-->"><!--{$arrForm[$key]|escape}--></textarea>
								</td>
							</tr>
						</table>
						</td>
					</tr>
				</table>
				<div id="button">
				<input type="image" onmouseover="chgImgImageSubmit('../img/button/confirm_on.gif',this)" onmouseout="chgImgImageSubmit('../img/button/confirm.gif',this)" src="../img/button/confirm.gif" width="150" height="30" alt="確認ページへ" border="0" name="confirm" id="confirm" /></div>
				</form>
				</td>
				<!--▲RIGHT CONTENTS-->
				
			</tr>
		</table>
		<!--▲MAIN CONTENTS-->
		</td>
		<td bgcolor="#ffffff" width="10"><img src="../img/_.gif" width="9" height="1" alt="" /></td>
		<td bgcolor="#cccccc" width="1"><img src="../img/_.gif" width="1" height="10" alt="" /></td>
	</tr>
</table>
<!--▲CONTENTS-->

<script language="JavaScript">
<!--
function setInputArea(val) {
	var fm = document.form1;
	tFlag = true;
	mFlag = true;
	tColor = '<!--{$smarty.const.DISABLED_RGB}-->';
	mColor = '<!--{$smarty.const.DISABLED_RGB}-->';
	errColor = '<!--{$smarty.const.ERR_COLOR}-->';
	
	if ( val == 1 ){
		mColor = '';
		mFlag = false;
	} else if ( val == 2 ){
		tColor = '';
		tFlag = false;
	}
	fm.email.disabled = mFlag;
	fm.email02.disabled = mFlag;
	fm.tel01.disabled = tFlag;
	fm.tel02.disabled = tFlag;
	fm.tel03.disabled = tFlag;
	fm.contact_time.disabled = tFlag;
	fm.message.disabled = tFlag;
	
	if ( fm.email02.style.backgroundColor != errColor ) 		fm.email02.style.backgroundColor = mColor;
	if ( fm.email.style.backgroundColor != errColor ) 			fm.email.style.backgroundColor = mColor;
	if ( fm.tel01.style.backgroundColor != errColor ) 			fm.tel01.style.backgroundColor = tColor;	
	if ( fm.tel02.style.backgroundColor != errColor )			fm.tel02.style.backgroundColor = tColor;
	if ( fm.tel03.style.backgroundColor != errColor )			fm.tel03.style.backgroundColor = tColor;
	if ( fm.contact_time.style.backgroundColor != errColor )	fm.contact_time.style.backgroundColor = tColor;
	if ( fm.message.style.backgroundColor != errColor )			fm.message.style.backgroundColor = tColor;
		
}

//-->
</script>

