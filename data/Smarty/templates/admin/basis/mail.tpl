<!--{*
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
*}-->
<script language="JavaScript">
<!--
var flag = 0;

function setFlag(){
	flag = 1;
}

function checkFlagAndSubmit(){
	if ( flag == 1 ){
		if( confirm('���Ƥ��ѹ�����Ƥ��ޤ���³�Ԥ�����ѹ����Ƥ��˴�����ޤ���\n�������Ǥ��礦����' )){
			fnSetvalAndSubmit( 'form1', 'mode', 'edit' );
		} else {
			return false;
		}
	} else {
		fnSetvalAndSubmit( 'form1', 'mode', 'edit' );
	}
}

function lfnCheckSubmit(){
	
	fm = document.form1;
	var err = '';
	
	if ( ! fm["send_type"][0].checked && ! fm["send_type"][1].checked ){
		err += '�᡼��η��������Ϥ��Ʋ�������';
	}
	if ( ! fm["subject"].value ){
		err += 'Subject�����Ϥ��Ʋ�������';
	}
	if ( ! fm["body"].value ){
		if ( err ) err += '\n';
		err += '�᡼�����ʸ�����Ϥ��Ʋ�������';
	}
	if( ! fm["template_name"]){
		if ( err ) err += '\n';
		err += '�ƥ�ץ졼�Ȥ�̾�������Ϥ��Ʋ�������';
	}
	if ( err ){
		alert(err);
		return false;
	} else {
		if(window.confirm('���Ƥ���Ͽ���Ƥ⵹�����Ǥ���')){
			return true;
		}else{
			return false;
		}
	}
}
//-->
</script>

<!--�����ᥤ�󥳥�ƥ�ġ���-->
<table width="878" border="0" cellspacing="0" cellpadding="0" summary=" ">
<form name="form1" id="form1" method="POST" action="<!--{$smarty.server.PHP_SELF|escape}-->" onsubmit="return lfnCheckSubmit();" >
<input type="hidden" name="mode" value="regist">
<!--{assign var=key value="template_id"}-->
<input type="hidden" name="template_id" value="<!--{$arrForm[$key]|escape}-->">
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
										<td bgcolor="#636469" width="638" class="fs14n"><span class="white"><!--����ƥ�ĥ����ȥ�-->�᡼������</span></td>
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
									<tr class="fs12n">
										<td bgcolor="#f2f1ec">�᡼�����<span class="red"> *</span></td>
										<td bgcolor="#ffffff">
										<!--{assign var=key value="send_type"}-->
										<!--{if $arrForm.template_id == 1}-->
											<input type="radio" name="send_type" value="0" id="send_type_0" checked="checked" /><label for="send_type_0">�ѥ�����</label>&nbsp;
										<!--{elseif $arrForm.template_id == 2}-->
											<input type="radio" name="send_type" value="1" id="send_type_1" checked="checked" /><label for="send_type_1">����</label>&nbsp;
										<!--{else}-->
											<!--{html_radios_ex name="send_type" options=$arrSendType separator="&nbsp;" selected=$arrForm[$key]}-->
										<!--{/if}-->
										</td>
									</tr>
									<tr>
										<td bgcolor="#f2f1ec" width="160" class="fs12n">�ƥ�ץ졼��<span class="red"> *</span></td>
										<td bgcolor="#ffffff" width="557" class="fs10n">
										<!--{assign var=key value="template_name"}-->
										<span class="red12"><!--{$arrErr[$key]}--></span>
										<input type="text" name="template_name" value="<!--{$arrForm[$key]|escape}-->" onChange="setFlag();" size="30" class="box30" style="<!--{$arrErr[$key]|sfGetErrorColor}-->">
										</td>
									</tr>
									<tr>
										<td bgcolor="#f2f1ec" width="160" class="fs12n">�᡼�륿���ȥ�<span class="red"> *</span></td>
										<td bgcolor="#ffffff" width="557" class="fs10n">
										<!--{assign var=key value="subject"}-->
										<span class="red12"><!--{$arrErr[$key]}--></span>
										<input type="text" name="subject" value="<!--{$arrForm[$key]|escape}-->" onChange="setFlag();" size="30" class="box30" style="<!--{$arrErr[$key]|sfGetErrorColor}-->">
										</td>
									</tr>
									<tr>
										<td bgcolor="#f2f1ec" colspan="2" class="fs12n"><span class="red"> *</span>̾���򺹤�������ϡ�{name}�����Ϥ��Ʋ�������<br>
										<span class="red"> *</span>��ʸ����򺹤�������ϡ�{order}�����Ϥ��Ʋ�������</td>
									</tr>
									<tr>
										<td bgcolor="#f2f1ec" width="160" class="fs12">��ʸ</td>
										<td bgcolor="#ffffff" width="557" class="fs10">
										<!--{assign var=key value="body"}-->
										<span class="red12"><!--{$arrErr[$key]}--></span>
										<textarea name="body" cols="75" rows="20" class="area75" onChange="setFlag();" style="<!--{$arrErr[$key]|sfGetErrorColor}-->"><!--{$arrForm[$key]|escape}--></textarea><br />
										<span class="red"> �ʾ��<!--{$smarty.const.LTEXT_LEN}-->ʸ����
										</span>
						
										<div align="right">
											<input type="button" width="110" height="30" value="ʸ�����������" onclick="fnCharCount('form1','body','cnt_body');" border="0" name="next" id="next" />
											<br>���ޤǤ����Ϥ����Τ�
											<input type="text" name="cnt_body" size="4" class="box4" readonly = true style="text-align:right">
											ʸ���Ǥ���
										</div>
						
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
												<td><input type="image" onMouseover="chgImgImageSubmit('<!--{$smarty.const.URL_DIR}-->img/contents/btn_regist_on.jpg',this)" onMouseout="chgImgImageSubmit('<!--{$smarty.const.URL_DIR}-->img/contents/btn_regist.jpg',this)" src="<!--{$smarty.const.URL_DIR}-->img/contents/btn_regist.jpg" width="123" height="24" alt="�������Ƥ���Ͽ����" border="0" name="subm" ></td>
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

