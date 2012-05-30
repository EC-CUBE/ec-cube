<!--{*
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2012 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */
*}-->

<!--{if $arrForm.page.value == "term" || $arrForm.page.value == ""}-->
    <strong>期間別集計</strong>&nbsp;（
    <!--{if $smarty.post.type == 'day' || $smarty.post.type == ''}-->
        <span class="over">日別</span>&nbsp;
    <!--{else}-->
        <a href="?" onclick="document.form1.mode.value='search'; return fnSetFormSubmit('form1', 'type', 'day');">日別</a>&nbsp;
    <!--{/if}-->
    <!--{if $smarty.post.type == 'month'}-->
        <span class="over">月別</span>&nbsp;
    <!--{else}-->
        <a href="?" onclick="document.form1.mode.value='search'; return fnSetFormSubmit('form1', 'type', 'month');">月別</a>&nbsp;
    <!--{/if}-->
    <!--{if $smarty.post.type == 'year'}-->
        <span class="over">年別</span>&nbsp;
    <!--{else}-->
        <a href="?" onclick="document.form1.mode.value='search'; return fnSetFormSubmit('form1', 'type', 'year');">年別</a>&nbsp;
    <!--{/if}-->
    <!--{if $smarty.post.type == 'wday'}-->
        <span class="over">曜日別</span>&nbsp;
    <!--{else}-->
        <a href="?" onclick="document.form1.mode.value='search'; return fnSetFormSubmit('form1', 'type', 'wday');">曜日別</a>&nbsp;
    <!--{/if}-->
    <!--{if $smarty.post.type == 'hour'}-->
        <span class="over">時間別</span>&nbsp;
    <!--{else}-->
        <a href="?" onclick="document.form1.mode.value='search'; return fnSetFormSubmit('form1', 'type', 'hour');">時間別</a>&nbsp;
    <!--{/if}-->
    ）
<!--{/if}-->

<!--{if $arrForm.page.value == "products"}-->
    <strong>商品別集計</strong>&nbsp;（
    <!--{if $smarty.post.type == 'all' || $smarty.post.type == ''}-->
        <span class="over">全体</span>&nbsp;
    <!--{else}-->
        <a href="?" onclick="document.form1.mode.value='search'; return fnSetFormSubmit('form1', 'type', 'all');">全体</a>&nbsp;
    <!--{/if}-->
    <!--{if $smarty.post.type == 'member'}-->
        <span class="over">会員</span>&nbsp;
    <!--{else}-->
        <a href="?" onclick="document.form1.mode.value='search'; return fnSetFormSubmit('form1', 'type', 'member');">会員</a>&nbsp;
    <!--{/if}-->
    <!--{if $smarty.post.type == 'nonmember'}-->
        <span class="over">非会員</span>&nbsp;
    <!--{else}-->
        <a href="?" onclick="document.form1.mode.value='search'; return fnSetFormSubmit('form1', 'type', 'nonmember');">非会員</a>&nbsp;
    <!--{/if}-->
    ）
<!--{/if}-->

<!--{if $arrForm.page.value == "age"}-->
    <strong>年代別集計</strong>&nbsp;（
    <!--{if $smarty.post.type == 'all' || $smarty.post.type == ''}-->
        <span class="over">全体</span>&nbsp;
    <!--{else}-->
        <a href="?" onclick="document.form1.mode.value='search'; return fnSetFormSubmit('form1', 'type', 'all');">全体</a>&nbsp;
    <!--{/if}-->
    <!--{if $smarty.post.type == 'member'}-->
        <span class="over">会員</span>&nbsp;
    <!--{else}-->
        <a href="?" onclick="document.form1.mode.value='search'; return fnSetFormSubmit('form1', 'type', 'member');">会員</a>&nbsp;
    <!--{/if}-->
    <!--{if $smarty.post.type == 'nonmember'}-->
        <span class="over">非会員</span>&nbsp;
    <!--{else}-->
        <a href="?" onclick="document.form1.mode.value='search'; return fnSetFormSubmit('form1', 'type', 'nonmember');">非会員</a>&nbsp;
    <!--{/if}-->
    ）
<!--{/if}-->

<!--{if $arrForm.page.value == "job"}-->
    <strong>職業別集計</strong>&nbsp;（
    <span class="over">全体</span>
    ）
    <!--{*（
    <!--{if $smarty.post.type == 'all' || $smarty.post.type == ''}-->
        <span class="over">全体</span>&nbsp;
    <!--{else}-->
        <a href="?" onclick="document.form1.mode.value='search'; return fnSetFormSubmit('form1', 'type', 'all');">全体</a>&nbsp;
    <!--{/if}-->
    <!--{if $smarty.post.type == 'member'}-->
        <span class="over">会員</span>&nbsp;
    <!--{else}-->
        <a href="?" onclick="document.form1.mode.value='search'; return fnSetFormSubmit('form1', 'type', 'member');">会員</a>&nbsp;
    <!--{/if}-->
    <!--{if $smarty.post.type == 'nonmember'}-->
        <span class="over">非会員</span>&nbsp;
    <!--{else}-->
        <a href="?" onclick="document.form1.mode.value='search'; return fnSetFormSubmit('form1', 'type', 'nonmember');">非会員</a>&nbsp;
    <!--{/if}-->
    ）*}-->
<!--{/if}-->

<!--{if $arrForm.page.value == "member"}-->
    <strong>会員別集計</strong>
<!--{/if}-->
