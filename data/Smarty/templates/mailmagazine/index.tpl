<!--��-->
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
<title>-�ȡ���Ʋ�����󥿡��ͥåȥ���åԥ�-���ޥ���Ͽ�����</title>

</head>
	
<body onload="preLoadImg();">
<noscript>
<link rel="stylesheet" href="/css/common.css" type="text/css" />
</noscript>

<div align="center">
<a name="top" id="top"></a>

<!--��CONTENTS-->
<table width="780" cellspacing="0" cellpadding="0" summary=" ">
	<tr>
		<td bgcolor="#cccccc" width="1"><img src="/img/_.gif" width="1" height="10" alt="" /></td>
		<td bgcolor="#ffffff" width="9"><img src="/img/_.gif" width="9" height="1" alt="" /></td>

		<td bgcolor="#ffffff" align="left"> 
		<!--��MAIN CONTENTS-->
		<!--�ѥ󥯥�-->
		<div id="pan"><span class="fs12n"><a href="/index.php">�ȥåץڡ���</a> �� <span class="redst">���ޥ���Ͽ�����</span></span></div>
		<!--�ѥ󥯥�-->
		<table cellspacing="0" cellpadding="0" summary=" " id="container">
			<tr><td height="10"></td></tr>
			<tr valign="top">

				<!--��LEFT CONTENTS-->
				<td id="left">
				<!--���Хʡ�--><!--{include file=$tpl_banner}--><!--���Хʡ�-->
				
				<!--�����ʸ���-->
					<!--{include_php file=$tpl_search_products_php}-->
				<!--�����ʸ���-->
				
				<!--�����ʥ�-->
					<!--{include file=$tpl_leftnavi}-->
				<!--�����ʥ�-->
				</td>
				<!--��LEFT CONTENTS-->
				
				<!--��RIGHT CONTENTS-->
				<td id="right">
				<div id="maintitle"><img src="/img/right_mailmagazine/title.jpg" width="570" height="40" alt="���ޥ���Ͽ�����" /></div>
				<div id="comment"><span class="fs12">�ȡ���Ʋ�Υ��ޥ��ǡ����㤤������򤤤��᤯���åȡ��������ʤΤ��������ָ��ꥻ����ʤɡ������ʾ����������������Ͽ�Ϥ�����̵���Ǥ������ץ쥼��ȴ��⤢��ޤ��Τǡ�������Ͽ�򡪡�</span></div>
				<div><img src="/img/right_mailmagazine/subtitle01.gif" width="133" height="15" alt="�����ɤ򤴴�˾����" /></div>

				<div id="comment02"><span class="fs12">�����ɤ򤴴�˾�����ϡ��ʲ������ϥե�����˥��ɥ쥹�����Ϥ������ޥ����ۿ�����������塢����Ͽ����ץܥ���򥯥�å����Ƥ���������</span></div>
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
								�ۿ�������<input type="radio" name="kind" value="1" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" checked/>HTML�᡼�롡<input type="radio" name="kind" value="2" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" />�ƥ����ȥ᡼��</td>
							</tr>
						</table>
						</td>

						<td id="entry_button"><input type="image" onmouseover="chgImgImageSubmit('/img/right_mailmagazine/entry_on.gif',this)" onmouseout="chgImgImageSubmit('/img/right_mailmagazine/entry.gif',this)" src="/img/right_mailmagazine/entry.gif" width="130" height="30" alt="��Ͽ����" border="0" name="submit" id="entry" value="entry" /></td>
					</tr>
					</form>
				</table>
				<div id="line"><img src="/img/right_mailmagazine/line.gif" width="570" height="1" alt="" /></div>
				<div><img src="/img/right_mailmagazine/subtitle02.gif" width="150" height="15" alt="�ۿ���ߤ򤴴�˾����" /></div>
				<div id="comment02"><span class="fs12">�ۿ���ߤ򤴴�˾�����ϡ��ʲ������ϥե�����˥��ɥ쥹�����ϸ塢�ֲ������ץܥ���򥯥�å����Ƥ���������</span></div>
				<table cellspacing="0" cellpadding="20" summary=" " id="entry01">

					<form name="stop_form" id="form1" method="POST" action="./index.php">
					<input type="hidden" name="mode" value="stop">
					<tr>
						<td id="entry_form02">
						<!--{assign var=key value="stop"}-->
						<span class="red12"><!--{$arrErr[$key]}--></span>
						<input type="text" name="stop" size="40" class="box40" style="<!--{$arrErr[$key]|sfGetErrorColor}-->; ime-mode: disabled;"/></td>
						<td id="entry_button"><input type="image" onmouseover="chgImgImageSubmit('/img/right_mailmagazine/release_on.gif',this)" onmouseout="chgImgImageSubmit('/img/right_mailmagazine/release.gif',this)" src="/img/right_mailmagazine/release.gif" width="130" height="30" alt="�������" border="0" name="submit" id="release" value="release" /></td>
					</tr>
					</form>
				</table>
				<div><img src="/img/_.gif" width="1" height="40" alt="" /></div>
				</td>

				<!--��RIGHT CONTENTS-->
				
			</tr>
		</table>
		<!--��MAIN CONTENTS-->
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

<!--��CONTENTS-->
</div>
</body>
</html>