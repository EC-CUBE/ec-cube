<!--��-->
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ja" lang="ja">

<head>
<meta http-equiv="content-type" content="application/xhtml+xml; charset=EUC-JP" />
<meta http-equiv="content-script-type" content="text/javascript" />
<meta http-equiv="content-style-type" content="text/css" />
<!--{if $smarty.server.PHP_SELF == '/index.php'}-->
<meta name="keywords" content="�ȡ���Ʋ,�ƥ�ӥ���åԥ�,TV����åԥ�,�ƥ쥷��å�,TV����,�ƥ������,�̿�����,���󥿡��ͥåȥ���åԥ�,�ȥ���Ʋ,�ȡ����ɥ�,�̵�§,�̤���,�̼�Ĺ,�ȡ���Ʋ�ե꡼����,��������������" />
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
<!--��HEADER-->
<!--{include file='frontparts/header.tpl'}-->
<!--��HEADER-->
<!--��CONTENTS-->
<table width="780" cellspacing="0" cellpadding="0" summary=" ">
	<tr>
		<td bgcolor="#cccccc"><img src="../img/_.gif" width="1" height="10" alt="" /></td>
		<td bgcolor="#ffffff"><img src="../img/_.gif" width="9" height="1" alt="" /></td>
		<td bgcolor="#ffffff" align="left">
		<!--��MAIN CONTENTS-->
		<!--�ѥ󥯥�-->
			<!--{include_php file=$tpl_pankuzu_php}-->
		<!--�ѥ󥯥�-->
		<table cellspacing="0" cellpadding="0" summary=" " id="container">
			<tr><td height="10"></td></tr>
			<tr valign="top">
				<!--��LEFT CONTENTS-->
				<td id="left">
				
				<!--���Хʡ�-->
					<!--{include file=$tpl_banner}-->
				<!--���Хʡ�-->
				
				<!--�����ʸ���-->
					<!--{include_php file=$tpl_search_products_php}-->
				<!--�����ʸ���-->
				
				<!--�����ƥ���-->
					<!--{include_php file=$tpl_category_php}-->
				<!--�����ƥ���-->
				
				<!--�����ʥ�-->
					<!--{include file=$tpl_leftnavi}-->
				<!--�����ʥ�-->
				
				</td>
				<!--��LEFT CONTENTS-->
				<!--��RIGHT CONTENTS-->
				<form name="form1" id="form1" method="post" action="<!--{$smarty.server.REQUEST_URI}-->">
				<input type="hidden" name="pageno" value="<!--{$tpl_pageno}-->">
				<td id="right">
				
				<!--{* �����Ф����� *}-->
					<!--{include file=$tpl_maintitle}-->
				<!--{* �����Ф����� *}-->
								
				<!--{if $tpl_linemax > 0}-->
				<div id="hit"><span class="red12st"><!--{$tpl_linemax}--></span><span class="fs12">��ξ��ʤ��������ޤ�</span></div>
				<!--{else}-->
				<!--{include file="frontparts/search_zero.tpl"}-->
				<!--{/if}-->
				
				<div id="page">
				<!--���ڡ����ʥ�-->
				<!--{$tpl_strnavi}-->
				<!--���ڡ����ʥ�-->
				</div>
				<table cellspacing="0" cellpadding="0" summary=" " id="contents">
					<!--{section name=cnt loop=$arrProducts}-->
					<!--�����ʤ�������-->
					<tr valign="top">
						<td id="left"><div id="picture"><a href="<!--{$smarty.const.DETAIL_P_HTML}--><!--{$arrProducts[cnt].product_id}-->"><!--���ʼ̿�--><img src="<!--{$smarty.const.IMAGE_SAVE_URL|sfTrimURL}-->/<!--{$arrProducts[cnt].main_list_image}-->" width="130" height="130" alt="<!--{$arrProducts[cnt].name|escape}-->" /></a></div></td>
						<td id="spacer"></td>
						<td id="right"><div id="title"><span class="fs14st"><a href="<!--{$smarty.const.DETAIL_P_HTML}--><!--{$arrProducts[cnt].product_id}-->.html"><!--����̾--><!--{$arrProducts[cnt].name|escape}--></a></span></div>
						<!--���ʥ��ơ�����-->
						<!--{section name=flg loop=$arrProducts[cnt].product_flag|count_characters}--><!--{if $arrProducts[cnt].product_flag[flg] == "1"}--><!--{assign var=key value="`$smarty.section.flg.iteration`"}--><img src="<!--{$arrSTATUS_IMAGE[$key]}-->" width="60" height="17" alt="<!--{$arrSTATUS[$key]}-->" id="icon" /><!--{/if}--><!--{/section}-->
						<!--���ʥ��ơ�����-->
						<table cellspacing="0" cellpadding="0" summary=" " id="contents">
							<tr><td height="6"></td></tr>
							<tr>
								<td colspan="2" class="fs12"><!--�����ᥤ�󥳥���--><!--{$arrProducts[cnt].main_list_comment|escape}--></td>
							</tr>
							<tr><td height="6"></td></tr>
							<tr valign="top">
								<td><span class="fs12">�ȡ���Ʋ���ʡ�</span><span class="red12st">
								<!--{if $arrProducts[cnt].price02_min == $arrProducts[cnt].price02_max}-->				
									<!--{$arrProducts[cnt].price02_min|sfPreTax:$arrInfo.tax:$arrInfo.tax_rule|number_format}-->
								<!--{else}-->
									<!--{$arrProducts[cnt].price02_min|sfPreTax:$arrInfo.tax:$arrInfo.tax_rule|number_format}-->��<!--{$arrProducts[cnt].price02_max|sfPreTax:$arrInfo.tax:$arrInfo.tax_rule|number_format}-->
								<!--{/if}-->
								��</span><span class="red10">���ǹ���</span><br />
								<!--{if $arrProducts[cnt].price01_max > 0}-->
									<span class="fs10">���ͻԾ���ʡ�
									<!--{if $arrProducts[cnt].price01_min == $arrProducts[cnt].price01_max}-->				
										<!--{$arrProducts[cnt].price01_min|number_format}-->
									<!--{else}-->
										<!--{$arrProducts[cnt].price01_min|number_format}-->��<!--{$arrProducts[cnt].price01_max|number_format}-->
									<!--{/if}-->
									��</span><br />
								<!--{/if}-->
								<span class="fs12">�ݥ���ȡ�</span><span class="red12st">
								<!--{if $arrProducts[cnt].price02_min == $arrProducts[cnt].price02_max}-->				
									<!--{$arrProducts[cnt].price02_min|sfPrePoint:$arrProducts[cnt].point_rate}-->
								<!--{else}-->
									<!--{if $arrProducts[cnt].price02_min|sfPrePoint:$arrProducts[cnt].point_rate == $arrProducts[cnt].price02_max|sfPrePoint:$arrProducts[cnt].point_rate}-->
										<!--{$arrProducts[cnt].price02_min|sfPrePoint:$arrProducts[cnt].point_rate}-->
									<!--{else}-->
										<!--{$arrProducts[cnt].price02_min|sfPrePoint:$arrProducts[cnt].point_rate}-->��<!--{$arrProducts[cnt].price02_max|sfPrePoint:$arrProducts[cnt].point_rate}-->
									<!--{/if}-->
								<!--{/if}-->
								</span><span class="red10">Pt</span></td>
								<!--{assign var=name value="detail`$smarty.section.cnt.iteration`"}-->
								<td align="right"><a href="<!--{$smarty.const.DETAIL_P_HTML}--><!--{$arrProducts[cnt].product_id}-->.html" onmouseover="chgImg('../img/right_product/detail_on.gif','<!--{$name}-->');" onmouseout="chgImg('../img/right_product/detail.gif','<!--{$name}-->');"><img src="../img/right_product/detail.gif" width="110" height="22" alt="���ʤ�ܤ�������" name="<!--{$name}-->" id="<!--{$name}-->" /></a></td>
							</tr>
							<!--{if $arrProducts[cnt].stock_max == 0 && $arrProducts[cnt].stock_unlimited_max != 1}-->
							<tr>
								<td><span class="red12st">�������������ޤ��󤬡��������ڤ���Ǥ���</span></td>
							</tr>
							<!--{/if}-->
						</table>
						</td>
					</tr>
					<tr><td height="25"></td></tr>
					<!--�����ʤ����ޤ�-->
					<!--{/section}-->
										
				</table>
				<div id="page">
				<!--���ڡ����ʥ�-->
				<!--{$tpl_strnavi}-->
				<!--���ڡ����ʥ�-->
				</div>
				<!--��RIGHT CONTENTS-->
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
		<!--��MAIN CONTENTS-->
		</td>
		<td bgcolor="#ffffff"><img src="../img/_.gif" width="9" height="1" alt="" /></td>
		<td bgcolor="#cccccc"><img src="../img/_.gif" width="1" height="10" alt="" /></td>
	</tr>
