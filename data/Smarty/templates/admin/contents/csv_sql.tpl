<!--{*
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
*}-->
<script type="text/javascript">
<!--
// �ꥹ�ȥܥå����Υ������ѹ�
function ChangeSize(button, TextArea, Max, Min, row_tmp){
	if(TextArea.rows <= Min){
		TextArea.rows=Max; button.value="����������"; row_tmp.value=Max;
	}else{
		TextArea.rows =Min; button.value="�礭������"; row_tmp.value=Min;
	}
}

// SQL��ǧ���̵�ư
function doPreview(){
	document.form1.mode.value="preview"
	document.form1.target = "_blank";
	document.form1.submit();
}

// form�Υ������åȤ�ʬ���᤹
function fnTargetSelf(){
	document.form1.target = "_self";
}

//-->
</script>

<!--�����ᥤ�󥳥�ƥ�ġ���-->
<table width="878" border="0" cellspacing="0" cellpadding="0" summary=" ">
<form name="form1" id="form1" method="post" action="<!--{$smarty.server.PHP_SELF|escape}-->">
<input type="hidden" name="mode" value="confirm">
<input type="hidden" name="sql_id" value="<!--{$sql_id}-->">
<input type="hidden" name="csv_output_id" value="">
<input type="hidden" name="selectTable" value="">
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

									<!-- SQL���� �������� -->
									<table width="678" border="0" cellspacing="0" cellpadding="0" summary=" ">
										<tr>
											<td colspan="3"><img src="<!--{$smarty.const.URL_DIR}-->img/contents/contents_title_top.gif" width="678" height="7" alt=""></td>
										</tr>
										<tr>
											<td background="<!--{$smarty.const.URL_DIR}-->img/contents/contents_title_left_bg.gif"><img src="<!--{$smarty.const.URL_DIR}-->img/contents/contents_title_left.gif" width="22" height="12" alt=""></td>
											<td bgcolor="#636469" width="638" class="fs14n"><span class="white"><!--����ƥ�ĥ����ȥ�-->SQL����</span></td>
											<td background="<!--{$smarty.const.URL_DIR}-->img/contents/contents_title_right_bg.gif"><img src="<!--{$smarty.const.URL_DIR}-->img/common/_.gif" width="18" height="1" alt=""></td>
										</tr>
										<tr>
											<td colspan="3"><img src="<!--{$smarty.const.URL_DIR}-->img/contents/contents_title_bottom.gif" width="678" height="7" alt=""></td>
										</tr>
										<tr>
											<td colspan="3"><img src="<!--{$smarty.const.URL_DIR}-->img/contents/main_bar.jpg" width="678" height="10" alt=""></td>
										</tr>
									</table>

									<table width="678" border="0" cellspacing="1" cellpadding="5" summary=" ">
										<tr class="fs12n">
											<td bgcolor="#f2f1ec" align="center" colspan=3 ><strong>SQL����</strong></td>
										</tr>

										<!--{ foreach key=key item=item from=$arrSqlList }-->
										<tr class="fs12n" height=20>
											<td align="center" width=600 bgcolor="<!--{if $item.sql_id == $sql_id}--><!--{$smarty.const.SELECT_RGB}--><!--{else}-->#ffffff<!--{/if}-->">
												<a href="<!--{$smarty.server.PHP_SELF|escape}-->?sql_id=<!--{$item.sql_id}-->" ><!--{$item.sql_name}--></a>
											</td>
											<td align="center" width=78 bgcolor="<!--{if $item.sql_id == $sql_id}--><!--{$smarty.const.SELECT_RGB}--><!--{else}-->#ffffff<!--{/if}-->">
												<input type='button' value='CSV����' name='csv' onclick="fnTargetSelf(); fnFormModeSubmit('form1','csv_output','csv_output_id',<!--{$item.sql_id}-->);"  />
											</td>
											<td align="center" width=78 bgcolor="<!--{if $item.sql_id == $sql_id}--><!--{$smarty.const.SELECT_RGB}--><!--{else}-->#ffffff<!--{/if}-->">
												<input type='button' value='���' name='del' onclick="fnTargetSelf(); fnFormModeSubmit('form1','delete','sql_id',<!--{$item.sql_id}-->);"  />
											</td>
										</tr>
										<!--{ /foreach }-->
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
													<td><input type='button' value='����SQL����' name='subm' onclick="fnTargetSelf(); fnFormModeSubmit('form1','new_page','','');"  /></td>
												</tr>
											</table>
											</td>
											<td bgcolor="#cccccc"><img src="<!--{$smarty.const.URL_DIR}-->img/common/_.gif" width="1" height="10" alt=""></td>
										</tr>
										<tr>
											<td colspan="3"><img src="<!--{$smarty.const.URL_DIR}-->img/contents/tbl_bottom.gif" width="678" height="8" alt=""></td>
										</tr>
									</table>									
									<!-- SQL���� �����ޤ� -->

									<table width="678" border="0" cellspacing="0" cellpadding="0" summary=" ">
										<tr><td colspan="3"><img src="<!--{$smarty.const.URL_DIR}-->img/contents/main_bar.jpg" width="678" height="10" alt=""></td></tr>
									</table>

									<!-- SQL���� �������� -->
									<table width="678" border="0" cellspacing="0" cellpadding="0" summary=" ">
										<tr>
											<td colspan="3"><img src="<!--{$smarty.const.URL_DIR}-->img/contents/contents_title_top.gif" width="678" height="7" alt=""></td>
										</tr>
										<tr>
											<td background="<!--{$smarty.const.URL_DIR}-->img/contents/contents_title_left_bg.gif"><img src="<!--{$smarty.const.URL_DIR}-->img/contents/contents_title_left.gif" width="22" height="12" alt=""></td>
											<td bgcolor="#636469" width="638" class="fs14n"><span class="white"><!--����ƥ�ĥ����ȥ�-->CSV��������</span></td>
											<td background="<!--{$smarty.const.URL_DIR}-->img/contents/contents_title_right_bg.gif"><img src="<!--{$smarty.const.URL_DIR}-->img/common/_.gif" width="18" height="1" alt=""></td>
										</tr>
										<tr>
											<td colspan="3"><img src="<!--{$smarty.const.URL_DIR}-->img/contents/contents_title_bottom.gif" width="678" height="7" alt=""></td>
										</tr>
										<tr>
											<td colspan="3"><img src="<!--{$smarty.const.URL_DIR}-->img/contents/main_bar.jpg" width="678" height="10" alt=""></td>
										</tr>
									</table>

									<table width="678" border="0" cellspacing="1" cellpadding="5" summary=" ">
										<tr class="fs12n">
											<td bgcolor="#f2f1ec" align="center" colspan=2><strong>SQL����</strong></td>
										</tr>
										<tr class="fs12n">
											<td bgcolor="#f2f1ec" align="center" width="100">̾��<span class="red"> *</span></td>
											<td bgcolor="#ffffff" align="left">
												<span class="red12"><!--{$arrErr.sql_name}--></span>
												<input type="text" name="sql_name" value="<!--{$arrSqlData.sql_name|escape}-->" maxlength="<!--{$smarty.const.STEXT_LEN}-->" style="<!--{if $arrErr.name != ""}-->background-color: <!--{$smarty.const.ERR_COLOR}--><!--{/if}-->" size="60" class="box60" /><span class="red"> �ʾ��<!--{$smarty.const.STEXT_LEN}-->ʸ����</span>
											</td>
										</tr>
										<tr class="fs12n">
											<td bgcolor="#f2f1ec" align="center" width="100">SQLʸ<br>��SELECT�ϵ��Ҥ��ʤ��Ǥ�����������<span class="red"> *</span></td>
											<td bgcolor="#ffffff" align="left">
												<span class="red12"><!--{$arrErr.csv_sql}--></span>
												<div>
												<textarea name="csv_sql" cols=75 rows=30 align="left" wrap=off style="<!--{if $arrErr.csv_sql != ""}-->background-color: <!--{$smarty.const.ERR_COLOR}-->;<!--{/if}--> width: 547px;"><!--{$arrSqlData.csv_sql}--></textarea>
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
													<td>
														<input type="image" onMouseover="chgImgImageSubmit('<!--{$smarty.const.URL_DIR}-->img/contents/btn_regist_on.jpg',this)" onMouseout="chgImgImageSubmit('<!--{$smarty.const.URL_DIR}-->img/contents/btn_regist.jpg',this)" src="<!--{$smarty.const.URL_DIR}-->img/contents/btn_regist.jpg" width="123" height="24" alt="�������Ƥ���Ͽ����" border="0" name="subm" onClick="mode.value='confirm'; fnTargetSelf();">
														<input type="image" onMouseover="chgImgImageSubmit('<!--{$smarty.const.URL_DIR}-->img/contents/btn_confirm_on.jpg',this)" onMouseout="chgImgImageSubmit('<!--{$smarty.const.URL_DIR}-->img/contents/btn_confirm.jpg',this)" src="<!--{$smarty.const.URL_DIR}-->img/contents/btn_confirm.jpg" width="123" height="24" alt="��ǧ�ڡ�����" border="0" name="subm" onClick="doPreview(); return false;">
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
									<!-- SQL���� �����ޤ� -->

									<table width="678" border="0" cellspacing="0" cellpadding="0" summary=" ">
										<tr><td colspan="3"><img src="<!--{$smarty.const.URL_DIR}-->img/contents/main_bar.jpg" width="678" height="10" alt=""></td></tr>
									</table>

									<!-- DB���� �������� -->
									<table width="678" border="0" cellspacing="0" cellpadding="0" summary=" ">
										<tr>
											<td width="50%">
											<table width="100%" border="0" cellspacing="1" cellpadding="5" summary=" ">
												<tr class="fs12n">
													<td bgcolor="#f2f1ec" align="center" colspan=2><strong>�ơ��֥����</strong></td>
												</tr>
												<tr class="fs12n">
													<td bgcolor="#ffffff" align="center">
														<select name="arrTableList[]" size="20" style="width:325px; height:300px;" onChange="mode.value=''; selectTable.value=this.value; submit();" onDblClick="csv_sql.value = csv_sql.value +' , ' + this.value;">
														<!--{html_options options=$arrTableList selected=$selectTable}-->
														</select>
													</td>
												</tr>
											</table>
											</td>
											<td width="50%">
												<table width="100%" border="0" cellspacing="1" cellpadding="5" summary=" ">
													<tr class="fs12n">
														<td bgcolor="#f2f1ec" align="center" colspan=2><strong>���ܰ���</strong></td>
													</tr>
													<tr class="fs12n">
														<td bgcolor="#ffffff" align="center">
															<select name="arrColList[]" size="20" style="width:325px; height:300px;" onDblClick="csv_sql.value = csv_sql.value +' , ' + this.value;">
															<!--{html_options options=$arrColList}-->
															</select>
														</td>
													</tr>
												</table>
											</td>
										</tr>
									</table>
									<!-- DB���� �����ޤ� -->
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
