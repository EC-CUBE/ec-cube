<!--　-->
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ja" lang="ja">

<head>
<meta http-equiv="content-type" content="application/xhtml+xml; charset=EUC-JP" />
<meta http-equiv="content-script-type" content="text/javascript" />
<meta http-equiv="content-style-type" content="text/css" />
<!--{if $smarty.server.PHP_SELF == '/index.php'}-->
<meta name="keywords" content="トーカ堂,テレビショッピング,TVショッピング,テレショップ,TV通販,テレビ通販,通信販売,インターネットショッピング,トウカ堂,トーカドウ,北義則,北さん,北社長,トーカ堂フリーダム,ごちそう旬選便" />
<!--{/if}-->
<link rel="stylesheet" href="/css/main.css" type="text/css" media="all" />
<link rel="stylesheet" href="<!--{$tpl_css}-->" type="text/css" media="all" />
<script type="text/javascript" src="/js/css.js"></script>
<script type="text/javascript" src="/js/navi.js"></script>
<script type="text/javascript" src="/js/win_op.js"></script>
<script type="text/javascript" src="/js/site.js"></script>
<script type="text/javascript" src="/user_data/js/site.js"></script>
<title>-<!--{$smarty.const.SITE_TITLE}-->-<!--{$tpl_title|escape}--></title>
<script type="text/javascript">
<!--
	<!--{$tpl_javascript}-->
//-->
</script>
</head>

<body onload="preLoadImg(); <!--{$tpl_onload}-->">
<noscript>
<link rel="stylesheet" href="/css/common.css" type="text/css" />
</noscript>

<div align="center">
<a name="top" id="top"></a>
<!--{if $smarty.const.ADMIN_MODE == '1'}-->
<table width="780" border="0" cellspacing="0" cellpadding="0" summary=" ">
	<tr><td bgcolor="#cc0000" height="3" colspan="2" align="center"><font color="#ffffff"><span class="fs12n">ADMIN_MODE ON</span></font></td></tr>
</table>
<!--{/if}-->

<!--▼HEADER-->
<table width="780" border="0" cellspacing="0" cellpadding="0" summary=" ">
  <tr>
    <td bgcolor="#cccccc"><img src="/user_data/topimg/_.gif" width="1" height="10" alt="" /></td>
    <td align="center" background="/user_data/topimg/header/bg.jpg">
      <table width="778" border="0" cellspacing="0" cellpadding="0" summary=" ">
        <tr>
          <td bgcolor="#9f0000" height="3"></td>
        </tr>
        <tr>
          <td bgcolor="#cc0000" height="5"></td>
        </tr>
      </table>
      <table width="760" border="0" cellspacing="0" cellpadding="0" summary=" ">
        <tr>
          <td height="3"></td>
        </tr>
        <tr>
          <td width="179" valign="top"><a href="<!--{$smarty.const.SITE_URL}-->"><img src="/user_data/topimg/header/logos.gif" width="179" height="85" /></a></td>
          <td width="276">
            <table width="276" border="0" cellspacing="0" cellpadding="0" summary=" ">
<!--▼ログインフォームここから（編集しないで下さい）-->
              <tr>
               <td colspan="3"><img src="/img/header/login_top.gif" width="284" height="8" alt="" /></td>
              </tr>
				<!--{include_php file=$tpl_login_php}-->
              <tr>
                <td colspan="3"><img src="/img/header/login_bottom.gif" width="284" height="8" alt="" /></td>
              </tr>
