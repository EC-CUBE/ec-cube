<!--{*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *}-->
<!--▼CONTENTS-->
<table width="" border="0" cellspacing="0" cellpadding="0" summary=" ">
	<tr valign="top">
		<td align="center" bgcolor="#ffffff">
		<!--▼MAIN ONTENTS-->
		<table width="580" border="0" cellspacing="0" cellpadding="0" summary=" ">
			<form name="form1" id="form1" method="post" action="<!--{$smarty.server.PHP_SELF|escape}-->">
			<input type="hidden" name="mode" value="confirm">
			<tr>
				<td><img src="<!--{$smarty.const.URL_DIR}-->img/contact/title.jpg" width="580" height="40" alt="お問い合わせ"></td>
			</tr>
			<tr><td height="15"></td></tr>
			<tr>
				<td class="fs12">お問い合わせはメールにて承っています。<br>
				内容によっては回答をさしあげるのにお時間をいただくこともございます。また、土日、祝祭日、年末年始、夏季期間は翌営業日以降の対応となりますのでご了承ください。</td>
			</tr>
			<tr><td height="10"></td></tr>
			<tr>
				<td class="fs10"><span class="red">※ご注文に関するお問い合わせには、必ず「ご注文番号」と「お名前」をご記入の上、メールくださいますようお願いいたします。</span></td>
			</tr>
			<tr><td height="20"></td></tr>
			<tr>
				<td bgcolor="#cccccc" align="center">
				<!--入力フォームここから-->
				<table width="" border="0" cellspacing="1" cellpadding="10" summary=" ">
					<tr>
						<td width="135" bgcolor="#f0f0f0" class="fs12n">お名前<span class="red">※</span></td>
						<td width="402" bgcolor="#ffffff" class="fs12n">
							<span class="red"><!--{$arrErr.name01}--><!--{$arrErr.name02}--></span>
							姓&nbsp;<input type="text" name="name01" value="<!--{$name01|escape|default:$arrData.name01|escape}-->" maxlength="<!--{$smarty.const.STEXT_LEN}-->" style="<!--{$arrErr.name01|sfGetErrorColor}-->" class="box15" />　名&nbsp;<input type="text" name="name02" value="<!--{$name02|escape|default:$arrData.name02|escape}-->" maxlength="<!--{$smarty.const.STEXT_LEN}-->" style="<!--{$arrErr.name02|sfGetErrorColor}-->" class="box15" />
						</td>
					</tr>
					<tr>
						<td bgcolor="#f0f0f0" class="fs12n">お名前（フリガナ）<span class="red">※</span></td>
						<td bgcolor="#ffffff" class="fs12n"><span class="red">
							<!--{$arrErr.kana01}--><!--{$arrErr.kana02}--></span>
							セイ&nbsp;<input type="text" name="kana01" value="<!--{$kana01|escape|default:$arrData.kana01|escape}-->" maxlength="<!--{$smarty.const.STEXT_LEN}-->" style="<!--{$arrErr.kana01|sfGetErrorColor}-->" class="box15" />　メイ&nbsp;<input type="text" name="kana02" value="<!--{$kana02|escape|default:$arrData.kana02|escape}-->" maxlength="<!--{$smarty.const.STEXT_LEN}-->" style="<!--{$arrErr.kana02|sfGetErrorColor}-->" class="box15" />
						</td>
					</tr>
					<tr>
						<td bgcolor="#f0f0f0" class="fs12n">郵便番号</td>
						<td bgcolor="#ffffff">
						<table border="0" cellspacing="0" cellpadding="0" summary=" ">
							<tr>
								<td colspan="2"><span class="fs12n"><!--{$arrErr.zip01}--><!--{$arrErr.zip02}--></span>
								〒&nbsp;<input type="text" name="zip01" size="6" value="<!--{$zip01|escape|default:$arrData.zip01|escape}-->" maxlength="<!--{$smarty.const.ZIP01_LEN}-->" style="<!--{$arrErr.zip01|sfGetErrorColor}-->" />&nbsp;-&nbsp;<input type="text" name="zip02" size="6" value="<!--{$zip02|escape|default:$arrData.zip02|escape}-->" maxlength="<!--{$smarty.const.ZIP02_LEN}-->" style="<!--{$arrErr.zip02|sfGetErrorColor}-->" />　<a href="http://search.post.japanpost.jp/7zip/" target="_blank"><span class="fs10">郵便番号検索</span></a></td>
							</tr>
							<tr><td height="5"></td></tr>
							<tr>
								<td><a href="#" onClick="fnCallAddress('<!--{$smarty.const.URL_INPUT_ZIP}-->', 'zip01', 'zip02', 'pref', 'addr01'); return false;"><img src="<!--{$smarty.const.URL_DIR}-->img/common/address.gif" width="86" height="20" alt="住所自動入力" /></a></td>
								<td class="fs10n">&nbsp;郵便番号を入力後、クリックしてください。</td>
							</tr>
						</table>
						</td>
					</tr>
					<tr>
						<td bgcolor="#f0f0f0" class="fs12">住所</td>
						<td bgcolor="#ffffff">
						<table border="0" cellspacing="0" cellpadding="0" summary=" ">
							<tr>
								<td><span class="red"><!--{$arrErr.pref}--><!--{$arrErr.addr01}--><!--{$arrErr.addr02}--></span>
								<select name="pref" style="<!--{$arrErr.pref|sfGetErrorColor}-->">
								<option value="" selected>都道府県を選択</option>
								<!--{html_options options=$arrPref selected=$pref|default:$arrData.pref}-->
								</select></td>
							</tr>
							<tr><td height="7"></td>
							</tr>
							<tr>
								<td><input type="text" name="addr01" size="40" class="box40" value="<!--{$addr01|escape|default:$arrData.addr01|escape}-->" style="<!--{$arrErr.addr01|sfGetErrorColor}-->"/></td>
							</tr>
							<tr>
								<td class="fs10n"><!--{$smarty.const.SAMPLE_ADDRESS1}--></td>
							</tr>
							<tr><td height="5"></td></tr>
							<tr>
								<td><input type="text" name="addr02" size="40" class="box40" value="<!--{$addr02|escape|default:$arrData.addr02|escape}-->" style="<!--{$arrErr.addr02|sfGetErrorColor}-->"/><span class="mini"></td>
							</tr>
							<tr>
								<td class="fs10n"><!--{$smarty.const.SAMPLE_ADDRESS2}--></td>
							</tr>
							<tr><td height="5"></td></tr>
							<tr>
								<td class="fs10"><span class="red">住所は2つに分けてご記入いただけます。マンション名は必ず記入してください。</span></td>
							</tr>
						</table>
						</td>
					</tr>
					<tr>
						<td bgcolor="#f0f0f0" class="fs12n">電話番号</td>
						<td bgcolor="#ffffff" class="fs12n">
							<!--{$arrErr.tel01}--><!--{$arrErr.tel02}--><!--{$arrErr.tel03}--></span>
							<input type="text" name="tel01" size="6" value="<!--{$tel01|escape|default:$arrData.tel01|escape}-->" maxlength="<!--{$smarty.const.TEL_ITEM_LEN}-->" style="<!--{$arrErr.tel01|sfGetErrorColor}-->" />&nbsp;-&nbsp;<input type="text" name="tel02" size="6" value="<!--{$tel02|escape|default:$arrData.tel02|escape}-->" maxlength="<!--{$smarty.const.TEL_ITEM_LEN}-->" style="<!--{$arrErr.tel02|sfGetErrorColor}-->" />&nbsp;-&nbsp;<input type="text" name="tel03" size="6" value="<!--{$tel03|escape|default:$arrData.tel03|escape}-->" maxlength="<!--{$smarty.const.TEL_ITEM_LEN}-->" style="<!--{$arrErr.tel03|sfGetErrorColor}-->" />
						</td>
					</tr>
					<tr>
						<td bgcolor="#f0f0f0" class="fs12n">メールアドレス<span class="red">※</span></td>
						<td bgcolor="#ffffff">
						<table border="0" cellspacing="0" cellpadding="0" summary=" ">
							<tr>
								<td bgcolor="#ffffff" class="fs12n">
									<span class="red"><!--{$arrErr.email}--><!--{$arrErr.email02}--></span>
									<input type="text" name="email" size="40" class="box40" value="<!--{$email|escape|default:$arrData.email|escape}-->" maxlength="<!--{$smarty.const.MTEXT_LEN}-->" style="<!--{$arrErr.email|sfGetErrorColor}-->" />
								</td>
							</tr>
							<tr><td height="5"></td></tr>
							<tr>
								<td><input type="text" name="email02" size="40" class="box40" value=<!--{if $smarty.server.REQUEST_METHOD != 'POST' & $smarty.session.customer}--> "<!--{$arrData.email|escape}-->" <!--{else}--> "<!--{$email02|escape}-->" <!--{/if}--> maxlength="<!--{$smarty.const.MTEXT_LEN}-->" style="<!--{$arrErr.email02|sfGetErrorColor}-->" /></td>
							</tr>
							<tr><td height="2"></td></tr>
							<tr>
								<td class="fs10n"><span class="red">確認のため2度入力してください。</span></td>
							</tr>
						</table>
						</td>
					</tr>
					<tr>
						<td bgcolor="#f0f0f0" class="fs12">お問い合わせ内容<span class="red">※</span><br>
						<span class="mini">（全角<!--{$smarty.const.MLTEXT_LEN}-->字以下）</span></td>
						<td bgcolor="#ffffff" class="fs12n">
							<span class="red"><!--{$arrErr.contents}--></span>
							<textarea name="contents" cols="60" rows="20" class="area60" wrap="hard" maxlength="<!--{$smarty.const.LTEXT_LEN}-->" style="<!--{$arrErr.contents|sfGetErrorColor}-->"><!--{$contents|escape}--></textarea>
						</td>
					</tr>
				</table>
				<!--入力フォームここまで-->
				</td>
			</tr>
			<tr><td height="25"></td></tr>
			<tr align="center">
				<td>
					<input type="image" onmouseover="chgImgImageSubmit('<!--{$smarty.const.URL_DIR}-->img/common/b_confirm_on.gif',this)" onmouseout="chgImgImageSubmit('<!--{$smarty.const.URL_DIR}-->img/common/b_confirm.gif',this)" src="<!--{$smarty.const.URL_DIR}-->img/common/b_confirm.gif" width="150" height="30" alt="確認ページへ" border="0" name="confirm" id="confirm" />
				</td>
			</tr>
		</form>
		</table>
		<!--▲MAIN ONTENTS-->
		</td>
	</tr>
</table>
<!--▲CONTENTS-->





