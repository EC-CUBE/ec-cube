<div align="center">�ѹ���ǧ</div>
<hr>
<form name="form1" id="form1" method="post" action="<!--{$smarty.server.PHP_SELF|escape}-->">
	<input type="hidden" name="mode" value="complete">
	<!--{foreach from=$list_data key=key item=item}-->
		<input type="hidden" name="<!--{$key|escape}-->" value="<!--{$item|escape}-->">
	<!--{/foreach}-->
	���������ƤǤ���Ͽ���Ƥ������Ǥ�����<br>
	<br>
	�ڎҎ��َ��Ďގڎ���<br>
	<!--{$list_data.email|escape}--><br>
	<br>

	�ڎʎߎ��܎��Ď޳�ǧ�Ѽ����<br>
	<!--{$arrReminder[$list_data.reminder]|escape}--><br>
	<br>

	�ڼ����������<br>
	<!--{$list_data.reminder_answer|escape}--><br>
	<br>

	�ڸĿ;����<br>
	<!--{$list_data.name01|escape}-->��<!--{$list_data.name02|escape}--><br>
	<!--{$list_data.kana01|escape}-->��<!--{$list_data.kana02|escape}--><br>
	<!--{if $list_data.sex eq 1}-->����<!--{else}-->����<!--{/if}--><br>
	<!--{if strlen($list_data.year) > 0 && strlen($list_data.month) > 0 && strlen($list_data.day) > 0}--><!--{$list_data.year|escape}-->ǯ<!--{$list_data.month|escape}-->��<!--{$list_data.day|escape}-->�����ޤ�<!--{else}-->̤��Ͽ<!--{/if}--><br>
	��<!--{$list_data.zip01|escape}--> - <!--{$list_data.zip02|escape}--><br>
	<!--{$arrPref[$list_data.pref]|escape}--><!--{$list_data.addr01|escape}--><!--{$list_data.addr02|escape}--><br>
	<!--{$list_data.tel01|escape}-->-<!--{$list_data.tel02|escape}-->-<!--{$list_data.tel03|escape}--><br>
	<br>
	
	�ڎҎ��َώ��ގ��ގݎޡ�<br>
	<!--{if $list_data.mail_flag eq 2}-->��˾����<!--{else}-->��˾���ʤ�<!--{/if}--><br>
	<br>

	<input type="submit" name="submit" value="�ѹ�">
</form>

<br>
<hr>

<a href="<!--{$smarty.const.URL_CART_TOP}-->" accesskey="9"><!--{9|numeric_emoji}-->�����򸫤�</a><br>
<a href="<!--{$smarty.const.URL_SITE_TOP}-->" accesskey="0"><!--{0|numeric_emoji}-->TOP�ڡ�����</a><br>

<br>

<!-- ���եå��� �������� -->
<!--{include file='footer.tpl'}-->
<!-- ���եå��� �����ޤ� -->
