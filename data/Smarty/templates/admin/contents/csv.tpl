<!--{*
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
*}-->
<script type="text/javascript">
<!--
function fnMoveOption(sel , moveflg) {
	var fm = document.form1;
	var arrChoice = new Array();	// ���򤵤�Ƥ������
	var arrNotChoice = new Array();	// ���򤵤�Ƥ��ʤ�����
	var arrNew = new Array();		// ��ư��Υꥹ��
	var arrTmp = new Array();
	var arrRev = new Array();
	
	if(fm[sel].selectedIndex == -1) alert("�������򤵤�Ƥ��ޤ���");
	else {
		// ���˰�ư������ˤϤޤ�OPTION��դˤ���
		if (moveflg == 'bottom') {
			for(i=fm[sel].length-1, j=0; i >= 0; i--, j++){
				fm[sel].options[i].label=i;		// ���֤�label������
				arrRev[j] = fm[sel].options[i];
			}
			for(i=0; i < arrRev.length; i++){
				fm[sel].options[i] = new Option(arrRev[i].text, arrRev[i].value);
				fm[sel].options[i].selected = arrRev[i].selected;
			}
		}

		// ���ֲ��˶�����ɲ�
		fm[sel].options[fm[sel].length] = new Option('', '');
		
		for(i = 0, choiceCnt = 0, notCnt = 0; i < fm[sel].length; i++) {
			if(!fm[sel].options[i].selected) {
				// ���򤵤�Ƥ��ʤ��������������
				fm[sel].options[i].label=i;		// ���֤�label������
				arrNotChoice[choiceCnt] = fm[sel].options[i];
				choiceCnt++;
			}else{
				// ���򤵤�Ƥ���������������
				fm[sel].options[i].label=i;		// ���֤�label������
				arrChoice[notCnt] = fm[sel].options[i];
				notCnt++;
			}
		}
		
		// ������ܤ��˰�ư
		for(i = arrChoice.length; i < 1; i--){
			arrChoice[i].label = arrChoice[i-1].label+1;
		}

		// ��������ܤ򲼤˰�ư
		for(i = 0; i < arrNotChoice.length - 1; i++){
			arrNotChoice[i].label = arrNotChoice[i+1].label-1;
		}	

		// ������ܤ���������ܤ�ޡ�������
		for(choiceCnt = 0, notCnt = 0, cnt = 0; cnt < fm[sel].length; cnt++){
			if (choiceCnt >= arrChoice.length) {
				arrNew[cnt] = arrNotChoice[notCnt];
				notCnt++;
			}else if (notCnt >= arrNotChoice.length) {
				arrNew[cnt] = arrChoice[choiceCnt];
				choiceCnt++;
			}else{
				if(arrChoice[choiceCnt].label-1 <= arrNotChoice[notCnt].label){
					arrNew[cnt] = arrChoice[choiceCnt];
					choiceCnt++;
				}else{
					arrNew[cnt] = arrNotChoice[notCnt];
					notCnt++;
				}
			}
		}

		// ���˰�ư������ˤϵդˤ�����Τ򸵤��᤹
		if (moveflg == 'bottom') {
			for(i=arrNew.length-2, j=0; i >= 0; i--, j++){
				arrTmp[j] = arrNew[i];
			}
			arrTmp[j]="";
			arrNew = arrTmp;
		}

		// option��ƺ���
		fm[sel].length = arrNew.length - 1;
		for(i=0; i < arrNew.length - 1; i++){
			fm[sel].options[i] = new Option(arrNew[i].text, arrNew[i].value);
			fm[sel].options[i].selected = arrNew[i].selected;
		}
	}
}

function fnReplaceOption(restSel, addSel) {
	var fm = document.form1;
	var arrRest = new Array();	// �Ĥ�Υꥹ��
	var arrAdd	= new Array();	// �ɲäΥꥹ��
	
	if(fm[restSel].selectedIndex == -1) alert("�������򤵤�Ƥ��ޤ���");
	else {
		for(i = 0, restCnt = 0, addCnt = 0; i < fm[restSel].length; i++) {
			if(!fm[restSel].options[i].selected) {
				// �����Ǥ����������
				arrRest[restCnt] = fm[restSel].options[i];
				restCnt++;
			}else{
				// �ɲ����Ǥ����������
				arrAdd[addCnt] = fm[restSel].options[i];
				addCnt++;
			}
		}

		// �ĥꥹ������
		fm[restSel].length = arrRest.length;
		for(i=0; i < arrRest.length; i++)
		{
			fm[restSel].options[i] = new Option(arrRest[i].text, arrRest[i].value);
		}

		// �ɲ���˹��ܤ��ɲ�
		//fm[addSel].options[fm[addSel].length] = new Option(fm[sel2].value, fm[sel2].value);
		
		for(i=0; i < arrAdd.length; i++)
		{
			fm[addSel].options[fm[addSel].length] = new Option(arrAdd[i].text, arrAdd[i].value);
			fm[addSel].options[fm[addSel].length-1].selected = true;
		}
	}
}

