<!--{*
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
*}-->
<script language="JavaScript">
<!--
function lfnCheckSubmit( fm ){
	
	var err = '';
	/*
	if ( ! fm["title"].value ){
		err += '見出しコメントを入力して下さい。';
	}
	*/
	if ( ! fm["comment"].value ){
		if ( err ) err += '\n';
		err += 'オススメコメントを入力して下さい。';
	}
	if ( err ){
		alert(err);
		return false;
	} else {
		if(window.confirm('内容を登録しても宜しいですか')){
			return true;
		}
	}
}

function lfnCheckSetItem( rank ){
	var flag = true;
	var checkRank = '<!--{$checkRank}-->';
	if ( checkRank ){
		if ( rank != checkRank ){
			if( ! window.confirm('さきほど選択した<!--{$checkRank}-->位の情報は破棄されます。宜しいでしょうか')){
				flag = false;
			}
		} 
	}
	
	if ( flag ){
		win03('./recommend_search.php?rank=' + rank,'search','500','500');
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
		<td class="mainbg" >
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
						
						<!--登録テーブルここから-->
						<table width="678" border="0" cellspacing="0" cellpadding="0" summary=" ">
							<tr>
								<td colspan="3"><img src="<!--{$smarty.const.URL_DIR}-->img/contents/contents_title_top.gif" width="678" height="7" alt=""></td>
							</tr>
							<tr>
								<td background="<!--{$smarty.const.URL_DIR}-->img/contents/contents_title_left_bg.gif"><img src="<!--{$smarty.const.URL_DIR}-->img/contents/contents_title_left.gif" width="22" height="12" alt=""></td>
								<td bgcolor="#636469" width="638" class="fs14n"><span class="white"><!--コンテンツタイトル-->オススメ管理</span></td>
								<td background="<!--{$smarty.const.URL_DIR}-->img/contents/contents_title_right_bg.gif"><img src="<!--{$smarty.const.URL_DIR}-->img/common/_.gif" width="18" height="1" alt=""></td>
							</tr>
							<tr>
								<td colspan="3"><img src="<!--{$smarty.const.URL_DIR}-->img/contents/contents_title_bottom.gif" width="678" height="7" alt=""></td>
							</tr>
							<tr>
								<td colspan="3"><img src="<!--{$smarty.const.URL_DIR}-->img/contents/main_bar.jpg" width="678" height="10" alt=""></td>
							</tr>
						</table>

						<!--{section name=cnt loop=$tpl_disp_max}-->
						<!--▼おすすめ<!--{$smarty.section.cnt.iteration}-->-->
						<table width="678" border="0" cellspacing="1" cellpadding="8" summary=" ">
						<form name="form<!--{$smarty.section.cnt.iteration}-->" id="form<!--{$smarty.section.cnt.iteration}-->" method="post" action="<!--{$smarty.server.PHP_SELF|escape}-->">
						<input type="hidden" name="mode" value="regist">
						<input type="hidden" name="product_id" value="<!--{$arrItems[$smarty.section.cnt.iteration].product_id|escape}-->">
						<input type="hidden" name="category_id" value="<!--{$category_id|escape}-->">
						<input type="hidden" name="rank" value="<!--{$arrItems[$smarty.section.cnt.iteration].rank|escape}-->">

							<tr class="fs12n">
								<td bgcolor="#f2f1ec" width="15" align="center"><!--{$smarty.section.cnt.iteration}--></td>
								<td bgcolor="#ffffff" width="130" align="center">
								<!--{if $arrItems[$smarty.section.cnt.iteration].main_list_image != ""}-->
									<!--{assign var=image_path value="`$smarty.const.IMAGE_SAVE_URL`/`$arrItems[$smarty.section.cnt.iteration].main_list_image`"}-->
								<!--{else}-->
									<!--{assign var=image_path value="`$smarty.const.NO_IMAGE_URL`"}-->
								<!--{/if}-->
								<img src="<!--{$image_path}-->" alt="" />
								</td>
								<td bgcolor="#ffffff" width="40" align="center">
									<!--{if $arrItems[$smarty.section.cnt.iteration].product_id}-->
									<a href="#" onClick="return fnInsertValAndSubmit( document.form<!--{$smarty.section.cnt.iteration}-->, 'mode', 'delete', '削除します。宜しいですか' )">削除</a>
									<!--{/if}-->
								</td>
								<td bgcolor="#ffffff" width="40" align="center">
									<a href="#" onclick="lfnCheckSetItem('<!--{$smarty.section.cnt.iteration}-->'); return false;" target="_blank">
									<!--{if $arrItems[$smarty.section.cnt.iteration].product_id}-->商品<br/>変更<!--{else}-->商品<br/>選択<!--{/if}-->
									</a></td>
								<td bgcolor="#ffffff" width="350">
								<table width="350" border="0" cellspacing="0" cellpadding="0" summary=" ">
									<tr class="fs12">
										<td width="70">商品名：<!--{$arrItems[$smarty.section.cnt.iteration].name|escape}--></td>
									</tr>
									<tr><td height="5"></td></tr>
									<tr class="fs12">
										<td colspan="2">オススメコメント：</td>
									</tr>
									<tr>
										<td colspan="2" class="fs12n">
										<span class="red"><!--{$arrErr[$smarty.section.cnt.iteration].comment}--></span>
										<textarea name="comment" cols="45" rows="4" style="width: 337px; height: 82px; " <!--{$arrItems[$smarty.section.cnt.iteration].product_id|sfGetEnabled}-->><!--{$arrItems[$smarty.section.cnt.iteration].comment}--></textarea>
										</td>
									</tr>
									<!--{if $arrItems[$smarty.section.cnt.iteration].product_id}-->
									<tr><td colspan=2><input type="submit" name="subm" value="登録する" onclick="return lfnCheckSubmit(document.form<!--{$smarty.section.cnt.iteration}-->);"/></td></tr>
									<!--{/if}-->
								</table>
								</td>
							</tr>
							</form>
						</table>
						<!--▲おすすめ<!--{$smarty.section.cnt.iteration}-->-->
						<!--{/section}-->
						
						<!--登録テーブルここまで-->
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
		</td>
	</tr>
</table>
<!--★★メインコンテンツ★★-->		
