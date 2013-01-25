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

<section id="mypagecolumn">
    <h2 class="title"><!--{$tpl_title|h}--></h2>
    <!--{include file=$tpl_navi}-->

    <h3 class="title_mypage"><!--{$tpl_subtitle|h}--></h3>

    <!--★インフォメーション★-->
    <div class="intro">
        <p>Confirm information</p>
    </div>

    <form name="form1" id="form1" method="post" action="<!--{$smarty.const.HTTPS_URL|sfTrimURL}-->/mypage/change.php">
        <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
        <input type="hidden" name="mode" value="complete" />
        <input type="hidden" name="customer_id" value="<!--{$arrForm.customer_id|h}-->" />
        <!--{foreach from=$arrForm key=key item=item}-->
            <!--{if $key ne "mode" && $key ne "subm"}-->
                <input type="hidden" name="<!--{$key|h}-->" value="<!--{$item|h}-->" />
            <!--{/if}-->
        <!--{/foreach}-->

        <dl class="form_entry">
            <dt>Name</dt>
            <dd><!--{$arrForm.name01|h}-->&nbsp;<!--{$arrForm.name02|h}--></dd>

            <dt>Address</dt>
            <dd>
                <!--{* <!--{$arrForm.zip01}-->-<!--{$arrForm.zip02}--><br /> *}-->
                <!--{$arrForm.zipcode}--><br />
                <!--{$arrForm.addr01|h}--><!--{$arrForm.addr02|h}-->
            </dd>

            <dt>Phone number</dt>
            <dd><!--{$arrForm.tel01|h}-->-<!--{$arrForm.tel02}-->-<!--{$arrForm.tel03}--></dd>

            <dt>FAX</dt>
            <dd>
                <!--{if strlen($arrForm.fax01) > 0}-->
                    <!--{$arrForm.fax01}-->-<!--{$arrForm.fax02}-->-<!--{$arrForm.fax03}-->
                <!--{else}-->
                    Not registered
                <!--{/if}-->
            </dd>

            <dt>E-mail address</dt>
            <dd><a href="<!--{$arrForm.email|escape:'hex'}-->" rel="external"><!--{$arrForm.email|escape:'hexentity'}--></a></dd>

            <dt>Mobile e-mail address</dt>
            <dd>
                <!--{if strlen($arrForm.email_mobile) > 0}-->
                    <a href="<!--{$arrForm.email_mobile|escape:'hex'}-->" rel="external"><!--{$arrForm.email_mobile|escape:'hexentity'}--></a>
                <!--{else}-->
                    Not registered
                <!--{/if}-->
            </dd>

            <dt>Gender</dt>
            <dd><!--{$arrSex[$arrForm.sex]}--></dd>

            <dt>Occupation</dt>
            <dd><!--{$arrJob[$arrForm.job]|default:"Not registered"|h}--></dd>

            <dt>Date of birth</dt>
            <dd>
                <!--{if strlen($arrForm.year) > 0 && strlen($arrForm.month) > 0 && strlen($arrForm.day) > 0}-->
                    <!--{$arrForm.year|h}--> / <!--{$arrForm.month|h}--> / <!--{$arrForm.day|h}-->
                <!--{else}-->
                    Not registered
                <!--{/if}-->
            </dd>

            <dt>Desired password</dt>
            <dd><!--{$passlen}--></dd>

            <dt>Hint for when you have forgotten your password</dt>
            <dd>
                Question:&nbsp;<!--{$arrReminder[$arrForm.reminder]|h}--><br />
                Answer:&nbsp;<!--{$arrForm.reminder_answer|h}-->
            </dd>

            <dt>About delivery of the mail magazine</dt>
            <dd><!--{$arrMAILMAGATYPE[$arrForm.mailmaga_flg]}--></dd>
        </dl>

        <div class="btn_area">
            <ul class="btn_btm">
                <li><input type="submit" value="Confirm" class="btn data-role-none" alt="Confirm" name="complete" id="complete" /></li>
                <li><a class="btn_back" href="Javascript:fnModeSubmit('return', '', '');" rel="external">Go back</a></li>
            </ul>
        </div>
    </form>
</section>

<!--▼検索バー -->
<section id="search_area">
    <form method="get" action="<!--{$smarty.const.ROOT_URLPATH}-->products/list.php">
        <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
        <input type="hidden" name="mode" value="search" />
        <input type="search" name="name" id="search" value="" placeholder="Enter keywords" class="searchbox" >
    </form>
</section>
<!--▲検索バー -->
