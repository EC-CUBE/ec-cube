<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html lang="ja">
<head>
<!--{*
 * Copyright (c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *}-->
<meta http-equiv="Content-Type" content="text/html; charset=<!--{$smarty.const.CHAR_CODE}-->">
<meta http-equiv="content-script-type" content="text/javascript">
<meta http-equiv="content-style-type" content="text/css">
<link rel="stylesheet" href="<!--{$smarty.const.URL_DIR}-->user_data/css/contents.css" type="text/css" media="all" />
<link rel="stylesheet" href="<!--{$smarty.const.URL_DIR}-->css/common.css" type="text/css" media="all" />
<link rel="stylesheet" href="<!--{$tpl_css}-->" type="text/css" media="all" />
<script type="text/javascript" src="<!--{$smarty.const.URL_DIR}-->js/css.js"></script>
<script type="text/javascript" src="<!--{$smarty.const.URL_DIR}-->js/navi.js"></script>
<script type="text/javascript" src="<!--{$smarty.const.URL_DIR}-->js/win_op.js"></script>
<script type="text/javascript" src="<!--{$smarty.const.URL_DIR}-->js/site.js"></script>
<title><!--{$arrSiteInfo.shop_name}-->/<!--{$tpl_title|escape}--></title>
</head>

<body bgcolor="#ffe9e6" text="#555555" link="#3a75af" vlink="#3a75af" alink="#3a75af" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" onLoad="preLoadImg('<!--{$smarty.const.URL_DIR}-->'); <!--{$tpl_onload}-->">
<noscript>
<link rel="stylesheet" href="<!--{$smarty.const.URL_DIR}-->css/common.css" type="text/css">
</noscript>
<div align="center">
<a name="top" id="top"></a>

<!--��CONTENTS-->
<table width="550" border="0" cellspacing="0" cellpadding="0" summary=" ">
	<tr><td height="15"></td></tr>
	<tr><td bgcolor="#ffa85c"><img src="<!--{$smarty.const.URL_DIR}-->misc/_.gif" width="1" height="5" alt=""></td></tr>
	<tr>
		<td align="center" bgcolor="#ffffff">

		<!--�����ϥե����ळ������-->
		<table width="500" border="0" cellspacing="0" cellpadding="0" summary=" ">
		<form name="form1" id="form1" method="post" action="<!--{$smarty.server.PHP_SELF|escape}-->">
		<input type="hidden" name="mode" value="edit">
		<input type="hidden" name="other_deliv_id" value="<!--{$smarty.session.other_deliv_id}-->" >
		<input type="hidden" name="ParentPage" value="<!--{$ParentPage}-->" >

			<tr><td height="15"></td></tr>
			<tr>
				<td><img src="<!--{$smarty.const.URL_DIR}-->img/shopping/delivadd_title.jpg" width="500" height="40" alt="���������Ϥ�����ɲá��ѹ�"></td>
			</tr>
			<tr><td height="15"></td></tr>
			<tr>
				<td class="fs12">�������ܤˤ����Ϥ�����������<span class="red">��</span>�װ�������ɬ�ܹ��ܤǤ���<br>
				���ϸ塢���ֲ��Ρֳ�ǧ�ڡ����ءץܥ���򥯥�å����Ƥ���������</td>
			</tr>
			<tr><td height="15"></td></tr>
			<tr>
				<td bgcolor="#cccccc">
				<!--���ϥե����ळ������-->
				<table width="500" border="0" cellspacing="1" cellpadding="10" summary=" ">
					<tr>
						<td width="117" bgcolor="#f0f0f0" class="fs12">��̾��<span class="red">��</span></td>
						<td width="340" bgcolor="#ffffff" class="fs12n">
							<span class="red"><!--{$arrErr.name01}--><!--{$arrErr.name02}--></span>
							��&nbsp;<input type="text" name="name01" value="<!--{if $name01 == ""}--><!--{$arrOtherDeliv.name01|escape}--><!--{else}--><!--{$name01|escape}--><!--{/if}-->" maxlength="<!--{$smarty.const.STEXT_LEN}-->" style="<!--{$arrErr.name01|sfGetErrorColor}-->" size=15 class="box15" />��
							̾&nbsp;<input type="text" name="name02" value="<!--{if $name02 == ""}--><!--{$arrOtherDeliv.name02|escape}--><!--{else}--><!--{$name02|escape}--><!--{/if}-->" maxlength="<!--{$smarty.const.STEXT_LEN}-->" style="<!--{$arrErr.name02|sfGetErrorColor}-->" size=15 class="box15" />
						</td>
					</tr>
					<tr>
						<td bgcolor="#f0f0f0" class="fs12">��̾���ʥեꥬ�ʡ�<span class="red">��</span></td>
						<td bgcolor="#ffffff" class="fs12n"><span class="red"><!--{$arrErr.kana01}--><!--{$arrErr.kana02}--></span>
							����&nbsp;<input type="text" name="kana01" value="<!--{if $kana01 == ""}--><!--{$arrOtherDeliv.kana01|escape}--><!--{else}--><!--{$kana01|escape}--><!--{/if}-->" maxlength="<!--{$smarty.const.STEXT_LEN}-->" style="<!--{$arrErr.kana01|sfGetErrorColor}-->" size=15 class="box15" />���ᥤ&nbsp;<input type="text" name="kana02" value="<!--{if $kana02 == ""}--><!--{$arrOtherDeliv.kana02|escape}--><!--{else}--><!--{$kana02|escape}--><!--{/if}-->" maxlength="<!--{$smarty.const.STEXT_LEN}-->" style="<!--{$arrErr.kana02|sfGetErrorColor}-->" size=15 class="box15" /></td>
					</tr>
					<tr>
						<td bgcolor="#f0f0f0" class="fs12">͹���ֹ�<span class="red">��</span></td>
						<td bgcolor="#ffffff">
							<table cellspacing="0" cellpadding="0" summary=" ">
								<tr>
									<td class="fs12n">
										<!--{assign var=key1 value="zip01"}-->
										<!--{assign var=key2 value="zip02"}-->
										<span class="red"><!--{$arrErr[$key1]}--><!--{$arrErr[$key2]}--></span>
										��&nbsp;<input type="text" name="zip01" value="<!--{if $zip01 == ""}--><!--{$arrOtherDeliv.zip01|escape}--><!--{else}--><!--{$zip01|escape}--><!--{/if}-->" maxlength="<!--{$smarty.const.ZIP01_LEN}-->" style="<!--{$arrErr[$key1]|sfGetErrorColor}-->; ime-mode: disabled;" size=6 class="box6" />&nbsp;-&nbsp;<input type="text" name="zip02" value="<!--{if $zip02 == ""}--><!--{$arrOtherDeliv.zip02|escape}--><!--{else}--><!--{$zip02|escape}--><!--{/if}-->" maxlength="<!--{$smarty.const.ZIP02_LEN}-->" style="<!--{$arrErr[$key2]|sfGetErrorColor}-->; ime-mode: disabled;" size=6 class="box6" />
									</td>
									<td>
										&nbsp;&nbsp;<a href="../address/index.php" onclick="fnCallAddress('<!--{$smarty.const.URL_INPUT_ZIP}-->', 'zip01', 'zip02', 'pref', 'addr01'); return false;" target="_blank"><img src="<!--{$smarty.const.URL_DIR}-->img/common/address.gif" width="86" height="20" alt="���꼫ư����" /></a></td>
									</td>
								</tr>
								<tr><td height="5"></td></tr>
								<tr>
									<td colspan="2" class="fs12">͹���ֹ椬�狼��ʤ����Ϣ�<a href="http://search.post.japanpost.jp/zipcode/" target="_blank">������</a></td>
								</tr>
							</table>
						</td>
					</tr>
					<tr>
						<td bgcolor="#f0f0f0" class="fs12">����<span class="red">��</span></td>
						<td bgcolor="#ffffff">
							<table cellspacing="0" cellpadding="0" summary=" " id="frame02">
								<tr>
									<td class="fs12n"><span class="red"><!--{$arrErr.pref}--></span>
									<select name="pref" style="<!--{$arrErr.pref|sfGetErrorColor}-->">
									<option value="" selected>���򤷤Ƥ�������</option>
									<!--{if $pref == ""}-->
									<!--{html_options options=$arrPref selected=$arrOtherDeliv.pref|escape}-->
									<!--{else}-->
									<!--{html_options options=$arrPref selected=$pref|escape}-->
									<!--{/if}-->
									</select></td>
								</tr>
								<tr><td height="5"></td></tr>
								<tr>
									<td class="fs12n"><span class="red"><!--{$arrErr.addr01}--></span>
									<input type="text" name="addr01" value="<!--{if $addr01 == ""}--><!--{$arrOtherDeliv.addr01|escape}--><!--{else}--><!--{$addr01|escape}--><!--{/if}-->" maxlength="<!--{$smarty.const.MTEXT_LEN}-->" style="<!--{$arrErr.addr01|sfGetErrorColor}-->" size=40 class="box40" /></td>
								</tr>
								<tr><td height="2"></td></tr>
								<tr>
									<td class="fs10n"><!--{$smarty.const.SAMPLE_ADDRESS1}--></td>
								</tr>
								<tr><td height="5"></td></tr>
								<tr>
									<td class="fs12n"><span class="red"><!--{$arrErr.addr02}--></span>
									<input type="text" name="addr02" value="<!--{if $addr02 == ""}--><!--{$arrOtherDeliv.addr02|escape}--><!--{else}--><!--{$addr02|escape}--><!--{/if}-->" maxlength="<!--{$smarty.const.MTEXT_LEN}-->" style="<!--{$arrErr.addr02|sfGetErrorColor}-->" size=40 class="box40" /></td>
								</tr>
								<tr><td height="2"></td></tr>
								<tr>
									<td class="fs10"><!--{$smarty.const.SAMPLE_ADDRESS2}--></td>
								</tr>
							</table>
						</td>
					</tr>
					<tr>
						<td bgcolor="#f0f0f0" class="fs12n">�����ֹ�<span class="red">��</span></td>
						<td bgcolor="#ffffff" class="fs12n"><span class="red"><!--{$arrErr.tel01}--><!--{$arrErr.tel02}--><!--{$arrErr.tel03}--></span>
							<input type="text" name="tel01" value="<!--{if $tel01 == ""}--><!--{$arrOtherDeliv.tel01|escape}--><!--{else}--><!--{$tel01|escape}--><!--{/if}-->" maxlength="<!--{$smarty.const.TEL_ITEM_LEN}-->" style="<!--{$arrErr.tel01|sfGetErrorColor}-->" size=6 class="box6" />&nbsp;-&nbsp;<input type="text" name="tel02" value="<!--{if $tel02 == ""}--><!--{$arrOtherDeliv.tel02|escape}--><!--{else}--><!--{$tel02|escape}--><!--{/if}-->" maxlength="<!--{$smarty.const.TEL_ITEM_LEN}-->" style="<!--{$arrErr.tel02|sfGetErrorColor}-->" size=6 class="box6" />&nbsp;-&nbsp;<input type="text" name="tel03" value="<!--{if $tel03 == ""}--><!--{$arrOtherDeliv.tel03|escape}--><!--{else}--><!--{$tel03|escape}--><!--{/if}-->" maxlength="<!--{$smarty.const.TEL_ITEM_LEN}-->" style="<!--{$arrErr.tel03|sfGetErrorColor}-->" size=6 class="box6" /></td>
					</tr>
				</table>
				<!--���ϥե����ळ���ޤ�-->
				</td>
			</tr>
			<tr><td height="15"></td></tr>
			<tr>
				<td align="center">
					<input type="image" onmouseover="chgImgImageSubmit('<!--{$smarty.const.URL_DIR}-->img/common/b_entry_on.gif',this);" onmouseout="chgImgImageSubmit('<!--{$smarty.const.URL_DIR}-->img/common/b_entry.gif',this);" src="<!--{$smarty.const.URL_DIR}-->img/common/b_entry.gif" width="150" height="30" alt="��Ͽ����" name="register" id="register" />
				</td>
			</tr>
			<tr><td height="30"></td></tr>
		</form>
		</table>
		</td>
	</tr>
	<tr><td bgcolor="#ffa85c"><img src="<!--{$smarty.const.URL_DIR}-->misc/_.gif" width="1" height="5" alt=""></td></tr>
	<tr><td height="20"></td></tr>
</table>
</div>
</body>
</html>



