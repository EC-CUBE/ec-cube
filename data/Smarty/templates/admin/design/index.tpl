<!--{*
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
*}-->
<style type="text/css">    
    div.dragged_elm {
        position:   absolute;
        border:     1px solid black;
        background: rgb(195,217,255);
        color:      #333;
        cursor:		move;
        PADDING-RIGHT: 	2px;
        PADDING-LEFT: 	2px;
        PADDING-BOTTOM: 5px; 
        PADDING-TOP: 	5px;
        FONT-SIZE: 		10pt;
    }

    div.drop_target {
        border:      0px solid gray;
        position:    relative;
        text-align:  center;
        color:       #333;
    }

</style>
<script type="text/javascript">

function doPreview(){
	document.form1.mode.value="preview"
	document.form1.target = "_blank";
	document.form1.submit();
}
function fnTargetSelf(){
	document.form1.target = "_self";
}

// �������
function init () {
    document.body.ondrag = function () { return false; };
    document.body.onselectstart = function () { return false; };
    
    // ������ɥ������������
	scrX = GetWindowSize("width");
	scrY = GetWindowSize("height");    
    
	// ������ɥ��������ѹ����٥�Ȥ˴�Ϣ�դ�
    window.onresize = fnMoveObject;

    // div���������
    all_elms = document.getElementsByTagName ( 'div' );
    
	// td���������
	all_td = document.getElementsByTagName ( 'td' );

	// �������
	fnCreateArr(0);
	
	// alerttest(0);
	
    // �¤��ؤ�
	fnMoveObject();

	<!--{$complate_msg}-->
}

</script>

<script type="text/javascript" src="<!--{$smarty.const.URL_DIR}-->js/layout_design.js"></script>

