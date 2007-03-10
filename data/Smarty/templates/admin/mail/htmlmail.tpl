<!--{*
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
*}-->
<script language="JavaScript">
<!--
function lfc_del_product( pname ){
	fm = document.form1;
	fm[pname].value = '';
	fm['photo_' + pname].src = '<!--{$smarty.const.NO_IMAGE_URL}-->';
	fm['name_' + pname].value = '';
	
	return false;
}
//-->
</script>

<!--★★メインコンテンツ★★-->
<table width="878" border="0" cellspacing="0" cellpadding="0" summary=" ">
<form name="form1" id="form1" method="post" action="<!--{$smarty.server.PHP_SELF|escape}-->" enctype="multipart/form-data">
<input type="hidden" name="template_id" value="<!--{$arrForm.template_id|escape}-->">
<input type="hidden" name="mail_method" value="3">
<input type="hidden" name="image_key" value="">
<input type="hidden" name="product_key" value="">
<input type="hidden" name="sub_product_num" value="">
<input type="hidden" name="mode" value="confirm">
<!--{foreach key=key item=item from=$arrHidden}-->
<!--{if $key ne "mode"}-->
<input type="hidden" name="<!--{$key}-->" value="<!--{$item|escape}-->">
<!--{/if}-->
<!--{/foreach}-->
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
										<td bgcolor="#636469" width="638" class="fs14n"><span class="white"><!--コンテンツタイトル-->HTMLメール作成</span></td>
										<td background="<!--{$smarty.const.URL_DIR}-->img/contents/contents_title_right_bg.gif"><img src="<!--{$smarty.const.URL_DIR}-->img/common/_.gif" width="18" height="1" alt=""></td>
									</tr>
									<tr>
										<td colspan="3"><img src="<!--{$smarty.const.URL_DIR}-->img/contents/contents_title_bottom.gif" width="678" height="7" alt=""></td>
									</tr>
									<tr>
										<td colspan="3"><img src="<!--{$smarty.const.URL_DIR}-->img/contents/main_bar.jpg" width="678" height="10" alt=""></td>
									</tr>
								</table>

								<table width="678" border="0" cellspacing="1" cellpadding="8" summary=" ">
									<tr>
										<td bgcolor="#f2f1ec" width="160" class="fs12n">Subject<span class="red"> *</span></td>
										<td bgcolor="#ffffff" width="547" class="fs12n"><input type="text" name="subject" size="65" class="box65" <!--{if $arrErr.subject}--><!--{sfSetErrorStyle}--><!--{/if}--> value="<!--{$arrForm.subject|escape}-->"/>
										<!--{if $arrErr.subject}--><br><span class="red"><!--{$arrErr.subject}--></span><!--{/if}-->
										</td>
									</tr>
									<tr>
										<td bgcolor="#f2f1ec" width="160" class="fs12n">メール担当写真<span class="red"> *</span><br />[130×130]</td>
										<!--{assign var=key value="charge_image"}-->
										<td bgcolor="#ffffff" width="547" class="fs12n">
										<!--{if $arrFile[$key].filepath != ""}-->
										<img src="<!--{$arrFile[$key].filepath}-->" alt="メール担当写真" width="<!--{$arrFile[$key].width}-->" height="<!--{$arrFile[$key].height}-->"><br/><br />
										<!--{/if}-->
										<!--{if $arrErr[$key]}--><span class="red"><!--{$arrErr[$key]}--></span><!--{/if}-->
										<input type="file" name="<!--{$key}-->" size="45" class="box45"　style="<!--{$arrErr[$key]|sfGetErrorColor}-->" />
										<input type="button" name="btn" value="アップロード" onclick="fnModeSubmit('upload_image','image_key','<!--{$key}-->');" />
										</td>
									</tr>
									<tr>
										<td bgcolor="#f2f1ec" width="160" class="fs12n">ヘッダーテキスト<span class="red"> *</span></td>
										<td bgcolor="#ffffff" width="547" class="fs12n"><textarea name="header" cols="70" rows="8" class="area70" <!--{if $arrErr.header}--><!--{sfSetErrorStyle}--><!--{/if}-->><!--{$arrForm.header|escape}--></textarea>
										<!--{if $arrErr.header}--><br><span class="red"><!--{$arrErr.header}--></span><!--{/if}-->
										</td>
									</tr>
									<tr>
										<td bgcolor="#f2f1ec" width="160" class="fs12n">メイン商品タイトル<span class="red"> *</span></td>
										<td bgcolor="#ffffff" width="547" class="fs12n"><input type="text" name="main_title" size="65" class="box65"  <!--{if $arrErr.main_title}--><!--{sfSetErrorStyle}--><!--{/if}--> value="<!--{$arrForm.main_title|escape}-->"/>
										<!--{if $arrErr.main_title}--><br><span class="red"><!--{$arrErr.main_title}--></span><!--{/if}-->
										</td>
									</tr>
									<tr>
										<td bgcolor="#f2f1ec" width="160" class="fs12n">メイン商品コメント<span class="red"> *</span></td>
										<td bgcolor="#ffffff" width="547" class="fs12n"><textarea name="main_comment" cols="70" rows="8" class="area70" <!--{if $arrErr.main_comment}--><!--{sfSetErrorStyle}--><!--{/if}-->><!--{$arrForm.main_comment|escape}--></textarea>
										<!--{if $arrErr.main_comment}--><br><span class="red"><!--{$arrErr.main_comment}--></span><!--{/if}-->
										</td>
									</tr>
									<tr>
										<td bgcolor="#f2f1ec" width="160" class="fs12n">メイン商品選択<span class="red"> *</span><br />
										[110×120]</td>
										<td bgcolor="#ffffff" width="547" class="fs12n">
											<!--{if is_numeric($arrForm.template_id)}-->
												<!--{assign var=image_path value="`$smarty.const.IMAGE_SAVE_URL`/`$arrFileName[0].main_image`"}-->
											<!--{elseif $arrFileName[0].main_image != ""}-->
												<!--{assign var=image_path value="`$smarty.const.IMAGE_SAVE_URL`/`$arrFileName[0].main_image`"}-->
											<!--{else}-->
												<!--{assign var=image_path value="`$smarty.const.NO_IMAGE_URL`"}-->
											<!--{/if}-->
											<img src="<!--{$image_path}-->" width="<!--{$smarty.const.SMALL_IMAGE_WIDTH}-->" height="<!--{$smarty.const.SMALL_IMAGE_HEIGHT}-->" alt="商品画像main" />
											<input type="hidden" name="main_product_id" value="<!--{$arrForm.main_product_id}-->"/>
											　<a href="#" onclick="win03('./htmlmail_select.php?name=main_product_id','select','450','300'); return false;" target="_blank">商品選択</a><br />
											<!--{if $arrErr.main_product_id}--><br><span class="red"><!--{$arrErr.main_product_id}--></span><!--{/if}-->
											<input type="text" name="name_main_product_id" value="<!--{$arrFileName[0].name|escape}-->" disabled="disabled" size="65" class="box65" style="background:#FFF;border-style:solid;border-color:#FFFFFF;"/>
										</td>
									</tr>
									<tr>
										<td bgcolor="#f2f1ec" width="160" class="fs12n">サブ商品群タイトル<span class="red"> *</span></td>
										<td bgcolor="#ffffff" width="547" class="fs12n"><input type="text" name="sub_title" size="65" class="box65" <!--{if $arrErr.sub_title}--><!--{sfSetErrorStyle}--><!--{/if}--> value="<!--{$arrForm.sub_title|escape}-->"/>
										<!--{if $arrErr.sub_title}--><br><span class="red"><!--{$arrErr.sub_title}--></span><!--{/if}-->
										</td>
									</tr>
									<tr>
										<td bgcolor="#f2f1ec" width="160" class="fs12n">サブ商品群コメント<span class="red"> *</span></td>
										<td bgcolor="#ffffff" width="547" class="fs12n"><textarea name="sub_comment" cols="70" rows="8" class="area70" <!--{if $arrErr.sub_comment}--><!--{sfSetErrorStyle}--><!--{/if}-->><!--{$arrForm.sub_comment|escape}--></textarea>
										<!--{if $arrErr.sub_comment}--><br><span class="red"><!--{$arrErr.sub_comment}--></span><!--{/if}-->
										</td>
									</tr>
									<!--{section name=cnt loop=$smarty.const.HTML_TEMPLATE_SUB_MAX}-->
									<!--{assign var=subProductNum value=`$smarty.section.cnt.iteration`}-->
									<tr class="fs12n">
										<td bgcolor="#f2f1ec" width="160">商品画像（<!--{$subProductNum}-->）</td>
										<td bgcolor="#ffffff" width="547">
											<!--{if is_numeric($arrForm.template_id)}-->
												<!--{if strlen($arrFileName[$smarty.section.cnt.iteration].main_image) > 0}--><!--{assign var=image_path value="`$smarty.const.IMAGE_SAVE_URL`/`$arrFileName[$smarty.section.cnt.iteration].main_image`"}--><!--{else}--><!--{assign var=image_path value="`$smarty.const.NO_IMAGE_URL`"}--><!--{/if}-->
											<!--{elseif $arrFileName[$smarty.section.cnt.iteration].main_image != ""}-->
												<!--{assign var=image_path value="`$smarty.const.IMAGE_SAVE_URL`/`$arrFileName[$smarty.section.cnt.iteration].main_image`"}-->
											<!--{else}-->
												<!--{assign var=image_path value="`$smarty.const.NO_IMAGE_URL`"}-->
											<!--{/if}-->
											<!--{if $smarty.section.cnt.iteration <= 9}-->
											<!--{assign var=sub_product_id value="sub_product_id0`$smarty.section.cnt.iteration`"}-->
											<!--{else}-->
											<!--{assign var=sub_product_id value="sub_product_id`$smarty.section.cnt.iteration`"}-->
											<!--{/if}-->
											<img src="<!--{$image_path}-->" width="<!--{$smarty.const.SMALL_IMAGE_WIDTH}-->" height="<!--{$smarty.const.SMALL_IMAGE_HEIGHT}-->" alt="商品画像<!--{$subProductNum}-->" />　
											<input type="hidden" name="<!--{$sub_product_id}-->" value="<!--{$arrFileName[$smarty.section.cnt.iteration].product_id|escape}-->"/>
											　<a href="#" onclick="win03('./htmlmail_select.php?name=<!--{$sub_product_id}-->' ,'select','450','300'); return false;" target="_blank">商品選択</a>
												<!--{assign var=sub_box value="delete_sub`$smarty.section.cnt.iteration`"}-->
											　<!--{if $arrForm[$sub_product_id]}--><input type="checkbox" name="delete_sub<!--{$smarty.section.cnt.iteration}-->" value="1" <!--{if $arrForm[$sub_box] == '1'}-->checked<!--{/if}-->>商品削除<br /><!--{/if}-->
											<input type="text" name="name_sub_product" value="<!--{$arrFileName[$smarty.section.cnt.iteration].name|escape}-->" disabled="disabled"  size="65" class="box65" style="background:#FFF;border-style:solid;border-color:#FFF;"/>
										</td>
									</tr>
									<!--{/section}-->
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
													<input type="image" onMouseover="chgImgImageSubmit('<!--{$smarty.const.URL_DIR}-->img/contents/btn_confirm_on.jpg',this)" onMouseout="chgImgImageSubmit('<!--{$smarty.const.URL_DIR}-->img/contents/btn_confirm.jpg',this)" src="<!--{$smarty.const.URL_DIR}-->img/contents/btn_confirm.jpg" width="123" height="24" alt="確認ページへ" border="0" name="subm" >
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
</form>
</table>
<!--★★メインコンテンツ★★-->
