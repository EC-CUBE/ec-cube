<!--¢§CONTENTS-->
<table width="780" border="0" cellspacing="0" cellpadding="0" summary=" ">
	<tr>
		<td bgcolor="#ffffff" align="center" valign="top" height="400">
		<table width="740" border="0" cellspacing="0" cellpadding="0" summary=" ">
			<tr>
				<!--¢§SUB NAVI-->
				<td class="fs12n"><!--{include file=$sub_navipage}--></td>
				<!--¢¥SUB NAVI-->
			</tr><tr><td height="25"></td></tr>
		</table>
		<!--¢§MAIN CONTENTS-->
		<table width="740" border="0" cellspacing="0" cellpadding="0" summary=" ">
			<tr>
				<td class="fs14n"><strong>¢£TV¾¦ÉÊ´ÉÍý</strong></td>
			</tr>
			<tr><td height="10"></td></tr>
		</table>
		<form name="form1" id="form1" method="post" action="<!--{$smarty.server.PHP_SELF}-->" enctype="multipart/form-data">
		<input type="hidden" name="mode" value="edit">
		<input type="hidden" name="image_key" value="">
		<!--{foreach key=key item=item from=$arrHidden}-->
		<input type="hidden" name="<!--{$key}-->" value="<!--{$item|escape}-->">
		<!--{/foreach}-->
		<!--{section name=cnt loop=$smarty.const.TV_PRODUCTS_MAX}-->
		<table bgcolor="#f0f0f0" width="740" border="1" cellspacing="1" cellpadding="5" summary=" ">
		<tr class="fs12n">
			<td bgcolor="#f0f0f0" colspan="2"><strong><!--{$smarty.section.cnt.iteration}--></strong></td>
		</tr>
		<tr>
		<td>
		<!--{assign var=key value="tv_product_id`$smarty.section.cnt.iteration`"}-->
		<input type="hidden" name="<!--{$key}-->" value="<!--{$arrProducts[$smarty.section.cnt.iteration].product_id|escape}-->">
		<!--{assign var=key value="tv_product_image`$smarty.section.cnt.iteration`"}-->
		<span class="red12"><!--{$arrErr[$key]}--></span>
		<!--{if $arrFile[$key].filepath != ""}-->
		<img src="<!--{$arrFile[$key].filepath}-->" alt="<!--{$arrForm.name|escape}-->" width="<!--{$arrFile[$key].width}-->" height="<!--{$arrFile[$key].height}-->" /><!--{* <a href="" onclick="fnModeSubmit('delete_image', 'image_key', '<!--{$key}-->'); return false;">[²èÁü¤Î¼è¤ê¾Ã¤·]</a><br>*}-->
		<!--{else}-->
		<img src="<!--{$smarty.const.NO_IMAGE_URL}-->" alt="" width="170" height="95" />
		<!--{/if}-->
		</td>
		<td bgcolor="#ffffff" width="557" class="fs12n">
		<!--{if $arrFile[$key].filepath != ""}-->
			<input type="checkbox" name="tv_product_delete<!--{$smarty.section.cnt.iteration}-->" value="1">ºï½ü<br/><br/>
		<!--{/if}-->		
			<input type="file" name="<!--{$key}-->" size="60" class="box60" style="<!--{$arrErr[$key]|sfGetErrorColor}-->">
			<input type="button" name="btn" onclick="fnModeSubmit('upload_image', 'image_key', '<!--{$key}-->')" value="¥¢¥Ã¥×¥í¡¼¥É"><br/><br/>
			<!--{* ¾¦ÉÊÌ¾:<!--{if $arrProducts[$smarty.section.cnt.iteration].name}-->
			<!--{$arrProducts[$smarty.section.cnt.iteration].name|escape}-->
			<!--{/if}-->
			<br><input type="button" name="btn" value="ÁªÂò" onclick="win02('./tv_products_search.php?no=<!--{$smarty.section.cnt.iteration}-->','product_search','500','500'); return false;" >
			*}-->
			</td>
		</tr>
		</table>
		
		<!--{/section}-->
		<table width="740">
		<tr>
		<td align="center"><input type="button" name="bot" value="ÅÐÏ¿¤¹¤ë" onclick="fnModeSubmit('regist','','');"></td>
		</tr>
		</form>
		<!--¢¥ÅÐÏ¿¥Õ¥©¡¼¥à¤³¤³¤«¤é-->

		<!--¢¥MAIN CONTENTS-->
		</td>
	</tr>
</table>
<!--¢¥CONTENTS-->