<!--▲ログインフォームここまで（編集しないで下さい）-->
            </table>
          </td>
          <td width="305">
            <table width="304" border="0" cellspacing="0" cellpadding="0" summary=" ">
              <tr>
                <td align="right" colspan="5" height="38"><img src="/user_data/topimg/header/info.gif" width="300" height="50" /> </td>
              </tr>
              <tr align="right">
                <td><a href="/entry/kiyaku.php" onmouseover="chgImg('/user_data/topimg/header/entry_on.gif','entry');" onmouseout="chgImg('/user_data/topimg/header/entry.gif','entry');"><img src="/user_data/topimg/header/entry.gif" width="95" height="20" alt="会員登録" border="0" name="entry" id="entry" /></a>
                <a href="/contact/index.php" onmouseover="chgImg('/user_data/topimg/header/contact_on.gif','contact');" onmouseout="chgImg('/user_data/topimg/header/contact.gif','contact');"><img src="/user_data/topimg/header/contact.gif" width="95" height="20" alt="お問い合わせ" border="0" name="contact" id="contact" /></a>
                <a href="/cart/index.php" onmouseover="chgImg('/user_data/topimg/header/cartin_on.gif','cartin');" onmouseout="chgImg('/user_data/topimg/header/cartin.gif','cartin');"><img src="/user_data/topimg/header/cartin.gif" width="95" height="20" alt="カゴの中を見る" border="0" name="cartin" id="cartin" /></a></td>
              </tr>
            </table>
          </td>
        </tr>
      </table>

	<table width="760" border="0" cellspacing="0" cellpadding="0" summary=" ">
		<tr>
			<td><img src="/user_data/topimg/space.gif" width="758" height="3" /></td>
		</tr>
		<tr>
		<!--▼TOPバナー-->
			<td><img src="/user_data/topimg/banner/head.jpg" width="758" height="40" /></td>
		<!--▲TOPバナー-->
		</tr>
		<tr>
			<td><img src="/user_data/topimg/space.gif" width="758" height="5" /></td>
		</tr>
	</table>

	<table width="778" cellspacing="0" cellpadding="0" summary=" ">
		<tr><td bgcolor="#666666" height="1"></td></tr>
		<tr><td bgcolor="#cccccc" height="4"></td></tr>
	</table>
    
    </td>
    <td bgcolor="#cccccc"><img src="/user_data/topimg/_.gif" width="1" height="10" alt="" /></td>
  </tr>
</table>
<!--▲HEADER-->

