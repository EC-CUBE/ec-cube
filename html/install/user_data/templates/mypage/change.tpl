<!--{*
 * Copyright © 2000-2006 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *}-->
<!--▼CONTENTS-->
<table width="" border="0" cellspacing="0" cellpadding="0" summary=" ">
	<tr>
		<td align="center" bgcolor="#ffffff">
		<!--▼MAIN ONTENTS-->
		<table width="700" border="0" cellspacing="0" cellpadding="0" summary=" ">
			<tr>
				<td><img src="<!--{$smarty.const.URL_DIR}-->img/mypage/title.jpg" width="700" height="40" alt="MYページ"></td>
			</tr>
			<tr><td height="15"></td></tr>
		</table>
		<table width="700" border="0" cellspacing="0" cellpadding="0" summary=" ">
			<tr valign="top">
				<td>
					<!--{include file=$tpl_navi}-->
				</td>
				<td align="right">
				<table width="515" border="0" cellspacing="0" cellpadding="0" summary=" ">
				<form name="form1" id="form1" method="post" action="<!--{$smarty.server.PHP_SELF}-->">
				<input type="hidden" name="mode" value="confirm">
				<input type="hidden" name="customer_id" value="<!--{$arrForm.customer_id|escape}-->">
					<tr>
						<td><!--★タイトル--><img src="<!--{$smarty.const.URL_DIR}-->img/mypage/subtitle02.gif" width="515" height="32" alt="会員登録内容変更"></td>
					</tr>
					<tr><td height="15"></td></tr>
					<tr>
						<td class="fs12">下記項目にご入力ください。「<span class="red">※</span>」印は入力必須項目です。<br>
						入力後、一番下の「確認ページへ」ボタンをクリックしてください。</td>
					</tr>
					<tr><td height="10"></td></tr>
					<tr>
						<td bgcolor="#cccccc" align="center">
						<!--入力フォームここから-->
						<table width="515" border="0" cellspacing="1" cellpadding="10" summary=" ">
							<tr>
								<td width="122" bgcolor="#f0f0f0" class="fs12n">お名前<span class="red">※</span></td>
								<td width="350" bgcolor="#ffffff" class="fs12n"><span class="red"><!--{$arrErr.name01}--><!--{$arrErr.name02}--></span>
									姓&nbsp;<input type="text" name="name01" value="<!--{$arrForm.name01}-->" maxlength="<!--{$smarty.const.STEXT_LEN}-->" style="<!--{$arrErr.name01|sfGetErrorColor}-->; ime-mode: active;" size="15" class="box15" />　名&nbsp;<input type="text" name="name02" value="<!--{$arrForm.name02}-->" maxlength="<!--{$smarty.const.STEXT_LEN}-->" style="<!--{$arrErr.name02|sfGetErrorColor}-->; ime-mode: active;" size="15" class="box15" />	
								</td>
							</tr>
							<tr>
								<td bgcolor="#f0f0f0" class="fs12n">お名前（フリガナ）<span class="red">※</span></td>
								<td bgcolor="#ffffff" class="fs12n"><span class="red"><!--{$arrErr.kana01}--><!--{$arrErr.kana02}--></span>
								セイ&nbsp;<input type="text" name="kana01" value="<!--{$arrForm.kana01}-->" maxlength="<!--{$smarty.const.STEXT_LEN}-->" style="<!--{$arrErr.kana01|sfGetErrorColor}-->; ime-mode: active;" size="15" class="box15" />　メイ&nbsp;<input type="text" name="kana02" value="<!--{$arrForm.kana02}-->" maxlength="<!--{$smarty.const.STEXT_LEN}-->" style="<!--{$arrErr.kana02|sfGetErrorColor}-->; ime-mode: active;" size="15" class="box15" /></td>
							</tr>
							<tr>
								<td bgcolor="#f0f0f0" class="fs12n">郵便番号<span class="red">※</span></td>
								<td bgcolor="#ffffff">
								<table border="0" cellspacing="0" cellpadding="0" summary=" ">
									<tr>
										<!--{assign var=key1 value="zip01"}-->
										<!--{assign var=key2 value="zip02"}-->
										<td colspan="2"><span class="fs12n"><span class="red"><!--{$arrErr[$key1]}--><!--{$arrErr[$key2]}--></span>
										〒&nbsp;<input type="text" name="zip01" value="<!--{$arrForm.zip01}-->" maxlength="<!--{$smarty.const.ZIP01_LEN}-->" style="<!--{$arrErr.zip01|sfGetErrorColor}-->; ime-mode: disabled;" size=6 class="box6" />&nbsp;-&nbsp;<input type="text" name="zip02" value="<!--{$arrForm.zip02}-->" maxlength="<!--{$smarty.const.ZIP02_LEN}-->" style="<!--{$arrErr.zip02|sfGetErrorColor}-->; ime-mode: disabled;" size=6 class="box6" />　
										<a href="http://search.post.japanpost.jp/7zip/" target="_blank"><span class="fs12">郵便番号検索</span></a></td>
									</tr>
									<tr><td height="5"></td></tr>
									<tr>
										<td><a href="<!--{$smarty.const.URL_DIR}-->input_zip.php" onclick="fnCallAddress('<!--{$smarty.const.URL_INPUT_ZIP}-->', 'zip01', 'zip02', 'pref', 'addr01'); return false;" target="_blank"><img src="<!--{$smarty.const.URL_DIR}-->img/common/address.gif" width="86" height="20" alt="住所自動入力" /></a></td>
										<td class="fs10n">&nbsp;郵便番号を入力後、クリックしてください。</td>
									</tr>
								</table>
								</td>
							</tr>
							<tr>
								<td bgcolor="#f0f0f0" class="fs12">住所<span class="red">※</span></td>
								<td bgcolor="#ffffff">
								<table border="0" cellspacing="0" cellpadding="0" summary=" ">
									<tr>
										<td class="fs12n"><span class="red"><!--{$arrErr.pref}--><!--{$arrErr.addr01}--><!--{$arrErr.addr02}--></span>
										<select name="pref" style="<!--{$arrErr.pref|sfGetErrorColor}-->">
										<option value="" selected>都道府県を選択</option>
										<!--{html_options options=$arrPref selected=$arrForm.pref}-->
										</select></td>
									</tr>
									<tr><td height="7"></td>
									</tr>
									<tr>
										<td><input type="text" name="addr01" value="<!--{$arrForm.addr01}-->" size="60" class="box60" style="<!--{$arrErr.addr01|sfGetErrorColor}-->; ime-mode: active;" /></td>
									</tr>
									<tr>
										<td class="fs10n">市区町村名（例：大阪市北区堂島）</td>
									</tr>
									<tr><td height="5"></td></tr>
									<tr>
										<td><input type="text" name="addr02" value="<!--{$arrForm.addr02}-->" size="60" class="box60" style="<!--{$arrErr.addr02|sfGetErrorColor}-->; ime-mode: active;" /></td>
									</tr>
									<tr>
										<td class="fs10n">番地・ビル名（例：6-1-1）</td>
									</tr>
									<tr><td height="5"></td></tr>
									<tr>
										<td class="fs10"><span class="red">住所は2つに分けてご記入いただけます。マンション名は必ず記入してください。</span></td>
									</tr>
								</table>
								</td>
							</tr>
							<tr>
								<td bgcolor="#f0f0f0" class="fs12n">電話番号<span class="red">※</span></td>
								<td bgcolor="#ffffff" class="fs12n"><span class="red"><!--{$arrErr.tel01}--><!--{$arrErr.tel02}--><!--{$arrErr.tel03}--></span><input type="text" name="tel01" value="<!--{$arrForm.tel01}-->" maxlength="<!--{$smarty.const.TEL_ITEM_LEN}-->" size="6" style="<!--{$arrErr.tel01|sfGetErrorColor}-->; ime-mode: disabled;" />&nbsp;-&nbsp;<input type="text" name="tel02" value="<!--{$arrForm.tel02}-->" maxlength="<!--{$smarty.const.TEL_ITEM_LEN}-->" size="6" style="<!--{$arrErr.tel02|sfGetErrorColor}-->; ime-mode: disabled;" />&nbsp;-&nbsp;<input type="text" name="tel03" value="<!--{$arrForm.tel03}-->" maxlength="<!--{$smarty.const.TEL_ITEM_LEN}-->" size="6" style="<!--{$arrErr.tel03|sfGetErrorColor}-->; ime-mode: disabled;" /></td>
								</td>
							</tr>
							<tr>
								<td bgcolor="#f0f0f0" class="fs12n">FAX</td>
								<td bgcolor="#ffffff" class="fs12n"><span class="red"><!--{$arrErr.fax01}--><!--{$arrErr.fax02}--><!--{$arrErr.fax03}--></span><input type="text" name="fax01" value="<!--{$arrForm.fax01}-->" maxlength="<!--{$smarty.const.TEL_ITEM_LEN}-->" size="6" style="<!--{$arrErr.fax01|sfGetErrorColor}-->; ime-mode: disabled;" />&nbsp;-&nbsp;<input type="text" name="fax02" value="<!--{$arrForm.fax02}-->" maxlength="<!--{$smarty.const.TEL_ITEM_LEN}-->" size="6" style="<!--{$arrErr.fax02|sfGetErrorColor}-->; ime-mode: disabled;" />&nbsp;-&nbsp;<input type="text" name="fax03" value="<!--{$arrForm.fax03}-->" maxlength="<!--{$smarty.const.TEL_ITEM_LEN}-->" size="6" style="<!--{$arrErr.fax03|sfGetErrorColor}-->; ime-mode: disabled;" /></td>
							</tr>
							<tr>
								<td bgcolor="#f0f0f0" class="fs12n">メールアドレス<span class="red">※</span></td>
								<td bgcolor="#ffffff">
								<table border="0" cellspacing="0" cellpadding="0" summary=" ">
									<tr>
										<td class="fs12n"><span class="red"><!--{$arrErr.email}--></span>
										<input type="text" name="email" value= "<!--{$arrForm.email}-->" style="<!--{$arrErr.email|sfGetErrorColor}-->; ime-mode: disabled;" maxlength="<!--{$smarty.const.MTEXT_LEN}-->" size=40 class="box40" /></td>
									</tr>
									<tr><td height="5"></td></tr>
									<tr>
										<td class="fs12n"><span class="red"><!--{$arrErr.email02}--></span>
										<input type="text" name="email02" value= "<!--{if $arrForm.email02 == ""}--><!--{$arrForm.email}--><!--{else}--><!--{$arrForm.email02}--><!--{/if}-->" style="<!--{$arrErr.email02|sfGetErrorColor}-->; ime-mode: disabled;" maxlength="<!--{$smarty.const.MTEXT_LEN}-->" size=40 class="box40" /></td>
									</tr>
									<tr><td height="2"></td></tr>
									<tr>
										<td class="fs10n"><span class="red">確認のため2度入力してください。</span></td>
									</tr>
								</table>
								</td>
							</tr>
							<tr>
								<td bgcolor="#f0f0f0" class="fs12n">性別<span class="red">※</span></td>
								<td bgcolor="#ffffff" class="fs12n">
								<span class="red"><!--{$arrErr.sex}--></span><input type="radio" id="man" name="sex" value="1" style="<!--{$arrErr.sex|sfGetErrorColor}-->" <!--{if $arrForm.sex eq 1}--> checked <!--{/if}--> /><label for="man">男性</label>　<input type="radio" id="woman" name="sex" value="2" style="<!--{$arrErr.sex|sfGetErrorColor}-->" <!--{if $arrForm.sex eq 2}--> checked <!--{/if}--> /><label for="woman">女性</label></td>
							</tr>
							<tr>
								<td bgcolor="#f0f0f0" class="fs12n">職業</td>
								<td bgcolor="#ffffff">
									<span class="red"><!--{$arrErr.job}--></span>
									<select name="job">
									<option value="" selected>選択してください</option>
									<!--{html_options options=$arrJob selected=$arrForm.job}-->
									</select>
								</td>
							</tr>
							<tr>
								<td bgcolor="#f0f0f0" class="fs12n">生年月日</td>
								<td bgcolor="#ffffff" class="fs12n"><span class="red"><!--{$arrErr.year}--><!--{$arrErr.month}--><!--{$arrErr.day}--></span>
									<select name="year" style="<!--{$arrErr.year|sfGetErrorColor}-->">
									<option value="" selected>--</option>
									<!--{html_options options=$arrYear selected=$arrForm.year}-->
									</select>&nbsp;年
									<select name="month" style="<!--{$arrErr.month|sfGetErrorColor}-->"><span class="red"><!--{$arrErr.month}--></span>
									<option value="" selected>--</option>
									<!--{html_options options=$arrMonth selected=$arrForm.month}-->
									</select>&nbsp;月
									<select name="day" style="<!--{$arrErr.day|sfGetErrorColor}-->"><span class="red"><!--{$arrErr.day}--></span>
									<option value="" selected>--</option>
									<!--{html_options options=$arrDay selected=$arrForm.day}-->
									</select>&nbsp;日
								</td>
							</tr>

							<tr>
								<td bgcolor="#f0f0f0"><span class="fs12">希望するパスワード<span class="red">※</span></span><br>
								<span class="fs10">パスワードは購入時に必要です</span></td>
								<td bgcolor="#ffffff">
								<table cellspacing="0" cellpadding="0" summary=" ">
									<tr>
										<td class="fs12n"><span class="red"><!--{$arrErr.password}--></span>
										<input type="password" name="password" value="<!--{$arrForm.password}-->" maxlength="<!--{$smarty.const.PASSWORD_LEN2}-->" style="<!--{$arrErr.password|sfGetErrorColor}-->" size=15 class="box15" /></td>
									</tr>
									<tr><td height="2"></td></tr>
									<tr>
										<td class="fs10n"><span class="red">半角英数字4〜10文字でお願いします。（記号不可）</span></td>
									</tr>
									<tr><td height="5"></td></tr>
									<tr>
										<td class="fs12n"><span class="red"><!--{$arrErr.password02}--></span>
									  	<input type="password" name="password02" value="<!--{$arrForm.password02}-->" maxlength="<!--{$smarty.const.PASSWORD_LEN2}-->" style="<!--{$arrErr.password02|sfGetErrorColor}-->" size=15 class="box15" /></td>
									</tr>
									<tr><td height="2"></td></tr>
									<tr>
										<td class="fs10n"><span class="red">確認のために2度入力してください。</span></td>
									</tr>
								</table>
								</td>
							</tr>
							<tr>
								<td bgcolor="#f0f0f0" class="fs12">パスワードを忘れた時のヒント<span class="red">※</span></td>
								<td bgcolor="#ffffff">
								<table cellspacing="0" cellpadding="0" summary=" ">
									<tr>
										<td class="fs12n"><span class="red"><!--{$arrErr.reminder}--></span><select name="reminder" style="<!--{$arrErr.reminder|sfGetErrorColor}-->">
											<option value="" selected>選択してください</option>
											<!--{html_options options=$arrReminder selected=$arrForm.reminder}-->
											</select>
										</td>
									</tr>
									<tr><td height="5"></td></tr>
									<tr>
										<td class="fs12n"><span class="red"><!--{$arrErr.reminder_answer}--></span><input type="text" name="reminder_answer" value="<!--{$arrForm.reminder_answer}-->" style="<!--{$arrErr.reminder_answer|sfGetErrorColor}-->; ime-mode: active;" size=40 class="box40" /></td>
									</tr>
								</table>
								</td>
							</tr>
							<tr>
								<td bgcolor="#f0f0f0" class="fs12">メールマガジン送付について<span class="red">※</span></td>
								<td bgcolor="#ffffff" class="fs12">
									<span class="red"><!--{$arrErr.mail_flag}--></span>
									<input type="radio" name="mail_flag" value="1" id="html" style="<!--{$arrErr.mail_flag|sfGetErrorColor}-->" <!--{if $arrForm.mail_flag eq 1}--> checked <!--{/if}--> />HTMLメール＋テキストメールを受け取る</label><br>
									<input type="radio" name="mail_flag" value="2" id="text" style="<!--{$arrErr.mail_flag|sfGetErrorColor}-->" <!--{if $arrForm.mail_flag eq 2}--> checked <!--{/if}--> /><label for="text">テキストメールを受け取る</label><br>
									<input type="radio" name="mail_flag" value="3" id="no" style="<!--{$arrErr.mail_flag|sfGetErrorColor}-->" <!--{if $arrForm.mail_flag eq 3}--> checked <!--{/if}--> /><label for="no">受け取らない</label>
								</td>
							</tr>
						</table>
						<!--入力フォームここまで-->
						</td>
					</tr>
					<tr><td height="20"></td></tr>
					<tr>
						<td align="center">
							<input type="image" onmouseover="chgImgImageSubmit('<!--{$smarty.const.URL_DIR}-->img/common/b_confirm_on.gif',this)" onmouseout="chgImgImageSubmit('<!--{$smarty.const.URL_DIR}-->img/common/b_confirm.gif',this)" src="<!--{$smarty.const.URL_DIR}-->img/common/b_confirm.gif" width="150" height="30" alt="確認ページへ" name="refusal" id="refusal" />
						</td>
					</tr>
				</form>
				</table>
				</td>
			</tr>
		</table>
		<!--▲MAIN ONTENTS-->
		</td>
	</tr>
</table>
<!--▲CONTENTS-->

