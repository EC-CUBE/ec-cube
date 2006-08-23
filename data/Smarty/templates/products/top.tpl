<!--¢§CONTENTS-->
<table width="780" cellspacing="0" cellpadding="0" summary=" ">
	<tr>
		<td bgcolor="#cccccc" width="1"><img src="../img/_.gif" width="1" height="10" alt="" /></td>
		<td bgcolor="#ffffff" width="9"><img src="../img/_.gif" width="9" height="1" alt="" /></td>
		<td bgcolor="#ffffff" align="left"> 
		<!--¢§MAIN CONTENTS-->
		<!--¥Ñ¥ó¥¯¥º-->
			<!--{include_php file=$tpl_pankuzu_php}-->
		<!--¥Ñ¥ó¥¯¥º-->
		<table cellspacing="0" cellpadding="0" summary=" " id="container">
			<tr><td height="10"></td></tr>
			<tr valign="top">
				<!--¢§LEFT CONTENTS-->

				<td id="left">
				<!--¢§¥Ð¥Ê¡¼--><!--{include file=$tpl_banner}--><!--¢¥¥Ð¥Ê¡¼-->
				
				<!--¢§¾¦ÉÊ¸¡º÷-->
					<!--{include_php file=$tpl_search_products_php}-->
				<!--¢¥¾¦ÉÊ¸¡º÷-->
				
				<!--¢§¥«¥Æ¥´¥ê-->
					<!--{include_php file=$tpl_category_php}-->
				<!--¢¥¥«¥Æ¥´¥ê-->
				
				<!--¢§º¸¥Ê¥Ó-->
					<!--{include file=$tpl_leftnavi}-->
				<!--¢¥º¸¥Ê¥Ó-->
			
				</td>
				<!--¢¥LEFT CONTENTS-->
				
				<!--¢§RIGHT CONTENTS-->
				<td id="right">
				
				<!--{* ¾®¸«½Ð¤·²èÁü *}-->
					<!--{include file=$tpl_maintitle}-->
				<!--{* ¾®¸«½Ð¤·²èÁü *}-->
				
				<!--{if count($arrBestItems) > 0}-->
				<table cellspacing="0" cellpadding="0" summary=" " id="bestone">
					<!--¢§Çä¤ì¶Ú1-->
					<tr valign="top">
						<!--{assign var=image_path value="`$smarty.const.IMAGE_SAVE_URL`/`$arrBestItems[0].main_image`"}-->
						<td id="left"><div id="picture"><a href="<!--{$smarty.const.DETAIL_P_HTML}--><!--{$arrBestItems[0].product_id}-->.html"><!--¾¦ÉÊ¼Ì¿¿--><img src="<!--{$image_path}-->" width="<!--{$smarty.const.NORMAL_IMAGE_WIDTH}-->" height="<!--{$smarty.const.NORMAL_IMAGE_HEIGHT}-->" alt="<!--{$arrBestItems[0].name|escape}-->" /></a></div></td>
						<td id="spacer"></td>
						<td id="right">
						<table cellspacing="0" cellpadding="0" summary=" " id="contents">
							<tr>
								<td><img src="../img/right_product/best01.gif" width="129" height="31" alt="Çä¤ì¶ÚBEST1" /></td>
							</tr>
							<tr><td height="6"></td></tr>
						</table>

						<div id="title"><span class="fs14st"><a href="<!--{$smarty.const.DETAIL_P_HTML}--><!--{$arrBestItems[0].product_id}-->.html"><!--¾¦ÉÊÌ¾--><!--{$arrBestItems[0].name|escape}--></a></span></div>
						<table cellspacing="0" cellpadding="0" summary=" " id="contents">
							<tr><td height="6"></td></tr>
							<tr>
								<td class="fs12"><!--°ìÍ÷¥á¥¤¥ó¥³¥á¥ó¥È--><!--{$arrBestItems[0].comment|escape|nl2br}--></td>
							</tr>
							<tr><td height="6"></td></tr>
							<tr>

								<td><span class="fs10">¥È¡¼¥«Æ²²Á³Ê¡§</span><span class="red12st">
									<!--{if $arrBestItems[0].price02_min == $arrBestItems[0].price02_max}-->				
										<!--{$arrBestItems[0].price02_min|sfPreTax:$arrInfo.tax:$arrInfo.tax_rule|number_format}-->
									<!--{else}-->
										<!--{$arrBestItems[0].price02_min|sfPreTax:$arrInfo.tax:$arrInfo.tax_rule|number_format}-->¡Á<!--{$arrBestItems[0].price02_max|sfPreTax:$arrInfo.tax:$arrInfo.tax_rule|number_format}-->
									<!--{/if}-->±ß
								</span><span class="red10">¡ÊÀÇ¹þ¡Ë</span><br />
								<span class="fs10">
								<!--{if $arrBestItems[0].price01_min > 0 || $arrBestItems[0].price01_max > 0}-->
								»²¹Í»Ô¾ì²Á³Ê¡§
									<!--{if $arrBestItems[0].price01_min == $arrBestItems[0].price01_max}-->				
										<!--{$arrBestItems[0].price01_min|number_format}-->
									<!--{else}-->
										<!--{$arrBestItems[0].price01_min|number_format}-->¡Á<!--{$arrBestItems[0].price01_max|number_format}-->
									<!--{/if}-->
								±ß<br />
								<!--{/if}-->
								¥Ý¥¤¥ó¥È¡§</span><span class="red12st">
									<!--{if $arrBestItems[0].price02_min == $arrBestItems[0].price02_max}-->				
										<!--{$arrBestItems[0].price02_min|sfPrePoint:$arrBestItems[0].point_rate:$smarty.const.POINT_RULE:$arrBestItems[0].product_id}-->
									<!--{else}-->
										<!--{if $arrBestItems[0].price02_min|sfPrePoint:$arrBestItems[0].point_rate:$smarty.const.POINT_RULE:$arrBestItems[0].product_id == $arrBestItems[0].price02_max|sfPrePoint:$arrBestItems[0].point_rate:$smarty.const.POINT_RULE:$arrBestItems[0].product_id}-->
											<!--{$arrBestItems[0].price02_min|sfPrePoint:$arrBestItems[0].point_rate:$smarty.const.POINT_RULE:$arrBestItems[0].product_id}-->
										<!--{else}-->	
											<!--{$arrBestItems[0].price02_min|sfPrePoint:$arrBestItems[0].point_rate:$smarty.const.POINT_RULE:$arrBestItems[0].product_id}-->¡Á<!--{$arrBestItems[0].price02_max|sfPrePoint:$arrBestItems[0].point_rate:$smarty.const.POINT_RULE:$arrBestItems[0].product_id}-->
										<!--{/if}-->
									<!--{/if}-->
								</span><span class="red10">Pt</span></td>
							</tr>
							<tr><td height="5"></td></tr>

							<tr>
								<td><a href="<!--{$smarty.const.DETAIL_P_HTML}--><!--{$arrBestItems[0].product_id}-->.html" onmouseover="chgImg('../img/right_product/detail_on.gif','detail1');" onmouseout="chgImg('../img/right_product/detail.gif','detail1');"><img src="../img/right_product/detail.gif" width="110" height="22" alt="¾¦ÉÊ¤ò¾Ü¤·¤¯¸«¤ë" name="detail1" id="detail1" /></a></td>
							</tr>
						</table>
						</td>
					</tr>
					<!--¢¥Çä¤ì¶Ú1-->
				</table>
				
				<table cellspacing="0" cellpadding="0" summary=" " id="contents">
					<!--{section name=cnt loop=$arrBestItems step=2 start=1 max=$BEST_ROOP_MAX}-->
					<tr>
						<td height="30"><img src="../img/right_product/best_line.gif" width="277" height="1" alt="" /></td>
						<td></td>
						<td><img src="../img/right_product/best_line.gif" width="277" height="1" alt="" /></td>
					</tr>
					<tr valign="top">
						<td id="left">
						<!--¢§Çä¤ì¶Ú<!--{$smarty.section.cnt.index+1}-->-->
						<table cellspacing="0" cellpadding="0" summary=" " id="besttwo">
							<!--{assign var=image_path value="`$smarty.const.IMAGE_SAVE_URL`/`$arrBestItems[cnt].main_list_image`"}-->
							<tr>
								<td colspan="3"><img src="../img/right_product/best0<!--{$smarty.section.cnt.index+1}-->.gif" width="117" height="19" alt="Çä¤ì¶ÚBEST<!--{$smarty.section.cnt.index+1}-->" /></td>
							</tr>
							<tr><td height="6"></td></tr>
							<tr valign="top">
								<td id="left"><div id="picture"><a href="<!--{$smarty.const.DETAIL_P_HTML}--><!--{$arrBestItems[cnt].product_id}-->.html"><!--¾¦ÉÊ¼Ì¿¿--><img src="<!--{$image_path}-->" width="<!--{$smarty.const.SMALL_IMAGE_WIDTH}-->" height="<!--{$smarty.const.SMALL_IMAGE_HEIGHT}-->" alt="<!--{$arrBestItems[cnt].name|escape}-->" /></a></div></td>
								<td id="spacer"></td>
								<td id="right"><div id="title"><span class="fs12st"><a href="<!--{$smarty.const.DETAIL_P_HTML}--><!--{$arrBestItems[cnt].product_id}-->.html"><!--¾¦ÉÊÌ¾--><!--{$arrBestItems[cnt].name|escape}--></a></span></div></td>

							</tr>
							<tr><td height="10"></td></tr>
							<tr>
								<td colspan="3" class="fs12"><!--°ìÍ÷¥á¥¤¥ó¥³¥á¥ó¥È--><!--{$arrBestItems[cnt].comment|escape|nl2br}--></td>
							</tr>
							<tr><td height="5"></td></tr>
							<tr>
								<td colspan="3"><span class="fs10">¥È¡¼¥«Æ²²Á³Ê¡§</span>
								<span class="red12st">
									<!--{if $arrBestItems[cnt].price02_min == $arrBestItems[cnt].price02_max}-->				
										<!--{$arrBestItems[cnt].price02_min|sfPreTax:$arrInfo.tax:$arrInfo.tax_rule|number_format}-->
									<!--{else}-->
										<!--{$arrBestItems[cnt].price02_min|sfPreTax:$arrInfo.tax:$arrInfo.tax_rule|number_format}-->¡Á<!--{$arrBestItems[cnt].price02_max|sfPreTax:$arrInfo.tax:$arrInfo.tax_rule|number_format}-->
									<!--{/if}-->±ß
								</span><span class="red10">¡ÊÀÇ¹þ¡Ë</span><br />
								<span class="fs10">
								<!--{if $arrBestItems[cnt].price01_min > 0 || $arrBestItems[cnt].price01_max > 0}-->
									»²¹Í»Ô¾ì²Á³Ê¡§
									<!--{if $arrBestItems[cnt].price01_min == $arrBestItems[cnt].price01_max}-->				
										<!--{$arrBestItems[cnt].price01_min|number_format}-->
									<!--{else}-->
										<!--{$arrBestItems[cnt].price01_min|number_format}-->¡Á<!--{$arrBestItems[cnt].price01_max|number_format}-->
									<!--{/if}-->
								±ß<br />
								<!--{/if}-->
								¥Ý¥¤¥ó¥È¡§</span><span class="red12st">
									<!--{if $arrBestItems[cnt].price02_min == $arrBestItems[cnt].price02_max}-->				
										<!--{$arrBestItems[cnt].price02_min|sfPrePoint:$arrBestItems[cnt].point_rate:$smarty.const.POINT_RULE:$arrBestItems[cnt].product_id}-->
									<!--{else}-->
										<!--{if $arrBestItems[cnt].price02_min|sfPrePoint:$arrBestItems[cnt].point_rate:$smarty.const.POINT_RULE:$arrBestItems[cnt].product_id == $arrBestItems[cnt].price02_max|sfPrePoint:$arrBestItems[cnt].point_rate:$smarty.const.POINT_RULE:$arrBestItems[cnt].product_id}-->
											<!--{$arrBestItems[cnt].price02_min|sfPrePoint:$arrBestItems[cnt].point_rate:$smarty.const.POINT_RULE:$arrBestItems[cnt].product_id}-->
										<!--{else}-->
											<!--{$arrBestItems[cnt].price02_min|sfPrePoint:$arrBestItems[cnt].point_rate:$smarty.const.POINT_RULE:$arrBestItems[cnt].product_id}-->¡Á<!--{$arrBestItems[cnt].price02_max|sfPrePoint:$arrBestItems[cnt].point_rate:$smarty.const.POINT_RULE:$arrBestItems[cnt].product_id}-->
										<!--{/if}-->
									<!--{/if}-->
								</span><span class="red10">Pt</span></td>
							</tr>
							<tr><td height="5"></td></tr>
							<tr>
								<td colspan="3"><a href="<!--{$smarty.const.DETAIL_P_HTML}--><!--{$arrBestItems[cnt].product_id}-->.html" onmouseover="chgImg('../img/right_product/detail_on.gif','detail<!--{$smarty.section.cnt.index+1}-->');" onmouseout="chgImg('../img/right_product/detail.gif','detail<!--{$smarty.section.cnt.index+1}-->');"><img src="../img/right_product/detail.gif" width="110" height="22" alt="¾¦ÉÊ¤ò¾Ü¤·¤¯¸«¤ë" name="detail<!--{$smarty.section.cnt.index+1}-->" id="detail<!--{$smarty.section.cnt.index+1}-->" /></a></td>
							</tr>

						</table>
						<!--¢¥Çä¤ì¶Ú<!--{$smarty.section.cnt.index+1}-->-->
						</td>
						<td id="spacer"></td>
						<!--{assign var=nextCnt value=$smarty.section.cnt.index+1}-->
						<td id="right">
						<!--{if $arrBestItems[$nextCnt].product_id}-->
						<!--¢§Çä¤ì¶Ú<!--{$smarty.section.cnt.index+2}-->-->
						<table cellspacing="0" cellpadding="0" summary=" " id="besttwo">
							<!--{assign var=image_path value="`$smarty.const.IMAGE_SAVE_URL`/`$arrBestItems[$nextCnt].main_list_image`"}-->
							<tr>
								<td colspan="3"><img src="../img/right_product/best0<!--{$smarty.section.cnt.index+2}-->.gif" width="117" height="19" alt="Çä¤ì¶ÚBEST<!--{$smarty.section.cnt.index+2}-->" /></td>
							</tr>
							<tr><td height="6"></td></tr>
							<tr valign="top">
								<td id="left"><div id="picture"><a href="<!--{$smarty.const.DETAIL_P_HTML}--><!--{$arrBestItems[$nextCnt].product_id}-->.html"><!--¾¦ÉÊ¼Ì¿¿--><img src="<!--{$image_path}-->" width="<!--{$smarty.const.SMALL_IMAGE_WIDTH}-->" height="<!--{$smarty.const.SMALL_IMAGE_HEIGHT}-->" alt="<!--{$arrBestItems[$nextCnt].name|escape}-->" /></a></div></td>
								<td id="spacer"></td>
								<td id="right"><div id="title"><span class="fs12st"><a href="<!--{$smarty.const.DETAIL_P_HTML}--><!--{$arrBestItems[$nextCnt].product_id}-->.html"><!--¾¦ÉÊÌ¾--><!--{$arrBestItems[$nextCnt].name|escape}--></a></span></div></td>

							</tr>
							<tr><td height="10"></td></tr>
							<tr>
								<td colspan="3" class="fs12"><!--°ìÍ÷¥á¥¤¥ó¥³¥á¥ó¥È--><!--{$arrBestItems[$nextCnt].comment|escape|nl2br}--></td>
							</tr>
							<tr><td height="5"></td></tr>
							<tr>
								<td colspan="3"><span class="fs10">¥È¡¼¥«Æ²²Á³Ê¡§</span>
								<span class="red12st">
									<!--{if $arrBestItems[$nextCnt].price02_min == $arrBestItems[$nextCnt].price02_max}-->				
										<!--{$arrBestItems[$nextCnt].price02_min|sfPreTax:$arrInfo.tax:$arrInfo.tax_rule|number_format}-->
									<!--{else}-->
										<!--{$arrBestItems[$nextCnt].price02_min|sfPreTax:$arrInfo.tax:$arrInfo.tax_rule|number_format}-->¡Á<!--{$arrBestItems[$nextCnt].price02_max|sfPreTax:$arrInfo.tax:$arrInfo.tax_rule|number_format}-->
									<!--{/if}-->±ß
								</span><span class="red10">¡ÊÀÇ¹þ¡Ë</span><br />
								<span class="fs10">»²¹Í»Ô¾ì²Á³Ê¡§
								<!--{if $arrBestItems[$nextCnt].price01_min == $arrBestItems[$nextCnt].price01_max}-->				
									<!--{$arrBestItems[$nextCnt].price01_min|number_format}-->
								<!--{else}-->
									<!--{$arrBestItems[$nextCnt].price01_min|number_format}-->¡Á<!--{$arrBestItems[$nextCnt].price01_max|number_format}-->
								<!--{/if}-->
								±ß<br />
								¥Ý¥¤¥ó¥È¡§</span><span class="red12st">
									<!--{if $arrBestItems[$nextCnt].price02_min == $arrBestItems[$nextCnt].price02_max}-->				
										<!--{$arrBestItems[$nextCnt].price02_min|sfPrePoint:$arrBestItems[$nextCnt].point_rate:$smarty.const.POINT_RULE:$arrBestItems[$nextCnt].product_id}-->
									<!--{else}-->
										<!--{if $arrBestItems[$nextCnt].price02_min|sfPrePoint:$arrBestItems[$nextCnt].point_rate:$smarty.const.POINT_RULE:$arrBestItems[$nextCnt].product_id == $arrBestItems[$nextCnt].price02_max|sfPrePoint:$arrBestItems[$nextCnt].point_rate:$smarty.const.POINT_RULE:$arrBestItems[$nextCnt].product_id}-->
											<!--{$arrBestItems[$nextCnt].price02_min|sfPrePoint:$arrBestItems[$nextCnt].point_rate:$smarty.const.POINT_RULE:$arrBestItems[$nextCnt].product_id}-->
										<!--{else}-->
											<!--{$arrBestItems[$nextCnt].price02_min|sfPrePoint:$arrBestItems[$nextCnt].point_rate:$smarty.const.POINT_RULE:$arrBestItems[$nextCnt].product_id}-->¡Á<!--{$arrBestItems[$nextCnt].price02_max|sfPrePoint:$arrBestItems[$nextCnt].point_rate:$smarty.const.POINT_RULE:$arrBestItems[$nextCnt].product_id}-->
										<!--{/if}-->
									<!--{/if}-->
								</span><span class="red10">Pt</span></td>
							</tr>
							<tr><td height="5"></td></tr>
							<tr>
								<td colspan="3"><a href="<!--{$smarty.const.DETAIL_P_HTML}--><!--{$arrBestItems[$nextCnt].product_id}-->.html" onmouseover="chgImg('../img/right_product/detail_on.gif','detail<!--{$smarty.section.cnt.index+2}-->');" onmouseout="chgImg('../img/right_product/detail.gif','detail<!--{$smarty.section.cnt.index+2}-->');"><img src="../img/right_product/detail.gif" width="110" height="22" alt="¾¦ÉÊ¤ò¾Ü¤·¤¯¸«¤ë" name="detail<!--{$smarty.section.cnt.index+2}-->" id="detail<!--{$smarty.section.cnt.index+2}-->" /></a></td>
							</tr>
						</table>
						<!--¢¥Çä¤ì¶Ú<!--{$smarty.section.cnt.index+2}-->-->
						<!--{/if}-->
						</td>
					</tr>
					<!--{/section}-->
				</table>
				<!--{else}-->
				<span class="fs12n">Çä¤ì¶Ú¾¦ÉÊ¤Ï¡¢ÅÐÏ¿¤µ¤ì¤Æ¤¤¤Þ¤»¤ó¡£</span>
				<!--{/if}-->
				
				</td>
				<!--¢¥RIGHT CONTENTS-->
			</tr>
				<tr>
					<td bgcolor="#ffffff">
					<!-- EBiS start -->
					<script type="text/javascript">
					if ( location.protocol == 'http:' ){ 
						strServerName = 'http://daikoku.ebis.ne.jp'; 
					} else { 
						strServerName = 'https://secure2.ebis.ne.jp/ver3';
					}
					cid = 'tqYg3k6U'; pid = 'list-c<!--{$category_id}-->'; m1id=''; a1id=''; o1id=''; o2id=''; o3id=''; o4id=''; o5id='';
					document.write("<scr" + "ipt type=\"text\/javascript\" src=\"" + strServerName + "\/ebis_tag.php?cid=" + cid + "&pid=" + pid + "&m1id=" + m1id + "&a1id=" + a1id + "&o1id=" + o1id + "&o2id=" + o2id + "&o3id=" + o3id + "&o4id=" + o4id + "&o5id=" + o5id + "\"><\/scr" + "ipt>");
					</script>
					<!-- EBiS end -->
					</td>
				</tr>
		</table>
		<!--¢¥MAIN CONTENTS-->
		</td>
		<td bgcolor="#ffffff" width="10"><img src="../img/_.gif" width="9" height="1" alt="" /></td>
		<td bgcolor="#cccccc" width="1"><img src="../img/_.gif" width="1" height="10" alt="" /></td>
	</tr>
</table>
<!--¢¥CONTENTS-->
