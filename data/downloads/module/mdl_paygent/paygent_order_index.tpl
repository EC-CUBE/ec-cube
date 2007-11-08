<tr bgcolor="#636469" align="center" class="fs12n">
	<td width="60"><span class="white">受注日</span></td>
	<td width="70"><span class="white">受注番号</span></td>
	<td width="120"><span class="white">顧客名</span></td>
	<td width="75"><span class="white">支払方法</span></td>
	<td width="80"><span class="white">購入金額(円)</span></td>
	<td width="60"><span class="white">発送日</span></td>
	<td width="75"><span class="white">対応状況</span></td>
	<td width="80"><span class="white">カード状況</span></td>								
	<td width="50"><span class="white">編集</span></td>
	<td width="50"><span class="white">メール</span></td>
	<td width="50"><span class="white">削除</span></td>
</tr>

<!--{section name=cnt loop=$arrResults}-->
<!--{assign var=status value="`$arrResults[cnt].status`"}-->
<tr bgcolor="<!--{$arrORDERSTATUS_COLOR[$status]}-->" class="fs12n">
	<td align="center"><!--{$arrResults[cnt].create_date|sfDispDBDate}--></td>
	<td align="center"><!--{$arrResults[cnt].order_id}--></td>
	<td><!--{$arrResults[cnt].order_name01|escape}--> <!--{$arrResults[cnt].order_name02|escape}--></td>
	<!--{assign var=payment_id value="`$arrResults[cnt].payment_id`"}-->
	<td align="center"><!--{$arrPayment[$payment_id]}--></td>
	<td align="right"><!--{$arrResults[cnt].total|number_format}--></td>
	<td align="center"><!--{$arrResults[cnt].commit_date|sfDispDBDate|default:"未発送"}--></td>
	<td align="center"><!--{$arrORDERSTATUS[$status]}--></td>
	<!--{assign var=memo09 value=`$arrResults[cnt].memo09`}-->
	<td align="center"><!--{$arrDispKind[$memo09]}--></span></td>
	<td align="center"><a href="<!--{$smarty.server.PHP_SELF|escape}-->" onclick="fnChangeAction('<!--{$smarty.const.URL_ORDER_EDIT}-->'); fnModeSubmit('pre_edit', 'order_id', '<!--{$arrResults[cnt].order_id}-->'); return false;"><span class="icon_edit">編集</span></a></td>
	<td align="center"><a href="<!--{$smarty.server.PHP_SELF|escape}-->" onclick="fnChangeAction('<!--{$smarty.const.URL_ORDER_MAIL}-->'); fnModeSubmit('pre_edit', 'order_id', '<!--{$arrResults[cnt].order_id}-->'); return false;"><span class="icon_mail">通知</span></a></td>
	<td align="center"><a href="<!--{$smarty.server.PHP_SELF|escape}-->" onclick="fnModeSubmit('delete', 'order_id', <!--{$arrResults[cnt].order_id}-->); return false;"><span class="icon_delete">削除</span></a></td>
</tr>
<!--{/section}-->