<!--{*
 * Copyright(c) 2000-2006 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *}-->
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html lang="ja">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<!--{$smarty.const.CHAR_CODE}-->">
<meta http-equiv="content-script-type" content="text/javascript">
<meta http-equiv="content-style-type" content="text/css">
<link rel="stylesheet" href="<!--{$smarty.const.URL_DIR}-->user_data/css/contents.css" type="text/css" media="all" />
<link rel="stylesheet" href="<!--{$smarty.const.URL_DIR}-->css/common.css" type="text/css" media="all" />
<link rel="stylesheet" href="<!--{$tpl_css}-->" type="text/css" media="all" />
<title><!--{$arrSiteInfo.shop_name}-->/<!--{$tpl_title|escape}--></title>
<meta name="author" content="<!--{$arrPageLayout.author|escape}-->">
<meta name="description" content="<!--{$arrPageLayout.description|escape}-->">
<meta name="keywords" content="<!--{$arrPageLayout.keyword|escape}-->">

<script type="text/javascript">
<!--
	function preLoadImg(URL){
		arrImgList = new Array (
			URL+"img/header/basis_on.jpg",URL+"img/header/product_on.jpg",URL+"img/header/customer_on.jpg",URL+"img/header/order_on.jpg",
			URL+"img/header/sales_on.jpg",URL+"img/header/mail_on.jpg",URL+"img/header/contents_on.jpg",
			URL+"img/header/mainpage_on.gif",URL+"img/header/sitecheck_on.gif",URL+"img/header/logout.gif",
			URL+"img/contents/btn_search_on.jpg",URL+"img/contents/btn_regist_on.jpg",
			URL+"img/contents/btn_csv_on.jpg",URL+"img/contents/arrow_left.jpg",URL+"img/contents/arrow_right.jpg"
		);
		arrPreLoad = new Array();
		for (i in arrImgList) {
			arrPreLoad[i] = new Image();
			arrPreLoad[i].src = arrImgList[i];
		}
		preLoadFlag = "true";
	}

	function chgImg(fileName,imgName){
		if (preLoadFlag == "true") {
			document.images[imgName].src = fileName;
		}
	}

	function chgImgImageSubmit(fileName,imgObj){
		imgObj.src = fileName;
	}

	function fnFormModeSubmit(form, mode, keyname, keyid) {
		document.forms[form]['mode'].value = mode;
		if(keyname != "" && keyid != "") {
			document.forms[form][keyname].value = keyid;
		}
		document.forms[form].submit();
	}
//-->

</script>
</head>

<!-- ▼ ＢＯＤＹ部 スタート -->
<body bgcolor="#ffe9e6" text="#555555" link="#3a75af" vlink="#3a75af" alink="#3a75af" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" onLoad="preLoadImg('/'); ">
<noscript>
<link rel="stylesheet" href="/css/common.css" type="text/css" />
</noscript>

<div align="center">
<a name="top" id="top"></a>

<!--▼HEADER-->
<!--{if $arrPageLayout.header_chk != 2}--> 
<!--{assign var=header_dir value="`$smarty.const.HTML_PATH`user_data/include/header.tpl"}-->
<!--{include file= $header_dir}-->
<!--{/if}-->
<!--▲HEADER-->

<!--▼MAIN-->
<div id="base">
<table width="780" border="0" cellspacing="0" cellpadding="0" summary=" ">
	<tr>
		<td bgcolor="#cccccc" width="1"><img src="/img/_.gif" width="1" height="10" alt="" /></td>
		<td bgcolor="#ffffff" width="1"><img src="/img/_.gif" width="5" height="1" alt="" /></td>
		<td bgcolor="#ffffff" align="left" width=100%> 

		
		<table width="100%" border="0" cellspacing="0" cellpadding="0" summary=" ">

			<tr valign="top">
				<!--▼左ナビ-->
								<!--▲左ナビ-->
			
				<td align="center" width=100%>
			        <table border="0" cellspacing="0" cellpadding="0" summary=" ">
			        
					<!--▼メイン上部-->
										<!--▲メイン上部-->
					
					<tr><td align="center">
