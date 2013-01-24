<!--{*
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
 *}-->

<section id="undercolumn">
    <!--☆お問い合わせ内容確認 -->
    <h2 class="title"><!--{$tpl_title|h}--></h2>
        <div class="intro">
            <p>Confirm information</p>
        </div>

        <form name="form1" id="form1" method="post" action="?">
            <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
            <input type="hidden" name="mode" value="complete" />
            <!--{foreach key=key item=item from=$arrForm}-->
                <!--{if $key ne 'mode'}-->
                    <input type="hidden" name="<!--{$key}-->" value="<!--{$item.value|h}-->" />
                <!--{/if}-->
            <!--{/foreach}-->

            <dl class="form_entry">

            <dt>Name</dt>
            <dd><!--{$arrForm.name01.value|h}-->&nbsp;<!--{$arrForm.name02.value|h}--></dd>

            <dt>Address</dt>
            <dd>
                <!--{*
                <!--{if strlen($arrForm.zip01.value) > 0 && strlen($arrForm.zip02.value) > 0}-->
                <!--{$arrForm.zip01.value|h}-->-<!--{$arrForm.zip02.value|h}-->
                <!--{/if}--><br />
                *}-->
                <!--{if strlen($arrForm.zipcode.value) > 0}-->
                <!--{$arrForm.zipcode.value|h}-->
                <!--{/if}--><br />
                <!--{$arrForm.addr01.value|h}--><!--{$arrForm.addr02.value|h}-->
            </dd>

            <dt>Phone number</dt>
            <dd>
                <!--{if strlen($arrForm.tel01.value) > 0 && strlen($arrForm.tel02.value) > 0 && strlen($arrForm.tel03.value) > 0}-->
                    <!--{$arrForm.tel01.value|h}-->-<!--{$arrForm.tel02.value|h}-->-<!--{$arrForm.tel03.value|h}-->
                <!--{/if}-->
            </dd>

            <dt>E-mail address</dt>
            <dd><a href="mailto:<!--{$arrForm.email.value|escape:'hex'}-->"><!--{$arrForm.email.value|escape:'hexentity'}--></a></dd>

            <dt>Details of inquiry</dt>
            <dd><!--{$arrForm.contents.value|h|nl2br}--></dd>
        </dl>

        <div class="btn_area">
            <ul class="btn_btm">
                <li><input type="submit" value="Send" class="btn data-role-none" name="send" id="send" /></li>
                <li><a class="btn_back" href="?" onClick="fnModeSubmit('return', '', ''); return false;">Go back</a></li>
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
