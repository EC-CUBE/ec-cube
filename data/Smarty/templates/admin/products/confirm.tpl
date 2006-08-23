<!--★★メインコンテンツ★★-->
<table width="878" border="0" cellspacing="0" cellpadding="0" summary=" ">
	<tr valign="top">
		<td background="/img/contents/navi_bg.gif" height="402">
			<!--▼SUB NAVI-->
			<!--{include file=$tpl_subnavi}-->
			<!--▲SUB NAVI-->
		</td>
		<td class="mainbg">
			<!--▼CONTENTS-->
			<table width="737" border="0" cellspacing="0" cellpadding="0" summary=" ">
				<!--メインエリア-->
				<tr>
					<td align="center">
					<!--▼MAIN CONTENTS-->
						<table width="706" border="0" cellspacing="0" cellpadding="0" summary=" ">
						<!--▼登録テーブルここから-->
						<form name="form1" id="form1" method="post" action="<!--{$smarty.server.PHP_SELF}-->" enctype="multipart/form-data">
						<!--{foreach key=key item=item from=$arrForm}-->
						<input type="hidden" name="<!--{$key}-->" value="<!--{$item|escape}-->">
						<!--{/foreach}-->
						<tr><td height="14"></td></tr>
						<tr>
							<td colspan="3"><img src="/img/contents/main_top.jpg" width="706" height="14" alt=""></td>
						</tr>
						<tr>
							<td background="/img/contents/main_left.jpg"><img src="/img/common/_.gif" width="14" height="1" alt=""></td>
							<td bgcolor="#cccccc">
							<table width="678" border="0" cellspacing="0" cellpadding="0" summary=" ">
								<tr>
									<td colspan="3"><img src="/img/contents/contents_title_top.gif" width="678" height="7" alt=""></td>
								</tr>
								<tr>
									<td background="/img/contents/contents_title_left_bg.gif"><img src="/img/contents/contents_title_left.gif" width="22" height="12" alt=""></td>
									<td bgcolor="#636469" width="638" class="fs14n"><span class="white"><!--コンテンツタイトル-->商品登録</span></td>
									<td background="/img/contents/contents_title_right_bg.gif"><img src="/img/common/_.gif" width="18" height="1" alt=""></td>
								</tr>
								<tr>
									<td colspan="3"><img src="/img/contents/contents_title_bottom.gif" width="678" height="7" alt=""></td>
								</tr>
								<tr>
									<td colspan="3"><img src="/img/contents/main_bar.jpg" width="678" height="10" alt=""></td>
								</tr>
							</table>
							<table width="678" border="0" cellspacing="1" cellpadding="8" summary=" ">				
								<tr>
									<td bgcolor="#f2f1ec" width="160" class="fs12n">商品名</td>
									<td bgcolor="#ffffff" width="557" class="fs12n">
									<!--{$arrForm.name|escape}-->
									</td>
								</tr>
								<tr>
									<td bgcolor="#f2f1ec" width="160" class="fs12n">商品カテゴリ</td>
									<td bgcolor="#ffffff" width="557" class="fs12n">
									<!--{assign var=key value=$arrForm.category_id}-->
									<!--{$arrCatList[$key]|strip|sfTrim}-->
									</td>
								</tr>
								<tr>
									<td bgcolor="#f2f1ec" width="160" class="fs12n">公開・非公開</td>
									<td bgcolor="#ffffff" width="557" class="fs12n">
									<!--{$arrDISP[$arrForm.status]}-->
									</td>
								</tr>
								<tr>
									<td bgcolor="#f2f1ec" width="160" class="fs12n">商品ステータス</td>
									<td bgcolor="#ffffff" width="557" class="fs12n">
									<!--{section name=cnt loop=$arrForm.product_flag|count_characters}-->
										<!--{if $arrForm.product_flag[cnt] == "1"}--><!--{assign var=key value="`$smarty.section.cnt.iteration`"}--><img src="<!--{$arrSTATUS_IMAGE[$key]}-->"><!--{/if}-->
									<!--{/section}-->
									</td>
								</tr>
								
								<!--{if $tpl_nonclass == true}-->
								<tr>
									<td bgcolor="#f2f1ec" width="160" class="fs12n">商品コード</td>
									<td bgcolor="#ffffff" width="557" class="fs12n">
									<!--{$arrForm.product_code|escape}-->
									</td>
								</tr>
								<tr>
									<td bgcolor="#f2f1ec" width="160" class="fs12n">商品価格</td>
									<td bgcolor="#ffffff" width="557" class="fs12n">
									<!--{$arrForm.price01|escape}-->
									円</td>
								</tr>
								<tr>
									<td bgcolor="#f2f1ec" width="160" class="fs12n">キャンペーン価格</td>
									<td bgcolor="#ffffff" width="557" class="fs12n">
									<!--{$arrForm.price02|escape}-->
									</td>
								</tr>
								<tr>
									<td bgcolor="#f2f1ec" width="160" class="fs12n">在庫数</td>
									<td bgcolor="#ffffff" width="557" class="fs12n">
									<!--{if $arrForm.stock_unlimited == 1}-->
									無制限
									<!--{else}-->
									<!--{$arrForm.stock|escape}-->
									<!--{/if}-->
									</td>
								</tr>
								<!--{/if}-->
								
								<tr>
									<td bgcolor="#f2f1ec" width="160" class="fs12n">商品送料</td>
									<td bgcolor="#ffffff" width="557" class="fs12n">
									<!--{$arrForm.deliv_fee|escape}-->
									円</td>
								</tr>	
								
								<tr>
									<td bgcolor="#f2f1ec" width="160" class="fs12n">ポイント付与率</td>
									<td bgcolor="#ffffff" width="557" class="fs12n">
									<!--{$arrForm.point_rate|escape}-->
									％</td>
								</tr>
								<tr>
									<td bgcolor="#f2f1ec" width="160" class="fs12n">発送日目安</td>
									<td bgcolor="#ffffff" width="557" class="fs12n">
									<!--{$arrDELIVERYDATE[$arrForm.deliv_date_id]|escape}-->
									</td>
								</tr>			
								<tr>
									<td bgcolor="#f2f1ec" width="160" class="fs12n">購入制限</td>
									<td bgcolor="#ffffff" width="557" class="fs12n">
									<!--{if $arrForm.sale_unlimited == 1}-->
									無制限
									<!--{else}-->
									<!--{$arrForm.sale_limit|escape}-->
									<!--{/if}-->
									</td>
								</tr>
								<tr>
									<td bgcolor="#f2f1ec" width="160" class="fs12n">メーカーURL</td>
									<td bgcolor="#ffffff" width="557" class="fs12n">
									<!--{$arrForm.comment1|escape|sfPutBR:$smarty.const.LINE_LIMIT_SIZE}-->
									</td>
								</tr>
								<!--{*
								<tr>
									<td bgcolor="#f2f1ec" width="160" class="fs12n">成分</td>
									<td bgcolor="#ffffff" width="557" class="fs12n">
									<!--{$arrForm.comment2|escape}-->
									</td>
								</tr>
								*}-->
								<tr>
									<td bgcolor="#f2f1ec" width="160" class="fs12n">検索ワード</td>
									<td bgcolor="#ffffff" width="557" class="fs12n">
									<!--{$arrForm.comment3|escape}-->
									</td>
								</tr>
								<tr>
									<td bgcolor="#f2f1ec" width="160" class="fs12n">一覧-メインコメント</td>
									<td bgcolor="#ffffff" width="557" class="fs12n">
									<!--{$arrForm.main_list_comment|escape|nl2br}-->
									</td>
								</tr>
								<tr>
									<td bgcolor="#f2f1ec" width="160" class="fs12n">一覧-メイン画像</td>
									<td bgcolor="#ffffff" width="557">
									<!--{assign var=key value="main_list_image"}-->
									<!--{if $arrFile[$key].filepath != ""}-->
									<img src="<!--{$arrFile[$key].filepath}-->" alt="<!--{$arrForm.name|escape}-->" width="<!--{$arrFile[$key].width}-->" height="<!--{$arrFile[$key].height}-->" /><br />
									<!--{/if}-->
									</td>
								</tr>
								<tr>
									<td bgcolor="#f2f1ec" width="160" class="fs12n">詳細-メインコメント</td>
									<td bgcolor="#ffffff" width="557" class="fs12n">
									<!--{$arrForm.main_comment|nl2br}-->
									</td>
								</tr>
								<tr>
									<td bgcolor="#f2f1ec" width="160" class="fs12n">詳細-メイン画像</td>
									<td bgcolor="#ffffff" width="557">
									<!--{assign var=key value="main_image"}-->
									<!--{if $arrFile[$key].filepath != ""}-->
									<img src="<!--{$arrFile[$key].filepath}-->" alt="<!--{$arrForm.name|escape}-->" width="<!--{$arrFile[$key].width}-->" height="<!--{$arrFile[$key].height}-->" /><br />
									<!--{/if}-->
									</td>
								</tr>
								<tr>
									<td bgcolor="#f2f1ec" width="160" class="fs12n">詳細-メイン拡大画像</td>
									<td bgcolor="#ffffff" width="557">
									<!--{assign var=key value="main_large_image"}-->
									<!--{if $arrFile[$key].filepath != ""}-->
									<img src="<!--{$arrFile[$key].filepath}-->" alt="<!--{$arrForm.name|escape}-->" width="<!--{$arrFile[$key].width}-->" height="<!--{$arrFile[$key].height}-->" /><br />
									<!--{/if}-->
									</td>
								</tr>
								<tr>
									<td bgcolor="#f2f1ec" width="160" class="fs12n">カラー比較画像</td>
									<td bgcolor="#ffffff" width="557" class="fs12n">
									<!--{assign var=key value="file1"}-->
									<!--{if $arrFile[$key].filepath != ""}-->
									<img src="<!--{$arrFile[$key].filepath}-->" alt="<!--{$arrForm.name|escape}-->" width="<!--{$arrFile[$key].width}-->" height="<!--{$arrFile[$key].height}-->" /><br />
									<!--{/if}-->
								</tr>
								<tr>
									<td bgcolor="#f2f1ec" width="160" class="fs12n">商品詳細ファイル</td>
									<td bgcolor="#ffffff" width="557" class="fs12n">
									<!--{assign var=key value="file2"}-->
									<!--{if $arrFile[$key].filepath != ""}-->
									<input type="button" name="pdf" value="ファイル確認" onclick="fnOpenNoMenu('<!--{$arrFile[$key].filepath}-->')"/>
									<!--{/if}-->
									</td>
								</tr>			
								<!--{section name=cnt loop=$smarty.const.PRODUCTSUB_MAX}-->
								<!--▼商品<!--{$smarty.section.cnt.iteration}-->-->
								<tr>
									<td bgcolor="#f2f1ec" width="160" class="fs12n">詳細-サブタイトル（<!--{$smarty.section.cnt.iteration}-->）</td>
									<td bgcolor="#ffffff" width="557" class="fs12n">
									<!--{assign var=key value="sub_title`$smarty.section.cnt.iteration`"}-->
									<!--{$arrForm[$key]|escape}-->
									</td>
								</tr>
								<tr>
									<td bgcolor="#f2f1ec" width="160" class="fs12n">詳細-サブコメント（<!--{$smarty.section.cnt.iteration}-->）</td>
									<td bgcolor="#ffffff" width="557" class="fs12n">
									<!--{assign var=key value="sub_comment`$smarty.section.cnt.iteration`"}-->
									<!--{$arrForm[$key]}-->
									</td>
								</tr>
								<tr>
									<td bgcolor="#f2f1ec" width="160" class="fs12n">詳細-サブ画像（<!--{$smarty.section.cnt.iteration}-->）</td>
									<td bgcolor="#ffffff" width="557" class="fs12n">
									<!--{assign var=key value="sub_image`$smarty.section.cnt.iteration`"}-->
									<!--{if $arrFile[$key].filepath != ""}-->
									<img src="<!--{$arrFile[$key].filepath}-->" alt="<!--{$arrForm.name|escape}-->" width="<!--{$arrFile[$key].width}-->" height="<!--{$arrFile[$key].height}-->" /><br />
									<!--{/if}-->
									</td>
								</tr>
								<tr>
									<td bgcolor="#f2f1ec" width="160" class="fs12n">詳細-サブ拡大画像（<!--{$smarty.section.cnt.iteration}-->）</td>
									<td bgcolor="#ffffff" width="557" class="fs12n">
									<!--{assign var=key value="sub_large_image`$smarty.section.cnt.iteration`"}-->
									<!--{if $arrFile[$key].filepath != ""}-->
									<img src="<!--{$arrFile[$key].filepath}-->" alt="<!--{$arrForm.name|escape}-->" width="<!--{$arrFile[$key].width}-->" height="<!--{$arrFile[$key].height}-->" /><br />
									<!--{/if}-->
									</td>
								</tr>
								<!--▲商品<!--{$smarty.section.cnt.iteration}-->-->
								<!--{/section}-->
								
								<!--{if $smarty.const.OPTION_RECOMMEND == 1}-->	
								<!--▼関連商品-->
								<!--{section name=cnt loop=$smarty.const.RECOMMEND_PRODUCT_MAX}-->			
								<!--{assign var=recommend_no value="`$smarty.section.cnt.iteration`"}-->
								<tr>
									<td bgcolor="#f2f1ec" width="160" class="fs12n">関連商品(<!--{$smarty.section.cnt.iteration}-->)<br>
									<!--{if $arrRecommend[$recommend_no].main_list_image != ""}-->
										<!--{assign var=image_path value="`$smarty.const.IMAGE_SAVE_URL`/`$arrRecommend[$recommend_no].main_list_image`"}-->
									<!--{else}-->
										<!--{assign var=image_path value="`$smarty.const.NO_IMAGE_URL`"}-->
									<!--{/if}-->
									<img src="<!--{$image_path|sfRmDupSlash}-->" width="65" height="65" alt="<!--{$arrRecommend[$recommend_no].name|escape}-->" />
									</td>
									<td bgcolor="#ffffff" width="557" class="fs12n">
									<!--{if $arrRecommend[$recommend_no].name != ""}-->
									商品コード:<!--{$arrRecommend[$recommend_no].product_code_min}--><br>
									商品名:<!--{$arrRecommend[$recommend_no].name|escape}--><br>
									コメント:<br>
									<!--{$arrRecommend[$recommend_no].comment|escape}-->
									<!--{/if}-->
									</td>
								</tr>
								<!--{/section}-->
								<!--▲関連商品-->
								<!--{/if}-->
							</table>
							<table width="678" border="0" cellspacing="0" cellpadding="0" summary=" ">
								<tr>
									<td bgcolor="#cccccc"><img src="/img/common/_.gif" width="1" height="5" alt=""></td>
									<td><img src="/img/contents/tbl_top.gif" width="676" height="7" alt=""></td>
									<td bgcolor="#cccccc"><img src="/img/common/_.gif" width="1" height="5" alt=""></td>
								</tr>
								<tr>
									<td bgcolor="#cccccc"><img src="/img/common/_.gif" width="1" height="10" alt=""></td>
									<td bgcolor="#e9e7de" align="center">
									<table border="0" cellspacing="0" cellpadding="0" summary=" ">
										<tr>
											<td>
												<input type="image" onMouseover="chgImgImageSubmit('/img/contents/btn_back_on.jpg',this)" onMouseout="chgImgImageSubmit('/img/contents/btn_back.jpg',this)" src="/img/contents/btn_back.jpg" width="123" height="24" alt="前のページに戻る" border="0" name="back" onclick="fnModeSubmit('confirm_return','','')" >
												<input type="image" onMouseover="chgImgImageSubmit('/img/contents/btn_regist_on.jpg',this)" onMouseout="chgImgImageSubmit('/img/contents/btn_regist.jpg',this)" src="/img/contents/btn_regist.jpg" width="123" height="24" alt="この内容で登録する" border="0" name="subm" >
											</td>
										</tr>
									</table>
									</td>
									<td bgcolor="#cccccc"><img src="/img/common/_.gif" width="1" height="10" alt=""></td>
								</tr>
								<tr>
									<td colspan="3"><img src="/img/contents/tbl_bottom.gif" width="678" height="8" alt=""></td>
								</tr>
							</table>
						</td>
						<td background="/img/contents/main_right.jpg"><img src="/img/common/_.gif" width="14" height="1" alt=""></td>
					</tr>
					<tr>
						<td colspan="3"><img src="/img/contents/main_bottom.jpg" width="706" height="14" alt=""></td>
					</tr>
					<tr><td height="30"></td></tr>
			
					</table>
						
					<!--▲登録テーブルここまで-->
					</form>
					<!--▲MAIN CONTENTS-->
					</td>
				</tr>
			</table>
			<!--▲CONTENTS-->
		</td>
	</tr>
</table>
<!--★★メインコンテンツ★★-->