<!--�����ᥤ�󥳥�ƥ�ġ���-->
<table width="878" border="0" cellspacing="0" cellpadding="0" summary=" ">
<form name="form1" id="form1" method="post" action="<!--{$smarty.server.PHP_SELF|escape}-->" >
<input type="hidden" name="mode" value="">
<input type="hidden" name="page_id" value="<!--{$page_id}-->">
<input type="hidden" name="bloc_cnt" value="<!--{$bloc_cnt}-->">
	<tr valign="top">
		<td background="<!--{$smarty.const.URL_DIR}-->img/contents/navi_bg.gif" height="402">
			<!--��SUB NAVI-->
			<!--{include file=$tpl_subnavi}-->
			<!--��SUB NAVI-->
		</td>
		<td class="mainbg" >
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

						<!--��Ͽ�ơ��֥뤳������-->
						<table width="678" border="0" cellspacing="0" cellpadding="0" summary=" ">
							<tr>
								<td colspan="3"><img src="<!--{$smarty.const.URL_DIR}-->img/contents/contents_title_top.gif" width="678" height="7" alt=""></td>
							</tr>
							<tr>
								<td background="<!--{$smarty.const.URL_DIR}-->img/contents/contents_title_left_bg.gif"><img src="<!--{$smarty.const.URL_DIR}-->img/contents/contents_title_left.gif" width="22" height="12" alt=""></td>
								<td bgcolor="#636469" width="638" class="fs14n"><span class="white"><!--����ƥ�ĥ����ȥ�-->�쥤�������Խ�</span></td>
								<td background="<!--{$smarty.const.URL_DIR}-->img/contents/contents_title_right_bg.gif"><img src="<!--{$smarty.const.URL_DIR}-->img/common/_.gif" width="18" height="1" alt=""></td>
							</tr>
							<tr>
								<td colspan="3"><img src="<!--{$smarty.const.URL_DIR}-->img/contents/contents_title_bottom.gif" width="678" height="7" alt=""></td>
							</tr>
							<tr>
								<td colspan="3"><img src="<!--{$smarty.const.URL_DIR}-->img/contents/main_bar.jpg" width="678" height="10" alt=""></td>
							</tr>
						</table>

						<!--���쥤�������Խ�����������-->
						<table width="678" border="0" cellspacing="1" cellpadding="8" summary=" ">
							<tr class="fs12n">
								<td bgcolor="#f2f1ec" align="center"><strong>�쥤�������Խ�</strong></td>
								<td bgcolor="#f2f1ec" align="center"><strong>̤���ѥ֥�å�</strong></td>
							</tr>
							<tr>
								<!--���쥤�����ȡ���������-->
								<td bgcolor="#ffffff" align="center" valign = 'top'>
									<table width="450" border=0 cellspacing="1" cellpadding="" summary=" " bgcolor="ffffff">
										<tr class="fs12n"><td bgcolor="#ffffff" height=5px colspan=3></td></tr>
										<tr class="fs12n" height = 50>
											<td bgcolor="#cccccc" align="center" colspan=3> �إå����� </td>
										</tr>
										<tr class="fs12n"><td bgcolor="#ffffff" height=5px colspan=3></td></tr>
										<tr>
											<!-- ������ ���ʥӥơ��֥� ������ -->
											<td bgcolor="#ffffff" align="center" valign = 'top'>
												<table border="0" height="400" cellspacing="1" cellpadding="" summary=" " bgcolor="#cccccc">
													<tr class="fs12n">
														<td bgcolor="#ffffff" align="center" id="layout">
															<div tid="LeftNavi" class="drop_target" id="t1" style="width: 145px; height: 100px;"></div>
														</td>
													</tr>
												</table>
											</td>
											<!-- ������ ���ʥӥơ��֥� ������ -->
											<td bgcolor="#ffffff" align="center" valign = 'top'>
												<table border="0" cellspacing="1" cellpadding="" summary=" " bgcolor="#cccccc">
													<!-- ������ �ᥤ������ơ��֥� ������ -->
													<thead>
													<tr class="fs12n">
														<td bgcolor="#ffffff" valign="top" name='MainHead' height="100" id="layout">
															<div tid="MainHead" class="drop_target" id="t2" style="width: 145px; height: 100px;"></div>
														</td>
													</tr>
													</thead>
													<!-- ������ �ᥤ������ơ��֥� ������ -->
													<!-- ������ �ᥤ�� ������ -->
													<tr class="fs12n">
														<td height=198 align="center" name='Main'>�ᥤ��</td>
													</tr>
													<!-- ������ �ᥤ�� ������ -->
													<!-- ������ �ᥤ�����ơ��֥� ������ -->
													<tfoot>
													<tr class="fs12n">
														<td bgcolor="#ffffff" valign="top" name='MainFoot' height="100" id="layout">
															<div tid="MainFoot" class="drop_target" id="t4" style="width: 145px; height: 100px;"></div>
														</td>
													</tr>
													</tfoot>
													<!-- ������ �ᥤ�����ơ��֥� ������ -->
												</table>
											</td>
											<!-- ������ ���ʥӥơ��֥� ������ -->
											<td bgcolor="#ffffff" align="center" valign = 'top'>
												<table border="0" cellspacing="1" cellpadding="" summary=" " bgcolor="#cccccc">
													<tr class="fs12n">
														<td bgcolor="#ffffff" align="center">
															<div tid="RightNavi" class="drop_target" id="t3" style="width: 145px; height: 100px;"></div>
														</td>
													</tr>
												</table>
											</td>
											<!-- ������ ���ʥӥơ��֥� ������ -->
										</tr>
										<tr class="fs12n"><td bgcolor="#ffffff" height=5px colspan=3></td></tr>
										<tr class="fs12n" height=50><td bgcolor="#cccccc" align="center" colspan=3>�եå�����</td></tr>
										<tr class="fs12n"><td bgcolor="#ffffff" height=5px colspan=3></td></tr>
									</table>
								</td>
								<!--���쥤�����ȡ������ޤ�-->
				
								<!--��̤���ѥ֥�å�����������-->
								<td bgcolor="#ffffff" align="center" valign = 'top'>
									<table width="140" border="0" cellspacing="" cellpadding="" summary=" " bgcolor="#ffffff">
										<tr class="fs12n">
											<td bgcolor="#ffffff" align="center" >
												<div tid="Unused" class="drop_target" id="t5" style="width: 160px; height: 500px; border: 1px solid #cccccc;"></div>
											</td>
										</tr>
										<tr class="fs12n">
											<td bgcolor="#ffffff" align="center" height="30">
												<input type='button' value='�����֥�å�����' name='subm' onclick="fnTargetSelf(); fnFormModeSubmit('form1','new_bloc','','');"  />
											</td>
										</tr>
									</table>
								</td>
								<!--��̤���ѥ֥�å��������ޤ�-->
							</tr>
							<tr class="fs12n">
								<td bgcolor="#f2f1ec" align="center" colspan=2>
									<input type='button' value='��¸' name='subm' onclick="fnTargetSelf(); fnFormModeSubmit('form1','confirm','','');"  />
									<input type='button' value='�ץ�ӥ塼' name='preview' onclick="doPreview();" <!--{if $page_id == "0" or $exists_page == "0" }-->DISABLED<!--{/if}--> />
								</td>
							</tr>
						</table>
						<!--���쥤�������Խ��������ޤ�-->
						
						<table width="678" border="0" cellspacing="0" cellpadding="0" summary=" ">
							<tr><td colspan="3"><img src="<!--{$smarty.const.URL_DIR}-->img/contents/main_bar.jpg" width="678" height="10" alt=""></td></tr>
						</table>

						<!--���ڡ�����������������-->
						<table width="678" border="0" cellspacing="1" cellpadding="5" summary=" ">
							<tr class="fs12n">
								<td bgcolor="#f2f1ec" align="center" colspan=3 ><strong>�Խ���ǽ�ڡ���</strong></td>
							</tr>

							<!--{foreach key=key item=item from=$arrEditPage}-->
							<tr class="fs12n" height=20>
								<td align="center" width=600 bgcolor="<!--{if $item.page_id == $page_id}--><!--{$smarty.const.SELECT_RGB}--><!--{else}-->#ffffff<!--{/if}-->">
									<a href="<!--{$smarty.server.PHP_SELF|escape}-->?page_id=<!--{$item.page_id}-->" ><!--{$item.page_name}--></a>
								</td>
								<td align="center" width=78 bgcolor="<!--{if $item.page_id == $page_id}--><!--{$smarty.const.SELECT_RGB}--><!--{else}-->#ffffff<!--{/if}-->">
									<!--{if $item.tpl_dir != ""}-->
										<input type='button' value='�ᥤ���Խ�' name='page_edit' onclick="location.href='./main_edit.php?page_id=<!--{$item.page_id}-->'"  />
									<!--{else}-->
										�Խ��Բ�
									<!--{/if}-->
								</td>
								<td align="center" width=78 bgcolor="<!--{if $item.page_id == $page_id}--><!--{$smarty.const.SELECT_RGB}--><!--{else}-->#ffffff<!--{/if}-->">
									<!--{if $item.edit_flg == 1}-->
									<input type='button' value='���' name='del' onclick="fnTargetSelf(); fnFormModeSubmit('form1','delete','','');"  />
									<!--{/if}-->
								</td>
							</tr>
							<!--{/foreach}-->

							<tr class="fs12n">
								<td bgcolor="#f2f1ec" align="center" colspan=3>
									<input type='button' value='�����ڡ�������' name='subm' onclick="fnTargetSelf(); fnFormModeSubmit('form1','new_page','','');"  />
								</td>
							</tr>
						</table>
						<!--���ڡ��������������ޤ�-->

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
		</td>
	</tr>

</table>
<!--�����ᥤ�󥳥�ƥ�ġ���-->		

<!--{foreach key=key item=item from=$tpl_arrBloc name="bloc_loop"}-->
<div align=center target_id="<!--{$item.target_id}-->" did="<!--{$smarty.foreach.bloc_loop.iteration}-->" class="dragged_elm" id="<!--{$item.target_id}-->"
	 style="left:350px; top:0px; filter: alpha(opacity=100); opacity: 1; width: 120px;">
	 <!--{$item.name}-->
</div>

<input type="hidden" name="name_<!--{$smarty.foreach.bloc_loop.iteration}-->" value="<!--{$item.name}-->">
<input type="hidden" name="id_<!--{$smarty.foreach.bloc_loop.iteration}-->" value="<!--{$item.bloc_id}-->">
<input type="hidden" name="target_id_<!--{$smarty.foreach.bloc_loop.iteration}-->" value="<!--{$item.target_id}-->">
<input type="hidden" name="top_<!--{$smarty.foreach.bloc_loop.iteration}-->" value="<!--{$item.bloc_row}-->">
<!--{/foreach}-->
</form>
