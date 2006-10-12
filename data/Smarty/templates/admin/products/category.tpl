<!--{*
/*
 * Copyright(c) 2000-2006 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
*}-->
<!--★★メインコンテンツ★★-->
<table width="878" border="0" cellspacing="0" cellpadding="0" summary=" ">
<form name="form1" id="form1" method="post" action="<!--{$smarty.server.PHP_SELF}-->" enctype="multipart/form-data">
<input type="hidden" name="mode" value="edit">
<input type="hidden" name="parent_category_id" value="<!--{$arrForm.parent_category_id}-->">
<input type="hidden" name="category_id" value="<!--{$arrForm.category_id}-->">
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
						<td colspan="3"><img src="<!--{$smarty.const.URL_DIR}-->img/contents/main_top.jpg" width="706" height="14" alt=""></td>
					</tr>
					<tr>
						<td background="/img/contents/main_left.jpg"><img src="<!--{$smarty.const.URL_DIR}-->img/common/_.gif" width="14" height="1" alt=""></td>
						<td bgcolor="#cccccc">
						
							<!--▼登録テーブルここから-->
							<table width="678" border="0" cellspacing="0" cellpadding="0" summary=" ">
								<tr>
									<td colspan="3"><img src="<!--{$smarty.const.URL_DIR}-->img/contents/contents_title_top.gif" width="678" height="7" alt=""></td>
								</tr>
								<tr>
									<td background="/img/contents/contents_title_left_bg.gif"><img src="<!--{$smarty.const.URL_DIR}-->img/contents/contents_title_left.gif" width="22" height="12" alt=""></td>
									<td bgcolor="#636469" width="638" class="fs14n"><span class="white"><!--コンテンツタイトル-->カテゴリー設定(最大<!--{$smarty.const.LEVEL_MAX}-->階層まで)</span></td>
									<td background="/img/contents/contents_title_right_bg.gif"><img src="<!--{$smarty.const.URL_DIR}-->img/common/_.gif" width="18" height="1" alt=""></td>
								</tr>
								<tr>
									<td colspan="3"><img src="<!--{$smarty.const.URL_DIR}-->img/contents/contents_title_bottom.gif" width="678" height="7" alt=""></td>
								</tr>
								<tr>
									<td colspan="3"><img src="<!--{$smarty.const.URL_DIR}-->img/contents/main_bar.jpg" width="678" height="10" alt=""></td>
								</tr>
							</table>	
							
							<table width="678" border="0" cellspacing="1" cellpadding="5" summary=" " bgcolor="#cccccc">
								<tr bgcolor="#ffffff">
									<!--▼画面左-->
									<td width="250" valign="top" class="fs12">
									<a href="<!--{$smarty.server.PHP_SELF}-->">▼ホーム</a><br>
									<!--{section name=cnt loop=$arrTree}-->
										<!--{assign var=level value="`$arrTree[cnt].level`}-->

										<!--{* 上の階層表示の時にdivを閉じる *}-->
										<!--{assign var=close_cnt value="`$before_level-$level+1`}-->
										<!--{if $close_cnt > 0}-->
											<!--{section name=n loop=$close_cnt}--></div><!--{/section}-->
										<!--{/if}-->

										<!--{* スペース繰り返し *}-->
										<!--{section name=n loop=$level}-->　　<!--{/section}-->
										
										<!--{* カテゴリ名表示 *}-->
										<!--{assign var=disp_name value="`$arrTree[cnt].category_id`.`$arrTree[cnt].category_name`"}-->
										<!--{if $arrTree[cnt].level != $smarty.const.LEVEL_MAX}-->
											<a href="<!--{$smarty.server.PHP_SELF}-->" onclick="fnModeSubmit('tree', 'parent_category_id', <!--{$arrTree[cnt].category_id}-->); return false;">
											<!--{if $arrForm.parent_category_id == $arrTree[cnt].category_id}-->
												<img src="/misc/openf.gif" border="0">
											<!--{else}-->
												<img src="/misc/closef.gif" border="0">
											<!--{/if}-->
											<!--{$disp_name|sfCutString:20|escape}--></a><br>
										<!--{else}-->
											<img src="/misc/closef.gif" border="0">
											<!--{$disp_name|sfCutString:20|escape}--></a><br>
										<!--{/if}-->
										
										<!--{if $arrTree[cnt].display == true}-->
											<div id="f<!--{$arrTree[cnt].category_id}-->">
										<!--{else}-->
											<div id="f<!--{$arrTree[cnt].category_id}-->" style="display:none">
										<!--{/if}-->						
										
										<!--{assign var=before_level value="`$arrTree[cnt].level`}-->
									<!--{/section}-->				
									
									</td>
									
									<!--▼画面右-->				
									<td width="428" valign="top">
									
									<span class="red12"><!--{$arrErr.category_name}--></span>
									<input type="text" name="category_name" value="<!--{$arrForm.category_name|escape}-->" size="30" class="box30" maxlength="<!--{$smarty.const.STEXT_LEN}-->"/>
									<input type="submit" name="button" value="登録"/><span class="red10"> （上限<!--{$smarty.const.STEXT_LEN}-->文字）</span>
									<table width="428" border="0" cellspacing="0" cellpadding="0" summary=" ">	
										<tr><td height="15"></td></tr>
									</table>
									
									<!--{if count($arrList) > 0}-->
									<table border="0" cellspacing="1" cellpadding="5" summary=" " bgcolor="#cccccc">
										<tr bgcolor="#f2f1ec" align="center" class="fs12n">
											<td width="30">ID</td>
											<td width="160">カテゴリ名</td>
											<td width="60">編集</td>
											<td width="60">削除</td>
											<td width="60">移動</td>
										</tr>
										<!--{section name=cnt loop=$arrList}-->
										<tr bgcolor="<!--{if $arrForm.category_id != $arrList[cnt].category_id}-->#ffffff<!--{else}--><!--{$smarty.const.SELECT_RGB}--><!--{/if}-->" align="left" class="fs12n">
											<td><!--{$arrList[cnt].category_id}--></td>
											<td>
											<!--{if $arrList[cnt].level != $smarty.const.LEVEL_MAX}-->
												<a href="<!--{$smarty.server.PHP_SELF}-->" onclick="fnModeSubmit('tree', 'parent_category_id', <!--{$arrList[cnt].category_id}-->); return false"><!--{$arrList[cnt].category_name|escape}--></td>
											<!--{else}-->
												<!--{$arrList[cnt].category_name|escape}-->
											<!--{/if}-->
											<td align="center">
												<!--{if $arrForm.category_id != $arrList[cnt].category_id}-->
												<a href="<!--{$smarty.server.PHP_SELF}-->" onclick="fnModeSubmit('pre_edit', 'category_id', <!--{$arrList[cnt].category_id}-->); return false;" />編集</a>
												<!--{else}-->
												編集中
												<!--{/if}-->
											</td>
											<td align="center">
												<a href="<!--{$smarty.server.PHP_SELF}-->" onclick="fnModeSubmit('delete', 'category_id', <!--{$arrList[cnt].category_id}-->); return false;" />削除</a>
											</td>
											<td align="center">
											<!--{* 移動 *}-->
											<!--{if $smarty.section.cnt.iteration != 1}-->
											<a href="<!--{$smarty.server.PHP_SELF}-->" onclick="fnModeSubmit('up','category_id', <!--{$arrList[cnt].category_id}-->); return false;">上へ</a>
											<!--{/if}-->
											<!--{if $smarty.section.cnt.iteration != $smarty.section.cnt.last}-->
											<a href="<!--{$smarty.server.PHP_SELF}-->" onclick="fnModeSubmit('down','category_id', <!--{$arrList[cnt].category_id}-->); return false;">下へ</a>
											<!--{/if}-->
											</td>
										</tr>
										<!--{/section}-->
									</table>
					
									<!--{else}-->
									<table border="0" cellspacing="0" cellpadding="0" summary=" ">
										<tr>
											<td  class="fs12n">この階層には、カテゴリが登録されていません。</td>
										</tr>
									</table>
									<!--{/if}-->
									</td>
								</tr>
							</table>
							<!-- ▲登録テーブルここまで -->

						</td>
						<td background="/img/contents/main_right.jpg"><img src="<!--{$smarty.const.URL_DIR}-->img/common/_.gif" width="14" height="1" alt=""></td>
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
</form>
</table>
<!--★★メインコンテンツ★★-->		
