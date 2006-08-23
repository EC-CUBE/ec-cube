<!--　-->
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ja" lang="ja">

<head>
<meta http-equiv="content-type" content="application/xhtml+xml; charset=EUC-JP" />
<meta http-equiv="content-script-type" content="text/javascript" />
<meta http-equiv="content-style-type" content="text/css" />
<link rel="stylesheet" href="/user_data/css/contents.css" type="text/css" media="all" />
<link rel="stylesheet" href="/css/layout/mailmagazine/index.css" type="text/css" media="all" />
<script type="text/javascript" src="/js/css.js"></script>
<script type="text/javascript" src="/js/navi.js"></script>
<script type="text/javascript" src="/js/win_op.js"></script>
<title>-トーカ堂　インターネットショッピング-メルマガ登録・解除</title>

</head>
	
<body onload="preLoadImg();">
<noscript>
<link rel="stylesheet" href="/css/common.css" type="text/css" />
</noscript>

<div align="center">
<a name="top" id="top"></a>

<!--▼CONTENTS-->
<table width="780" cellspacing="0" cellpadding="0" summary=" ">
	<tr>
		<td bgcolor="#cccccc" width="1"><img src="/img/_.gif" width="1" height="10" alt="" /></td>
		<td bgcolor="#ffffff" width="9"><img src="/img/_.gif" width="9" height="1" alt="" /></td>

		<td bgcolor="#ffffff" align="left"> 
		<!--▼MAIN CONTENTS-->
		<!--パンクズ-->
		<div id="pan"><span class="fs12n"><a href="/index.php">トップページ</a> ＞ <span class="redst">メルマガ登録・解除</span></span></div>
		<!--パンクズ-->
		<table cellspacing="0" cellpadding="0" summary=" " id="container">
			<tr><td height="10"></td></tr>
			<tr valign="top">

				<!--▼LEFT CONTENTS-->
				<td id="left">
				<!--▼バナー--><!--{include file=$tpl_banner}--><!--▲バナー-->
				
				<!--▼商品検索-->
					<!--{include_php file=$tpl_search_products_php}-->
				<!--▲商品検索-->
				
				<!--▼左ナビ-->
					<!--{include file=$tpl_leftnavi}-->
				<!--▲左ナビ-->
				</td>
				<!--▲LEFT CONTENTS-->
				
				<!--▼RIGHT CONTENTS-->
				<td id="right">
				<div id="maintitle"><img src="/img/right_mailmagazine/title.jpg" width="570" height="40" alt="メルマガ登録・解除" /></div>
				<div id="comment"><span class="fs12">トーカ堂のメルマガで、お買い得情報をいち早くゲット！！新商品のご案内や期間限定セールなど、お得な情報が盛りだくさん。登録はもちろん無料です！！プレゼント企画もありますので、ぜひ登録を！！</span></div>
				<div><img src="/img/right_mailmagazine/subtitle01.gif" width="133" height="15" alt="ご購読をご希望の方" /></div>

				<div id="comment02"><span class="fs12">ご購読をご希望の方は、以下の入力フォームにアドレスを入力し、メルマガの配信形式を選択後、「登録する」ボタンをクリックしてください。</span></div>
				<table cellspacing="0" cellpadding="20" summary=" " id="entry01">
					<form name="entry_form" id="form1" method="POST" action="./index.php">
					<input type="hidden" name="mode" value="entry">
					<tr>
						<td id="entry_form01">
						<table cellspacing="0" cellpadding="0" summary=" " id="entry02">
							<!--{assign var=key value="entry"}-->
							<tr><span class="red12"><!--{$arrErr[$key]}--></span>
								<td>
								<input type="text" name="entry" size="40" class="box40" style="<!--{$arrErr[$key]|sfGetErrorColor}-->; ime-mode: disabled;"/></td>

							</tr>
							<tr><td height="5"></td></tr>
							<!--{assign var=key value="kind"}-->
							<tr><span class="red12"><!--{$arrErr[$key]}--></span>
								<td class="fs12">
								配信形式：<input type="radio" name="kind" value="1" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" checked/>HTMLメール　<input type="radio" name="kind" value="2" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" />テキストメール</td>
							</tr>
						</table>
						</td>

						<td id="entry_button"><input type="image" onmouseover="chgImgImageSubmit('/img/right_mailmagazine/entry_on.gif',this)" onmouseout="chgImgImageSubmit('/img/right_mailmagazine/entry.gif',this)" src="/img/right_mailmagazine/entry.gif" width="130" height="30" alt="登録する" border="0" name="submit" id="entry" value="entry" /></td>
					</tr>
					</form>
				</table>
				<div id="line"><img src="/img/right_mailmagazine/line.gif" width="570" height="1" alt="" /></div>
				<div><img src="/img/right_mailmagazine/subtitle02.gif" width="150" height="15" alt="配信停止をご希望の方" /></div>
				<div id="comment02"><span class="fs12">配信停止をご希望の方は、以下の入力フォームにアドレスを入力後、「解除する」ボタンをクリックしてください。</span></div>
				<table cellspacing="0" cellpadding="20" summary=" " id="entry01">

					<form name="stop_form" id="form1" method="POST" action="./index.php">
					<input type="hidden" name="mode" value="stop">
					<tr>
						<td id="entry_form02">
						<!--{assign var=key value="stop"}-->
						<span class="red12"><!--{$arrErr[$key]}--></span>
						<input type="text" name="stop" size="40" class="box40" style="<!--{$arrErr[$key]|sfGetErrorColor}-->; ime-mode: disabled;"/></td>
						<td id="entry_button"><input type="image" onmouseover="chgImgImageSubmit('/img/right_mailmagazine/release_on.gif',this)" onmouseout="chgImgImageSubmit('/img/right_mailmagazine/release.gif',this)" src="/img/right_mailmagazine/release.gif" width="130" height="30" alt="解除する" border="0" name="submit" id="release" value="release" /></td>
					</tr>
					</form>
				</table>
				<div><img src="/img/_.gif" width="1" height="40" alt="" /></div>
				</td>

				<!--▲RIGHT CONTENTS-->
				
			</tr>
		</table>
		<!--▲MAIN CONTENTS-->
		</td>
		<td bgcolor="#ffffff" width="10"><img src="/img/_.gif" width="9" height="1" alt="" /></td>
		<td bgcolor="#cccccc" width="1"><img src="/img/_.gif" width="1" height="10" alt="" />
<!-- EBiS start -->
<script type="text/javascript">
if ( location.protocol == 'http:' ){ 
	strServerName = 'http://daikoku.ebis.ne.jp'; 
} else { 
	strServerName = 'https://secure2.ebis.ne.jp/ver3';
}
cid = 'tqYg3k6U'; pid = 'mailmagazine_index'; m1id=''; a1id=''; o1id=''; o2id=''; o3id=''; o4id=''; o5id='';
document.write("<scr" + "ipt type=\"text\/javascript\" src=\"" + strServerName + "\/ebis_tag.php?cid=" + cid + "&pid=" + pid + "&m1id=" + m1id + "&a1id=" + a1id + "&o1id=" + o1id + "&o2id=" + o2id + "&o3id=" + o3id + "&o4id=" + o4id + "&o5id=" + o5id + "\"><\/scr" + "ipt>");
</script>
<!-- EBiS end -->
		</td>
	</tr>
</table>

<!--▲CONTENTS-->
</div>
</body>
</html>