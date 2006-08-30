<!--★★メインコンテンツ★★-->
<table width="878" border="0" cellspacing="0" cellpadding="0" summary=" ">
<form name="form1" id="form1" method="post" action="<!--{$smarty.server.PHP_SELF}-->" onsubmit="return lfnModeSubmit('confirm')">
<!--{foreach from=$smarty.post key="key" item="item"}-->
<!--{if $key ne "mode"}--><input type="hidden" name="<!--{$key|escape}-->" value="<!--{$item|escape}-->">
<!--{/if}-->
<!--{/foreach}-->
<input type="hidden" name="mode" value="">
<input type="hidden" name="tpl_subno_template" value="<!--{$tpl_subno_template}-->">
	<tr valign="top">
		<td background="/img/contents/navi_bg.gif" height="402">
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
										<td bgcolor="#636469" width="638" class="fs14n"><span class="white"><!--コンテンツタイトル-->デザインテンプレート選択</span></td>
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
									<tr class="fs12n">
										<td bgcolor="#f2f1ec" align="center"><strong>現在のテンプレート</strong></td>
									</tr>
									<tr class="fs12n">
										<td bgcolor="#ffffff" align="center"><img height=500 weight=500 src=<!--{$arrTemplate.image[$MainImage]}--> name="main_img" ></td>
									</tr>
								</table>

								<table width="678" border="0" cellspacing="0" cellpadding="0" summary=" ">
									<tr><td colspan="3"><img src="/img/contents/main_bar.jpg" width="678" height="10" alt=""></td></tr>
								</table>
								
								<table width="678" border="0" cellspacing="1" cellpadding="8" summary=" ">
									<tr class="fs12n">
										<td bgcolor="#f2f1ec" align="center" colspan="3"><strong>テンプレート一覧</strong></td>
									</tr>
									
									<!--{section name=template loop=$arrTemplate.image step=3}-->
									<!--{*foreach key=key item=item from=$arrTemplate.image*}-->
										<tr class="fs10n">
										<!--{assign var=key value=$smarty.section.template.iteration}-->
										<!--{assign var=item value=$arrTemplate.image[template]}-->
										<td bgcolor="#ffffff" align="center"><!--{$arrTemplate[template]}-->
											<label for="radio<!--{$key}-->"><img height=200 weight=200 src="<!--{$item}-->" name="1"></label><br>
											<label for="radio<!--{$key}-->"><input type="radio" name="check_template" value=<!--{$key}--> id="radio<!--{$key}-->" onClick="ChangeImage('<!--{$item}-->');" <!--{if $arrTemplate.check[$key] != ""}-->checked<!--{/if}-->>選択</label>
										</td>
										<!--{assign var=key value=$smarty.section.template.step*$smarty.section.template.iteration}-->
										<!--{assign var=item value=$arrTemplate.image[$key]}-->
										<td bgcolor="#ffffff" align="center"><!--{$arrTemplate[template]}-->
											<label for="radio<!--{$key}-->"><img height=200 weight=200 src="<!--{$item}-->" name="1"></label><br>
											<label for="radio<!--{$key}-->"><input type="radio" name="check_template" value=<!--{$key}--> id="radio<!--{$key}-->" onClick="ChangeImage('<!--{$item}-->');" <!--{if $arrTemplate.check[$key] != ""}-->checked<!--{/if}-->>選択</label>
										</td>
										<!--{assign var=key value=$smarty.section.template.iteration+2}-->
										<!--{assign var=item value=$arrTemplate.image[$key]}-->
										<td bgcolor="#ffffff" align="center"><!--{$arrTemplate[template]}-->
											<label for="radio<!--{$key}-->"><img height=200 weight=200 src="<!--{$item}-->" name="1"></label><br>
											<label for="radio<!--{$key}-->"><input type="radio" name="check_template" value=<!--{$key}--> id="radio<!--{$key}-->" onClick="ChangeImage('<!--{$item}-->');" <!--{if $arrTemplate.check[$key] != ""}-->checked<!--{/if}-->>選択</label>
										</td>
										
										</tr>
									<!--{/section}-->
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
												<td><input type="image" onMouseover="chgImgImageSubmit('/img/contents/btn_regist_on.jpg',this)" onMouseout="chgImgImageSubmit('/img/contents/btn_regist.jpg',this)" src="/img/contents/btn_regist.jpg" width="123" height="24" alt="この内容で登録する" border="0" name="subm"></td>
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

<script type="text/javascript">
function ChangeImage(strUrl)
{
	document.main_img.src=strUrl;
}

// モードとキーを指定してSUBMITを行う。
function lfnModeSubmit(mode) {
	if(!window.confirm('登録しても宜しいですか?')){
		return false;
	}
	document.form1['mode'].value = mode;
	return true;
}

<!--[if IE]>
window.onload=function(){
	var lbs = document.getElementsByTagName('label');
	for(var i=0;i<lbs.length;i++){
		var cimgs = lbs[i].getElementsByTagName('img');
		for(var j=0;j<cimgs.length;j++){
			cimgs[j].formCtrlId = lbs[i].htmlFor;
			cimgs[j].onclick = function(){document.getElementById(this.formCtrlId).click()};
		}
	}
}
<![endif]-->

</script>