<!--▼CONTENTS-->
<table width="760" border="0" cellspacing="0" cellpadding="0" summary=" ">
	<tr>
		<td align="center" bgcolor="#ffffff">
		<!--▼MAIN ONTENTS-->
		<!--{*購入手続きの流れ-->
		<table width="700" border="0" cellspacing="0" cellpadding="0" summary=" ">
			<tr>
				<td><img src="<!--{$smarty.const.URL_DIR}-->img/shopping/flow03.gif" width="700" height="36" alt="購入手続きの流れ"></td>
			</tr>
			<tr><td height="15"></td></tr>
		</table>
		<!--購入手続きの流れ*}-->
		
		<table width="700" border="0" cellspacing="0" cellpadding="0" summary=" ">
			<tr>
				<td><img src="<!--{$smarty.const.URL_DIR}-->img/shopping/credit_title.jpg" width="700" height="40" alt="クレジット決済"></td>
			</tr>
			<tr><td height="15"></td></tr>
			<tr>
				<td class="fs12">下記から、支払い方法をご選択してください。<br />
				一括払い、リボルビング払いを選択した場合は、分割回数を選択する必要はありません。<br />
				分割払いを選択した場合は、分割回数を選択し、一番下の「ご注文完了ページへ」ボタンをクリックしてください。</td>
			</tr>
			<tr><td height="20"></td></tr>
			<form name="form2" id="form2" method="post" action="<!--{$smarty.server.PHP_SELF|escape}-->">
			<input type="hidden" name="mode" value="">
			</form>
			<form name="form1" id="form1" method="post" action="<!--{$arrSendData.SEND_URL|escape}-->">
			<input type="hidden" name="SHOPCO" value="<!--{$arrSendData.SHOPCO|escape}-->">
			<input type="hidden" name="HOSTID" value="<!--{$arrSendData.HOSTID|escape}-->">
			<input type="hidden" name="S_TORIHIKI_NO" value="<!--{$arrSendData.S_TORIHIKI_NO|escape}-->">
			<input type="hidden" name="MAIL" value="<!--{$arrSendData.MAIL|escape}-->">
			<input type="hidden" name="AMOUNT" value="<!--{$arrSendData.AMOUNT|escape}-->">
			<input type="hidden" name="TAX" value="<!--{$arrSendData.TAX|escape}-->">
			<input type="hidden" name="TOTAL" value="<!--{$arrSendData.TOTAL|escape}-->">
			<input type="hidden" name="JOB" value="<!--{$arrSendData.JOB|escape}-->">
			<input type="hidden" name="ITEM" value="<!--{$arrSendData.ITEM|escape}-->">
			<input type="hidden" name="RETURL" value="<!--{$arrSendData.RETURL|escape}-->">
			<input type="hidden" name="NG_RETURL" value="<!--{$arrSendData.NG_RETURL|escape}-->">
			<input type="hidden" name="EXITURL" value="<!--{$arrSendData.EXITURL|escape}-->">
			<input type="hidden" name="REMARKS3" value="<!--{$arrSendData.REMARKS3|escape}-->">
			<tr>
				<td bgcolor="#cccccc">
				<!--お支払方法・お届け時間の指定・その他お問い合わせここから-->
				<table width="700" border="0" cellspacing="1" cellpadding="10" summary=" ">
					<tr>
						<td width="10%" align="center" bgcolor="#f0f0f0" class="fs12">選択</td>
						<td width="90%" bgcolor="#f0f0f0" class="fs12">支払い方法</td>
					</tr>
					<!--{foreach key=key item=item from=$arrCreMet}-->
					<tr>
						<td align="center" bgcolor="#ffffff" class="fs12"><input type="radio" name="METHOD" id="<!--{$key}-->" value="<!--{$key}-->" style="<!--{$arrErr.arrCreMet|sfGetErrorColor}-->" <!--{if $smarty.post.arrCreMet == $key}-->checked<!--{/if}-->></td>
						<td bgcolor="#ffffff" class="fs12"><label for="<!--{$key}-->"><!--{$item|escape}--></label></td>
					</tr>
					<!--{/foreach}-->
				</table>
				<!--お支払方法・お届け時間の指定・その他お問い合わせここまで-->
				</td>
			</tr>

			<tr><td height="20"></td></tr>

			<tr>
				<td bgcolor="#cccccc">
				<!--分割回数ここから-->		
				<table width="700" border="0" cellspacing="1" cellpadding="10" summary=" ">
					<tr>
						<td width="20%" bgcolor="#f0f0f0" class="fs12n">分割回数</td>
						<td width="80%" bgcolor="#ffffff" class="fs12n">
						<!--{assign var=key value="PTIMES"}-->
						<span class="red"><!--{$arrErr[$key]}--></span>
						<select name="<!--{$key}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->">
						<option value="1" selected="">指定なし</option>
						<!--{html_options options=$arrCreDiv selected=$arrForm[$key].value}-->
						</select></td>
					</tr>
				</table>
				<!--分割回数ここまで-->
				</td>
			</tr>
			
			<tr><td height="20"></td></tr>

			<tr>
				<td align="center">
					<a href="<!--{$smarty.const.URL_DIR}-->" onclick="fnFormModeSubmit('form2', 'return', '', '');return false;" onmouseover="chgImgImageSubmit('<!--{$smarty.const.URL_DIR}-->img/common/b_back_on.gif',back03)" onmouseout="chgImgImageSubmit('<!--{$smarty.const.URL_DIR}-->img/common/b_back.gif',back03)"><img src="<!--{$smarty.const.URL_DIR}-->img/common/b_back.gif" width="150" height="30" alt="戻る" border="0" name="back03" id="back03" /></a>
					<img src="<!--{$smarty.const.URL_DIR}-->img/_.gif" width="20" height="" alt="" />
					<input type="image" onmouseover="chgImgImageSubmit('<!--{$smarty.const.URL_DIR}-->img/shopping/b_ordercomp_on.gif',this)" onmouseout="chgImgImageSubmit('<!--{$smarty.const.URL_DIR}-->img/shopping/b_ordercomp.gif',this)" src="<!--{$smarty.const.URL_DIR}-->img/shopping/b_ordercomp.gif" width="150" height="30" alt="ご注文完了ページへ" border="0" name="next" id="next" />
				</td>
			</tr>
			</form>
		</table>
		<!--▲MAIN ONTENTS-->
		</td>
	</tr>
