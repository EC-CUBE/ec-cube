<!--{*
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
*}-->
<script type="text/javascript"><!--
function submitRegister() {
	var form = document.form1;
	var msg  = "�ƥ�ץ졼�Ȥ��ѹ����ޤ���\n��¸�Υǡ����Ͼ�񤭤���ޤ���������Ǥ�����";

	if (window.confirm(msg)) {
		form['mode'].value = 'register';
		form.submit();
	}
}
// -->
</script>
<!--�����ᥤ�󥳥�ƥ�ġ���-->
<table width="878" border="0" cellspacing="0" cellpadding="0" summary=" ">
<form name="form1" method="post" action="">
<input type="hidden" name="mode" value="">
<input type="hidden" name="template_code_delete" value="">
<input type="hidden" name="uniqid" value="<!--{$uniqid}-->">
	<tr valign="top">
		<td background="<!--{$smarty.const.URL_DIR}-->img/contents/navi_bg.gif" height="402">
			<!--��SUB NAVI-->
			<!--{include file=$tpl_subnavi}-->
			<!--��SUB NAVI-->
		</td>
		<td class="mainbg">
			<!--����Ͽ�ơ��֥뤳������-->
			<table width="737" border="0" cellspacing="0" cellpadding="0" summary=" ">
				<!--�ᥤ�󥨥ꥢ-->
				<tr>
					<td align="center">
						<table width="706" border="0" cellspacing="0" cellpadding="0" summary=" ">
							<tr><td height="14"></td></tr>
							<tr>
								<td colspan="3"><img src="<!--{$smarty.const.URL_DIR}-->img/contents/main_top.jpg" width="706" height="14" alt=""></td>
							</tr>
							<tr>
								<td background="<!--{$smarty.const.URL_DIR}-->img/contents/main_left.jpg"><img src="<!--{$smarty.const.URL_DIR}-->img/common/_.gif" width="14" height="1" alt=""></td>
								<td bgcolor="#cccccc">
								<table width="678" border="0" cellspacing="0" cellpadding="0" summary=" ">
									<tr>
										<td colspan="3"><img src="<!--{$smarty.const.URL_DIR}-->img/contents/contents_title_top.gif" width="678" height="7" alt=""></td>
									</tr>
									<tr>
										<td background="<!--{$smarty.const.URL_DIR}-->img/contents/contents_title_left_bg.gif"><img src="<!--{$smarty.const.URL_DIR}-->img/contents/contents_title_left.gif" width="22" height="12" alt=""></td>
										<td bgcolor="#636469" width="638" class="fs14n"><span class="white"><!--����ƥ�ĥ����ȥ�-->�ƥ�ץ졼������</span></td>
										<td background="<!--{$smarty.const.URL_DIR}-->img/contents/contents_title_right_bg.gif"><img src="<!--{$smarty.const.URL_DIR}-->img/common/_.gif" width="18" height="1" alt=""></td>
									</tr>
									<tr>
										<td colspan="3"><img src="<!--{$smarty.const.URL_DIR}-->img/contents/contents_title_bottom.gif" width="678" height="7" alt=""></td>
									</tr>
									<tr>
										<td colspan="3"><img src="<!--{$smarty.const.URL_DIR}-->img/contents/main_bar.jpg" width="678" height="10" alt=""></td>
									</tr>
								</table>
								
								<table width="678" border="0" cellspacing="1" cellpadding="8" summary=" ">
								<tr bgcolor="#f2f1ec" class="fs12n">
									<td>
										�ƥ�ץ졼�Ȥ����򤷡��֤������Ƥ���Ͽ����ץܥ���򲡤��ȡ�<br>
										���򤷤��ƥ�ץ졼�Ȥإǥ�������ѹ����뤳�Ȥ�����ޤ���<br>
										<br>
										�ѹ�������硢���Υե����뤬��񤭤���ޤ���
										<ul>
											<li>user_data/css/contents.css</li>
											<li>user_data/include/*</li>
											<li>user_data/templates/mypage/*</li>
											<li>user_data/templates/detail.tpl</li>
											<li>user_data/templates/list.tpl</li>
											<li>user_data/templates/top.tpl</li>
										</ul>
									</td>
								</tr>
								<tr bgcolor="#ffffff" class="fs12n">
									<td>
										<table width="650" bgcolor="#cccccc" border="0" cellspacing="1" cellpadding="5" summary=" ">
											<tr bgcolor="#f2f1ec" align="center" class="fs12n">
												<td width="">����</td>
												<td width="">̾��</td>
												<td width="">��¸��</td>
												<!--<td width="50">��ǧ</td>-->
												<td width="50">���</td>
											</tr>
											<tr bgcolor="#ffffff" align="center" class="fs12n">
												<td width=""><input type="radio" name="template_code" value="default" <!--{if !$tplcode}-->checked<!--{/if}-->></td>
												<td width="" colspan="3">�ƥ�ץ졼�Ȥ���Ѥ��ʤ�</td>
											</tr>
											<!--{foreach from=$templates item=tpl}-->
											<!--{assign var=tplcode value=$tpl.template_code}-->
											<tr bgcolor="#ffffff" align="center" class="fs12">
												<td width="" ><input type="radio" name="template_code" value="<!--{$tplcode|escape}-->" <!--{if $tplcode == $now_template}-->checked<!--{/if}-->></td>
												<td width=""><!--{$tpl.template_name|escape}--></td>
												<td width="">user_data/tpl_packages/<!--{$tplcode|escape}-->/</td>
												<!--<td width=""><span class="icon_confirm"><a href="" onClick="">��ǧ</span></a></td>-->
												<td width=""><span class="icon_delete"><a href="" onClick="fnModeSubmit('delete','template_code_delete','<!--{$tplcode}-->');return false;">���</a></span></td>
											</tr>
											<!--{/foreach}-->

										</table>
									</td>
								</tr>
								</table>
								
								<table width="678" border="0" cellspacing="0" cellpadding="0" summary=" ">
									<tr>
										<td bgcolor="#cccccc"><img src="<!--{$smarty.const.URL_DIR}-->img/common/_.gif" width="1" height="5" alt=""></td>
										<td><img src="<!--{$smarty.const.URL_DIR}-->img/contents/tbl_top.gif" width="676" height="7" alt=""></td>
										<td bgcolor="#cccccc"><img src="<!--{$smarty.const.URL_DIR}-->img/common/_.gif" width="1" height="5" alt=""></td>
									</tr>
									<tr>
										<td bgcolor="#cccccc"><img src="<!--{$smarty.const.URL_DIR}-->img/common/_.gif" width="1" height="10" alt=""></td>
										<td bgcolor="#e9e7de" align="center">
										<table border="0" cellspacing="0" cellpadding="0" summary=" ">
											<tr>
												<td>
													<a href="" onClick="submitRegister();return false;">
													<img onMouseover="chgImgImageSubmit('<!--{$smarty.const.URL_DIR}-->img/contents/btn_regist_on.jpg',this)" onMouseout="chgImgImageSubmit('<!--{$smarty.const.URL_DIR}-->img/contents/btn_regist.jpg',this)" src="<!--{$smarty.const.URL_DIR}-->img/contents/btn_regist.jpg" width="123" height="24" alt="�������Ƥ���Ͽ����" border="0" name="subm">
													</a>
												</td>
											</tr>
										</table>
										</td>
										<td bgcolor="#cccccc"><img src="<!--{$smarty.const.URL_DIR}-->img/common/_.gif" width="1" height="10" alt=""></td>
									</tr>
									<tr>
										<td colspan="3"><img src="<!--{$smarty.const.URL_DIR}-->img/contents/tbl_bottom.gif" width="678" height="8" alt=""></td>
									</tr>
								</table>
								</td>
								<td background="<!--{$smarty.const.URL_DIR}-->img/contents/main_right.jpg"><img src="<!--{$smarty.const.URL_DIR}-->img/common/_.gif" width="14" height="1" alt=""></td>
							</tr>
							<tr>
								<td colspan="3"><img src="<!--{$smarty.const.URL_DIR}-->img/contents/main_bottom.jpg" width="706" height="14" alt=""></td>
							</tr>
							<tr><td height="30"></td></tr>
						</table>
					</td>
				</tr>
				<!--�ᥤ�󥨥ꥢ-->
			</table>
			<!--����Ͽ�ơ��֥뤳���ޤ�-->
		</td>
	</tr>
</form>
</table>
<!--�����ᥤ�󥳥�ƥ�ġ���-->