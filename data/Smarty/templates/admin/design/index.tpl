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

// 初期処理
function init () {
    document.body.ondrag = function () { return false; };
    document.body.onselectstart = function () { return false; };
    
    // ウィンドウサイズを取得
	scrX = GetWindowSize("width");
	scrY = GetWindowSize("height");    
    
	// ウィンドウサイズ変更イベントに関連付け
    window.onresize = fnMoveObject;

    // divタグを取得
    all_elms = document.getElementsByTagName ( 'div' );
    
	// tdタグを取得
	all_td = document.getElementsByTagName ( 'td' );

	// 配列作成
	fnCreateArr(0);
	
    // 並び替え
	fnMoveObject();
	
	<!--{$complate_msg}-->
}

// 画面のロードイベントに関連付け
addEvent ( window, 'load', init, false );

</script>

<script type="text/javascript" src="/js/layout_design.js"></script>

<!--★★メインコンテンツ★★-->
<table width="878" border="0" cellspacing="0" cellpadding="0" summary=" ">
<form name="form1" id="form1" method="post" action="<!--{$smarty.server.PHP_SELF}-->" >
<input type="hidden" name="mode" value="">
<input type="hidden" name="page_id" value="<!--{$page_id}-->">
<input type="hidden" name="bloc_cnt" value="<!--{$bloc_cnt}-->">
	<tr valign="top">
		<td background="/img/contents/navi_bg.gif" height="402">
			<!--▼SUB NAVI-->
			<!--{include file=$tpl_subnavi}-->
			<!--▲SUB NAVI-->
		</td>
		<td class="mainbg" >
		<table width="737" border="0" cellspacing="0" cellpadding="0" summary=" ">
			<!--メインエリア-->
			<tr>
				<td align="center">
				<table width="706" border="0" cellspacing="0" cellpadding="0" summary=" ">

					<tr><td height="14"></td></tr>
					<tr>
						<td colspan="3"><img src="/img/contents/main_top.jpg" width="706" height="14" alt=""></td>
					</tr>
					<tr>
						<td background="/img/contents/main_left.jpg"><img src="/img/common/_.gif" width="14" height="1" alt=""></td>
						<td bgcolor="#cccccc">
						
						<!--登録テーブルここから-->
						<table width="678" border="0" cellspacing="0" cellpadding="0" summary=" ">
							<tr>
								<td colspan="3"><img src="/img/contents/contents_title_top.gif" width="678" height="7" alt=""></td>
							</tr>
							<tr>
								<td background="/img/contents/contents_title_left_bg.gif"><img src="/img/contents/contents_title_left.gif" width="22" height="12" alt=""></td>
								<td bgcolor="#636469" width="638" class="fs14n"><span class="white"><!--コンテンツタイトル-->レイアウト編集</span></td>
								<td background="/img/contents/contents_title_right_bg.gif"><img src="/img/common/_.gif" width="18" height="1" alt=""></td>
							</tr>
							<tr>
								<td colspan="3"><img src="/img/contents/contents_title_bottom.gif" width="678" height="7" alt=""></td>
							</tr>
							<tr>
								<td colspan="3"><img src="/img/contents/main_bar.jpg" width="678" height="10" alt=""></td>
							</tr>
						</table>

						<!--▼レイアウト編集　ここから-->
						<table width="678" border="0" cellspacing="1" cellpadding="8" summary=" ">
							<tr class="fs12n">
								<td bgcolor="#f2f1ec" align="center"><strong>レイアウト編集</strong></td>
								<td bgcolor="#f2f1ec" align="center"><strong>未使用ブロック</strong></td>
							</tr>
							<tr class="fs12n">
								<!--▼レイアウト　ここから-->
								<td bgcolor="#ffffff" align="center" valign = 'top'>
									<table width="450" border=0 cellspacing="1" cellpadding="" summary=" " bgcolor="ffffff">
										<tr class="fs12n"><td bgcolor="#ffffff" height=5px colspan=3></td></tr>
										<tr class="fs12n" height = 50>
											<td bgcolor="#cccccc" align="center" colspan=3> ヘッダー部 </td>
										</tr>
										<tr class="fs12n"><td bgcolor="#ffffff" height=5px colspan=3></td></tr>
										<tr class="fs12n">
											<!-- ★☆★ 左ナビテーブル ☆★☆ -->
											<td bgcolor="#ffffff" align="center" valign = 'top'>
												<table border="0" height="400" cellspacing="1" cellpadding="" summary=" " bgcolor="#cccccc">
													<tr class="fs12n">
														<td bgcolor="#ffffff" align="center" id="layout">
															<div tid="LeftNavi" class="drop_target" id="t1" style="width: 145px; height: 100px;"></div>
														</td>
													</tr>
												</table>
											</td>
											<!-- ★☆★ 左ナビテーブル ☆★☆ -->
											<td bgcolor="#ffffff" align="center" valign = 'top'>
												<table border="0" cellspacing="1" cellpadding="" summary=" " bgcolor="#cccccc">
													<!-- ★☆★ メイン上部テーブル ☆★☆ -->
													<thead>
													<tr class="fs12n">
														<td bgcolor="#ffffff" valign="top" name='MainHead' height="100" id="layout">
															<div tid="MainHead" class="drop_target" id="t2" style="width: 145px; height: 100px;"></div>
														</td>
													</tr>
													</thead>
													<!-- ★☆★ メイン上部テーブル ☆★☆ -->
													<!-- ★☆★ メイン ☆★☆ -->
													<tr class="fs12n">
														<td height=198 align="center" name='Main'>メイン</td>
													</tr>
													<!-- ★☆★ メイン ☆★☆ -->
													<!-- ★☆★ メイン下部テーブル ☆★☆ -->
													<tfoot>
													<tr class="fs12n">
														<td bgcolor="#ffffff" valign="top" name='MainFoot' height="100" id="layout">
															<div tid="MainFoot" class="drop_target" id="t4" style="width: 145px; height: 100px;"></div>
														</td>
													</tr>
													</tfoot>
													<!-- ★☆★ メイン下部テーブル ☆★☆ -->
												</table>
											</td>
											<!-- ★☆★ 右ナビテーブル ☆★☆ -->
											<td bgcolor="#ffffff" align="center" valign = 'top'>
												<table border="0" cellspacing="1" cellpadding="" summary=" " bgcolor="#cccccc">
													<tr class="fs12n">
														<td bgcolor="#ffffff" align="center">
															<div tid="RightNavi" class="drop_target" id="t3" style="width: 145px; height: 100px;"></div>
														</td>
													</tr>
												</table>
											</td>
											<!-- ★☆★ 右ナビテーブル ☆★☆ -->
										</tr>
										<tr class="fs12n"><td bgcolor="#ffffff" height=5px colspan=3></td></tr>
										<tr class="fs12n" height=50><td bgcolor="#cccccc" align="center" colspan=3>フッター部</td></tr>
										<tr class="fs12n"><td bgcolor="#ffffff" height=5px colspan=3></td></tr>
									</table>
								</td>
								<!--▲レイアウト　ここまで-->
				
								<!--▼未使用ブロック　ここから-->
								<td bgcolor="#ffffff" align="center" valign = 'top'>
									<table width="140" border="0" cellspacing="" cellpadding="" summary=" " bgcolor="#ffffff">
										<tr class="fs12n">
											<td bgcolor="#ffffff" align="center" >
												<div tid="Unused" class="drop_target" id="t5" style="width: 160px; height: 500px; border: 1px solid #cccccc;"></div>
											</td>
										</tr>
										<tr class="fs12n">
											<td bgcolor="#ffffff" align="center" height="30">
												<input type='button' value='新規ブロック作成' name='subm' onclick="fnTargetSelf(); fnFormModeSubmit('form1','new_bloc','','');"  />
											</td>
										</tr>
									</table>
								</td>
								<!--▲未使用ブロック　ここまで-->
							</tr>
							<tr class="fs12n">
								<td bgcolor="#f2f1ec" align="center" colspan=2>
									<input type='button' value='保存' name='subm' onclick="fnTargetSelf(); fnFormModeSubmit('form1','confirm','','');"  />
									<input type='button' value='プレビュー' name='preview' onclick="doPreview();" <!--{if $page_id == "0" or $exists_page == "0" }-->DISABLED<!--{/if}--> />
								</td>
							</tr>
						</table>
						<!--▲レイアウト編集　ここまで-->
						
						<table width="678" border="0" cellspacing="0" cellpadding="0" summary=" ">
							<tr><td colspan="3"><img src="/img/contents/main_bar.jpg" width="678" height="10" alt=""></td></tr>
						</table>

						<!--▼ページ一覧　ここから-->
						<table width="678" border="0" cellspacing="1" cellpadding="5" summary=" ">
							<tr class="fs12n">
								<td bgcolor="#f2f1ec" align="center" colspan=3 ><strong>編集可能ページ</strong></td>
							</tr>

							<!--{foreach key=key item=item from=$arrEditPage}-->
							<tr class="fs12n" height=20>
								<td align="center" width=600 bgcolor="<!--{if $item.page_id == $page_id}--><!--{$smarty.const.SELECT_RGB}--><!--{else}-->#ffffff<!--{/if}-->">
									<a href="<!--{$smarty.server.PHP_SELF}-->?page_id=<!--{$item.page_id}-->" ><!--{$item.page_name}--></a>
								</td>
								<td align="center" width=78 bgcolor="<!--{if $item.page_id == $page_id}--><!--{$smarty.const.SELECT_RGB}--><!--{else}-->#ffffff<!--{/if}-->">
									<!--{if $item.tpl_dir != ""}-->
										<input type='button' value='ページ編集' name='page_edit' onclick="location.href='./main_edit.php?page_id=<!--{$item.page_id}-->'"  />
									<!--{else}-->
										ページ編集できません
									<!--{/if}-->
								</td>
								<td align="center" width=78 bgcolor="<!--{if $item.page_id == $page_id}--><!--{$smarty.const.SELECT_RGB}--><!--{else}-->#ffffff<!--{/if}-->">
									<!--{if $item.edit_flg == 1}-->
									<input type='button' value='削除' name='del' onclick="fnTargetSelf(); fnFormModeSubmit('form1','delete','','');"  />
									<!--{/if}-->
								</td>
							</tr>
							<!--{/foreach}-->

							<tr class="fs12n">
								<td bgcolor="#f2f1ec" align="center" colspan=3>
									<input type='button' value='新規ページ作成' name='subm' onclick="fnTargetSelf(); fnFormModeSubmit('form1','new_page','','');"  />
								</td>
							</tr>
						</table>
						<!--▲ページ一覧　ここまで-->

						</td>
						<td background="/img/contents/main_right.jpg"><img src="/img/common/_.gif" width="14" height="1" alt=""></td>
					</tr>
					<tr>
						<td colspan="3"><img src="/img/contents/main_bottom.jpg" width="706" height="14" alt=""></td>
					</tr>
					<tr><td height="30"></td></tr>

				</table>
				</td>
			</tr>
			<!--メインエリア-->
		</table>
		</td>
	</tr>

</table>
<!--★★メインコンテンツ★★-->		

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