</table>
<!--▲CONTENTS-->
</td></tr>
					
					<!--▼メイン下部-->
					<tr><td align="center">
										</td><tr>
					<!--▲メイン下部-->					
	
					</table>
				</td>

				<!--▼右ナビ-->
								<!--▲右ナビ-->

			</tr>
		</table>
		<td bgcolor="#ffffff"><img src="<!--{$smarty.const.URL_DIR}-->img/_.gif" width="9" height="1" alt="" /></td>
		<td bgcolor="#cccccc"><img src="<!--{$smarty.const.URL_DIR}-->img/_.gif" width="1" height="10" alt="" /></td>
		</td>
	</tr>
</table>

</div>
<!--▲MAIN-->

<!--▼FOTTER-->
<!--{if $arrPageLayout.footer_chk != 2}--> 
<!--{include file="`$smarty.const.HTML_PATH`user_data/include/footer.tpl"}-->
<!--{/if}-->
<!--▲FOTTER-->
</div>
<!--{* EBiSタグ表示用 *}-->
<!--{$tpl_mainpage|sfPrintEbisTag}-->
<!--{* アフィリエイトタグ表示用 *}-->
<!--{$tpl_conv_page|sfPrintAffTag:$tpl_aff_option}-->


</body><!-- ▲ ＢＯＤＹ部 エンド -->

</html>