<!--▼CONTENTS-->
<table width="780" cellspacing="0" cellpadding="0" summary=" ">
	<tr>
		<td bgcolor="#cccccc"><img src="./img/_.gif" width="1" height="10" alt="" /></td>
		<td bgcolor="#ffffff"><img src="./img/_.gif" width="9" height="1" alt="" /></td>
		<td bgcolor="#ffffff" align="left">

		<!--▼MAIN CONTENTS-->
		<table cellspacing="0" cellpadding="0" summary=" " id="container">

			<tr><td height="8"></td></tr>
			<tr>
				
			<td colspan="3"><img src="./img/top/mainimage.jpg" alt="イメージ" width="760" height="250" usemap="#Map" /></td>
			</tr>
			<tr><td height="10"></td></tr>
			<tr valign="top" height="500">
				<td id="left">

				<!--▼LEFT CONTENTS-->
				
				<!--▼バナー-->
					<!--{include file=$tpl_banner}-->
				<!--▲バナー-->
				
				<!--▼商品検索-->
					<!--{include_php file=$tpl_search_products_php}-->
				<!--▲商品検索-->

				<!--▼テレビでご紹介した商品-->
					<!--{include file=$tpl_tv_products}-->
				<!--▲テレビでご紹介した商品-->
				
				<!--▼左ナビ-->
					<!--{include file=$tpl_leftnavi}-->
				<!--▲左ナビ-->
			
				<!--▲LEFT CONTENTS-->

				</td>
				<td id="right">

				<!--▼RIGHT CONTENTS-->
				<!--▼新着情報-->
				<table cellspacing="0" cellpadding="0" summary=" " id="osusumetitle">
					<tr>
						<td><img src="./img/top/info.jpg" width="570" height="33" alt="トーカ堂からのお知らせ" /></td>
					</tr>
					<tr>
						<td><span class="fs10st">☆★☆ 新着情報はRSS配信を行っております。★☆★ <a href="/rss/index.php"> > RSS </a></span></td>
					</tr>
					<tr><td height="10"></td></tr>
					<!--{section name=data loop=$arrNews}-->
					<tr>
						<td><span class="fs10st"><!--{$arrNews[data].news_date_disp|escape}--></span><br /> <span class="fs12"><!--{if $arrNews[data].news_url}--><a href="<!--{$arrNews[data].news_url}-->" <!--{if $arrNews[data].link_method eq "2"}-->target="_blank"<!--{/if}--> ><!--{/if}--><!--{$arrNews[data].news_comment|escape|nl2br}--><!--{if $arrNews[data].news_url}--></a><!--{/if}--></span></td>
					</tr>
					<!--{if !$smarty.section.data.last}-->
					<tr>
						<td height="15"><img src="./img/top/info_line.gif" width="570" height="1" alt="" /></td>
					</tr>
					<!--{/if}-->
					<!--{/section}-->
				</table>
				<!--▲新着情報-->
				<!--▼トーカ堂のオススメ品-->
				<table cellspacing="0" cellpadding="0" summary=" " id="osusumetitle">
					<tr><td height="20"></td></tr>
					<tr>
						<td><img src="/img/right_product/recommend.jpg" width="570" height="33" alt="トーカ堂のオススメ商品" /></td>
					</tr>
					<tr><td height="10"></td></tr>
				</table>
				<table cellspacing="0" cellpadding="0" summary=" " id="osusume">
					<!--{section name=cnt loop=$arrBestItems step=2}-->
					<tr valign="top">
						<td>
						<table cellspacing="0" cellpadding="0" summary=" " id="contents">
							<tr valign="top">
								<!--{assign var=image_path value="`$smarty.const.IMAGE_SAVE_URL`/`$arrBestItems[cnt].main_list_image`"}-->
								<!--（<!--{$smarty.section.cnt.iteration}-->）-->
								<td id="left"><div id="picture"><a href="/products/detail.php?product_id=<!--{$arrBestItems[cnt].product_id}-->"><!--商品写真--><img src="<!--{$image_path}-->" width="65" height="65" alt="<!--{$arrBestItems[cnt].name|escape}-->" /></a></div></td>
								<td id="spacer"></td>
								<td id="right"><span class="fs12"><strong><a href="/products/detail.php?product_id=<!--{$arrBestItems[cnt].product_id}-->"><!--{$arrBestItems[cnt].name|escape}--></a></strong></span><br />
								<span class="fs12">トーカ堂価格：</span>
								<span class="red12st">
									<!--{if $arrBestItems[cnt].price02_min == $arrBestItems[cnt].price02_max}-->				
										<!--{$arrBestItems[cnt].price02_min|sfPreTax:$arrInfo.tax:$arrInfo.tax_rule|number_format}-->
									<!--{else}-->
										<!--{$arrBestItems[cnt].price02_min|sfPreTax:$arrInfo.tax:$arrInfo.tax_rule|number_format}-->〜<!--{$arrBestItems[cnt].price02_max|sfPreTax:$arrInfo.tax:$arrInfo.tax_rule|number_format}-->
									<!--{/if}-->
									円
								</span><span class="red10">（税込）</span>
								<br />
								<span class="fs12">ポイント：</span>
								<span class="red12st">
									<!--{if $arrBestItems[cnt].price02_min == $arrBestItems[cnt].price02_max}-->				
										<!--{$arrBestItems[cnt].price02_min|sfPrePoint:$arrBestItems[cnt].point_rate}-->
									<!--{else}-->
										<!--{if $arrBestItems[cnt].price02_min|sfPrePoint:$arrBestItems[cnt].point_rate == $arrBestItems[cnt].price02_max|sfPrePoint:$arrBestItems[cnt].point_rate}-->
											<!--{$arrBestItems[cnt].price02_min|sfPrePoint:$arrBestItems[cnt].point_rate}-->
										<!--{else}-->
											<!--{$arrBestItems[cnt].price02_min|sfPrePoint:$arrBestItems[cnt].point_rate}-->〜<!--{$arrBestItems[cnt].price02_max|sfPrePoint:$arrBestItems[cnt].point_rate}-->
										<!--{/if}-->
									<!--{/if}-->
								</span><span class="red10">Pt</span><br />
								<span class="fs12"><!--{$arrBestItems[cnt].comment|escape|nl2br}--></span></td>
								<!--（<!--{$smarty.section.cnt.iteration}-->）-->
							</tr>
						</table>
						</td>
						<td id="spacer"></td>
						<!--{assign var=nextCnt value=$smarty.section.cnt.index+1}-->
						<td>
						<!--{if $arrBestItems[$nextCnt].product_id}-->
						<table cellspacing="0" cellpadding="0" summary=" " id="contents">
							<tr valign="top">
								<!--{assign var=image_path value="`$smarty.const.IMAGE_SAVE_URL`/`$arrBestItems[$nextCnt].main_list_image`"}-->
								<!--（<!--{$smarty.section.cnt.index_next}-->）-->
								<td id="left"><div id="picture"><a href="/products/detail.php?product_id=<!--{$arrBestItems[$nextCnt].product_id}-->"><!--商品写真--><img src="<!--{$image_path}-->" width="65" height="65" alt="<!--{$arrBestItems[$nextCnt].name|escape}-->" /></a></div></td>
								<td id="spacer"></td>
								<td id="right"><span class="fs12"><strong><a href="/products/detail.php?product_id=<!--{$arrBestItems[$nextCnt].product_id}-->"><!--{$arrBestItems[$nextCnt].name|escape}--></a></strong></span><br />
								<span class="fs12">トーカ堂価格：</span>
								<span class="red12st">
									<!--{if $arrBestItems[$nextCnt].price02_min == $arrBestItems[$nextCnt].price02_max}-->				
										<!--{$arrBestItems[$nextCnt].price02_min|sfPreTax:$arrInfo.tax:$arrInfo.tax_rule|number_format}-->
									<!--{else}-->
										<!--{$arrBestItems[$nextCnt].price02_min|sfPreTax:$arrInfo.tax:$arrInfo.tax_rule|number_format}-->〜<!--{$arrBestItems[$nextCnt].price02_max|sfPreTax:$arrInfo.tax:$arrInfo.tax_rule|number_format}-->
									<!--{/if}-->
									円
								</span><span class="red10">（税込）</span>
								<br />
								<span class="fs12">ポイント：</span>
								<span class="red12st">
									<!--{if $arrBestItems[$nextCnt].price02_min == $arrBestItems[$nextCnt].price02_max}-->				
										<!--{$arrBestItems[$nextCnt].price02_min|sfPrePoint:$arrBestItems[$nextCnt].point_rate}-->
									<!--{else}-->
										<!--{if $arrBestItems[$nextCnt].price02_min|sfPrePoint:$arrBestItems[$nextCnt].point_rate == $arrBestItems[$nextCnt].price02_max|sfPrePoint:$arrBestItems[$nextCnt].point_rate}-->
											<!--{$arrBestItems[$nextCnt].price02_min|sfPrePoint:$arrBestItems[$nextCnt].point_rate}-->
										<!--{else}-->
											<!--{$arrBestItems[$nextCnt].price02_min|sfPrePoint:$arrBestItems[$nextCnt].point_rate}-->〜<!--{$arrBestItems[$nextCnt].price02_max|sfPrePoint:$arrBestItems[$nextCnt].point_rate}-->
										<!--{/if}-->
									<!--{/if}-->
								</span><span class="red10">Pt</span><br />
								<span class="fs12"><!--{$arrBestItems[$nextCnt].comment|escape|nl2br}--></span></td>
								<!--（<!--{$smarty.section.cnt.index_next}-->）-->
							</tr>
						</table>
						<!--{/if}-->
						</td>
					</tr>
					<!--{if !$smarty.section.cnt.last}-->
					<tr>
						<td height="25"><img src="/img/right_product/recommend_line.gif" width="270" height="1" alt="" /></td>
						<td id="spacer"></td>
						<td align="right"><img src="/img/right_product/recommend_line.gif" width="270" height="1" alt="" /></td>
					</tr>
					<!--{/if}-->
					<!--{/section}-->

				</table>
				
				<!--▲トーカ堂のオススメ品-->
				</td>
			</tr>
		</table>
		<!--▲MAIN CONTENTS-->
