<!--{*
 * Copyright (c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *}-->
<script type="text/javascript">
<!--
	function fnCheckAfterOpenWin(){
		if (<!--{$tpl_linemax}--> >= <!--{$smarty.const.DELIV_ADDR_MAX}-->){
			alert('最大登録数を超えています');
			return false;
		}else{
			win02('./delivery_addr.php','new_deiv','600','640');
		}
	}

//-->
</script>

<!--▼CONTENTS-->
<table width="" border="0" cellspacing="0" cellpadding="0" summary=" ">
	<tr>
		<td align="center" bgcolor="#ffffff">
		<!--▼MAIN ONTENTS-->
		<table width="100%" border="0" cellspacing="0" cellpadding="0" summary=" ">
			<tr>
				<td><img src="<!--{$TPL_DIR}-->img/mypage/title.jpg" width="700" height="40" alt="MYページ"></td>
			</tr>
			<tr><td height="15"></td></tr>
		</table>
		<table width="100%" border="0" cellspacing="0" cellpadding="0" summary=" ">
		<form name="form1" method="post" action="<!--{$smarty.server.PHP_SELF|escape}-->" >
		<input type="hidden" name="mode" value=""> 
		<input type="hidden" name="other_deliv_id" value="">
		<input type="hidden" name="pageno" value="<!--{$tpl_pageno}-->">
			<tr valign="top">
				<td>
				<!--▼NAVI-->
					<!--{include file=$tpl_navi}-->
				<!--▲NAVI-->
				</td>
				<td align="right">
				
				<table width="515" border="0" cellspacing="0" cellpadding="0" summary=" ">
					<tr>
						<!--{if $tpl_linemax < $smarty.const.DELIV_ADDR_MAX}-->
						<td><!--★タイトル--><img src="<!--{$TPL_DIR}-->img/mypage/subtitle03.gif" width="515" height="32" alt="お届け先追加・変更"></td>
						<!--{/if}-->
					</tr>
					<tr><td height="15"></td></tr>
					<tr>
						<td align="center" bgcolor="#fff5e8">
						<table width="495" border="0" cellspacing="0" cellpadding="0" summary=" ">
							<tr>
								<td height="10"><img src="<!--{$TPL_DIR}-->img/common/_.gif" width="305" height="1" alt=""></td>
								<td><img src="<!--{$TPL_DIR}-->img/common/_.gif" width="190" height="1" alt=""></td>
							</tr>
							<tr>
								<td><span class="fs12">登録住所以外へのご住所へ送付される場合等にご利用いただくことができます。</span><br>
								<span class="fs10">※最大<!--{$smarty.const.DELIV_ADDR_MAX}-->件まで登録できます。</span></td>
								<td align="right"><!--{if $tpl_linemax < 20}--><a href="<!--{$smarty.const.URL_DIR}-->mypage/delivery_addr.php" onclick="win03('./delivery_addr.php','delivadd','600','640'); return false;" onmouseover="chgImg('<!--{$TPL_DIR}-->img/common/newadress_on.gif','newadress');" onmouseout="chgImg('<!--{$TPL_DIR}-->img/common/newadress.gif','newadress');" target="_blank"><img src="<!--{$TPL_DIR}-->img/common/newadress.gif" width="160" height="22" alt="新しいお届け先を追加" border="0" name="newadress"></a><!--{/if}--></td>
							</tr>
							<tr><td height="10"></td></tr>
						</table>
						</td>
					</tr>
					<tr><td height="20"></td></tr>
					<tr>
						<td bgcolor="#cccccc" align="center">
						<!--{if $tpl_linemax > 0}-->
						<!--表示エリアここから-->
						<table width="515" border="0" cellspacing="1" cellpadding="10" summary=" ">
							<tr>
								<td colspan="6" bgcolor="#f0f0f0" class="fs12n"><strong>▼お届け先</strong></td>
							</tr>
							<!--{section name=cnt loop=$arrOtherDeliv}-->
								<!--{assign var=OtherPref value="`$arrOtherDeliv[cnt].pref`"}--> 
								<tr bgcolor="#ffffff">
									<td width="10" align="center" class="fs12"><!--{$smarty.section.cnt.iteration}--></td>
									<td width="80" class="fs12">お届け先住所</td>
									<td width="290" class="fs12">〒<!--{$arrOtherDeliv[cnt].zip01}-->-<!--{$arrOtherDeliv[cnt].zip02}--><br>
									<!--{$arrPref[$OtherPref]|escape}--><!--{$arrOtherDeliv[cnt].addr01|escape}--><!--{$arrOtherDeliv[cnt].addr02|escape}--><br>
									<!--{$arrOtherDeliv[cnt].name01|escape}-->&nbsp;<!--{$arrOtherDeliv[cnt].name02|escape}--></td>
									<td width="30" align="center" class="fs12"><a href="./delivery_addr.php" onclick="win02('./delivery_addr.php?other_deliv_id=<!--{$arrOtherDeliv[cnt].other_deliv_id}-->','deliv_disp','600','640'); return false;">変更</a>
									<td width="30" align="center" class="fs12"><a href="#" onclick="fnModeSubmit('delete','other_deliv_id','<!--{$arrOtherDeliv[cnt].other_deliv_id}-->');">削除</a></td>
								</tr>
							<!--{/section}-->							
						</table>
						<!--表示エリアここまで-->
						<!--{else}-->
						<table width="515" border="0" cellspacing="1" cellpadding="10" summary=" ">
							<tr>
								<td colspan="5" bgcolor="#ffffff" class="fs12n" align="center"><strong>新しいお届け先はありません。</strong></td>
							</tr>
						</table>
						<!--{/if}-->
						</td>
					</tr>
				</table>
				</td>
			</tr>
		</form>
		</table>
		<!--▲MAIN ONTENTS-->
		</td>
	</tr>
</table>
<!--▲CONTENTS-->


