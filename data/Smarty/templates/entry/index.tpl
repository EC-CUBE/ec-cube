<!--{*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *}-->
 <!--▼CONTENTS-->
<table width="" border="0" cellspacing="0" cellpadding="0" summary=" ">
	<tr valign="top">
		<td align="right" bgcolor="#ffffff">
		<!--▼MAIN ONTENTS-->
		<table width="580" border="0" cellspacing="0" cellpadding="0" summary=" ">
			<form name="form1" id="form1" method="post" action="<!--{$smarty.server.PHP_SELF|escape}-->">
			<input type="hidden" name="mode" value="confirm">
			<tr>
				<td><img src="<!--{$smarty.const.URL_DIR}-->img/entry/title.jpg" width="580" height="40" alt="会員登録"></td>
			</tr>
			<tr><td height="15"></td></tr>
			<tr>
				<td class="fs12">ご登録されますと、まずは仮会員となります。<br>
				入力されたメールアドレスに、ご連絡が届きますので、本会員になった上でお買い物をお楽しみください。</td>
			</tr>
			<tr><td height="20"></td></tr>
			<tr>
				<td bgcolor="#cccccc" align="center">
				<!--入力フォームここから-->
				<table width="580" border="0" cellspacing="1" cellpadding="10" summary=" ">
					<tr>
						<td width="135" bgcolor="#f0f0f0" class="fs12n">お名前<span class="red">※</span></td>
						<td width="402" bgcolor="#ffffff" class="fs12n"><span class="red"><!--{$arrErr.name01}--><!--{$arrErr.name02}--></span>姓&nbsp;<input type="text" name="name01" size="15" class="box15" value="<!--{$name01|escape}-->" maxlength="<!--{$smarty.const.STEXT_LEN}-->" style="<!--{$arrErr.name01|sfGetErrorColor}-->; ime-mode: active;" />　名&nbsp;<input type="text" name="name02" size="15" class="box15"value="<!--{$name02|escape}-->" maxlength="<!--{$smarty.const.STEXT_LEN}-->" style="<!--{$arrErr.name02|sfGetErrorColor}-->; ime-mode: active;" /></td>
					</tr>
					<tr>
						<td bgcolor="#f0f0f0" class="fs12n">お名前（フリガナ）<span class="red">※</span></td>
						<td bgcolor="#ffffff" class="fs12n"><span class="red"><!--{$arrErr.kana01}--><!--{$arrErr.kana02}--></span>セイ&nbsp;<input type="text" name="kana01" size="15" class="box15" value="<!--{$kana01|escape}-->" maxlength="<!--{$smarty.const.STEXT_LEN}-->" style="<!--{$arrErr.kana01|sfGetErrorColor}-->; ime-mode: active;" />　メイ&nbsp;<input type="text" name="kana02" size="15" class="box15" value="<!--{$kana02|escape}-->" maxlength="<!--{$smarty.const.STEXT_LEN}-->" style="<!--{$arrErr.kana02|sfGetErrorColor}-->; ime-mode: active;" /></td>
					</tr>
					<tr>
						<td bgcolor="#f0f0f0" class="fs12n">郵便番号<span class="red">※</span></td>
						<td bgcolor="#ffffff">
						<table border="0" cellspacing="0" cellpadding="0" summary=" ">
							<tr>
								<td colspan="2">
									<!--{assign var=key1 value="zip01"}-->
									<!--{assign var=key2 value="zip02"}-->
									<span class="fs12n"><span class="red"><!--{$arrErr[$key1]}--><!--{$arrErr[$key2]}--></span></span>
									<span class="fs12n">〒&nbsp;</span><input type="text" name="zip01" value="<!--{if $zip01 == ""}--><!--{$arrOtherDeliv.zip01|escape}--><!--{else}--><!--{$zip01|escape}--><!--{/if}-->" maxlength="<!--{$smarty.const.ZIP01_LEN}-->" style="<!--{$arrErr.zip01|sfGetErrorColor}-->; ime-mode: disabled;" size=6 class="box6" />&nbsp;-&nbsp;<input type="text" name="zip02" value="<!--{if $zip02 == ""}--><!--{$arrOtherDeliv.zip02|escape}--><!--{else}--><!--{$zip02|escape}--><!--{/if}-->" maxlength="<!--{$smarty.const.ZIP02_LEN}-->" style="<!--{$arrErr.zip02|sfGetErrorColor}-->; ime-mode: disabled;" size=6 class="box6" />　
									<a href="http://search.post.japanpost.jp/7zip/" target="_blank"><span class="fs10">郵便番号検索</span></a>
								</td>
							</tr>
							<tr><td height="5"></td></tr>
							<tr>
								<td><a href="<!--{$smarty.const.URL_DIR}-->address/index.php" onclick="fnCallAddress('<!--{$smarty.const.URL_INPUT_ZIP}-->', 'zip01', 'zip02', 'pref', 'addr01'); return false;" target="_blank"><img src="<!--{$smarty.const.URL_DIR}-->img/common/address.gif" width="86" height="20" alt="住所自動入力" /></a></td>
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
								<!--{html_options options=$arrPref selected=$pref}-->
								</select></td>
							</tr>
							<tr><td height="7"></td>
							</tr>
							<tr>
								<td><input type="text" name="addr01" size="40" class="box40" value="<!--{$addr01|escape}-->" style="<!--{$arrErr.addr01|sfGetErrorColor}-->; ime-mode: active;"/></td>
							</tr>
							<tr>
								<td class="fs10n"><!--{$smarty.const.SAMPLE_ADDRESS1}--></td>
							</tr>
							<tr><td height="5"></td></tr>
							<tr>
								<td><input type="text" name="addr02" size="40" class="box40" value="<!--{$addr02|escape}-->" style="<!--{$arrErr.addr02|sfGetErrorColor}-->; ime-mode: active;" /></td>
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
						<td bgcolor="#f0f0f0" class="fs12n">電話番号<span class="red">※</span></td>
						<td bgcolor="#ffffff" class="fs12n"><span class="red"><!--{$arrErr.tel01}--><!--{$arrErr.tel02}--><!--{$arrErr.tel03}--></span><input type="text" name="tel01" size="6" value="<!--{$tel01|escape}-->" maxlength="<!--{$smarty.const.TEL_ITEM_LEN}-->" style="<!--{$arrErr.tel01|sfGetErrorColor}-->; ime-mode: disabled;" />&nbsp;-&nbsp;<input type="text" name="tel02" size="6" value="<!--{$tel02|escape}-->" maxlength="<!--{$smarty.const.TEL_ITEM_LEN}-->" style="<!--{$arrErr.tel02|sfGetErrorColor}-->; ime-mode: disabled;" />&nbsp;-&nbsp;<input type="text" name="tel03" size="6" value="<!--{$tel03|escape}-->" maxlength="<!--{$smarty.const.TEL_ITEM_LEN}-->" style="<!--{$arrErr.tel03|sfGetErrorColor}-->; ime-mode: disabled;" /></td>
					</tr>
					<tr>
						<td bgcolor="#f0f0f0" class="fs12n">FAX</td>
						<td bgcolor="#ffffff" class="fs12n"><span class="red"><!--{$arrErr.fax01}--><!--{$arrErr.fax02}--><!--{$arrErr.fax03}--></span><input type="text" name="fax01" size="6" value="<!--{$fax01|escape}-->" maxlength="<!--{$smarty.const.TEL_ITEM_LEN}-->"  style="<!--{$arrErr.fax01|sfGetErrorColor}-->; ime-mode: disabled;" />&nbsp;-&nbsp;<input type="text" name="fax02" size="6" value="<!--{$fax02|escape}-->" maxlength="<!--{$smarty.const.TEL_ITEM_LEN}-->" style="<!--{$arrErr.fax01|sfGetErrorColor}-->; ime-mode: disabled;" />&nbsp;-&nbsp;<input type="text" name="fax03" size="6" value="<!--{$fax02|escape}-->" maxlength="<!--{$smarty.const.TEL_ITEM_LEN}-->" style="<!--{$arrErr.fax01|sfGetErrorColor}-->; ime-mode: disabled;" /></td>
					</tr>
					<tr>
						<td bgcolor="#f0f0f0" class="fs12n">メールアドレス<span class="red">※</span></td>
						<td bgcolor="#ffffff">
						<table border="0" cellspacing="0" cellpadding="0" summary=" ">
							<tr><span class="fs12n"><span class="red"><!--{$arrErr.email}--><!--{$arrErr.email02}--></span></span>
								<td><input type="text" name="email" size="40" class="box40" value="<!--{$email|escape}-->" style="<!--{$arrErr.email|sfGetErrorColor}-->; ime-mode: disabled;" /></td>
							</tr>
							<tr><td height="5"></td></tr>
							<tr>
								<td><input type="text" name="email02" size="40" class="box40" value="<!--{$email02|escape}-->" style="<!--{$arrErr.email02|sfGetErrorColor}-->; ime-mode: disabled;" /></td>
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
						<td bgcolor="#ffffff" class="fs12n"><span class="red"><!--{$arrErr.sex}--></span>
							<input type="radio" name="sex" id="man" value="1" style="<!--{$arrErr.sex|sfGetErrorColor}-->" <!--{if $sex eq 1}-->checked<!--{/if}--> /><label for="man">男性</label>　<input type="radio" name="sex" id="woman" value="2" style="<!--{$arrErr.sex|sfGetErrorColor}-->" <!--{if $sex eq 2}-->checked<!--{/if}--> /><label for="woman">女性</label>
						</td>
					</tr>
					<tr>
						<td bgcolor="#f0f0f0" class="fs12n">職業</td>
						<td bgcolor="#ffffff"><span class="red"><!--{$arrErr.job}--></span>
						<select name="job" style="<!--{$arrErr.job|sfGetErrorColor}-->">
						<option value="" selected>選択してください</option>
						<!--{html_options options=$arrJob selected=$job}-->
						</select></td>
					</tr>
					<tr>
						<td bgcolor="#f0f0f0" class="fs12n">生年月日</td>
						<td bgcolor="#ffffff" class="fs12n">
						<table cellspacing="0" cellpadding="0" summary=" ">
							<tr>
								<td class="fs12n"><span class="red"><!--{$arrErr.year}--><!--{$arrErr.month}--><!--{$arrErr.day}--></span>
									<select name="year" style="<!--{$arrErr.year|sfGetErrorColor}-->">
										<!--{html_options options=$arrYear selected=$year}-->
									</select>年
									<select name="month" style="<!--{$arrErr.year|sfGetErrorColor}-->">
										<option value="">--</option>
										<!--{html_options options=$arrMonth selected=$month}-->
									</select>月
									<select value="" name="day" style="<!--{$arrErr.year|sfGetErrorColor}-->">
										<option value="">--</option>\
										<!--{html_options options=$arrDay selected=$day}-->
									</select>日</td>
							</tr>
							<tr><td height="2"></td></tr>
						</table>
						</td>
					</tr>
					<tr>
						<td bgcolor="#f0f0f0" ><span class="fs12">希望するパスワード<span class="red">※</span></span><br>
						<span class="fs10">パスワードは購入時に必要です</span></td>
						<td bgcolor="#ffffff">
						<table cellspacing="0" cellpadding="0" summary=" ">
							<tr>
								<td class="fs12n"><span class="red"><!--{$arrErr.password}--><!--{$arrErr.password02}--></span><input type="password" name="password" value="<!--{$arrForm.password}-->"size="15" class="box15"  style="<!--{$arrErr.password|sfGetErrorColor}-->"/></td>
							</tr>
							<tr><td height="2"></td></tr>
							<tr>
								<td class="fs10n"><span class="red">半角英数字4〜10文字でお願いします。（記号不可）</span></td>
							</tr>
							<tr><td height="5"></td></tr>
							<tr>
								<td><input type="password" name="password02" value="<!--{$arrForm.password02}-->" size="15" class="box15"  style="<!--{$arrErr.password02|sfGetErrorColor}-->"/></td>
							</tr>
							<tr><td height="2"></td></tr>
							<tr>
								<td class="fs10n"><span class="red">確認のために2度入力してください。</span></td>
							</tr>
						</table>
						</td>
					</tr>
					<tr>
						<td bgcolor="#f0f0f0"  class="fs12">パスワードを忘れた時のヒント<span class="red">※</span></td>
						<td bgcolor="#ffffff" class="fs12n"><span class="red"><!--{$arrErr.reminder}--><!--{$arrErr.reminder_answer}--></span>
						<table cellspacing="0" cellpadding="0" summary=" ">
							<tr>
								<td class="fs12n">質問：</td>
								<td>
									<select name="reminder" style="<!--{$arrErr.reminder|sfGetErrorColor}-->">
										<option value="" selected>選択してください</option>
										<!--{html_options options=$arrReminder selected=$reminder}-->
									</select>
								</td>
							</tr>
							<tr><td height="5"></td></tr>
							<tr>
								<td class="fs12n">答え：</td>

								<td><input type="text" name="reminder_answer" size="33" class="box33" value="<!--{$reminder_answer|escape}-->" style="<!--{$arrErr.reminder_answer|sfGetErrorColor}-->; ime-mode: active;" /></td>
							</tr>
						</table>
						</td>
					</tr>
					<tr>
						<td bgcolor="#f0f0f0" class="fs12">メールマガジン送付について<span class="red">※</span></td>
						<td bgcolor="#ffffff" class="fs12"><span class="red"><!--{$arrErr.mailmaga_flg}--></span>
						<input type="radio" name="mailmaga_flg" id="html" value="1" style="<!--{$arrErr.mailmaga_flg|sfGetErrorColor}-->" <!--{if $mailmaga_flg eq 1 || $mailmaga_flg eq ""}-->checked<!--{/if}--> /><label for="html">HTMLメール＋テキストメールを受け取る</label><br>
						<input type="radio" name="mailmaga_flg" id="text"value="2" style="<!--{$arrErr.mailmaga_flg|sfGetErrorColor}-->" <!--{if $mailmaga_flg eq 2}-->checked<!--{/if}--> /><label for="text">テキストメールを受け取る</label><br>
						<input type="radio" name="mailmaga_flg" id="no" value="3" style="<!--{$arrErr.mailmaga_flg|sfGetErrorColor}-->" <!--{if $mailmaga_flg eq 3}-->checked<!--{/if}--> /><label for="no">受け取らない</label></td>
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
