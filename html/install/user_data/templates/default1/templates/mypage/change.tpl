<!--{*
 * Copyright (c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *}--> 
<!--隨�ｽｼCONTENTS-->
<table width="" border="0" cellspacing="0" cellpadding="0" summary=" ">
	<tr>
		<td align="center" bgcolor="#ffffff">
		<!--隨�ｽｼMAIN ONTENTS-->
		<table width="700" border="0" cellspacing="0" cellpadding="0" summary=" ">
			<tr>
				<td><img src="<!--{$smarty.const.URL_DIR}-->img/mypage/title.jpg" width="700" height="40" alt="MY郢晏｣ｹ�ｽ郢ｧ�ｸ"></td>
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
				<form name="form1" id="form1" method="post" action="<!--{$smarty.server.PHP_SELF|escape}-->">
				<input type="hidden" name="mode" value="confirm">
				<input type="hidden" name="customer_id" value="<!--{$arrForm.customer_id|escape}-->">
					<tr>
						<td><!--隨假ｿｽ縺｡郢ｧ�､郢晏現ﾎ�-><img src="<!--{$smarty.const.URL_DIR}-->img/mypage/subtitle02.gif" width="515" height="32" alt="闔ｨ螢ｼ阯､騾具ｽｻ鬪ｭ�ｲ陷�ｿｽ�ｮ�ｹ陞溽判蟲ｩ"></td>
					</tr>
					<tr><td height="15"></td></tr>
					<tr>
						<td class="fs12">闕ｳ邇厄ｽｨ蛟ｬ�ｽ騾ｶ�ｮ邵ｺ�ｫ邵ｺ豕鯉ｿｽ陷牙ｸ呻ｿ･邵ｺ�ｽ��ｸｺ�ｽ�ｽ邵ｲ�ｽspan class="red">遯ｶ�ｻ</span>邵ｲ讎企ｭらｸｺ�ｯ陷茨ｽ･陷牙ｸ幢ｽｿ�ｽ�ｽ鬯�ｿｽ蟯ｼ邵ｺ�ｧ邵ｺ蜷ｶ�ｽ<br>
						陷茨ｽ･陷牙ｸ幢ｽｾ蠕鯉ｿｽ闕ｳ�ｽ蛻�叉荵晢ｿｽ邵ｲ讙趣ｽ｢�ｺ髫ｱ髦ｪ�ｽ郢晢ｽｼ郢ｧ�ｸ邵ｺ�ｸ邵ｲ髦ｪ�ｽ郢ｧ�ｿ郢晢ｽｳ郢ｧ蛛ｵ縺醍ｹ晢ｽｪ郢晢ｿｽ縺醍ｸｺ蜉ｱ窶ｻ邵ｺ荳岩味邵ｺ霈費ｼ樒ｸｲ�ｽ/td>
					</tr>
					<tr><td height="10"></td></tr>
					<tr>
						<td bgcolor="#cccccc" align="center">
						<!--陷茨ｽ･陷牙ｸ吶Ψ郢ｧ�ｩ郢晢ｽｼ郢晢ｿｽ��ｸｺ阮卍ｰ郢ｧ�ｽ->
						<table width="515" border="0" cellspacing="1" cellpadding="10" summary=" ">
							<tr>
								<td width="122" bgcolor="#f0f0f0" class="fs12n">邵ｺ髮�倹陷托ｿｽspan class="red">遯ｶ�ｻ</span></td>
								<td width="350" bgcolor="#ffffff" class="fs12n"><span class="red"><!--{$arrErr.name01}--><!--{$arrErr.name02}--></span>
									陝具ｿｽnbsp;<input type="text" name="name01" value="<!--{$arrForm.name01}-->" maxlength="<!--{$smarty.const.STEXT_LEN}-->" style="<!--{$arrErr.name01|sfGetErrorColor}-->; ime-mode: active;" size="15" class="box15" />邵ｲ�ｽ骭�nbsp;<input type="text" name="name02" value="<!--{$arrForm.name02}-->" maxlength="<!--{$smarty.const.STEXT_LEN}-->" style="<!--{$arrErr.name02|sfGetErrorColor}-->; ime-mode: active;" size="15" class="box15" />	
								</td>
							</tr>
							<tr>
								<td bgcolor="#f0f0f0" class="fs12n">邵ｺ髮�倹陷第誓�ｼ蛹ｻ繝ｵ郢晢ｽｪ郢ｧ�ｬ郢晏･�ｽｼ�ｽspan class="red">遯ｶ�ｻ</span></td>
								<td bgcolor="#ffffff" class="fs12n"><span class="red"><!--{$arrErr.kana01}--><!--{$arrErr.kana02}--></span>
								郢ｧ�ｻ郢ｧ�､&nbsp;<input type="text" name="kana01" value="<!--{$arrForm.kana01}-->" maxlength="<!--{$smarty.const.STEXT_LEN}-->" style="<!--{$arrErr.kana01|sfGetErrorColor}-->; ime-mode: active;" size="15" class="box15" />邵ｲ�ｽﾎ鍋ｹｧ�､&nbsp;<input type="text" name="kana02" value="<!--{$arrForm.kana02}-->" maxlength="<!--{$smarty.const.STEXT_LEN}-->" style="<!--{$arrErr.kana02|sfGetErrorColor}-->; ime-mode: active;" size="15" class="box15" /></td>
							</tr>
							<tr>
								<td bgcolor="#f0f0f0" class="fs12n">鬩幢ｽｵ關難ｽｿ騾｡�ｪ陷ｿ�ｷ<span class="red">遯ｶ�ｻ</span></td>
								<td bgcolor="#ffffff">
								<table border="0" cellspacing="0" cellpadding="0" summary=" ">
									<tr>
										<!--{assign var=key1 value="zip01"}-->
										<!--{assign var=key2 value="zip02"}-->
										<td colspan="2"><span class="fs12n"><span class="red"><!--{$arrErr[$key1]}--><!--{$arrErr[$key2]}--></span>
										邵ｲ�ｽnbsp;<input type="text" name="zip01" value="<!--{$arrForm.zip01}-->" maxlength="<!--{$smarty.const.ZIP01_LEN}-->" style="<!--{$arrErr.zip01|sfGetErrorColor}-->; ime-mode: disabled;" size=6 class="box6" />&nbsp;-&nbsp;<input type="text" name="zip02" value="<!--{$arrForm.zip02}-->" maxlength="<!--{$smarty.const.ZIP02_LEN}-->" style="<!--{$arrErr.zip02|sfGetErrorColor}-->; ime-mode: disabled;" size=6 class="box6" />邵ｲ�ｽ
										<a href="http://search.post.japanpost.jp/zipcode/" target="_blank"><span class="fs12">鬩幢ｽｵ關難ｽｿ騾｡�ｪ陷ｿ�ｷ隶���ｴ�｢</span></a></td>
									</tr>
									<tr><td height="5"></td></tr>
									<tr>
										<td><a href="<!--{$smarty.const.URL_DIR}-->input_zip.php" onclick="fnCallAddress('<!--{$smarty.const.URL_INPUT_ZIP}-->', 'zip01', 'zip02', 'pref', 'addr01'); return false;" target="_blank"><img src="<!--{$smarty.const.URL_DIR}-->img/common/address.gif" width="86" height="20" alt="闖ｴ荵怜恍髢ｾ�ｪ陷榊供�ｽ陷会ｿｽ /></a></td>
										<td class="fs10n">&nbsp;鬩幢ｽｵ關難ｽｿ騾｡�ｪ陷ｿ�ｷ郢ｧ雋橸ｿｽ陷牙ｸ幢ｽｾ蠕鯉ｿｽ郢ｧ�ｯ郢晢ｽｪ郢晢ｿｽ縺醍ｸｺ蜉ｱ窶ｻ邵ｺ荳岩味邵ｺ霈費ｼ樒ｸｲ�ｽ/td>
									</tr>
								</table>
								</td>
							</tr>
							<tr>
								<td bgcolor="#f0f0f0" class="fs12">闖ｴ荵怜恍<span class="red">遯ｶ�ｻ</span></td>
								<td bgcolor="#ffffff">
								<table border="0" cellspacing="0" cellpadding="0" summary=" ">
									<tr>
										<td class="fs12n"><span class="red"><!--{$arrErr.pref}--><!--{$arrErr.addr01}--><!--{$arrErr.addr02}--></span>
										<select name="pref" style="<!--{$arrErr.pref|sfGetErrorColor}-->">
										<option value="" selected>鬩幢ｽｽ鬩慕§�ｺ諛�＝郢ｧ蟶昶�隰夲ｿｽ/option>
										<!--{html_options options=$arrPref selected=$arrForm.pref}-->
										</select></td>
									</tr>
									<tr><td height="7"></td>
									</tr>
									<tr>
										<td><input type="text" name="addr01" value="<!--{$arrForm.addr01}-->" size="60" class="box60" style="<!--{$arrErr.addr01|sfGetErrorColor}-->; ime-mode: active;" /></td>
									</tr>
									<tr>
										<td class="fs10n"><!--{$smarty.const.SAMPLE_ADDRESS1}--></td>
									</tr>
									<tr><td height="5"></td></tr>
									<tr>
										<td><input type="text" name="addr02" value="<!--{$arrForm.addr02}-->" size="60" class="box60" style="<!--{$arrErr.addr02|sfGetErrorColor}-->; ime-mode: active;" /></td>
									</tr>
									<tr>
										<td class="fs10n"><!--{$smarty.const.SAMPLE_ADDRESS2}--></td>
									</tr>
									<tr><td height="5"></td></tr>
									<tr>
										<td class="fs10"><span class="red">闖ｴ荵怜恍邵ｺ�ｯ2邵ｺ�､邵ｺ�ｫ陋ｻ�ｽﾂ｢邵ｺ�ｦ邵ｺ遒托ｽｨ莨懶ｿｽ邵ｺ�ｽ笳�ｸｺ�ｽﾂ｢邵ｺ�ｾ邵ｺ蜷ｶ�ｽ郢晄ｧｭﾎｦ郢ｧ�ｷ郢晢ｽｧ郢晢ｽｳ陷ｷ髦ｪ�ｽ陟｢�ｽ笘�坎莨懶ｿｽ邵ｺ蜉ｱ窶ｻ邵ｺ荳岩味邵ｺ霈費ｼ樒ｸｲ�ｽ/span></td>
									</tr>
								</table>
								</td>
							</tr>
							<tr>
								<td bgcolor="#f0f0f0" class="fs12n">鬮ｮ�ｻ髫ｧ�ｱ騾｡�ｪ陷ｿ�ｷ<span class="red">遯ｶ�ｻ</span></td>
								<td bgcolor="#ffffff" class="fs12n"><span class="red"><!--{$arrErr.tel01}--><!--{$arrErr.tel02}--><!--{$arrErr.tel03}--></span><input type="text" name="tel01" value="<!--{$arrForm.tel01}-->" maxlength="<!--{$smarty.const.TEL_ITEM_LEN}-->" size="6" style="<!--{$arrErr.tel01|sfGetErrorColor}-->; ime-mode: disabled;" />&nbsp;-&nbsp;<input type="text" name="tel02" value="<!--{$arrForm.tel02}-->" maxlength="<!--{$smarty.const.TEL_ITEM_LEN}-->" size="6" style="<!--{$arrErr.tel02|sfGetErrorColor}-->; ime-mode: disabled;" />&nbsp;-&nbsp;<input type="text" name="tel03" value="<!--{$arrForm.tel03}-->" maxlength="<!--{$smarty.const.TEL_ITEM_LEN}-->" size="6" style="<!--{$arrErr.tel03|sfGetErrorColor}-->; ime-mode: disabled;" /></td>
								</td>
							</tr>
							<tr>
								<td bgcolor="#f0f0f0" class="fs12n">FAX</td>
								<td bgcolor="#ffffff" class="fs12n"><span class="red"><!--{$arrErr.fax01}--><!--{$arrErr.fax02}--><!--{$arrErr.fax03}--></span><input type="text" name="fax01" value="<!--{$arrForm.fax01}-->" maxlength="<!--{$smarty.const.TEL_ITEM_LEN}-->" size="6" style="<!--{$arrErr.fax01|sfGetErrorColor}-->; ime-mode: disabled;" />&nbsp;-&nbsp;<input type="text" name="fax02" value="<!--{$arrForm.fax02}-->" maxlength="<!--{$smarty.const.TEL_ITEM_LEN}-->" size="6" style="<!--{$arrErr.fax02|sfGetErrorColor}-->; ime-mode: disabled;" />&nbsp;-&nbsp;<input type="text" name="fax03" value="<!--{$arrForm.fax03}-->" maxlength="<!--{$smarty.const.TEL_ITEM_LEN}-->" size="6" style="<!--{$arrErr.fax03|sfGetErrorColor}-->; ime-mode: disabled;" /></td>
							</tr>
							<tr>
								<td bgcolor="#f0f0f0" class="fs12n">郢晢ｽ｡郢晢ｽｼ郢晢ｽｫ郢ｧ�｢郢晏ｳｨﾎ樒ｹｧ�ｹ<span class="red">遯ｶ�ｻ</span></td>
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
										<td class="fs10n"><span class="red">驕抵ｽｺ髫ｱ髦ｪ�ｽ邵ｺ貅假ｽ�陟趣ｽｦ陷茨ｽ･陷牙ｸ呻ｼ�ｸｺ�ｦ邵ｺ荳岩味邵ｺ霈費ｼ樒ｸｲ�ｽ/span></td>
									</tr>
								</table>
								</td>
							</tr>
							<tr>
								<td bgcolor="#f0f0f0" class="fs12n">隲､�ｧ陋ｻ�･<span class="red">遯ｶ�ｻ</span></td>
								<td bgcolor="#ffffff" class="fs12n">
								<span class="red"><!--{$arrErr.sex}--></span><input type="radio" id="man" name="sex" value="1" style="<!--{$arrErr.sex|sfGetErrorColor}-->" <!--{if $arrForm.sex eq 1}--> checked <!--{/if}--> /><label for="man">騾包ｽｷ隲､�ｧ</label>邵ｲ�ｽinput type="radio" id="woman" name="sex" value="2" style="<!--{$arrErr.sex|sfGetErrorColor}-->" <!--{if $arrForm.sex eq 2}--> checked <!--{/if}--> /><label for="woman">陞ゑｽｳ隲､�ｧ</label></td>
							</tr>
							<tr>
								<td bgcolor="#f0f0f0" class="fs12n">髢ｨ�ｷ隶鯉ｽｭ</td>
								<td bgcolor="#ffffff">
									<span class="red"><!--{$arrErr.job}--></span>
									<select name="job">
									<option value="" selected>鬩包ｽｸ隰壽ｧｭ��ｸｺ�ｦ邵ｺ荳岩味邵ｺ霈費ｼ�/option>
									<!--{html_options options=$arrJob selected=$arrForm.job}-->
									</select>
								</td>
							</tr>
							<tr>
								<td bgcolor="#f0f0f0" class="fs12n">騾墓ｺｷ�ｹ�ｴ隴帛沺蠕�/td>
								<td bgcolor="#ffffff" class="fs12n"><span class="red"><!--{$arrErr.year}--><!--{$arrErr.month}--><!--{$arrErr.day}--></span>
									<select name="year" style="<!--{$arrErr.year|sfGetErrorColor}-->">
									<option value="" selected>--</option>
									<!--{html_options options=$arrYear selected=$arrForm.year}-->
									</select>&nbsp;陝ｷ�ｴ
									<select name="month" style="<!--{$arrErr.month|sfGetErrorColor}-->"><span class="red"><!--{$arrErr.month}--></span>
									<option value="" selected>--</option>
									<!--{html_options options=$arrMonth selected=$arrForm.month}-->
									</select>&nbsp;隴幢ｿｽ
									<select name="day" style="<!--{$arrErr.day|sfGetErrorColor}-->"><span class="red"><!--{$arrErr.day}--></span>
									<option value="" selected>--</option>
									<!--{html_options options=$arrDay selected=$arrForm.day}-->
									</select>&nbsp;隴鯉ｽ･
								</td>
							</tr>

							<tr>
								<td bgcolor="#f0f0f0"><span class="fs12">陝ｶ譴ｧ謔咲ｸｺ蜷ｶ�狗ｹ昜ｻ｣縺帷ｹ晢ｽｯ郢晢ｽｼ郢晢ｿｽspan class="red">遯ｶ�ｻ</span></span><br>
								<span class="fs10">郢昜ｻ｣縺帷ｹ晢ｽｯ郢晢ｽｼ郢晏ｳｨ�ｽ髮会ｽｼ陷茨ｽ･隴弱ｅ竊楢��ｽ�ｦ竏壹�邵ｺ�ｽ/span></td>
								<td bgcolor="#ffffff">
								<table cellspacing="0" cellpadding="0" summary=" ">
									<tr>
										<td class="fs12n"><span class="red"><!--{$arrErr.password}--></span>
										<input type="password" name="password" value="<!--{$arrForm.password}-->" maxlength="<!--{$smarty.const.PASSWORD_LEN2}-->" style="<!--{$arrErr.password|sfGetErrorColor}-->" size=15 class="box15" /></td>
									</tr>
									<tr><td height="2"></td></tr>
									<tr>
										<td class="fs10n"><span class="red">陷企｡假ｽｧ螳壽�隰ｨ�ｰ陝�ｿｽ邵ｲ�ｽ0隴�ｿｽ�ｭ蜉ｱ縲堤ｸｺ莨�ｽ｡蛟･�樒ｸｺ蜉ｱ竏ｪ邵ｺ蜷ｶ�ｽ�ｽ驛�ｽｨ莨懈差闕ｳ讎雁ｺ�ｿｽ�ｽ/span></td>
									</tr>
									<tr><td height="5"></td></tr>
									<tr>
										<td class="fs12n"><span class="red"><!--{$arrErr.password02}--></span>
									  	<input type="password" name="password02" value="<!--{$arrForm.password02}-->" maxlength="<!--{$smarty.const.PASSWORD_LEN2}-->" style="<!--{$arrErr.password02|sfGetErrorColor}-->" size=15 class="box15" /></td>
									</tr>
									<tr><td height="2"></td></tr>
									<tr>
										<td class="fs10n"><span class="red">驕抵ｽｺ髫ｱ髦ｪ�ｽ邵ｺ貅假ｽ∫ｸｺ�ｫ2陟趣ｽｦ陷茨ｽ･陷牙ｸ呻ｼ�ｸｺ�ｦ邵ｺ荳岩味邵ｺ霈費ｼ樒ｸｲ�ｽ/span></td>
									</tr>
								</table>
								</td>
							</tr>
							<tr>
								<td bgcolor="#f0f0f0" class="fs12">郢昜ｻ｣縺帷ｹ晢ｽｯ郢晢ｽｼ郢晏ｳｨ�定�蛟･�檎ｸｺ貊灘�邵ｺ�ｮ郢晏�ﾎｦ郢晢ｿｽspan class="red">遯ｶ�ｻ</span></td>
								<td bgcolor="#ffffff">
								<table cellspacing="0" cellpadding="0" summary=" ">
									<tr>
										<td class="fs12n"><span class="red"><!--{$arrErr.reminder}--></span><select name="reminder" style="<!--{$arrErr.reminder|sfGetErrorColor}-->">
											<option value="" selected>鬩包ｽｸ隰壽ｧｭ��ｸｺ�ｦ邵ｺ荳岩味邵ｺ霈費ｼ�/option>
											<!--{html_options options=$arrReminder selected=$arrForm.reminder}-->
											</select>
										</td>
									</tr>
									<tr><td height="5"></td></tr>
									<tr>
										<td class="fs12n"><span class="red"><!--{$arrErr.reminder_answer}--></span><input type="text" name="reminder_answer" value="<!--{$arrForm.reminder_answer|escape}-->" style="<!--{$arrErr.reminder_answer|sfGetErrorColor}-->; ime-mode: active;" size=40 class="box40" /></td>
									</tr>
								</table>
								</td>
							</tr>
							<tr>
								<td bgcolor="#f0f0f0" class="fs12">郢晢ｽ｡郢晢ｽｼ郢晢ｽｫ郢晄ｧｭ縺守ｹｧ�ｸ郢晢ｽｳ鬨ｾ竏ｽ�ｻ蛟･竊鍋ｸｺ�､邵ｺ�ｽ窶ｻ<span class="red">遯ｶ�ｻ</span></td>
								<td bgcolor="#ffffff" class="fs12">
									<span class="red"><!--{$arrErr.mailmaga_flg}--></span>
									<input type="radio" name="mailmaga_flg" value="1" id="html" style="<!--{$arrErr.mailmaga_flg|sfGetErrorColor}-->" <!--{if $arrForm.mailmaga_flg eq 1}--> checked <!--{/if}--> />HTML郢晢ｽ｡郢晢ｽｼ郢晢ｽｫ�ｽ荵昴Θ郢ｧ�ｭ郢ｧ�ｹ郢晏現ﾎ鍋ｹ晢ｽｼ郢晢ｽｫ郢ｧ雋槫･ｳ邵ｺ螟ｧ蜿咏ｹｧ�ｽ/label><br>
									<input type="radio" name="mailmaga_flg" value="2" id="text" style="<!--{$arrErr.mailmaga_flg|sfGetErrorColor}-->" <!--{if $arrForm.mailmaga_flg eq 2}--> checked <!--{/if}--> /><label for="text">郢晢ｿｽ縺冗ｹｧ�ｹ郢晏現ﾎ鍋ｹ晢ｽｼ郢晢ｽｫ郢ｧ雋槫･ｳ邵ｺ螟ｧ蜿咏ｹｧ�ｽ/label><br>
									<input type="radio" name="mailmaga_flg" value="3" id="no" style="<!--{$arrErr.mailmaga_flg|sfGetErrorColor}-->" <!--{if $arrForm.mailmaga_flg eq 3}--> checked <!--{/if}--> /><label for="no">陷ｿ蜉ｱﾂ｢陷ｿ謔ｶ�臥ｸｺ�ｪ邵ｺ�ｽ/label>
								</td>
							</tr>
						</table>
						<!--陷茨ｽ･陷牙ｸ吶Ψ郢ｧ�ｩ郢晢ｽｼ郢晢ｿｽ��ｸｺ阮吮穐邵ｺ�ｧ-->
						</td>
					</tr>
					<tr><td height="20"></td></tr>
					<tr>
						<td align="center">
							<input type="image" onmouseover="chgImgImageSubmit('<!--{$smarty.const.URL_DIR}-->img/common/b_confirm_on.gif',this)" onmouseout="chgImgImageSubmit('<!--{$smarty.const.URL_DIR}-->img/common/b_confirm.gif',this)" src="<!--{$smarty.const.URL_DIR}-->img/common/b_confirm.gif" width="150" height="30" alt="驕抵ｽｺ髫ｱ髦ｪ�ｽ郢晢ｽｼ郢ｧ�ｸ邵ｺ�ｸ" name="refusal" id="refusal" />
						</td>
					</tr>
				</form>
				</table>
				</td>
			</tr>
		</table>
		<!--隨�ｽｲMAIN ONTENTS-->
		</td>
	</tr>
</table>
<!--隨�ｽｲCONTENTS-->

