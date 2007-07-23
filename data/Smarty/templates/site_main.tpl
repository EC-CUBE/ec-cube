<!--{*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *}-->
<body bgcolor="#ffe9e6" text="#555555" link="#3a75af" vlink="#3a75af" alink="#3a75af" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" onLoad="preLoadImg('<!--{$smarty.const.URL_DIR}-->'); <!--{$tpl_onload}-->">
<noscript>
<link rel="stylesheet" href="<!--{$smarty.const.URL_DIR}-->css/common.css" type="text/css" />
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
		<td bgcolor="#cccccc" width="1"><img src="<!--{$smarty.const.URL_DIR}-->img/_.gif" width="1" height="10" alt="" /></td>
		<td bgcolor="#ffffff" width="1"><img src="<!--{$smarty.const.URL_DIR}-->img/_.gif" width="5" height="1" alt="" /></td>
		<td bgcolor="#ffffff" align="left" width=100%> 

		<!--{*パンクズ-->
		<div id="pan"><span class="fs12n"><a href="<!--{$smarty.const.SITE_URL}-->index.php">トップページ</a> ＞ <span class="redst">お問い合わせ</span></span></div>
		<!--パンクズ*}-->

		<table width="100%" border="0" cellspacing="0" cellpadding="0" summary=" ">
			<tr valign="top">
				<!--▼左ナビ-->
				<!--{if $arrPageLayout.LeftNavi|@count > 0}-->
			        <td align="left">
			        <table border="0" cellspacing="0" cellpadding="0" summary=" ">
			        	<!--{foreach key=LeftNaviKey item=LeftNaviItem from=$arrPageLayout.LeftNavi}-->
				        <tr><td align="center">
				        <!-- ▼<!--{$LeftNaviItem.bloc_name}--> ここから-->
			        	<!--{if $LeftNaviItem.php_path != ""}-->
							<!--{include_php file=$LeftNaviItem.php_path}-->
						<!--{else}-->
							<!--{include file=$LeftNaviItem.tpl_path}-->
						<!--{/if}-->
				        <!-- ▲<!--{$LeftNaviItem.bloc_name}--> ここまで-->
				        </td></tr>
				    <!--{/foreach}-->
					</table>
					</td>
					<td bgcolor="#ffffff" width="5"><img src="<!--{$smarty.const.URL_DIR}-->img/_.gif" width="5" height="1" alt="" /></td>
				<!--{/if}-->
				<!--▲左ナビ-->
			
				<td align="center" width=100%>
			        <table border="0" cellspacing="0" cellpadding="0" summary=" ">
			        
					<!--▼メイン上部-->
					<!--{if $arrPageLayout.MainHead|@count > 0}-->
					<tr><td align="center">
				        <table border="0" cellspacing="0" cellpadding="0" summary=" ">
				        <!--{foreach key=MainHeadKey item=MainHeadItem from=$arrPageLayout.MainHead}-->
					        <tr><td height=3><td></tr>
					        <tr><td align="center">
					        <!-- ▼<!--{$MainHeadItem.bloc_name}--> ここから-->
				        	<!--{if $MainHeadItem.php_path != ""}-->
								<!--{include_php file=$MainHeadItem.php_path}-->
							<!--{else}-->
								<!--{include file=$MainHeadItem.tpl_path}-->
							<!--{/if}-->
					        <!-- ▲<!--{$MainHeadItem.bloc_name}--> ここまで-->
					        </td></tr>
						<!--{/foreach}-->
						</table>
					</td><tr>
					<!--{/if}-->
					<!--▲メイン上部-->
					
					<tr><td align="center"><!--{include file=$tpl_mainpage}--></td></tr>
					
					<!--▼メイン下部-->
					<tr><td align="center">
					<!--{if $arrPageLayout.MainFoot|@count > 0}-->
			        <table border="0" cellspacing="0" cellpadding="0" summary=" ">
				        <!--{foreach key=MainFootKey item=MainFootItem from=$arrPageLayout.MainFoot}-->
					        <tr><td height=3><td></tr>
					        <tr><td align="center">
					        <!-- ▼<!--{$MainFootItem.bloc_name}--> ここから-->
				        	<!--{if $MainFootItem.php_path != ""}-->
								<!--{include_php file=$MainFootItem.php_path}-->
							<!--{else}-->
								<!--{include file=$MainFootItem.tpl_path}-->
							<!--{/if}-->
					        <!-- ▲<!--{$MainFootItem.bloc_name}--> ここまで-->
					        </td></tr>
						<!--{/foreach}-->
						</table>
					<!--{/if}-->
					</td><tr>
					<!--▲メイン下部-->					
	
					</table>
				</td>

				<!--▼右ナビ-->
				<!--{if $arrPageLayout.RightNavi|@count > 0}-->
					<td bgcolor="#ffffff" width="5"><img src="<!--{$smarty.const.URL_DIR}-->img/_.gif" width="5" height="1" alt="" /></td>
					<td align="right" bgcolor="#ffffff">
				        <table border="0" cellspacing="0" cellpadding="0" summary=" ">
				        <!--{foreach key=RightNaviKey item=RightNaviItem from=$arrPageLayout.RightNavi}-->
					        <tr><td align="center">
					        <!-- ▼<!--{$RightNaviItem.bloc_name}--> ここから-->
				        	<!--{if $RightNaviItem.php_path != ""}-->
								<!--{include_php file=$RightNaviItem.php_path}-->
							<!--{else}-->
								<!--{include file=$RightNaviItem.tpl_path}-->
							<!--{/if}-->
					        <!-- ▲<!--{$RightNaviItem.bloc_name}--> ここまで-->
					        </td></tr>
						<!--{/foreach}-->
						</table>
					</td>
				<!--{/if}-->
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
</body>