<!--{*
 * Copyright (c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *}-->
<!--笆ｼCONTENTS-->
<table width="" border="0" cellspacing="0" cellpadding="0" summary=" ">
	<tr>
		<td align="center" bgcolor="#ffffff">
		<!--笆ｼMAIN ONTENTS-->
		<table width="700" border="0" cellspacing="0" cellpadding="0" summary=" ">
			<tr>
				<td><img src="<!--{$smarty.const.URL_DIR}-->img/mypage/title.jpg" width="700" height="40" alt="MY繝壹�繧ｸ"></td>
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
						<td><!--笘�ち繧､繝医Ν--><img src="<!--{$smarty.const.URL_DIR}-->img/mypage/subtitle02.gif" width="515" height="32" alt="莨壼藤逋ｻ骭ｲ蜀�ｮｹ螟画峩"></td>
					</tr>
					<tr><td height="15"></td></tr>
					<tr>
						<td class="fs12">荳玖ｨ倬�逶ｮ縺ｫ縺泌�蜉帙￥縺�＆縺��縲�span class="red">窶ｻ</span>縲榊魂縺ｯ蜈･蜉帛ｿ��鬆�岼縺ｧ縺吶�<br>
						蜈･蜉帛ｾ後�荳�分荳九�縲檎｢ｺ隱阪�繝ｼ繧ｸ縺ｸ縲阪�繧ｿ繝ｳ繧偵け繝ｪ繝�け縺励※縺上□縺輔＞縲�/td>
					</tr>
					<tr><td height="10"></td></tr>
					<tr>
						<td bgcolor="#cccccc" align="center">
						<!--蜈･蜉帙ヵ繧ｩ繝ｼ繝�％縺薙°繧�->
						<table width="515" border="0" cellspacing="1" cellpadding="10" summary=" ">
							<tr>
								<td width="122" bgcolor="#f0f0f0" class="fs12n">縺雁錐蜑�span class="red">窶ｻ</span></td>
								<td width="350" bgcolor="#ffffff" class="fs12n"><span class="red"><!--{$arrErr.name01}--><!--{$arrErr.name02}--></span>
									蟋�nbsp;<input type="text" name="name01" value="<!--{$arrForm.name01}-->" maxlength="<!--{$smarty.const.STEXT_LEN}-->" style="<!--{$arrErr.name01|sfGetErrorColor}-->; ime-mode: active;" size="15" class="box15" />縲�錐&nbsp;<input type="text" name="name02" value="<!--{$arrForm.name02}-->" maxlength="<!--{$smarty.const.STEXT_LEN}-->" style="<!--{$arrErr.name02|sfGetErrorColor}-->; ime-mode: active;" size="15" class="box15" />	
								</td>
							</tr>
							<tr>
								<td bgcolor="#f0f0f0" class="fs12n">縺雁錐蜑搾ｼ医ヵ繝ｪ繧ｬ繝奇ｼ�span class="red">窶ｻ</span></td>
								<td bgcolor="#ffffff" class="fs12n"><span class="red"><!--{$arrErr.kana01}--><!--{$arrErr.kana02}--></span>
								繧ｻ繧､&nbsp;<input type="text" name="kana01" value="<!--{$arrForm.kana01}-->" maxlength="<!--{$smarty.const.STEXT_LEN}-->" style="<!--{$arrErr.kana01|sfGetErrorColor}-->; ime-mode: active;" size="15" class="box15" />縲�Γ繧､&nbsp;<input type="text" name="kana02" value="<!--{$arrForm.kana02}-->" maxlength="<!--{$smarty.const.STEXT_LEN}-->" style="<!--{$arrErr.kana02|sfGetErrorColor}-->; ime-mode: active;" size="15" class="box15" /></td>
							</tr>
							<tr>
								<td bgcolor="#f0f0f0" class="fs12n">驛ｵ萓ｿ逡ｪ蜿ｷ<span class="red">窶ｻ</span></td>
								<td bgcolor="#ffffff">
								<table border="0" cellspacing="0" cellpadding="0" summary=" ">
									<tr>
										<!--{assign var=key1 value="zip01"}-->
										<!--{assign var=key2 value="zip02"}-->
										<td colspan="2"><span class="fs12n"><span class="red"><!--{$arrErr[$key1]}--><!--{$arrErr[$key2]}--></span>
										縲�nbsp;<input type="text" name="zip01" value="<!--{$arrForm.zip01}-->" maxlength="<!--{$smarty.const.ZIP01_LEN}-->" style="<!--{$arrErr.zip01|sfGetErrorColor}-->; ime-mode: disabled;" size=6 class="box6" />&nbsp;-&nbsp;<input type="text" name="zip02" value="<!--{$arrForm.zip02}-->" maxlength="<!--{$smarty.const.ZIP02_LEN}-->" style="<!--{$arrErr.zip02|sfGetErrorColor}-->; ime-mode: disabled;" size=6 class="box6" />縲�
										<a href="http://search.post.japanpost.jp/zipcode/" target="_blank"><span class="fs12">驛ｵ萓ｿ逡ｪ蜿ｷ讀懃ｴ｢</span></a></td>
									</tr>
									<tr><td height="5"></td></tr>
									<tr>
										<td><a href="<!--{$smarty.const.URL_DIR}-->input_zip.php" onclick="fnCallAddress('<!--{$smarty.const.URL_INPUT_ZIP}-->', 'zip01', 'zip02', 'pref', 'addr01'); return false;" target="_blank"><img src="<!--{$smarty.const.URL_DIR}-->img/common/address.gif" width="86" height="20" alt="菴乗園閾ｪ蜍募�蜉� /></a></td>
										<td class="fs10n">&nbsp;驛ｵ萓ｿ逡ｪ蜿ｷ繧貞�蜉帛ｾ後�繧ｯ繝ｪ繝�け縺励※縺上□縺輔＞縲�/td>
									</tr>
								</table>
								</td>
							</tr>
							<tr>
								<td bgcolor="#f0f0f0" class="fs12">菴乗園<span class="red">窶ｻ</span></td>
								<td bgcolor="#ffffff">
								<table border="0" cellspacing="0" cellpadding="0" summary=" ">
									<tr>
										<td class="fs12n"><span class="red"><!--{$arrErr.pref}--><!--{$arrErr.addr01}--><!--{$arrErr.addr02}--></span>
										<select name="pref" style="<!--{$arrErr.pref|sfGetErrorColor}-->">
										<option value="" selected>驛ｽ驕灘ｺ懃恁繧帝∈謚�/option>
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
										<td class="fs10"><span class="red">菴乗園縺ｯ2縺､縺ｫ蛻�¢縺ｦ縺碑ｨ伜�縺�◆縺�¢縺ｾ縺吶�繝槭Φ繧ｷ繝ｧ繝ｳ蜷阪�蠢�★險伜�縺励※縺上□縺輔＞縲�/span></td>
									</tr>
								</table>
								</td>
							</tr>
							<tr>
								<td bgcolor="#f0f0f0" class="fs12n">髮ｻ隧ｱ逡ｪ蜿ｷ<span class="red">窶ｻ</span></td>
								<td bgcolor="#ffffff" class="fs12n"><span class="red"><!--{$arrErr.tel01}--><!--{$arrErr.tel02}--><!--{$arrErr.tel03}--></span><input type="text" name="tel01" value="<!--{$arrForm.tel01}-->" maxlength="<!--{$smarty.const.TEL_ITEM_LEN}-->" size="6" style="<!--{$arrErr.tel01|sfGetErrorColor}-->; ime-mode: disabled;" />&nbsp;-&nbsp;<input type="text" name="tel02" value="<!--{$arrForm.tel02}-->" maxlength="<!--{$smarty.const.TEL_ITEM_LEN}-->" size="6" style="<!--{$arrErr.tel02|sfGetErrorColor}-->; ime-mode: disabled;" />&nbsp;-&nbsp;<input type="text" name="tel03" value="<!--{$arrForm.tel03}-->" maxlength="<!--{$smarty.const.TEL_ITEM_LEN}-->" size="6" style="<!--{$arrErr.tel03|sfGetErrorColor}-->; ime-mode: disabled;" /></td>
								</td>
							</tr>
							<tr>
								<td bgcolor="#f0f0f0" class="fs12n">FAX</td>
								<td bgcolor="#ffffff" class="fs12n"><span class="red"><!--{$arrErr.fax01}--><!--{$arrErr.fax02}--><!--{$arrErr.fax03}--></span><input type="text" name="fax01" value="<!--{$arrForm.fax01}-->" maxlength="<!--{$smarty.const.TEL_ITEM_LEN}-->" size="6" style="<!--{$arrErr.fax01|sfGetErrorColor}-->; ime-mode: disabled;" />&nbsp;-&nbsp;<input type="text" name="fax02" value="<!--{$arrForm.fax02}-->" maxlength="<!--{$smarty.const.TEL_ITEM_LEN}-->" size="6" style="<!--{$arrErr.fax02|sfGetErrorColor}-->; ime-mode: disabled;" />&nbsp;-&nbsp;<input type="text" name="fax03" value="<!--{$arrForm.fax03}-->" maxlength="<!--{$smarty.const.TEL_ITEM_LEN}-->" size="6" style="<!--{$arrErr.fax03|sfGetErrorColor}-->; ime-mode: disabled;" /></td>
							</tr>
							<tr>
								<td bgcolor="#f0f0f0" class="fs12n">繝｡繝ｼ繝ｫ繧｢繝峨Ξ繧ｹ<span class="red">窶ｻ</span></td>
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
										<td class="fs10n"><span class="red">遒ｺ隱阪�縺溘ａ2蠎ｦ蜈･蜉帙＠縺ｦ縺上□縺輔＞縲�/span></td>
									</tr>
								</table>
								</td>
							</tr>
							<tr>
								<td bgcolor="#f0f0f0" class="fs12n">諤ｧ蛻･<span class="red">窶ｻ</span></td>
								<td bgcolor="#ffffff" class="fs12n">
								<span class="red"><!--{$arrErr.sex}--></span><input type="radio" id="man" name="sex" value="1" style="<!--{$arrErr.sex|sfGetErrorColor}-->" <!--{if $arrForm.sex eq 1}--> checked <!--{/if}--> /><label for="man">逕ｷ諤ｧ</label>縲�input type="radio" id="woman" name="sex" value="2" style="<!--{$arrErr.sex|sfGetErrorColor}-->" <!--{if $arrForm.sex eq 2}--> checked <!--{/if}--> /><label for="woman">螂ｳ諤ｧ</label></td>
							</tr>
							<tr>
								<td bgcolor="#f0f0f0" class="fs12n">閨ｷ讌ｭ</td>
								<td bgcolor="#ffffff">
									<span class="red"><!--{$arrErr.job}--></span>
									<select name="job">
									<option value="" selected>驕ｸ謚槭＠縺ｦ縺上□縺輔＞</option>
									<!--{html_options options=$arrJob selected=$arrForm.job}-->
									</select>
								</td>
							</tr>
							<tr>
								<td bgcolor="#f0f0f0" class="fs12n">逕溷ｹｴ譛域律</td>
								<td bgcolor="#ffffff" class="fs12n"><span class="red"><!--{$arrErr.year}--><!--{$arrErr.month}--><!--{$arrErr.day}--></span>
									<select name="year" style="<!--{$arrErr.year|sfGetErrorColor}-->">
									<option value="" selected>--</option>
									<!--{html_options options=$arrYear selected=$arrForm.year}-->
									</select>&nbsp;蟷ｴ
									<select name="month" style="<!--{$arrErr.month|sfGetErrorColor}-->"><span class="red"><!--{$arrErr.month}--></span>
									<option value="" selected>--</option>
									<!--{html_options options=$arrMonth selected=$arrForm.month}-->
									</select>&nbsp;譛�
									<select name="day" style="<!--{$arrErr.day|sfGetErrorColor}-->"><span class="red"><!--{$arrErr.day}--></span>
									<option value="" selected>--</option>
									<!--{html_options options=$arrDay selected=$arrForm.day}-->
									</select>&nbsp;譌･
								</td>
							</tr>

							<tr>
								<td bgcolor="#f0f0f0"><span class="fs12">蟶梧悍縺吶ｋ繝代せ繝ｯ繝ｼ繝�span class="red">窶ｻ</span></span><br>
								<span class="fs10">繝代せ繝ｯ繝ｼ繝峨�雉ｼ蜈･譎ゅ↓蠢�ｦ√〒縺�/span></td>
								<td bgcolor="#ffffff">
								<table cellspacing="0" cellpadding="0" summary=" ">
									<tr>
										<td class="fs12n"><span class="red"><!--{$arrErr.password}--></span>
										<input type="password" name="password" value="<!--{$arrForm.password}-->" maxlength="<!--{$smarty.const.PASSWORD_LEN2}-->" style="<!--{$arrErr.password|sfGetErrorColor}-->" size=15 class="box15" /></td>
									</tr>
									<tr><td height="2"></td></tr>
									<tr>
										<td class="fs10n"><span class="red">蜊願ｧ定恭謨ｰ蟄�縲�0譁�ｭ励〒縺企｡倥＞縺励∪縺吶��郁ｨ伜捷荳榊庄��/span></td>
									</tr>
									<tr><td height="5"></td></tr>
									<tr>
										<td class="fs12n"><span class="red"><!--{$arrErr.password02}--></span>
									  	<input type="password" name="password02" value="<!--{$arrForm.password02}-->" maxlength="<!--{$smarty.const.PASSWORD_LEN2}-->" style="<!--{$arrErr.password02|sfGetErrorColor}-->" size=15 class="box15" /></td>
									</tr>
									<tr><td height="2"></td></tr>
									<tr>
										<td class="fs10n"><span class="red">遒ｺ隱阪�縺溘ａ縺ｫ2蠎ｦ蜈･蜉帙＠縺ｦ縺上□縺輔＞縲�/span></td>
									</tr>
								</table>
								</td>
							</tr>
							<tr>
								<td bgcolor="#f0f0f0" class="fs12">繝代せ繝ｯ繝ｼ繝峨ｒ蠢倥ｌ縺滓凾縺ｮ繝偵Φ繝�span class="red">窶ｻ</span></td>
								<td bgcolor="#ffffff">
								<table cellspacing="0" cellpadding="0" summary=" ">
									<tr>
										<td class="fs12n"><span class="red"><!--{$arrErr.reminder}--></span><select name="reminder" style="<!--{$arrErr.reminder|sfGetErrorColor}-->">
											<option value="" selected>驕ｸ謚槭＠縺ｦ縺上□縺輔＞</option>
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
								<td bgcolor="#f0f0f0" class="fs12">繝｡繝ｼ繝ｫ繝槭ぎ繧ｸ繝ｳ騾∽ｻ倥↓縺､縺�※<span class="red">窶ｻ</span></td>
								<td bgcolor="#ffffff" class="fs12">
									<span class="red"><!--{$arrErr.mailmaga_flg}--></span>
									<input type="radio" name="mailmaga_flg" value="1" id="html" style="<!--{$arrErr.mailmaga_flg|sfGetErrorColor}-->" <!--{if $arrForm.mailmaga_flg eq 1}--> checked <!--{/if}--> />HTML繝｡繝ｼ繝ｫ�九ユ繧ｭ繧ｹ繝医Γ繝ｼ繝ｫ繧貞女縺大叙繧�/label><br>
									<input type="radio" name="mailmaga_flg" value="2" id="text" style="<!--{$arrErr.mailmaga_flg|sfGetErrorColor}-->" <!--{if $arrForm.mailmaga_flg eq 2}--> checked <!--{/if}--> /><label for="text">繝�く繧ｹ繝医Γ繝ｼ繝ｫ繧貞女縺大叙繧�/label><br>
									<input type="radio" name="mailmaga_flg" value="3" id="no" style="<!--{$arrErr.mailmaga_flg|sfGetErrorColor}-->" <!--{if $arrForm.mailmaga_flg eq 3}--> checked <!--{/if}--> /><label for="no">蜿励¢蜿悶ｉ縺ｪ縺�/label>
								</td>
							</tr>
						</table>
						<!--蜈･蜉帙ヵ繧ｩ繝ｼ繝�％縺薙∪縺ｧ-->
						</td>
					</tr>
					<tr><td height="20"></td></tr>
					<tr>
						<td align="center">
							<input type="image" onmouseover="chgImgImageSubmit('<!--{$smarty.const.URL_DIR}-->img/common/b_confirm_on.gif',this)" onmouseout="chgImgImageSubmit('<!--{$smarty.const.URL_DIR}-->img/common/b_confirm.gif',this)" src="<!--{$smarty.const.URL_DIR}-->img/common/b_confirm.gif" width="150" height="30" alt="遒ｺ隱阪�繝ｼ繧ｸ縺ｸ" name="refusal" id="refusal" />
						</td>
					</tr>
				</form>
				</table>
				</td>
			</tr>
		</table>
		<!--笆ｲMAIN ONTENTS-->
		</td>
	</tr>
</table>
<!--笆ｲCONTENTS-->