<!-- EBiS start -->
<script type="text/javascript">
if ( location.protocol == 'http:' ){ 
	strServerName = 'http://daikoku.ebis.ne.jp'; 
} else { 
	strServerName = 'https://secure2.ebis.ne.jp/ver3';
}
cid = 'tqYg3k6U'; pid = '1'; m1id=''; a1id=''; o1id=''; o2id=''; o3id=''; o4id=''; o5id='';
document.write("<scr" + "ipt type=\"text\/javascript\" src=\"" + strServerName + "\/ebis_tag.php?cid=" + cid + "&pid=" + pid + "&m1id=" + m1id + "&a1id=" + a1id + "&o1id=" + o1id + "&o2id=" + o2id + "&o3id=" + o3id + "&o4id=" + o4id + "&o5id=" + o5id + "\"><\/scr" + "ipt>");
</script>
<!-- EBiS end -->
		</td>
		<td bgcolor="#ffffff"><img src="./img/_.gif" width="9" height="1" alt="" /></td>
		<td bgcolor="#cccccc"><img src="./img/_.gif" width="1" height="10" alt="" /></td>
	</tr>
</table>
<!--▲CONTENTS-->

<!--▼FOTTER-->
<table width="780" cellspacing="0" cellpadding="0" summary=" ">
	<tr>
		<td bgcolor="#cccccc"><img src="/img/_.gif" width="1" height="10" alt="" /></td>
		<td>
		<table width="778" cellspacing="0" cellpadding="0" summary=" ">
			<tr>
				<td bgcolor="#ffffff" align="center">
				<table width="760" cellspacing="0" cellpadding="0" summary=" ">
					<tr>
						<td align="right" height="40" valign="bottom"><a href="#top"><img src="/img/fotter/pagetop.gif" width="61" height="8" alt="PAGETOP" /></a></td>
					</tr>
					<tr>
						<td height="40" align="right" class="fs12"><a href="/products/list-c2.html">ファッション</a>｜<a href="/products/list-c3.html">雑貨</a>｜<a href="/products/list-c4.html">家 電</a>｜<a href="/products/list-c5.html">PC・周辺機器</a>｜<a href="/products/list-c6.html">美容・健康</a>｜<a href="/products/list-c7.html">食品</a>｜<a href="/products/list-c8.html">TVショッピング</a></td>
					</tr>
				</table>
				</td>
			</tr>
			<tr><td bgcolor="#cccccc" height="1"></td></tr>
			<tr>
				<td bgcolor="#eeeeee" align="center">
				<table width="760" cellspacing="0" cellpadding="0" summary=" ">
					<tr>
						<td height="38" align="left"><a href="/tokado/index.php" onmouseover="chgImg('/img/fotter/tokado_on.gif','tokado');" onmouseout="chgImg('/img/fotter/tokado.gif','tokado');"><img src="/img/fotter/tokado.gif" width="95" height="11" alt="トーカ堂について" name="tokado" id="tokado" /></a><img src="/img/_.gif" width="15" height="1" alt="" /><a href="/requirements/index.php" onmouseover="chgImg('/img/fotter/movement_on.gif','movement');" onmouseout="chgImg('/img/fotter/movement.gif','movement');"><img src="/img/fotter/movement.gif" width="97" height="11" alt="動作環境について" name="movement" id="movement" /></a><img src="/img/_.gif" width="15" height="1" alt="" /><a href="/privacy/index.php" onmouseover="chgImg('/img/fotter/privacy_on.gif','privacy');" onmouseout="chgImg('/img/fotter/privacy.gif','privacy');"><img src="/img/fotter/privacy.gif" width="147" height="11" alt="個人情報の取扱いについて" name="privacy" id="privacy" /></a></td>
						<td align="right"><img src="/img/fotter/copyright.gif" width="278" height="10" alt="Copyright (C)2005 TOKADO CO.,LTD. All Rights Reserved." /></td>
					</tr>
					<tr>
                		<td height="20" class="fs12">株式会社トーカ堂　 〒811-2412　福岡県糟屋郡篠栗町乙犬888番地</td>
              		</tr>
				</table>
				</td>
			</tr>
			<tr><td bgcolor="#cc0000" height="5"></td></tr>
			<tr><td bgcolor="#9f0000" height="3"></td></tr>
		</table>
		</td>
		<td bgcolor="#cccccc"><img src="/img/_.gif" width="1" height="10" alt="" /></td>
	</tr>
	<tr><td bgcolor="#cccccc" height="1"></td></tr>
	<!--追加-->
	<tr>
    <td colspan="2" height="72" valign="middle" align="left">
      <table>
        <tr>
          <td><script src=https://seal.verisign.com/getseal?host_name=<!--{$smarty.server.HTTP_HOST}-->&size=S&use_flash=YES&use_transparent=YES&lang=ja></script></td>
          <td><img src="/user_data/topimg/_.gif" width="20" /></td>
          <td><img src="/user_data/topimg/footer/jdma.gif" /></td>
          <td><img src="/user_data/topimg/_.gif" width="20" /></td>
          <td><img src="/user_data/topimg/footer/freecall.gif" width="200" height="45" /></td>
          <td><img src="/user_data/topimg/_.gif" width="20" height="1" /></td>
          <td><img src="/user_data/topimg/footer/free.gif" /></td>
        </tr>
      </table>
    </td>
  	</tr>
	<!--追加-->
</table>
<!--▲FOTTER-->
</div>

<map name="Map" id="Map"> 
<area shape="rect" coords="374,20,578,225" href="http://www.tokado-hot.com/densyo/index.html" alt="リニューアルキャンペーン" target="_blank" />
<area shape="rect" coords="587,21,742,227" href="/campaign/nabe.php" alt="お鍋特集" />
</map>

<!--{if $conversion_tag == 1}-->
<!--▼コンバージョンタグ-->
	<!--{include file=conversion_tag.tpl}-->
<!--▲コンバージョンタグ-->
<!--{/if}-->

</body>
</html>