</form>
</table>
<!--��CONTENTS-->

<!--��FOTTER-->
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
						<td height="40" align="right" class="fs12"><a href="/products/list-c2.html">�ե��å����</a>��<a href="/products/list-c3.html">����</a>��<a href="/products/list-c4.html">�� ��</a>��<a href="/products/list-c5.html">PC�����յ���</a>��<a href="/products/list-c6.html">���ơ���</a>��<a href="/products/list-c7.html">����</a>��<a href="/products/list-c8.html">TV����åԥ�</a></td>
					</tr>
				</table>
				</td>
			</tr>
			<tr><td bgcolor="#cccccc" height="1"></td></tr>
			<tr>
				<td bgcolor="#eeeeee" align="center">
				<table width="760" cellspacing="0" cellpadding="0" summary=" ">
					<tr>
						<td height="38" align="left"><a href="/tokado/index.php" onmouseover="chgImg('/img/fotter/tokado_on.gif','tokado');" onmouseout="chgImg('/img/fotter/tokado.gif','tokado');"><img src="/img/fotter/tokado.gif" width="95" height="11" alt="�ȡ���Ʋ�ˤĤ���" name="tokado" id="tokado" /></a><img src="/img/_.gif" width="15" height="1" alt="" /><a href="/requirements/index.php" onmouseover="chgImg('/img/fotter/movement_on.gif','movement');" onmouseout="chgImg('/img/fotter/movement.gif','movement');"><img src="/img/fotter/movement.gif" width="97" height="11" alt="ư��Ķ��ˤĤ���" name="movement" id="movement" /></a><img src="/img/_.gif" width="15" height="1" alt="" /><a href="/privacy/index.php" onmouseover="chgImg('/img/fotter/privacy_on.gif','privacy');" onmouseout="chgImg('/img/fotter/privacy.gif','privacy');"><img src="/img/fotter/privacy.gif" width="147" height="11" alt="�Ŀ;���μ谷���ˤĤ���" name="privacy" id="privacy" /></a></td>
						<td align="right"><img src="/img/fotter/copyright.gif" width="278" height="10" alt="Copyright (C)2005 TOKADO CO.,LTD. All Rights Reserved." /></td>
					</tr>
					<tr>
                		<td height="20" class="fs12">������ҥȡ���Ʋ�� ��811-2412��ʡ�����첰���ķ�Į����888����</td>
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
	<!--�ɲ�-->
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
	<!--�ɲ�-->
</table>
<!--��FOTTER-->
</div>

<map name="Map" id="Map"> 
<area shape="rect" coords="374,20,578,225" href="http://www.tokado-hot.com/densyo/index.html" alt="��˥塼���륭���ڡ���" target="_blank" />
<area shape="rect" coords="587,21,742,227" href="/campaign/nabe.php" alt="�����ý�" />
</map>

<!--{if $conversion_tag == 1}-->
<!--������С�����󥿥�-->
	<!--{include file=conversion_tag.tpl}-->
<!--������С�����󥿥�-->
<!--{/if}-->

</body>
</html>