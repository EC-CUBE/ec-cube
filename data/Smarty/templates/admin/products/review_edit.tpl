<!--★★メインコンテンツ★★-->
<table width="878" border="0" cellspacing="0" cellpadding="0" summary=" ">
<form name="form1" id="form1" method="post" action="<!--{$smarty.server.PHP_SELF}-->" >
<input type="hidden" name="mode" value="complete">
<input type="hidden" name="review_id" value="<!--{$tpl_review_id}-->" >
<input type="hidden" name="pre_status" value="<!--{$tpl_pre_status}-->">
<!--{foreach key=key item=item from=$arrReview}-->
<!--{if $key ne "mode"}-->
<input type="hidden" name="<!--{$key}-->" value="<!--{$item|escape}-->">
<!--{/if}-->
<!--{/foreach}-->
<!--{foreach key=key item=item from=$arrSearchHidden}-->
<input type="hidden" name="<!--{$key}-->" value="<!--{$item|escape}-->">
<!--{/foreach}-->
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
						
							<!--▼登録テーブルここから-->
							<table width="678" border="0" cellspacing="0" cellpadding="0" summary=" ">
								<tr>
									<td colspan="3"><img src="/img/contents/contents_title_top.gif" width="678" height="7" alt=""></td>
								</tr>
								<tr>
									<td background="/img/contents/contents_title_left_bg.gif"><img src="/img/contents/contents_title_left.gif" width="22" height="12" alt=""></td>
									<td bgcolor="#636469" width="638" class="fs14n"><span class="white"><!--コンテンツタイトル-->カテゴリー設定</span></td>
									<td background="/img/contents/contents_title_right_bg.gif"><img src="/img/common/_.gif" width="18" height="1" alt=""></td>
								</tr>
								<tr>
									<td colspan="3"><img src="/img/contents/contents_title_bottom.gif" width="678" height="7" alt=""></td>
								</tr>
								<tr>
									<td colspan="3"><img src="/img/contents/main_bar.jpg" width="678" height="10" alt=""></td>
								</tr>
							</table>


							<!--▼編集テーブルここから-->
							<table width="678" border="0" cellspacing="1" cellpadding="8" summary=" " bgcolor="#cccccc">
								<tr class="fs12n">
									<td bgcolor="#f2f1ec" width="200">商品名</td>
									<td bgcolor="#ffffff" width="443"><!--{$arrReview.name}--></td>
								</tr>
								<tr class="fs12n">
									<td bgcolor="#f2f1ec">レビュー表示</td>
									<td bgcolor="#ffffff"><!--{if $arrErr.status}--><span class="red12"><!--{$arrErr.status}--></span><br /><!--{/if}-->
									<input type="radio" name="status" value="2" <!--{if $arrReview.status eq 2}-->checked<!--{/if}-->>非表示<!--{if $arrReview.status eq 2 && !$tpl_status_change}--><!--{else}--><input type="radio" name="status" value="1" <!--{if $arrReview.status eq 1}-->checked<!--{/if}-->>表示<!--{/if}--></td>
								</tr>
								<tr class="fs12n">
									<td bgcolor="#f2f1ec">投稿日</td>
									<td bgcolor="#ffffff"><!--{$arrReview.create_date|sfDispDBDate}--></td>
								</tr>
								<tr class="fs12n">
									<td bgcolor="#f2f1ec">投稿者名</td>
									<td bgcolor="#ffffff"><!--{$arrReview.reviewer_name|escape}--></td>
								</tr>
								<tr class="fs12n">
									<td bgcolor="#f2f1ec">ホームページアドレス</td>
									<td bgcolor="#ffffff"><a href="<!--{$arrReview.reviewer_url}-->" target="_blank"><!--{$arrReview.reviewer_url}--></a></td>
								</tr>
								<tr class="fs12n">
									<td bgcolor="#f2f1ec">性別</td>
									<td bgcolor="#ffffff"><!--{if $arrReview.sex eq 1}-->男性<!--{elseif $arrReview.sex eq 2}-->女性<!--{/if}--></td>
								</tr>
								<tr class="fs12n">
									<td bgcolor="#f2f1ec">おすすめレベル</td>
									<td bgcolor="#ffffff">
									<!--{assign var=key value="recommend_level"}-->
									<select name="<!--{$key}-->" style="<!--{$arrErr.recommend_level|sfGetErrorColor}-->" >
									<option value="" selected="selected">選択してください</option>
									<!--{html_options options=$arrRECOMMEND selected=$arrReview[$key]}-->
									</select>
									<span class="red12"><!--{$arrErr.recommend_level}--></span>
									</td>
								</tr>
								<tr class="fs12n">
									<td bgcolor="#f2f1ec">タイトル</td>
									<td bgcolor="#ffffff"><span class="red12"><!--{$arrErr.title}--></span>
									<input type="text" name="title" value="<!--{$arrReview.title|escape}-->" style="<!--{$arrErr.title|sfGetErrorColor}-->" size=30><span class="red12"><!--{$arrErr.title}--></td>
								</tr>
								<tr class="fs12n">
									<td bgcolor="#f2f1ec">コメント</td>
									<td bgcolor="#ffffff"><span class="red12"><!--{$arrErr.comment}--></span>
									<textarea name="comment" rows="20" cols="60" class="area60" wrap="soft" style="<!--{$arrErr.comment|sfGetErrorColor}-->" ><!--{$arrReview.comment|escape}--></textarea></td>
								</tr>
							</table>
							<!--▲編集テーブルここまで-->
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
												<input type="image" onmouseover="chgImgImageSubmit('/img/contents/btn_search_back_on.jpg',this);" onmouseout="chgImgImageSubmit('/img/contents/btn_search_back.jpg',this);" src="/img/contents/btn_search_back.jpg" width="123" height="24" alt="検索画面に戻る" border="0" name="back" onclick="document.form1.action='./review.php'; fnModeSubmit('search','','');" ></a>
												<input type="image" onMouseover="chgImgImageSubmit('/img/contents/btn_regist_on.jpg',this)" onMouseout="chgImgImageSubmit('/img/contents/btn_regist.jpg',this)" src="/img/contents/btn_regist.jpg" width="123" height="24" alt="この内容で登録する" border="0" name="subm" onclick="fnModeSubmit('complete','','');" />
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
							<!-- ▲登録テーブルここまで -->

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
</form>
</table>
<!--★★メインコンテンツ★★-->		