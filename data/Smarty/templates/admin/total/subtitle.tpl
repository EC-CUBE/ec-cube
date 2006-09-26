<!--{*
/*
 * Copyright © 2000-2006 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
*}-->
<!--{if $arrForm.page.value == "term" || $arrForm.page.value == ""}-->
	<td class="fs12n">
	<strong>期間別集計</strong>&nbsp;（
	<!--{if $smarty.post.type == 'day' || $smarty.post.type == ''}-->
	<span class="over">日別</span>&nbsp;
	<!--{else}-->
	<a href="<!--{$smarty.server.PHP_SELF}-->" onclick="document.form1.mode.value='search'; return fnSetFormSubmit('form1', 'type', 'day');">日別</a>&nbsp;
	<!--{/if}-->
	<!--{if $smarty.post.type == 'month'}-->
	<span class="over">月別</span>&nbsp;
	<!--{else}-->
	<a href="<!--{$smarty.server.PHP_SELF}-->" onclick="document.form1.mode.value='search'; return fnSetFormSubmit('form1', 'type', 'month');">月別</a>&nbsp;
	<!--{/if}-->
	<!--{if $smarty.post.type == 'year'}-->
	<span class="over">年別</span>&nbsp;
	<!--{else}-->
	<a href="<!--{$smarty.server.PHP_SELF}-->" onclick="document.form1.mode.value='search'; return fnSetFormSubmit('form1', 'type', 'year');">年別</a>&nbsp;
	<!--{/if}-->
	<!--{if $smarty.post.type == 'wday'}-->
	<span class="over">曜日別</span>&nbsp;
	<!--{else}-->
	<a href="<!--{$smarty.server.PHP_SELF}-->" onclick="document.form1.mode.value='search'; return fnSetFormSubmit('form1', 'type', 'wday');">曜日別</a>&nbsp;
	<!--{/if}-->
	<!--{if $smarty.post.type == 'hour'}-->
	<span class="over">時間別</span>&nbsp;
	<!--{else}-->
	<a href="<!--{$smarty.server.PHP_SELF}-->" onclick="document.form1.mode.value='search'; return fnSetFormSubmit('form1', 'type', 'hour');">時間別</a>&nbsp;
	<!--{/if}-->
	）
	</td>
<!--{/if}-->

<!--{if $arrForm.page.value == "products"}-->
	<td class="fs12n">
	<strong>商品別集計</strong>&nbsp;（
	<!--{if $smarty.post.type == 'all' || $smarty.post.type == ''}-->
	<span class="over">全体</span>&nbsp;
	<!--{else}-->
	<a href="<!--{$smarty.server.PHP_SELF}-->" onclick="document.form1.mode.value='search'; return fnSetFormSubmit('form1', 'type', 'all');">全体</a>&nbsp;
	<!--{/if}-->
	<!--{if $smarty.post.type == 'member'}-->
	<span class="over">会員</span>&nbsp;
	<!--{else}-->
	<a href="<!--{$smarty.server.PHP_SELF}-->" onclick="document.form1.mode.value='search'; return fnSetFormSubmit('form1', 'type', 'member');">会員</a>&nbsp;
	<!--{/if}-->
	<!--{if $smarty.post.type == 'nonmember'}-->
	<span class="over">非会員</span>&nbsp;
	<!--{else}-->
	<a href="<!--{$smarty.server.PHP_SELF}-->" onclick="document.form1.mode.value='search'; return fnSetFormSubmit('form1', 'type', 'nonmember');">非会員</a>&nbsp;
	<!--{/if}-->
	）
	</td>
<!--{/if}-->

<!--{if $arrForm.page.value == "age"}-->
	<td class="fs12n">
	<strong>年代別集計</strong>&nbsp;（
	<!--{if $smarty.post.type == 'all' || $smarty.post.type == ''}-->
	<span class="over">全体</span>&nbsp;
	<!--{else}-->
	<a href="<!--{$smarty.server.PHP_SELF}-->" onclick="document.form1.mode.value='search'; return fnSetFormSubmit('form1', 'type', 'all');">全体</a>&nbsp;
	<!--{/if}-->
	<!--{if $smarty.post.type == 'member'}-->
	<span class="over">会員</span>&nbsp;
	<!--{else}-->
	<a href="<!--{$smarty.server.PHP_SELF}-->" onclick="document.form1.mode.value='search'; return fnSetFormSubmit('form1', 'type', 'member');">会員</a>&nbsp;
	<!--{/if}-->
	<!--{if $smarty.post.type == 'nonmember'}-->
	<span class="over">非会員</span>&nbsp;
	<!--{else}-->
	<a href="<!--{$smarty.server.PHP_SELF}-->" onclick="document.form1.mode.value='search'; return fnSetFormSubmit('form1', 'type', 'nonmember');">非会員</a>&nbsp;
	<!--{/if}-->
	）
	</td>
<!--{/if}-->

<!--{if $arrForm.page.value == "job"}-->
	<td class="fs12n">
	<strong>職業別集計</strong>&nbsp;（
	<span class="over">全体</span>
	）
	<!--{*（
	<!--{if $smarty.post.type == 'all' || $smarty.post.type == ''}-->
	<span class="over">全体</span>&nbsp;
	<!--{else}-->
	<a href="<!--{$smarty.server.PHP_SELF}-->" onclick="document.form1.mode.value='search'; return fnSetFormSubmit('form1', 'type', 'all');">全体</a>&nbsp;
	<!--{/if}-->
	<!--{if $smarty.post.type == 'member'}-->
	<span class="over">会員</span>&nbsp;
	<!--{else}-->
	<a href="<!--{$smarty.server.PHP_SELF}-->" onclick="document.form1.mode.value='search'; return fnSetFormSubmit('form1', 'type', 'member');">会員</a>&nbsp;
	<!--{/if}-->
	<!--{if $smarty.post.type == 'nonmember'}-->
	<span class="over">非会員</span>&nbsp;
	<!--{else}-->
	<a href="<!--{$smarty.server.PHP_SELF}-->" onclick="document.form1.mode.value='search'; return fnSetFormSubmit('form1', 'type', 'nonmember');">非会員</a>&nbsp;
	<!--{/if}-->
	）*}-->
	</td>
<!--{/if}-->

<!--{if $arrForm.page.value == "member"}-->
	<td class="fs12n">
	<strong>会員別集計</strong>
	</td>
<!--{/if}-->