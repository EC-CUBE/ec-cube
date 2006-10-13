<!--{*
/*
 * Copyright(c) 2000-2006 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
*}-->
<!--　-->
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ja" lang="ja">

<head>
<meta http-equiv="content-type" content="application/xhtml+xml; charset=EUC-JP" />
<meta http-equiv="content-script-type" content="text/javascript" />
<meta http-equiv="content-style-type" content="text/css" />
<link rel="stylesheet" href="<!--{$smarty.const.URL_DIR}-->admin/css/contents.css" type="text/css" media="all" />
<script type="text/javascript" src="<!--{$smarty.const.URL_DIR}-->js/css.js"></script>
<script type="text/javascript" src="<!--{$smarty.const.URL_DIR}-->js/navi.js"></script>
<script type="text/javascript" src="<!--{$smarty.const.URL_DIR}-->js/win_op.js"></script>
<script type="text/javascript" src="<!--{$smarty.const.URL_DIR}-->js/site.js"></script>
<script type="text/javascript" src="<!--{$smarty.const.URL_DIR}-->js/admin.js"></script>
<!--{include file='css/contents.tpl'}-->
<title>ECサイト管理者ページ</title>
<script type="text/javascript">
<!--
self.moveTo(20,20);self.focus();
//-->
</script>
</head>

<body bgcolor="#ffffff" text="#666666" link="#007bb7" vlink="#007bb7" alink="#cc0000" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" onload="<!--{$tpl_onload}-->">
<noscript>
<link rel="stylesheet" href="<!--{$smarty.const.URL_ADMIN_CSS}-->common.css" type="text/css" />
</noscript>

<div align="center">
<!--★★メインコンテンツ★★-->
<table width="700" border="0" cellspacing="0" cellpadding="0" summary=" ">
<form name="form1" id="form1" method="post" action="<!--{$smarty.server.PHP_SELF}-->">
<input type="hidden" name="mode" value="search">
	<tr valign="top">
		<td class="mainbg">
			<!--▼登録テーブルここから-->
			<table width="680" border="0" cellspacing="0" cellpadding="0" summary=" ">
				<!--メインエリア-->
				<tr>
					<td align="center">
						<table width="660" border="0" cellspacing="0" cellpadding="0" summary=" ">
							<tr><td height="14"></td></tr>
							<tr>
								<td colspan="3"><img src="<!--{$smarty.const.URL_DIR}-->img/contents/main_top.jpg" width="668" height="14" alt=""></td>
							</tr>
							<tr>
								<td background="<!--{$smarty.const.URL_DIR}-->img/contents/main_left.jpg"><img src="<!--{$smarty.const.URL_DIR}-->img/common/_.gif" width="14" height="1" alt=""></td>
								<td bgcolor="#cccccc">
									<table width="640" border="0" cellspacing="0" cellpadding="0" summary=" ">
										<tr>
											<td colspan="3"><img src="<!--{$smarty.const.URL_DIR}-->img/contents/contents_title_top.gif" width="640" height="7" alt=""></td>
										</tr>
										<tr>
											<td background="<!--{$smarty.const.URL_DIR}-->img/contents/contents_title_left_bg.gif"><img src="<!--{$smarty.const.URL_DIR}-->img/contents/contents_title_left.gif" width="22" height="12" alt=""></td>
											<td bgcolor="#636469" width="600" class="fs14n"><span class="white"><!--コンテンツタイトル-->メンバー登録/編集</span></td>
											<td background="<!--{$smarty.const.URL_DIR}-->img/contents/contents_title_right_bg.gif"><img src="<!--{$smarty.const.URL_DIR}-->img/common/_.gif" width="18" height="1" alt=""></td>
										</tr>
										<tr>
											<td colspan="3"><img src="<!--{$smarty.const.URL_DIR}-->img/contents/contents_title_bottom.gif" width="640" height="7" alt=""></td>
										</tr>
										<tr>
											<td colspan="3"><img src="<!--{$smarty.const.URL_DIR}-->img/contents/main_bar.jpg" width="640" height="10" alt=""></td>
										</tr>
									</table>
									
									<table width="640" border="0" cellspacing="1" cellpadding="5" summary=" " bgcolor="#cccccc">
										<tr class="fs12n">
											<td bgcolor="#f0f0f0" width="110">顧客名</td>
											<td bgcolor="#ffffff" width="198"><!--{$list_data.name|escape|default:"（未指定）"}--></td>
											<td bgcolor="#f0f0f0" width="110">顧客名（カナ）</td>
											<td bgcolor="#ffffff" width="249"><!--{$list_data.kana|escape|default:"（未指定）"}--></td>
										</tr>
										<tr class="fs12n">
											<td bgcolor="#f0f0f0" width="110">都道府県</td>
											<td bgcolor="#ffffff" width="198"><!--{$list_data.pref_disp|default:"（未指定）"}--></td>
											<td bgcolor="#f0f0f0" width="110">TEL</td>
											<td bgcolor="#ffffff" width="249"><!--{$list_data.tel|escape|default:"（未指定）"}--></td>
										</tr>
										<tr class="fs12n">
											<td bgcolor="#f0f0f0" width="110">性別</td>
											<td bgcolor="#ffffff" width="198"><!--{$list_data.sex_disp|default:"（未指定）"}--></td>
											<td bgcolor="#f0f0f0" width="110">誕生月</td>
											<td bgcolor="#ffffff" width="249"><!--{if $list_data.birth_month}--><!--{$list_data.birth_month|escape}-->月<!--{else}-->（未指定）<!--{/if}--></td>				
										</tr>
										<tr class="fs12n">
											<td bgcolor="#f0f0f0" width="110">配信形式</td>
											<td bgcolor="#ffffff" width="198"><!--{$list_data.htmlmail_disp|escape|default:"（未指定）"}--></td>
											<td bgcolor="#f0f0f0" width="110">購入回数</td>
											<td bgcolor="#ffffff" width="199"><!--{if $list_data.buy_times_from}--><!--{$list_data.buy_times_from|escape}-->回 〜 <!--{$list_data.buy_times_to|escape}-->回<!--{else}-->（未指定）<!--{/if}--></td>
										</tr>
										<tr class="fs12n">
										<!--{*非会員は選択できない
											<td bgcolor="#f0f0f0" width="110">種別</td>
											<td bgcolor="#ffffff" width="198">
											<!--{$list_data.customer|escape|default:"すべて"}-->
											</td>
										*}-->
											<td bgcolor="#f0f0f0" width="110">購入商品コード</td>
											<td bgcolor="#ffffff" width="198"><!--{$list_data.buy_product_code|escape|default:"（未指定）"}--></td>
											<td bgcolor="#f0f0f0" width="110">購入金額</td>
											<td bgcolor="#ffffff" width="199"><!--{if $list_data.buy_total_from}--><!--{$list_data.buy_total_from|escape}-->円 〜 <!--{$list_data.buy_total_to|escape}-->円<!--{else}-->（未指定）<!--{/if}--></td>
										</tr>
										<tr class="fs12n">
											<td bgcolor="#f0f0f0" width="110">メールアドレス</td>
											<td bgcolor="#ffffff" width="507" colspan="3"><!--{$list_data.email|escape|default:"（未指定）"}--></td>
										</tr>
										<tr class="fs12n">
											<td bgcolor="#f0f0f0" width="110">職業</td>
											<td bgcolor="#ffffff" width="507" colspan="3"><!--{$list_data.job_disp|escape|default:"（未指定）"}--></td>
										</tr>
							
										<tr class="fs12n">
											<td bgcolor="#f0f0f0" width="110">生年月日</td>
											<td bgcolor="#ffffff" width="507" colspan="3">
											<!--{if $list_data.b_start_year}-->
												<!--{$list_data.b_start_year}-->年<!--{$list_data.b_start_month}-->月<!--{$list_data.b_start_day}-->日&nbsp;〜&nbsp;<!--{$list_data.b_end_year}-->年<!--{$list_data.b_end_month}-->月<!--{$list_data.b_end_day}-->日
											<!--{else}-->（未指定）<!--{/if}-->
											</td>
										</tr>	
										<tr class="fs12n">
											<td bgcolor="#f0f0f0" width="110">登録日</td>
											<td bgcolor="#ffffff" width="507" colspan="3">
											<!--{if $list_data.start_year}-->
												<!--{$list_data.start_year}-->年<!--{$list_data.start_month}-->月<!--{$list_data.start_day}-->日&nbsp;〜&nbsp;<!--{$list_data.end_year}-->年<!--{$list_data.end_month}-->月<!--{$list_data.end_day}-->日
											<!--{else}-->（未指定）<!--{/if}-->
											</td>
										</tr>			
										<tr class="fs12n">
											<td bgcolor="#f0f0f0" width="110">最終購入日</td>
											<td bgcolor="#ffffff" width="507" colspan="3">
											<!--{if $list_data.buy_start_year}-->
												<!--{$list_data.buy_start_year}-->年<!--{$list_data.buy_start_month}-->月<!--{$list_data.buy_start_day}-->日&nbsp;〜&nbsp;<!--{$list_data.buy_end_year}-->年<!--{$list_data.buy_end_month}-->月<!--{$list_data.buy_end_day}-->日
											<!--{else}-->（未指定）<!--{/if}-->	
											</td>
										</tr>
										<tr class="fs12n">
											<td bgcolor="#f0f0f0" width="110">購入商品名</td>
											<td bgcolor="#ffffff" width="198"><!--{$list_data.buy_product_name|escape|default:"（未指定）"}--></td>
											<td bgcolor="#f0f0f0" width="110">カテゴリ</td>
											<td bgcolor="#ffffff" width="199"><!--{$list_data.category_name|escape|default:"（未指定）"}--></td>
										</tr>
									</table>
	
									<table width="640" border="0" cellspacing="0" cellpadding="0" summary=" ">
										<tr>
											<td bgcolor="#cccccc"><img src="<!--{$smarty.const.URL_DIR}-->img/common/_.gif" width="1" height="5" alt=""></td>
											<td><img src="<!--{$smarty.const.URL_DIR}-->img/contents/tbl_top.gif" width="638" height="7" alt=""></td>
											<td bgcolor="#cccccc"><img src="<!--{$smarty.const.URL_DIR}-->img/common/_.gif" width="1" height="5" alt=""></td>
										</tr>
										<tr>
											<td bgcolor="#cccccc"><img src="<!--{$smarty.const.URL_DIR}-->img/common/_.gif" width="1" height="10" alt=""></td>
											<td bgcolor="#e9e7de" align="center">
											<table border="0" cellspacing="0" cellpadding="0" summary=" ">
												<tr>
													<td><input type="button" name="close" value="ウインドウを閉じる" onclick="window.close();" /></td>
												</tr>
											</table>
											</td>
											<td bgcolor="#cccccc"><img src="<!--{$smarty.const.URL_DIR}-->img/common/_.gif" width="1" height="10" alt=""></td>
										</tr>
										<tr>
											<td colspan="3"><img src="<!--{$smarty.const.URL_DIR}-->img/contents/tbl_bottom.gif" width="640" height="8" alt=""></td>
										</tr>
									</table>
								</td>
								<td background="<!--{$smarty.const.URL_DIR}-->img/contents/main_right.jpg"><img src="<!--{$smarty.const.URL_DIR}-->img/common/_.gif" width="14" height="1" alt=""></td>
							</tr>
							<tr>
								<td colspan="3"><img src="<!--{$smarty.const.URL_DIR}-->img/contents/main_bottom.jpg" width="668" height="14" alt=""></td>
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
</div>

</body>
</html>