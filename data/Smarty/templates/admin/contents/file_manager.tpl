<!--{*
/*
 * Copyright(c) 2000-2006 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
*}-->

■エラー
<!--{foreach key=key item=item from=$arrErr}-->
	<!--{$key}-->：<!--{$item}--><br/>
<!--{/foreach}--><br/><br/>


<!--{*
/*
 * Copyright(c) 2000-2006 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
*}-->
<script type="text/javascript">
<!--

function func_regist(url) {
	res = confirm('この内容で<!--{if $edit_mode eq "on"}-->編集<!--{else}-->登録<!--{/if}-->しても宜しいですか？');
	if(res == true) {
		document.form1.mode.value = 'regist';
		document.form1.submit();
		return false;
	}
	return false;
}

function func_edit(news_id) {
	document.form1.mode.value = "search";
	document.form1.news_id.value = news_id;
	document.form1.submit();
}

function func_del(news_id) {
	res = confirm('この新着情報を削除しても宜しいですか？');
	if(res == true) {
		document.form1.mode.value = "delete";
		document.form1.news_id.value = news_id;
		document.form1.submit();
	}
	return false;
}

function func_rankMove(term,news_id) {
	document.form1.mode.value = "move";
	document.form1.news_id.value = news_id;
	document.form1.term.value = term;
	document.form1.submit();
}

function moving(news_id,rank, max_rank) {

	var val;
	var ml;
	var len;

	ml = document.move;
	len = document.move.elements.length;
	j = 0;
	for( var i = 0 ; i < len ; i++) {
	    if ( ml.elements[i].name == 'position' && ml.elements[i].value != "" ) {
			val = ml.elements[i].value;
			j ++;
	    }
	}
	
	if ( j > 1) {
		alert( '移動順位は１つだけ入力してください。' );
		return false;
	} else if( ! val ) {
		alert( '移動順位を入力してください。' );
		return false;
	} else if( val.length > 4){
		alert( '移動順位は4桁以内で入力してください。' );
		return false;
	} else if( val.match(/[0-9]+/g) != val){
		alert( '移動順位は数字で入力してください。' );
		return false;
	} else if( val == rank ){
		alert( '移動させる番号が重複しています。' );
		return false;
	} else if( val == 0 ){
		alert( '移動順位は0以上で入力してください。' );
		return false;
	} else if( val > max_rank ){
		alert( '入力された順位は、登録数の最大値を超えています。' );
		return false;	
	} else {
		ml.moveposition.value = val;
		ml.rank.value = rank;
		ml.news_id.value = news_id;
		ml.submit();
		return false;
	}
}

//-->
</script>

<!--★★メインコンテンツ★★-->
<table width="878" border="0" cellspacing="0" cellpadding="0" summary=" ">
	<tr valign="top">
		<td background="<!--{$smarty.const.URL_DIR}-->img/contents/navi_bg.gif" height="402">
			<!--▼SUB NAVI-->
			<!--{include file=$tpl_subnavi}-->
			<!--▲SUB NAVI-->
		</td>
		<td class="mainbg">
			<!--▼登録テーブルここから-->
			<table width="737" border="0" cellspacing="0" cellpadding="0" summary=" ">
				<!--メインエリア-->
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
										<td bgcolor="#636469" width="638" class="fs14n"><span class="white"><!--コンテンツタイトル-->ファイル管理</span></td>
										<td background="<!--{$smarty.const.URL_DIR}-->img/contents/contents_title_right_bg.gif"><img src="<!--{$smarty.const.URL_DIR}-->img/common/_.gif" width="18" height="1" alt=""></td>
									</tr>
									<tr>
										<td colspan="3"><img src="<!--{$smarty.const.URL_DIR}-->img/contents/contents_title_bottom.gif" width="678" height="7" alt=""></td>
									</tr>
									<tr>
										<td colspan="3"><img src="<!--{$smarty.const.URL_DIR}-->img/contents/main_bar.jpg" width="678" height="10" alt=""></td>
									</tr>
								</table>

								<!--▼ファイル管理テーブルここから-->
								<form name="form1" method="post" action="<!--{$smarty.server.PHP_SELF|escape}-->"  enctype="multipart/form-data">
								<input type="hidden" name="mode" value="">
								<input type="hidden" name="now_file" value="<!--{$tpl_now_file}-->">
								<input type="hidden" name="tree_select_file" value="">
								<input type="hidden" name="tree_status" value="">
								<input type="hidden" name="select_file" value="">	
								<input type="text" name="test" id="test" value="" onclick="test('test')">	
								
								<table width="678" border="0" cellspacing="1" cellpadding="8" summary=" ">	
										<tr>
											<td valign="top">
											■ツリー
											<div id="tree"></div>
											</td>
											<td valign="top">
											■ファイル
											<div id="file_view">
												<table>
													<!--{section name=cnt loop=$arrFileList}-->
													<!--{assign var="id" value="select_file`$smarty.section.cnt.index`"}-->
													<tr id="<!--{$id}-->" onclick="fnSetFormVal('form1', 'select_file', '<!--{$arrFileList[cnt].file_path|escape}-->');fnSelectFile('<!--{$id}-->', '#3333FF');" style="" onMouseOver="fnChangeBgColor('<!--{$id}-->', '#3333FF');" onMouseOut="fnChangeBgColor('<!--{$id}-->', '');">
														<td><!--{$arrFileList[cnt].file_name|escape}--></td>
														<td><!--{$arrFileList[cnt].file_size|escape}--></td>
														<td><!--{$arrFileList[cnt].file_time|escape}--></td>
													</tr>
													<!--{/section}-->
												</table>
											</div>
											<input type="button" onclick="setTreeStatus('tree_status');fnModeSubmit('view','',''); return false;" value="表示">
											<input type="button" onclick="setTreeStatus('tree_status');fnModeSubmit('download','',''); return false;" value="ダウンロード">
											<input type="button" onclick="setTreeStatus('tree_status');fnModeSubmit('delete','',''); return false;" value="削除">
											</td>
										</tr>
									■フォルダ作成<br />
								</table>
								<input type="file" name="upload_file"><input type="button" onclick="setTreeStatus('tree_status');fnModeSubmit('upload','',''); return false;" value="アップロード"><br/>
								<input type="text" name="create_file" value=""><input type="button" onclick="setTreeStatus('tree_status');fnModeSubmit('create','',''); return false;" value="作成">
								</form>
								<!--▲ファイル管理テーブルここまで-->
								
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
												<td><input type="image" onMouseover="chgImgImageSubmit('<!--{$smarty.const.URL_DIR}-->img/contents/btn_regist_on.jpg',this)" onMouseout="chgImgImageSubmit('<!--{$smarty.const.URL_DIR}-->img/contents/btn_regist.jpg',this)" src="<!--{$smarty.const.URL_DIR}-->img/contents/btn_regist.jpg" width="123" height="24" alt="この内容で登録する" border="0" name="subm" onclick="return func_regist();"></td>
											</tr>
										</table>
										</td>
										<td bgcolor="#cccccc"><img src="<!--{$smarty.const.URL_DIR}-->img/common/_.gif" width="1" height="10" alt=""></td>
									</tr>
									<tr>
										<td colspan="3"><img src="<!--{$smarty.const.URL_DIR}-->img/contents/tbl_bottom.gif" width="678" height="8" alt=""></td>
									</tr>
								</table>
								
								<table width="678" border="0" cellspacing="0" cellpadding="0" summary=" ">
									<tr><td colspan="3"><img src="<!--{$smarty.const.URL_DIR}-->img/contents/main_bar.jpg" width="678" height="10" alt=""></td></tr>
								</table>
								
								<table width="678" border="0" cellspacing="0" cellpadding="0" summary=" ">
									<tr>
										<td colspan="3"><img src="<!--{$smarty.const.URL_DIR}-->img/contents/contents_title_top.gif" width="678" height="7" alt=""></td>
									</tr>
									<tr>
										<td background="<!--{$smarty.const.URL_DIR}-->img/contents/contents_title_left_bg.gif"><img src="<!--{$smarty.const.URL_DIR}-->img/contents/contents_title_left.gif" width="22" height="12" alt=""></td>
										<td bgcolor="#636469" width="638" class="fs14n"><span class="white"><!--コンテンツタイトル-->登録済み新着情報</span></td>
										<td background="<!--{$smarty.const.URL_DIR}-->img/contents/contents_title_right_bg.gif"><img src="<!--{$smarty.const.URL_DIR}-->img/common/_.gif" width="18" height="1" alt=""></td>
									</tr>
									<tr>
										<td colspan="3"><img src="<!--{$smarty.const.URL_DIR}-->img/contents/contents_title_bottom.gif" width="678" height="7" alt=""></td>
									</tr>
									<tr>
										<td colspan="3"><img src="<!--{$smarty.const.URL_DIR}-->img/contents/main_bar.jpg" width="678" height="10" alt=""></td>
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
				<!--メインエリア-->
			</table>
			<!--▲登録テーブルここまで-->
		</td>
	</tr>
</table>
<!--★★メインコンテンツ★★-->


