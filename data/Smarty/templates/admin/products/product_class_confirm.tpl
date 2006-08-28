<!--¡ú¡ú¥á¥¤¥ó¥³¥ó¥Æ¥ó¥Ä¡ú¡ú-->
<table width="878" border="0" cellspacing="0" cellpadding="0" summary=" ">
	<tr valign="top">
		<td background="/img/contents/navi_bg.gif" height="402">
			<!--¢§SUB NAVI-->
			<!--{include file=$tpl_subnavi}-->
			<!--¢¥SUB NAVI-->
		</td>
		<td class="mainbg">
			<!--¢§ÅÐÏ¿¥Æ¡¼¥Ö¥ë¤³¤³¤«¤é-->
			<table width="737" border="0" cellspacing="0" cellpadding="0" summary=" ">
				<!--¥á¥¤¥ó¥¨¥ê¥¢-->
				<tr>
					<td align="center">
						<table width="706" border="0" cellspacing="0" cellpadding="0" summary=" ">
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
										<td bgcolor="#636469" width="638" class="fs14n"><span class="white"><!--¥³¥ó¥Æ¥ó¥Ä¥¿¥¤¥È¥ë-->µ¬³ÊÅÐÏ¿</span></td>
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
								<!--{if $tpl_check > 0}-->
									<!--{assign var=class_id1 value=$arrForm.class_id1}-->
									<!--{assign var=class_id2 value=$arrForm.class_id2}-->
									<tr bgcolor="#f2f1ec" align="center" class="fs12n">
										<td width="150">µ¬³Ê1(<!--{$arrClass[$class_id1]|default:"Ì¤ÁªÂò"}-->)</td>
										<td width="150">µ¬³Ê2(<!--{$arrClass[$class_id2]|default:"Ì¤ÁªÂò"}-->)</td>
										<td width="100">¾¦ÉÊ¥³¡¼¥É</td>
										<td width="80">ºß¸Ë(¸Ä)</td>
										<td width="120">»²¹Í»Ô¾ì²Á³Ê(±ß)</td>
										<td width="100">²Á³Ê(±ß)</td>
									</tr>
									<!--{section name=cnt loop=$tpl_count}-->
									<!--{assign var=key value="check:`$smarty.section.cnt.iteration`"}-->
									<!--{if $arrForm[$key] == 1}-->
									<tr  bgcolor="#ffffff" class="fs12">
										<!--{assign var=key value="name1:`$smarty.section.cnt.iteration`"}-->
										<td width="150"><!--{$arrForm[$key]}--></td>
										<!--{assign var=key value="name2:`$smarty.section.cnt.iteration`"}-->
										<td width="150"><!--{$arrForm[$key]}--></td>
										<!--{assign var=key value="product_code:`$smarty.section.cnt.iteration`"}-->
										<td width="100" align="right"><!--{$arrForm[$key]}--></td>
										<!--{assign var=key1 value="stock:`$smarty.section.cnt.iteration`"}-->
										<!--{assign var=key2 value="stock_unlimited:`$smarty.section.cnt.iteration`"}-->
										<td width="80" align="right">
										<!--{if $arrForm[$key2] == 1}-->
											ÌµÀ©¸Â
										<!--{else}-->
											<!--{$arrForm[$key1]}-->
										<!--{/if}-->
										</td>
										<!--{assign var=key value="price01:`$smarty.section.cnt.iteration`"}-->
										<td width="120" align="right"><!--{$arrForm[$key]}--></td>
										<!--{assign var=key value="price02:`$smarty.section.cnt.iteration`"}-->
										<td width="100" align="right"><!--{$arrForm[$key]}--></td>
									</tr>
									<!--{/if}-->
									<!--{/section}-->
								<!--{else}-->
									<tr bgcolor="#ffffff" class="fs12" align="center"><td>µ¬³Ê¤¬ÁªÂò¤µ¤ì¤Æ¤¤¤Þ¤»¤ó¡£</td></tr>
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
												<td><a href="#" onmouseover="chgImg('/img/install/back_on.jpg','return')" onmouseout="chgImg('/img/install/back.jpg','return')" onclick="fnModeSubmit('confirm_return','',''); return false" /><img  width="105" src="/img/install/back.jpg"  height="24" alt="Á°¤ØÌá¤ë" border="0" name="return"></a>
												<!--{if $tpl_check > 0}-->
												<input type="image" onMouseover="chgImgImageSubmit('/img/contents/btn_regist_on.jpg',this)" onMouseout="chgImgImageSubmit('/img/contents/btn_regist.jpg',this)" src="/img/contents/btn_regist.jpg" width="123" height="24" alt="¤³¤ÎÆâÍÆ¤ÇÅÐÏ¿¤¹¤ë" border="0" name="subm" >
												<!--{/if}-->
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
					</td>
				</tr>
				<!--¥á¥¤¥ó¥¨¥ê¥¢-->
			</table>
			<!--¢¥ÅÐÏ¿¥Æ¡¼¥Ö¥ë¤³¤³¤Þ¤Ç-->
		</td>
	</tr>
</form>
</table>
<!--¡ú¡ú¥á¥¤¥ó¥³¥ó¥Æ¥ó¥Ä¡ú¡ú-->								
