<!--��CONTENTS-->
<table width="780" border="0" cellspacing="0" cellpadding="0" summary=" ">
	<tr>
		<td bgcolor="#ffffff" align="center" valign="top" height="400">
		<table width="740" border="0" cellspacing="0" cellpadding="0" summary=" ">
			<tr>
				<!--��SUB NAVI-->
				<td class="fs12n">
				<!--{include file=$tpl_subnavi}-->
				</td>
				<!--��SUB NAVI-->
				</tr><tr><td height="25"></td></tr>
		</table>
		
		<!--��MAIN CONTENTS-->
		<table width="740" border="0" cellspacing="0" cellpadding="0" summary=" ">
			<tr>
				<td class="fs14n"><strong>�������ڡ�����Ͽ</strong></td>
			</tr>
			<tr><td height="10"></td></tr>
		</table>
		
		<form name="form1" id="form1" method="post" action="<!--{$smarty.server.PHP_SELF}-->">
		<input type="hidden" name="mode" value="campaign_regist">
		<input type="hidden" name="campaign_id" value="<!--{$smarty.post.campaign_id}-->">
		<!--{foreach key=key item=item from=$arrSearchHidden}-->
		<input type="hidden" name="<!--{$key}-->" value="<!--{$item|escape}-->">
		<!--{/foreach}-->
		<!--�������ơ��֥뤳������-->
		<table width="740" border="0" cellspacing="1" cellpadding="5" summary=" " bgcolor="#cccccc">			
			<tr class="fs12n">
				<td bgcolor="#f0f0f0" width="110">�����ڡ������<span class="red"> *</span></td>
				<td bgcolor="#ffffff" width="607" colspan="3">
				<span class="red"><!--{$arrErr.startyear}--></span>
				<span class="red"><!--{$arrErr.startmonth}--></span>
				<span class="red"><!--{$arrErr.startday}--></span>
				<span class="red"><!--{$arrErr.starthour}--></span>
				<span class="red"><!--{$arrErr.endyear}--></span>
				<span class="red"><!--{$arrErr.endmonth}--></span>
				<span class="red"><!--{$arrErr.endday}--></span>
				<span class="red"><!--{$arrErr.endhour}--></span>		
				<select name="startyear" style="<!--{$arrErr.startyear|sfGetErrorColor}-->">
				<option value="">----</option>
				<!--{html_options options=$arrStartYear selected=$arrCamp.startyear}-->
				</select>ǯ
				<select name="startmonth" style="<!--{$arrErr.startmonth|sfGetErrorColor}-->">
				<option value="">--</option>
				<!--{html_options options=$arrStartMonth selected=$arrCamp.startmonth}-->
				</select>��
				<select name="startday" style="<!--{$arrErr.startday|sfGetErrorColor}-->">
				<option value="">--</option>
				<!--{html_options options=$arrStartDay selected=$arrCamp.startday}-->
				</select>��
				<select name="starthour" style="<!--{$arrErr.starthour|sfGetErrorColor}-->">
				<option value="">--</option>
				<!--{html_options options=$arrStartHour selected=$arrCamp.starthour}-->
				</select>����
				<select name="endyear" style="<!--{$arrErr.endyear|sfGetErrorColor}-->">
				<option value="">----</option>
				<!--{html_options options=$arrEndYear selected=$arrCamp.endyear}-->
				</select>ǯ
				<select name="endmonth" style="<!--{$arrErr.endmonth|sfGetErrorColor}-->">
				<option value="">--</option>
				<!--{html_options options=$arrEndMonth selected=$arrCamp.endmonth}-->
				</select>��
				<select name="endday" style="<!--{$arrErr.endday|sfGetErrorColor}-->">
				<option value="">--</option>
				<!--{html_options options=$arrEndDay selected=$arrCamp.endday}-->
				</select>��
				<select name="endhour" style="<!--{$arrErr.endhour|sfGetErrorColor}-->">
				<option value="">--</option>
				<!--{html_options options=$arrEndHour selected=$arrCamp.endhour}-->
				</select>��
				</td>
			</tr>
			<tr class="fs12n">
				<td bgcolor="#f0f0f0" width="110">�����ڡ���̾<span class="red"> *</span></td>
				<td bgcolor="#ffffff" width="607" colspan="3">
				<!--{assign var=key value="campaign_name"}-->
				<span class="red12"><!--{$arrErr[$key]}--></span>
				<input type="text" name="<!--{$key}-->" value="<!--{$arrCamp[$key]|escape}-->" maxlength="<!--{$smarty.const.STEXT_LEN}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" size="30" class="box30">
				</td>
			</tr>
			<tr class="fs12n">
				<td bgcolor="#f0f0f0" width="110">�ݥ������ͿΨ<span class="red"> *</span></td>
				<td bgcolor="#ffffff" width="607">
				<!--{assign var=key value="campaign_point_rate"}-->
				<span class="red12"><!--{$arrErr[$key]}--></span>
				<input type="text" name="<!--{$key}-->" value="<!--{$arrCamp[$key]|escape}-->" maxlength="<!--{$smarty.const.INT_LEN}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" size="6" class="box6" />
				�󡡾������ʲ��ڤ�Τ�</td>
			</tr>
			
		</table>
		<!--�������ơ��֥뤳���ޤ�-->
		
		<br />
		<input type="button" name="back" value="���" onclick="fnChangeAction('../products/index.php'); fnModeSubmit('search', '', '');">��<input type="submit" value="��Ͽ" />
		</form>
		
		</td>
	</tr>

</table>
<!--��CONTENTS-->