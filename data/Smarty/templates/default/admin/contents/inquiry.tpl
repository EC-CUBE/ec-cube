<!--{*
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
*}-->
<script type="text/javascript">
<!--
	function func_check() {
		res = confirm('登録します。宜しいですか？');
		if( res == true ) {
			return true;
		}
		return false;
	}
		
		
	function func_disp( no ){

		ml = document.form1.elements['question[' + no + '][kind]'];
		len = ml.length;

   		var flag = 0;
		
		for( i = 0; i < len ; i++) {
			
    		td = document.getElementById("TD" + no);
    				
	    	if ( ml[i].checked ){
	    		if ( (ml[i].value == 3) || (ml[i].value == 4) ) {
	    			td.style.display = 'block';
	    		} else {
		    		td.style.display = 'none';
	    		}
				flag = 1;
	    	} 
		
		}

		if ( flag == 0 ){
			td.style.display = 'none';
		}
		
	}
	
	function delete_check() {
		res = confirm('アンケートを削除しても宜しいですか？');
		if(res == true) {
			return true;
		}
		return false;
	}
// -->
</script>

<!--★★メインコンテンツ★★-->
<table width="878" border="0" cellspacing="0" cellpadding="0" summary=" ">
	<tr valign="top">
		<td background="<!--{$TPL_DIR}-->img/contents/navi_bg.gif" height="402">
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
						<td colspan="3"><img src="<!--{$TPL_DIR}-->img/contents/main_top.jpg" width="706" height="14" alt=""></td>
					</tr>
					<tr>
						<td background="<!--{$TPL_DIR}-->img/contents/main_left.jpg"><img src="<!--{$TPL_DIR}-->img/common/_.gif" width="14" height="1" alt=""></td>
						<td bgcolor="#cccccc">
						
						<!--登録テーブルここから-->
						<table width="678" border="0" cellspacing="0" cellpadding="0" summary=" ">
							<tr>
								<td colspan="3"><img src="<!--{$TPL_DIR}-->img/contents/contents_title_top.gif" width="678" height="7" alt=""></td>
							</tr>
							<tr>
								<td background="<!--{$TPL_DIR}-->img/contents/contents_title_left_bg.gif"><img src="<!--{$TPL_DIR}-->img/contents/contents_title_left.gif" width="22" height="12" alt=""></td>
								<td bgcolor="#636469" width="638" class="fs14n"><span class="white"><!--コンテンツタイトル--><!--{if $QUESTION_ID}-->修正<!--{else}-->新規<!--{/if}-->登録</span></td>
								<td background="<!--{$TPL_DIR}-->img/contents/contents_title_right_bg.gif"><img src="<!--{$TPL_DIR}-->img/common/_.gif" width="18" height="1" alt=""></td>
							</tr>
							<tr>
								<td colspan="3"><img src="<!--{$TPL_DIR}-->img/contents/contents_title_bottom.gif" width="678" height="7" alt=""></td>
							</tr>
							<tr>
								<td colspan="3"><img src="<!--{$TPL_DIR}-->img/contents/main_bar.jpg" width="678" height="10" alt=""></td>
							</tr>
						</table>
						
						<!--▼FORM-->
						<table width="678" border="0" cellspacing="1" cellpadding="8" summary=" ">
						<form name="form1" method="post" action="<!--{$smarty.server.PHP_SELF|escape}-->?mode=regist" onSubmit="return func_check(); false;">
						<input type="hidden" name="question_id" value="<!--{$QUESTION_ID}-->">
							
							<!--{if $MESSAGE != ""}-->
							<tr>
								<td height="20" class="fs14n">
									<span class="red"><!--{$MESSAGE}--></span>
								</td>
							</tr>
							<!--{/if}-->
							<tr class="fs12n">
								<td width="140" bgcolor="#f2f1ec">稼働・非稼働</td>
								<td width="637" bgcolor="#ffffff">
								<span <!--{if $ERROR.active}--><!--{sfSetErrorStyle}--><!--{/if}-->>
								<!--{html_radios name="active" options=$arrActive selected=$smarty.post.active}-->
								</span>
								<!--{if $ERROR.active}--><br><span class="red"><!--{$ERROR.active}--></span><!--{/if}-->
								</td>
							</tr>
							<tr class="fs12n">
								<td width="140" bgcolor="#f2f1ec">アンケートタイトル<span class="red">*</span></td>
								<td width="637" bgcolor="#ffffff"><input type="text" name="title" size="70" class="box70"  maxlength="<!--{$smarty.const.STEXT_LEN}-->" value="<!--{$smarty.post.title|escape}-->" <!--{if $ERROR.title}--><!--{sfSetErrorStyle}--><!--{/if}-->>
									<!--{if $ERROR.title}--><br><span class="red"><!--{$ERROR.title}--></span><!--{/if}-->
								</td>
							</tr>
								<tr class="fs12n">
								<td width="140" bgcolor="#f2f1ec">アンケート内容</td>
								<td width="637" bgcolor="#ffffff"><textarea name="contents" cols="60" rows="4" class="area60" wrap="physical" <!--{if $ERROR.contents}--><!--{sfSetErrorStyle}--><!--{/if}-->><!--{$smarty.post.contents}--></textarea>
								<!--{if $ERROR.contents}--><br><span class="red"><!--{$ERROR.contents}--></span><!--{/if}--></td>
							</tr>		
							<!--{section name=question loop=$cnt_question}-->
							<tr class="fs12n">
								<td width="140" bgcolor="#f2f1ec">質問<!--{if $smarty.section.question.iteration eq 1}--><span class="red">*</span><!--{/if}--><!--{$smarty.section.question.iteration}--></td>
								<td width="637" bgcolor="#ffffff">
								<input type="text" name="question[<!--{$smarty.section.question.index}-->][name]" size="70" class="box70" maxlength="<!--{$smarty.const.STEXT_LEN}-->" value="<!--{$smarty.post.question[$smarty.section.question.index].name|escape}-->" <!--{if $ERROR.question[$smarty.section.question.index].name}--><!--{sfSetErrorStyle}--><!--{/if}-->>
								<!--{if $ERROR.question[$smarty.section.question.index].name}--><br><span class="red"><!--{$ERROR.question[$smarty.section.question.index].name}--></span><!--{/if}-->
								</td>
							</tr>
							<tr class="fs12n" bgcolor="#ffffff">
								<td colspan="2">
								<span style=background-color:"<!--{$ERROR_COLOR.question[$smarty.section.question.index].kind}-->">
								<!--{html_radios_ex onClick="func_disp(`$smarty.section.question.index`)" name="question[`$smarty.section.question.index`][kind]" options="$arrQuestion" selected="`$smarty.post.question[$smarty.section.question.index].kind`"}-->
								</span>
								<!--{if $ERROR.question[$smarty.section.question.index].kind}--><br><span class="red"><!--{$ERROR.question[$smarty.section.question.index].kind}--></span><!--{/if}-->
								</td>
							</tr>
							<tr class="fs12n" bgcolor="#ffffff"><td colspan="2">
								<table id="TD<!--{$smarty.section.question.index}-->">
								<tr class="fs12n" bgcolor="#ffffff">
									<td colspan="2">1 <input type="text" name="question[<!--{$smarty.section.question.index}-->][option][0]" size="40" class="box40" maxlength="<!--{$smarty.const.STEXT_LEN}-->" value="<!--{$smarty.post.question[$smarty.section.question.index].option.0|escape}-->" <!--{if $ERROR.question[$smarty.section.question.index].kind}--><!--{sfSetErrorStyle}--><!--{/if}-->>　2 <input type="text" name="question[<!--{$smarty.section.question.index}-->][option][1]" size="40" class="box40" value="<!--{$smarty.post.question[$smarty.section.question.index].option.1|escape}-->" <!--{if $ERROR.question[$smarty.section.question.index].kind}--><!--{sfSetErrorStyle}--><!--{/if}-->></td>
								</tr>
								<tr class="fs12n" bgcolor="#ffffff">
									<td colspan="2">3 <input type="text" name="question[<!--{$smarty.section.question.index}-->][option][2]" size="40" class="box40" maxlength="<!--{$smarty.const.STEXT_LEN}-->" value="<!--{$smarty.post.question[$smarty.section.question.index].option.2|escape}-->">　4 <input type="text" name="question[<!--{$smarty.section.question.index}-->][option][3]" size="40" class="box40" value="<!--{$smarty.post.question[$smarty.section.question.index].option.3|escape}-->"></td>
								</tr>
								<tr class="fs12n" bgcolor="#ffffff">
									<td colspan="2">5 <input type="text" name="question[<!--{$smarty.section.question.index}-->][option][4]" size="40" class="box40" maxlength="<!--{$smarty.const.STEXT_LEN}-->" value="<!--{$smarty.post.question[$smarty.section.question.index].option.4|escape}-->">　6 <input type="text" name="question[<!--{$smarty.section.question.index}-->][option][5]" size="40" class="box40" value="<!--{$smarty.post.question[$smarty.section.question.index].option.5|escape}-->"></td>
								</tr>
								<tr class="fs12n" bgcolor="#ffffff">
									<td colspan="2">7 <input type="text" name="question[<!--{$smarty.section.question.index}-->][option][6]" size="40" class="box40" maxlength="<!--{$smarty.const.STEXT_LEN}-->" value="<!--{$smarty.post.question[$smarty.section.question.index].option.6|escape}-->">　8 <input type="text" name="question[<!--{$smarty.section.question.index}-->][option][7]" size="40" class="box40" value="<!--{$smarty.post.question[$smarty.section.question.index].option.7|escape}-->"></td>
								</tr>
								</table>
							</td></tr>
							<!--{/section}-->
						</table>
						<!--▲FORM-->
						
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
										<td>
											<input type="submit" name="subm1" value="アンケートを<!--{if $QUESTION_ID}-->修正<!--{else}-->作成<!--{/if}-->" />&nbsp;&nbsp;<input type="reset" value="内容をクリア" />
										</td>
									</tr>
								</table>
								</td>
								<td bgcolor="#cccccc"><img src="<!--{$TPL_DIR}-->img/common/_.gif" width="1" height="10" alt=""></td>
							</tr>
							<tr>
								<td colspan="3"><img src="<!--{$TPL_DIR}-->img/contents/tbl_bottom.gif" width="678" height="8" alt=""></td>
							</tr>
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
								<td bgcolor="#636469" width="638" class="fs14n"><span class="white"><!--コンテンツタイトル-->登録済みアンケート</span></td>
								<td background="<!--{$TPL_DIR}-->img/contents/contents_title_right_bg.gif"><img src="<!--{$TPL_DIR}-->img/common/_.gif" width="18" height="1" alt=""></td>
							</tr>
							<tr>
								<td colspan="3"><img src="<!--{$TPL_DIR}-->img/contents/contents_title_bottom.gif" width="678" height="7" alt=""></td>
							</tr>
							<tr>
								<td colspan="3"><img src="<!--{$TPL_DIR}-->img/contents/main_bar.jpg" width="678" height="10" alt=""></td>
							</tr>
						</table>

						<!--▼FORM-->
						<table width="678" border="0" cellspacing="1" cellpadding="8" summary=" ">
						<form name="form2" method="post" action="<!--{$smaryt.server.PHP_SELF|escape}-->">
							<tr class="fs12n" bgcolor="#f2f1ec" align="center">
								<td width="42">編集</td>
								<td width="80">登録日</td>
								<td width="280">アンケートタイトル</td>
								<td width="80">ページ参照</td>
								<td width="80">結果取得</td>
								<td width="42">削除</td>
							</tr>
							<!--{section name=data loop=$list_data}-->
							<tr bgcolor="#FFFFFF" class="fs12" <!--{if $list_data[data].question_id eq $smarty.request.question_id}--><!--{sfSetErrorStyle}--><!--{/if}-->>
								<td align="center" class="main"><a href="<!--{$smarty.server.PHP_SELF|escape}-->?question_id=<!--{$list_data[data].question_id}-->">編集</a></td>
								<td align="center"><!--{$list_data[data].disp_date}--></td>
								<td><!--{$list_data[data].question_name|escape}--></td>
								<td align="center"><a href="<!--{$smarty.const.SITE_URL}-->inquiry/index.php?question_id=<!--{$list_data[data].question_id}-->" target="_blank">参照</a></td>
								<td align="center"><a href="<!--{$smarty.server.PHP_SELF|escape}-->?mode=csv&question_id=<!--{$list_data[data].question_id}-->">download</a></td>
								<td align="center"><a href="<!--{$smarty.server.PHP_SELF|escape}-->?mode=delete&question_id=<!--{$list_data[data].question_id}-->" onClick="return delete_check()">削除</a></td>
							</tr>
							<!--{/section}-->
						</form>
						</table>
						<!--▲FORM-->
						
						<!--登録テーブルここまで-->
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
		</td>
	</tr>
</form>
</table>
<!--★★メインコンテンツ★★-->		
				
<script type="text/javascript">
<!--
	<!--{section name=question loop=$cnt_question}-->
		func_disp(<!--{$smarty.section.question.index}-->);
	<!--{/section}-->	
//-->
</script>

