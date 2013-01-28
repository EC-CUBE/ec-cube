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
    <strong><!--{t string="tpl_Sales by period_01"}--></strong>&nbsp;(
    <!--{if $smarty.post.type == 'day' || $smarty.post.type == ''}-->
        <span class="over"><!--{t string="tpl_By date_01"}--></span>&nbsp;
    <!--{else}-->
        <a href="?" onclick="document.form1.mode.value='search'; return fnSetFormSubmit('form1', 'type', 'day');"><!--{t string="tpl_By date_01"}--></a>&nbsp;
    <!--{/if}-->
    <!--{if $smarty.post.type == 'month'}-->
        <span class="over"><!--{t string="tpl_By month_01"}--></span>&nbsp;
    <!--{else}-->
        <a href="?" onclick="document.form1.mode.value='search'; return fnSetFormSubmit('form1', 'type', 'month');"><!--{t string="tpl_By month_01"}--></a>&nbsp;
    <!--{/if}-->
    <!--{if $smarty.post.type == 'year'}-->
        <span class="over"><!--{t string="tpl_By year_01"}--></span>&nbsp;
    <!--{else}-->
        <a href="?" onclick="document.form1.mode.value='search'; return fnSetFormSubmit('form1', 'type', 'year');"><!--{t string="tpl_By year_01"}--></a>&nbsp;
    <!--{/if}-->
    <!--{if $smarty.post.type == 'wday'}-->
        <span class="over"><!--{t string="tpl_By day_01"}--></span>&nbsp;
    <!--{else}-->
        <a href="?" onclick="document.form1.mode.value='search'; return fnSetFormSubmit('form1', 'type', 'wday');"><!--{t string="tpl_By day_01"}--></a>&nbsp;
    <!--{/if}-->
    <!--{if $smarty.post.type == 'hour'}-->
        <span class="over"><!--{t string="tpl_By time_01"}--></span>&nbsp;
    <!--{else}-->
        <a href="?" onclick="document.form1.mode.value='search'; return fnSetFormSubmit('form1', 'type', 'hour');"><!--{t string="tpl_By time_01"}--></a>&nbsp;
    <!--{/if}-->
    )
<!--{/if}-->

<!--{if $arrForm.page.value == "products"}-->
    <strong><!--{t string="tpl_Sales by product_01"}--></strong>&nbsp;(
    <!--{if $smarty.post.type == 'all' || $smarty.post.type == ''}-->
        <span class="over"><!--{t string="tpl_Overall_01"}--></span>&nbsp;
    <!--{else}-->
        <a href="?" onclick="document.form1.mode.value='search'; return fnSetFormSubmit('form1', 'type', 'all');"><!--{t string="tpl_Overall_01"}--></a>&nbsp;
    <!--{/if}-->
    <!--{if $smarty.post.type == 'member'}-->
        <span class="over"><!--{t string="tpl_Member_01"}--></span>&nbsp;
    <!--{else}-->
        <a href="?" onclick="document.form1.mode.value='search'; return fnSetFormSubmit('form1', 'type', 'member');"><!--{t string="tpl_Member_01"}--></a>&nbsp;
    <!--{/if}-->
    <!--{if $smarty.post.type == 'nonmember'}-->
        <span class="over"><!--{t string="tpl_Non-member_01"}--></span>&nbsp;
    <!--{else}-->
        <a href="?" onclick="document.form1.mode.value='search'; return fnSetFormSubmit('form1', 'type', 'nonmember');"><!--{t string="tpl_Non-member_01"}--></a>&nbsp;
    <!--{/if}-->
    )
<!--{/if}-->

<!--{if $arrForm.page.value == "age"}-->
    <strong><!--{t string="tpl_Sales by age group_01"}--></strong>&nbsp;(
    <!--{if $smarty.post.type == 'all' || $smarty.post.type == ''}-->
        <span class="over"><!--{t string="tpl_Overall_01"}--></span>&nbsp;
    <!--{else}-->
        <a href="?" onclick="document.form1.mode.value='search'; return fnSetFormSubmit('form1', 'type', 'all');"><!--{t string="tpl_Overall_01"}--></a>&nbsp;
    <!--{/if}-->
    <!--{if $smarty.post.type == 'member'}-->
        <span class="over"><!--{t string="tpl_Member_01"}--></span>&nbsp;
    <!--{else}-->
        <a href="?" onclick="document.form1.mode.value='search'; return fnSetFormSubmit('form1', 'type', 'member');"><!--{t string="tpl_Member_01"}--></a>&nbsp;
    <!--{/if}-->
    <!--{if $smarty.post.type == 'nonmember'}-->
        <span class="over"><!--{t string="tpl_Non-member_01"}--></span>&nbsp;
    <!--{else}-->
        <a href="?" onclick="document.form1.mode.value='search'; return fnSetFormSubmit('form1', 'type', 'nonmember');"><!--{t string="tpl_Non-member_01"}--></a>&nbsp;
    <!--{/if}-->
    )
<!--{/if}-->

<!--{if $arrForm.page.value == "job"}-->
    <strong><!--{t string="tpl_Sales by occupation_01"}--></strong>&nbsp;(
    <span class="over"><!--{t string="tpl_Overall_01"}--></span>
    )
    <!--{*(
    <!--{if $smarty.post.type == 'all' || $smarty.post.type == ''}-->
        <span class="over"><!--{t string="tpl_Overall_01"}--></span>&nbsp;
    <!--{else}-->
        <a href="?" onclick="document.form1.mode.value='search'; return fnSetFormSubmit('form1', 'type', 'all');"><!--{t string="tpl_Overall_01"}--></a>&nbsp;
    <!--{/if}-->
    <!--{if $smarty.post.type == 'member'}-->
        <span class="over"><!--{t string="tpl_Member_01"}--></span>&nbsp;
    <!--{else}-->
        <a href="?" onclick="document.form1.mode.value='search'; return fnSetFormSubmit('form1', 'type', 'member');"><!--{t string="tpl_Member_01"}--></a>&nbsp;
    <!--{/if}-->
    <!--{if $smarty.post.type == 'nonmember'}-->
        <span class="over"><!--{t string="tpl_Non-member_01"}--></span>&nbsp;
    <!--{else}-->
        <a href="?" onclick="document.form1.mode.value='search'; return fnSetFormSubmit('form1', 'type', 'nonmember');"><!--{t string="tpl_Non-member_01"}--></a>&nbsp;
    <!--{/if}-->
    )*}-->
<!--{/if}-->

<!--{if $arrForm.page.value == "member"}-->
    <strong><!--{t string="tpl_Sales by member_01"}--></strong>
<!--{/if}-->
