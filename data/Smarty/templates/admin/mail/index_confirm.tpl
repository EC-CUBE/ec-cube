
<!--��CONTENTS-->
<table width="780" border="0" cellspacing="0" cellpadding="0" summary=" ">
	<tr>
		<td bgcolor="#ffffff" align="center" valign="top" height="400">
		<table width="740" border="0" cellspacing="0" cellpadding="0" summary=" ">
			<tr>
				<!--��SUB NAVI-->
				<td class="fs12n"><!--{include file=$tpl_subnavi}--></td>
				<!--��SUB NAVI-->
			</tr><tr><td height="25"></td></tr>
		</table>

		<!--��MAIN CONTENTS-->
				
		
		<!--���ʤ�������ꤳ������-->
		<form name="form1" id="form1" method="post" action="<!--{$smarty.server.PHP_SELF}-->">
		<!--{foreach key=key item=val from=$arrHidden}-->	
			<input type="hidden" name="<!--{$key}-->" value="<!--{$val|escape}-->">
		<!--{/foreach}-->
		
		<!--{if ! $all_flag}-->
		<br />
		<table width="740" border="0" cellspacing="0" cellpadding="0" summary=" ">
			<tr>
				<td class="fs14n"><strong>���ۿ����</strong></td>
			</tr>
			<tr><td height="10"></td></tr>
		</table>
		<br />		
		<!--�������ơ��֥뤳������-->
		<table width="740" border="0" cellspacing="1" cellpadding="5" summary=" " bgcolor="#cccccc">
			<tr class="fs12n">
				<td bgcolor="#f0f0f0" width="110">�ܵ�̾</td>
				<td bgcolor="#ffffff" width="248"><!--{$list_data.name|escape|default:"(̤����)"}-->	</td>
				<td bgcolor="#f0f0f0" width="110">�ܵ�̾�ʥ��ʡ�</td>
				<td bgcolor="#ffffff" width="249"><!--{$list_data.kana|escape|default:"(̤����)"}-->	</td>
			</tr>
			<tr class="fs12n">
				<td bgcolor="#f0f0f0" width="110">��ƻ�ܸ�</td>
				<td bgcolor="#ffffff" width="248"><!--{$arrPref[$list_data.pref]|escape|default:"(̤����)"}--></td>
				<td bgcolor="#f0f0f0" width="110">TEL</td>
				<td bgcolor="#ffffff" width="249"><!--{$list_data.tel|escape|default:"(̤����)"}--></td>
			</tr>
			<tr class="fs12n">
				<td bgcolor="#f0f0f0" width="110">����</td>
				<td bgcolor="#ffffff" width="248">
					<!--{if $list_data.sex}-->
						<!--{foreach item=sub from=$list_data.sex}-->
							<!--{$arrSex[$sub]}-->
						<!--{/foreach}-->
					<!--{else}-->
						(̤����)
					<!--{/if}-->
				</td>
				<td bgcolor="#f0f0f0" width="110">���������</td>
				<td bgcolor="#ffffff" width="249"><!--{if $list_data.customer eq "1"}-->���<!--{elseif $list_data.customer eq "2"}-->����<!--{/if}--></td>
			</tr>
			<tr class="fs12n">
				<td bgcolor="#f0f0f0" width="110">�᡼�륢�ɥ쥹</td>
				<td bgcolor="#ffffff" width="607" colspan="3"><!--{$list_data.email|escape|default:"(̤����)"}--></td>
			</tr>
			</tr>
			<tr class="fs12n">
				<td bgcolor="#f0f0f0" width="110">������</td>
				<td bgcolor="#ffffff" width="607" colspan="3">
					<!--{$list_data.birth_month|escape|default:"(̤����)"}--><!--{if $list_data.birth_month}-->��<!--{/if}-->
				</td>
			</tr>	
			<tr class="fs12n">
				<td bgcolor="#f0f0f0" width="110">��ǯ����</td>
				<td bgcolor="#ffffff" width="607" colspan="3">
					<!--{if $list_data.b_start_year && $list_data.b_start_month && $list_data.b_start_day}-->
						<!--{$list_data.b_start_year}-->ǯ<!--{$list_data.b_start_month}-->��<!--{$list_data.b_start_day}-->��
					<!--{else}-->
						��̤�����
					<!--{/if}-->
					��-��
					<!--{if $list_data.b_end_year && $list_data.b_end_month && $list_data.b_end_day}-->
						<!--{$list_data.b_end_year}-->ǯ<!--{$list_data.b_end_month}-->��<!--{$list_data.b_end_day}-->��
					<!--{else}-->
						��̤�����
					<!--{/if}-->
				</td>
			</tr>
			<tr class="fs12n">
				<td bgcolor="#f0f0f0" width="110">����</td>
				<td bgcolor="#ffffff" width="607" colspan="3">
					<!--{if $list_data.job}-->
						<!--{foreach item=sub from=$list_data.job}-->
							<!--{$arrJob[$sub]|escape}-->
						<!--{/foreach}-->
					<!--{else}-->
						(̤����)
					<!--{/if}-->
				</td>
			</tr>
			<tr class="fs12n">
				<td bgcolor="#f0f0f0" width="110">��Ͽ��������</td>
				<td bgcolor="#ffffff" width="607" colspan="3">
					<!--{if $list_data.start_year && $list_data.start_month && $list_data.start_day}-->
						<!--{$list_data.start_year}-->ǯ<!--{$list_data.start_month}-->��<!--{$list_data.start_day}-->��
					<!--{else}-->
						��̤�����
					<!--{/if}-->
					��-��
					<!--{if $list_data.end_year && $list_data.end_month && $list_data.end_day}-->
						<!--{$list_data.end_year}-->ǯ<!--{$list_data.end_month}-->��<!--{$list_data.end_day}-->��
					<!--{else}-->
						��̤�����
					<!--{/if}-->
				</td>
			</tr>			
			<tr class="fs12n">
				<td bgcolor="#f0f0f0" width="110">�᡼���ۿ�����<span class="red"> *</span></td>
				<td bgcolor="#ffffff" width="248">
					<!--{$arrHtmlmail[$list_data.htmlmail]|default:"ξ��"}-->
				<td bgcolor="#f0f0f0" width="110">�������</td>
				<td bgcolor="#ffffff" width="249">
					<!--{if $list_data.buy_times_from}--><!--{$list_data.buy_times_from|escape}-->��<!--{else}-->��̤�����<!--{/if}-->��-��
					<!--{if $list_data.buy_times_to}--><!--{$list_data.buy_times_to|escape}-->��<!--{else}-->��̤�����<!--{/if}-->
				</td>
			</tr>
			<tr class="fs12n">
				<td bgcolor="#f0f0f0" width="110">�ǽ�������</td>
				<td bgcolor="#ffffff" width="607" colspan="3">
					<!--{if $list_data.buy_start_year && $list_data.buy_start_month && $list_data.buy_start_day}-->
						<!--{$list_data.buy_start_year}-->ǯ<!--{$list_data.buy_start_month}-->��<!--{$list_data.buy_start_day}-->��
					<!--{else}-->
						��̤�����
					<!--{/if}-->
					��-��
					<!--{if $list_data.buy_end_year && $list_data.buy_end_month && $list_data.buy_end_day}-->
						<!--{$list_data.buy_end_year}-->ǯ<!--{$list_data.buy_end_month}-->��<!--{$list_data.buy_end_day}-->��
					<!--{else}-->
						��̤�����
					<!--{/if}-->
				</td>
			</tr>
		</table>
		<!--�������ơ��֥뤳���ޤ�-->
		<!--{/if}-->
		
		<br />
		<br />
		<span class="fs12">
		<!--{if $dataCnt > 0}-->
			<!--{$dataCnt}-->�郎�������ޤ�����
		<!--{else}-->
			��������ǡ����Ϥ���ޤ���
		<!--{/if}-->
		</span>
		<br />
		<br />
		<input type="hidden" name="mode" value="input">
		<input type="submit" name="back01" value="���" onClick="return fnInsertValAndSubmit( document.form1, 'mode', 'back', '' )"/>
		��<!--{if $search_data}--><input type="submit" name="subm" value="����" /><!--{/if}-->
		</form>		
		<!--��MAIN CONTENTS-->
		</td>
	</tr>
</table>
<!--��CONTENTS-->