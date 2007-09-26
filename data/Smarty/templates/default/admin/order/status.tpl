<!--{*
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
*}-->
<!--★★メインコンテンツ★★-->
<table width="878" border="0" cellspacing="0" cellpadding="0" summary=" ">
<form name="form1" id="form1" method="POST" action="<!--{$smarty.server.PHP_SELF|escape}-->" >
<input type="hidden" name="mode" value="">
<input type="hidden" name="status" value="<!--{if $arrForm.status == ""}-->1<!--{else}--><!--{$arrForm.status}--><!--{/if}-->" >
<input type="hidden" name="search_pageno" value="<!--{$tpl_pageno}-->" >
<input type="hidden" name="order_id" value="">
	<tr valign="top">
		<td background="<!--{$TPL_DIR}-->img/contents/navi_bg.gif" height="402">
			<!-- サブナビ -->
			<!--{include file=$tpl_subnavi}-->
		</td>
		<td class="mainbg">
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
								<td bgcolor="#636469" width="638" class="fs14n"><span class="white"><!--コンテンツタイトル-->ステータス管理</span></td>
								<td background="<!--{$TPL_DIR}-->img/contents/contents_title_right_bg.gif"><img src="<!--{$TPL_DIR}-->img/common/_.gif" width="18" height="1" alt=""></td>
							</tr>
							<tr>
								<td colspan="3"><img src="<!--{$TPL_DIR}-->img/contents/contents_title_bottom.gif" width="678" height="7" alt=""></td>
							</tr>
							<tr>
								<td colspan="3"><img src="<!--{$TPL_DIR}-->img/contents/main_bar.jpg" width="678" height="10" alt=""></td>
							</tr>
						</table>
						<!--登録テーブルここから-->
						<table width="678" border="0" cellspacing="1" cellpadding="8" summary=" ">
							<tr bgcolor="#ffffff">
								<td bgcolor="#ffffff" align="center" valign="top" height="400">
								<!--{if $tpl_linemax > 0 }-->
									<table border="0" cellspacing="0" cellpadding="0" summary=" ">
										<tr>
											<td>
												<select name="change_status">
												<option value="" selected="selected" style="<!--{$Errormes|sfGetErrorColor}-->" >選択してください</option> 
												<!--{foreach key=key item=item from=$arrORDERSTATUS}-->
												<!--{if $key ne $SelectedStatus}-->
												<option value="<!--{$key}-->" ><!--{$item}--></option>
												<!--{/if}-->
												<!--{/foreach}-->
												<option value="delete">削除</option>
												</select>
											</td>
											<td><input type="button" name="regist" value="反映" onclick="fnSelectCheckSubmit();"></td>
										</tr>
									</table>

									<table width="650" border="0" cellspacing="0" cellpadding="0" summary=" ">
										<tr class="fs12"><td align="left"><!--{$tpl_linemax}-->件が該当しました。	</td></tr>
										<tr class="fs12">
											<td align="center">
											<!--▼ページナビ-->
											<!--{$tpl_strnavi}-->
											<!--▲ページナビ-->
											</td>
										</tr>
									</table>
									
									<table width="650" border="0" cellspacing="0" cellpadding="0" summary=" ">
										<tr>
										<td align="right"><input type="button" name="btn01" value="全て選択" onclick="fnBoxChecked(true);"> <input type="button" name="btn01" value="全て解除" onclick="fnBoxChecked(false);"></td>
										</tr>
										<tr><td height="10"></td></tr>
									</table>
									
									<table width="650" border="0" cellspacing="1" cellpadding="5" summary=" " bgcolor="#cccccc">
									<tr bgcolor="#f2f1ec" align="center" class="fs12n">
										<td width="30">受注番号</td>
										<td width="90">受注日</td>				
										<td width="100">顧客名</td>				
										<td width="90">支払方法</td>
										<td width="81">購入金額（円）</td>
										<td width="70">発送日</td>
										<td width="70">対応状況</td>
										<td width="30">選択</td>
									</tr>
									<!--{section name=cnt loop=$arrStatus}-->
									<!--{assign var=status value="`$arrStatus[cnt].status`"}-->
									<tr bgcolor="<!--{$arrORDERSTATUS_COLOR[$status]}-->" class="fs12">
									<td align="center"><a href ="<!--{$smarty.server.PHP_SELF|escape}-->" onclick="fnOpenWindow('./edit.php?order_id=<!--{$arrStatus[cnt].order_id}-->','order_disp','800','900'); return false;" ><!--{$arrStatus[cnt].order_id}--></td>
									<td align="center"><!--{$arrStatus[cnt].create_date|sfDispDBDate:false}--></td>
									<td><!--{$arrStatus[cnt].order_name01|escape}--><!--{$arrStatus[cnt].order_name02|escape}--></td>
									<!--{assign var=payment_id value=`$arrStatus[cnt].payment_id`}-->
									<td align="center"><!--{$arrPayment[$payment_id]|escape}--></td>
									<td align="right"><!--{$arrStatus[cnt].total|number_format}--></td>
									<td align="center"><!--{if $arrStatus[cnt].status eq 5}--><!--{$arrStatus[cnt].commit_date|sfDispDBDate:false}--><!--{else}-->未発送<!--{/if}--></td>
									<td align="center"><!--{$arrORDERSTATUS[$status]}--></td>
									<td align="center"><input type="checkbox" name="move[]" value="<!--{$arrStatus[cnt].order_id}-->" ></td>
									</tr>
									<!--{/section}-->
									</table>
									<input type="hidden" name="move[]" value="" >
									
									<table width="650" border="0" cellspacing="0" cellpadding="0" summary=" ">
									<tr><td height="10"></td></tr>
									<tr>
									<td align="right"><input type="button" name="btn01" value="全て選択" onclick="fnBoxChecked(true);"> <input type="button" name="btn01" value="全て解除" onclick="fnBoxChecked(false);"></td>
									</tr>
									<tr><td height="10"></td></tr>
									</table>
									
									<table width="650" border="0" cellspacing="0" cellpadding="0" summary=" ">
										<tr class="fs12">
											<td align="center">
											<!--▼ページナビ-->
											<!--{$tpl_strnavi}-->
											<!--▲ページナビ-->
											</td>
										</tr>
									</table>
									
								<!--{elseif $arrStatus != "" & $tpl_linemax == 0}-->
									<table width="650" border="0" cellspacing="0" cellpadding="0" summary=" ">
										<tr class="fs12"><td align="center">該当するデータはありません。</td></tr>
									</table>
								<!--{/if}-->
									
								</td>
							</tr>
						</table>
						
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
	function fnSelectCheckSubmit(){ 

		var selectflag = 0; 
		var fm = document.form1;
				
		if(fm.change_status.options[document.form1.change_status.selectedIndex].value == ""){ 
		selectflag = 1; 
		} 
		
		if(selectflag == 1){ 
			alert('セレクトボックスが選択されていません'); 
			return false;
		}
		var i;
		var checkflag = 0;
		var max = fm["move[]"].length;
		
		if(max) {
			for (i=0;i<max;i++){
				if(fm["move[]"][i].checked == true){
					checkflag = 1;
				}
			}
		} else {
			if(fm["move[]"].checked == true) {
				checkflag = 1;
			}
		}

		if(checkflag == 0){
			alert('チェックボックスが選択されていません');
			return false;
		}
		
		if(selectflag == 0 && checkflag == 1){ 
		document.form1.mode.value = 'search';
		document.form1.submit(); 
		}
	}
	
	function fnBoxChecked(check){
		var count;
		var fm = document.form1;
		var max = fm["move[]"].length;
		for(count=0; count<max; count++){
			fm["move[]"][count].checked = check;
		}
	}
	
//-->
</script>
