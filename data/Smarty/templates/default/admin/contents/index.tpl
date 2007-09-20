<!--{*
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
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
<form name="form1" id="form1" method="post" action="<!--{$smarty.server.PHP_SELF|escape}-->">
<input type="hidden" name="mode" value="">
<input type="hidden" name="news_id" value="<!--{$news_id|escape}-->">
<input type="hidden" name="term" value="">
	<tr valign="top">
		<td background="<!--{$TPL_DIR}-->img/contents/navi_bg.gif" height="402">
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
								<td colspan="3"><img src="<!--{$TPL_DIR}-->img/contents/main_top.jpg" width="706" height="14" alt=""></td>
							</tr>
							<tr>
								<td background="<!--{$TPL_DIR}-->img/contents/main_left.jpg"><img src="<!--{$TPL_DIR}-->img/common/_.gif" width="14" height="1" alt=""></td>
								<td bgcolor="#cccccc">
								<table width="678" border="0" cellspacing="0" cellpadding="0" summary=" ">
									<tr>
										<td colspan="3"><img src="<!--{$TPL_DIR}-->img/contents/contents_title_top.gif" width="678" height="7" alt=""></td>
									</tr>
									<tr>
										<td background="<!--{$TPL_DIR}-->img/contents/contents_title_left_bg.gif"><img src="<!--{$TPL_DIR}-->img/contents/contents_title_left.gif" width="22" height="12" alt=""></td>
										<td bgcolor="#636469" width="638" class="fs14n"><span class="white"><!--コンテンツタイトル-->新規登録</span></td>
										<td background="<!--{$TPL_DIR}-->img/contents/contents_title_right_bg.gif"><img src="<!--{$TPL_DIR}-->img/common/_.gif" width="18" height="1" alt=""></td>
									</tr>
									<tr>
										<td colspan="3"><img src="<!--{$TPL_DIR}-->img/contents/contents_title_bottom.gif" width="678" height="7" alt=""></td>
									</tr>
									<tr>
										<td colspan="3"><img src="<!--{$TPL_DIR}-->img/contents/main_bar.jpg" width="678" height="10" alt=""></td>
									</tr>
								</table>

								<!--▼登録テーブルここから-->
								<table width="678" border="0" cellspacing="1" cellpadding="8" summary=" ">
									<thead>
									<tr class="fs12n">
										<td bgcolor="#f2f1ec" width="78">日付<span class="red"> *</span></td>
										<td bgcolor="#ffffff" width="600"><span class="red"><!--{$arrErr.year}--><!--{$arrErr.month}--><!--{$arrErr.day}--></span>
											<select name="year" <!--{if $arrErr.year}-->style="background-color:<!--{$smarty.const.ERR_COLOR|escape}-->"<!--{/if}-->>
												<option value="" selected>----</option>
												<!--{html_options options=$arrYear selected=$selected_year}-->
											</select>年
											<select name="month" <!--{if $arrErr.month}-->style="background-color:<!--{$smarty.const.ERR_COLOR|escape}-->"<!--{/if}-->>
												<option value="" selected>--</option>
												<!--{html_options options=$arrMonth selected=$selected_month}-->
											</select>月
											<select name="day" <!--{if $arrErr.day}-->style="background-color:<!--{$smarty.const.ERR_COLOR|escape}-->"<!--{/if}-->>
												<option value="" selected>--</option>
												<!--{html_options options=$arrDay selected=$selected_day}-->
											</select>日
										</td>
									</tr>
									<tr>
										<td bgcolor="#f2f1ec" width="" class="fs12n">タイトル<span class="red"> *</span></td>
										<td bgcolor="#ffffff" width="" class="fs12n"><!--{if $arrErr.news_title}--><span class="red"><!--{$arrErr.news_title}--></span><!--{/if}-->
										<textarea name="news_title" cols="60" rows="8" class="area60" maxlength="<!--{$smarty.const.MTEXT_LEN}-->" <!--{if $arrErr.news_title}-->style="background-color:<!--{$smarty.const.ERR_COLOR|escape}-->"<!--{/if}-->><!--{$news_title|escape}--></textarea><br/><span class="red"> （上限<!--{$smarty.const.MTEXT_LEN}-->文字）</span>
										</td>
									</tr>
									</thead>
									<tfoot>
									<tr>
										<td bgcolor="#f2f1ec" width="38" class="fs12n">URL</td>
										<td bgcolor="#ffffff" width="600" class="fs12n"><span class="red"><!--{$arrErr.news_url}--></span><input type="text" name="news_url" size="60" class="box60"  value="<!--{$news_url|escape}-->" <!--{if $arrErr.news_url}-->style="background-color:<!--{$smarty.const.ERR_COLOR|escape}-->"<!--{/if}--> maxlength="<!--{$smarty.const.URL_LEN}-->"/><span class="red"> （上限<!--{$smarty.const.URL_LEN}-->文字）</span>
										</td>
									</tr>
									<tr class="fs12n">
										<td bgcolor="#f2f1ec" width="78">リンク</td>
										<td bgcolor="#ffffff" width="600"><input type="checkbox" name="link_method" value="2" <!--{if $link_method eq 2}--> checked <!--{/if}--> >別ウィンドウで開く</td>
									</tr>
									<tr>
										<td bgcolor="#f2f1ec" width="38" class="fs12n">本文作成</td>
										<td bgcolor="#ffffff" width="600" class="fs12n"><!--{if $arrErr.news_comment}--><span class="red"><!--{$arrErr.news_comment}--></span><!--{/if}--><textarea name="news_comment" cols="60" rows="8" wrap="soft" class="area60" maxlength="<!--{$smarty.const.LTEXT_LEN}-->" style="background-color:<!--{if $arrErr.news_comment}--><!--{$smarty.const.ERR_COLOR|escape}--><!--{/if}-->"><!--{$news_comment|escape}--></textarea><br/><span class="red"> （上限3000文字）</span>
										</td>
									</tr>
									</tfoot>
								</table>
								<!--▲登録テーブルここまで-->

								<table width="678" border="0" cellspacing="0" cellpadding="0" summary=" ">
									<tr>
										<td bgcolor="#cccccc"><img src="<!--{$TPL_DIR}-->img/common/_.gif" width="1" height="5" alt=""></td>
										<td><img src="<!--{$TPL_DIR}-->img/contents/tbl_top.gif" width="676" height="7" alt=""></td>
										<td bgcolor="#cccccc"><img src="<!--{$TPL_DIR}-->img/common/_.gif" width="1" height="5" alt=""></td>
									</tr>
									<tr>
										<td bgcolor="#cccccc"><img src="<!--{$TPL_DIR}-->img/common/_.gif" width="1" height="10" alt=""></td>
										<td bgcolor="#e9e7de" align="center">
										<table border="0" cellspacing="0" cellpadding="0" summary=" ">
											<tr>
												<td><input type="image" onMouseover="chgImgImageSubmit('<!--{$TPL_DIR}-->img/contents/btn_regist_on.jpg',this)" onMouseout="chgImgImageSubmit('<!--{$TPL_DIR}-->img/contents/btn_regist.jpg',this)" src="<!--{$TPL_DIR}-->img/contents/btn_regist.jpg" width="123" height="24" alt="この内容で登録する" border="0" name="subm" onclick="return func_regist();"></td>
											</tr>
										</table>
										</td>
										<td bgcolor="#cccccc"><img src="<!--{$TPL_DIR}-->img/common/_.gif" width="1" height="10" alt=""></td>
									</tr>
									<tr>
										<td colspan="3"><img src="<!--{$TPL_DIR}-->img/contents/tbl_bottom.gif" width="678" height="8" alt=""></td>
									</tr>
									</form>
								</table>

								<table width="678" border="0" cellspacing="0" cellpadding="0" summary=" ">
									<tr><td colspan="3"><img src="<!--{$TPL_DIR}-->img/contents/main_bar.jpg" width="678" height="10" alt=""></td></tr>
								</table>

								<table width="678" border="0" cellspacing="0" cellpadding="0" summary=" ">
									<tr>
										<td colspan="3"><img src="<!--{$TPL_DIR}-->img/contents/contents_title_top.gif" width="678" height="7" alt=""></td>
									</tr>
									<tr>
										<td background="<!--{$TPL_DIR}-->img/contents/contents_title_left_bg.gif"><img src="<!--{$TPL_DIR}-->img/contents/contents_title_left.gif" width="22" height="12" alt=""></td>
										<td bgcolor="#636469" width="638" class="fs14n"><span class="white"><!--コンテンツタイトル-->登録済み新着情報</span></td>
										<td background="<!--{$TPL_DIR}-->img/contents/contents_title_right_bg.gif"><img src="<!--{$TPL_DIR}-->img/common/_.gif" width="18" height="1" alt=""></td>
									</tr>
									<tr>
										<td colspan="3"><img src="<!--{$TPL_DIR}-->img/contents/contents_title_bottom.gif" width="678" height="7" alt=""></td>
									</tr>
									<tr>
										<td colspan="3"><img src="<!--{$TPL_DIR}-->img/contents/main_bar.jpg" width="678" height="10" alt=""></td>
									</tr>
								</table>

								<!--▼一覧表示エリアここから-->
								<table width="678" border="0" cellspacing="1" cellpadding="8" summary=" ">
								<form name="move" id="move" method="post" action="<!--{$smarty.server.PHP_SELF|escape}-->">
								<input type="hidden" name="mode" value="moveRankSet">
								<input type="hidden" name="term" value="setposition">
								<input type="hidden" name="news_id" value="">
								<input type="hidden" name="moveposition" value="">
								<input type="hidden" name="rank" value="">
									<tr bgcolor="#f2f1ec" align="center" class="fs12n">
										<td width="30">順位</td>
										<td width="76">日付</td>
										<td width="269">タイトル</td>
										<td width="50">編集</td>
										<td width="50">削除</td>
										<td width="100">移動</td>
									</tr>
									<!--{if $arrErr.moveposition}-->
									<tr bgcolor="#ffffff" class="fs12n"><td bgcolor="#ffffff" colspan="6"><span class="red"><!--{$arrErr.moveposition}--></span></td></tr>
									<!--{/if}-->
									<!--{section name=data loop=$list_data}-->
									<tr bgcolor="<!--{if $list_data[data].news_id eq $news_id}--><!--{$smarty.const.SELECT_RGB}--><!--{else}-->#ffffff<!--{/if}-->" class="fs12">
										<!--{assign var=db_rank value="`$list_data[data].rank`"}-->
										<!--{assign var=rank value="`$line_max-$db_rank+1`"}-->
										<td width="" align="center"><!--{$rank}--></td>
										<td width="" align="center"><!--{$list_data[data].cast_news_date|date_format:"%Y/%m/%d"}--></td>
										<td width="">
											<!--{if $list_data[data].link_method eq 1 && $list_data[data].news_url != ""}--><a href="<!--{$list_data[data].news_url}-->" ><!--{$list_data[data].news_title|escape|nl2br}--></a>
											<!--{elseif $list_data[data].link_method eq 1 && $list_data[data].news_url == ""}--><!--{$list_data[data].news_title|escape|nl2br}-->
											<!--{elseif $list_data[data].link_method eq 2 && $list_data[data].news_url != ""}--><a href="<!--{$list_data[data].news_url}-->" target="_blank" ><!--{$list_data[data].news_title|escape|nl2br}--></a>
											<!--{else}--><!--{$list_data[data].news_title|escape|nl2br}-->
											<!--{/if}-->
										</td>
										<td width="" align="center"><a href="#" onclick="return func_edit('<!--{$list_data[data].news_id|escape}-->');">編集</a></td>
										<td width="" align="center"><a href="#" onclick="return func_del('<!--{$list_data[data].news_id|escape}-->');">削除</a></td>
										<td width="" align="center">
										<!--{if count($list_data) != 1}-->
										<input type="text" name="pos-<!--{$list_data[data].news_id}-->" size="3" class="box3" />番目へ<a href="<!--{$smarty.server.PHP_SELF|escape}-->" onclick="fnFormModeSubmit('move', 'moveRankSet','news_id', '<!--{$list_data[data].news_id}-->'); return false;">移動</a><br />
										<!--{/if}-->
										<!--{if $list_data[data].rank ne $max_rank}--><a href="#" onclick="return func_rankMove('up', '<!--{$list_data[data].news_id|escape}-->', '<!--{$max_rank|escape}-->');">上へ</a><!--{/if}-->　<!--{if $list_data[data].rank ne 1}--><a href="#" onclick="return func_rankMove('down', '<!--{$list_data[data].news_id|escape}-->', '<!--{$max_rank|escape}-->');">下へ</a><!--{/if}-->
										</td>
									</tr>
									<!--{sectionelse}-->
									<tr bgcolor="#ffffff" class="fs12n">
										<td colspan="6">現在データはありません。</td>
									</tr>
									<!--{/section}-->
								</form>
								</table>
								<!--▲一覧表示エリアここまで-->

								</td>
								<td background="<!--{$TPL_DIR}-->img/contents/main_right.jpg"><img src="<!--{$TPL_DIR}-->img/common/_.gif" width="14" height="1" alt=""></td>
							</tr>
							<tr>
								<td colspan="3"><img src="<!--{$TPL_DIR}-->img/contents/main_bottom.jpg" width="706" height="14" alt=""></td>
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
