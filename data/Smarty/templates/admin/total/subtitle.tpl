<!--{*
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
*}-->
<!--{if $arrForm.page.value == "term" || $arrForm.page.value == ""}-->
	<td class="fs12n">
	<strong>�����̽���</strong>&nbsp;��
	<!--{if $smarty.post.type == 'day' || $smarty.post.type == ''}-->
	<span class="over">����</span>&nbsp;
	<!--{else}-->
	<a href="<!--{$smarty.server.PHP_SELF|escape}-->" onclick="document.form1.mode.value='search'; return fnSetFormSubmit('form1', 'type', 'day');">����</a>&nbsp;
	<!--{/if}-->
	<!--{if $smarty.post.type == 'month'}-->
	<span class="over">����</span>&nbsp;
	<!--{else}-->
	<a href="<!--{$smarty.server.PHP_SELF|escape}-->" onclick="document.form1.mode.value='search'; return fnSetFormSubmit('form1', 'type', 'month');">����</a>&nbsp;
	<!--{/if}-->
	<!--{if $smarty.post.type == 'year'}-->
	<span class="over">ǯ��</span>&nbsp;
	<!--{else}-->
	<a href="<!--{$smarty.server.PHP_SELF|escape}-->" onclick="document.form1.mode.value='search'; return fnSetFormSubmit('form1', 'type', 'year');">ǯ��</a>&nbsp;
	<!--{/if}-->
	<!--{if $smarty.post.type == 'wday'}-->
	<span class="over">������</span>&nbsp;
	<!--{else}-->
	<a href="<!--{$smarty.server.PHP_SELF|escape}-->" onclick="document.form1.mode.value='search'; return fnSetFormSubmit('form1', 'type', 'wday');">������</a>&nbsp;
	<!--{/if}-->
	<!--{if $smarty.post.type == 'hour'}-->
	<span class="over">������</span>&nbsp;
	<!--{else}-->
	<a href="<!--{$smarty.server.PHP_SELF|escape}-->" onclick="document.form1.mode.value='search'; return fnSetFormSubmit('form1', 'type', 'hour');">������</a>&nbsp;
	<!--{/if}-->
	��
	</td>
<!--{/if}-->

<!--{if $arrForm.page.value == "products"}-->
	<td class="fs12n">
	<strong>�����̽���</strong>&nbsp;��
	<!--{if $smarty.post.type == 'all' || $smarty.post.type == ''}-->
	<span class="over">����</span>&nbsp;
	<!--{else}-->
	<a href="<!--{$smarty.server.PHP_SELF|escape}-->" onclick="document.form1.mode.value='search'; return fnSetFormSubmit('form1', 'type', 'all');">����</a>&nbsp;
	<!--{/if}-->
	<!--{if $smarty.post.type == 'member'}-->
	<span class="over">���</span>&nbsp;
	<!--{else}-->
	<a href="<!--{$smarty.server.PHP_SELF|escape}-->" onclick="document.form1.mode.value='search'; return fnSetFormSubmit('form1', 'type', 'member');">���</a>&nbsp;
	<!--{/if}-->
	<!--{if $smarty.post.type == 'nonmember'}-->
	<span class="over">����</span>&nbsp;
	<!--{else}-->
	<a href="<!--{$smarty.server.PHP_SELF|escape}-->" onclick="document.form1.mode.value='search'; return fnSetFormSubmit('form1', 'type', 'nonmember');">����</a>&nbsp;
	<!--{/if}-->
	��
	</td>
<!--{/if}-->

<!--{if $arrForm.page.value == "age"}-->
	<td class="fs12n">
	<strong>ǯ���̽���</strong>&nbsp;��
	<!--{if $smarty.post.type == 'all' || $smarty.post.type == ''}-->
	<span class="over">����</span>&nbsp;
	<!--{else}-->
	<a href="<!--{$smarty.server.PHP_SELF|escape}-->" onclick="document.form1.mode.value='search'; return fnSetFormSubmit('form1', 'type', 'all');">����</a>&nbsp;
	<!--{/if}-->
	<!--{if $smarty.post.type == 'member'}-->
	<span class="over">���</span>&nbsp;
	<!--{else}-->
	<a href="<!--{$smarty.server.PHP_SELF|escape}-->" onclick="document.form1.mode.value='search'; return fnSetFormSubmit('form1', 'type', 'member');">���</a>&nbsp;
	<!--{/if}-->
	<!--{if $smarty.post.type == 'nonmember'}-->
	<span class="over">����</span>&nbsp;
	<!--{else}-->
	<a href="<!--{$smarty.server.PHP_SELF|escape}-->" onclick="document.form1.mode.value='search'; return fnSetFormSubmit('form1', 'type', 'nonmember');">����</a>&nbsp;
	<!--{/if}-->
	��
	</td>
<!--{/if}-->

<!--{if $arrForm.page.value == "job"}-->
	<td class="fs12n">
	<strong>�����̽���</strong>&nbsp;��
	<span class="over">����</span>
	��
	<!--{*��
	<!--{if $smarty.post.type == 'all' || $smarty.post.type == ''}-->
	<span class="over">����</span>&nbsp;
	<!--{else}-->
	<a href="<!--{$smarty.server.PHP_SELF|escape}-->" onclick="document.form1.mode.value='search'; return fnSetFormSubmit('form1', 'type', 'all');">����</a>&nbsp;
	<!--{/if}-->
	<!--{if $smarty.post.type == 'member'}-->
	<span class="over">���</span>&nbsp;
	<!--{else}-->
	<a href="<!--{$smarty.server.PHP_SELF|escape}-->" onclick="document.form1.mode.value='search'; return fnSetFormSubmit('form1', 'type', 'member');">���</a>&nbsp;
	<!--{/if}-->
	<!--{if $smarty.post.type == 'nonmember'}-->
	<span class="over">����</span>&nbsp;
	<!--{else}-->
	<a href="<!--{$smarty.server.PHP_SELF|escape}-->" onclick="document.form1.mode.value='search'; return fnSetFormSubmit('form1', 'type', 'nonmember');">����</a>&nbsp;
	<!--{/if}-->
	��*}-->
	</td>
<!--{/if}-->

<!--{if $arrForm.page.value == "member"}-->
	<td class="fs12n">
	<strong>����̽���</strong>
	</td>
<!--{/if}-->