// submit�������ˡ����Ϲ��ܰ�����������֤ˤ���
function lfnCheckList(sel) {
	var fm = document.form1;
	for(i = 0; i < fm[sel].length; i++) {
		fm[sel].options[i].selected = true;
	}
}

// �ꥹ�ȥܥå����Υ������ѹ�
function ChangeSize(button, TextArea, Max, Min, row_tmp){
	if(TextArea.rows <= Min){
		TextArea.rows=Max; button.value="����������"; row_tmp.value=Max;
	}else{
		TextArea.rows =Min; button.value="�礭������"; row_tmp.value=Min;
	}
}

//-->
</script>


<!--�����ᥤ�󥳥�ƥ�ġ���-->
<table width="878" border="0" cellspacing="0" cellpadding="0" summary=" ">
<form name="form1" id="form1" method="post" action="<!--{$smarty.server.PHP_SELF|escape}-->" onsubmit="lfnCheckList('output_list[]')">
<input type="hidden" name="mode" value="confirm">
<input type="hidden" name="tpl_subno_csv" value="<!--{$tpl_subno_csv}-->">
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
											<td bgcolor="#636469" width="638" class="fs14n"><span class="white"><!--����ƥ�ĥ����ȥ�--><!--{$SubnaviName}--></span></td>
											<td background="<!--{$smarty.const.URL_DIR}-->img/contents/contents_title_right_bg.gif"><img src="<!--{$smarty.const.URL_DIR}-->img/common/_.gif" width="18" height="1" alt=""></td>
										</tr>
										<tr>
											<td colspan="3"><img src="<!--{$smarty.const.URL_DIR}-->img/contents/contents_title_bottom.gif" width="678" height="7" alt=""></td>
										</tr>
										<tr>
											<td colspan="3"><img src="<!--{$smarty.const.URL_DIR}-->img/contents/main_bar.jpg" width="678" height="10" alt=""></td>
										</tr>
									</table>
									
									<table width="678" border="0" cellspacing="1" cellpadding="0" summary=" "><tr><td>
									<table width="676" border="0" cellspacing="" cellpadding="3" summary=" ">
										<tr class="fs12n">
											<td bgcolor="#ffffff" align="right">
												<input type="button" value=" �� " onClick="fnMoveOption('output_list[]', 'top');"><br/><br/><br/>
												<input type="button" value=" �� " onClick="fnMoveOption('output_list[]', 'bottom');">
											</td>
											<td bgcolor="#ffffff" align="left">
												<table width="270" border="1" cellspacing="0" cellpadding="3" summary=" ">
													<tr class="fs12n">
														<td bgcolor="#f2f1ec" align="center"><strong>���Ϲ��ܰ���</strong></td>
													</tr>
													<tr class="fs12n">
														<td bgcolor="#ffffff" align="center">
															<span class="red12"><!--{$arrErr.output_list}--></span>
															<select multiple name="output_list[]" size="30" style="<!--{$arrErr.output_list|sfGetErrorColor}-->; width:250px; height:425px;">
															<!--{html_options options=$arrOutput}-->
															</select>
														</td>
													</tr>
												</table>
											</td>
											<td bgcolor="#ffffff" align="cneter">
												<input type="button" value="<< �ɲ�" onClick="fnReplaceOption('choice_list[]', 'output_list[]');"><br/><br/><br/>
												<input type="button" value="��� >>" onClick="fnReplaceOption('output_list[]', 'choice_list[]');">
											</td>
											<td bgcolor="#ffffff" align="right">
												<table width="270" border="1" cellspacing="0" cellpadding="3" summary=" ">
													<tr class="fs12n">
														<td bgcolor="#f2f1ec" align="center"><strong>���ϲ�ǽ���ܰ���</strong></td>
													</tr>
													<tr class="fs12n">
														<td bgcolor="#ffffff" align="center">
															<select multiple name="choice_list[]" size="30" style="width:250px; height:425px;">
															<!--{html_options options=$arrChoice}-->
															</select>
														</td>
													</tr>
												</table>
											</td>
										</tr>
									</table>
									</td></tr></table>
	
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
													<td><input type="image" onMouseover="chgImgImageSubmit('<!--{$smarty.const.URL_DIR}-->img/contents/btn_regist_on.jpg',this)" onMouseout="chgImgImageSubmit('<!--{$smarty.const.URL_DIR}-->img/contents/btn_regist.jpg',this)" src="<!--{$smarty.const.URL_DIR}-->img/contents/btn_regist.jpg" width="123" height="24" alt="�������Ƥ���Ͽ����" border="0" name="subm"></td>
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


</script>
