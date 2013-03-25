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
    <h2 class="title"><!--{$tpl_title|h}--></h2>

    <!-- ▼その他決済情報を表示する場合は表示 -->
    <!--{if $arrOther.title.value}-->
        <p>
            <em>*<!--{$arrOther.title.name}--> information</em><br />
            <!--{foreach key=key item=item from=$arrOther}-->
                <!--{if $key != "title"}-->
                    <!--{if $item.name != ""}-->
                        <!--{$item.name}-->:
                    <!--{/if}-->
                    <!--{$item.value|nl2br}--><br />
                <!--{/if}-->
            <!--{/foreach}-->
        </p>
    <!--{/if}-->
    <!-- ▲コンビに決済の場合には表示 -->

    <div class="thankstext">
        <p>Thank you for ordering from <!--{$arrInfo.shop_name|h}--></p>
    </div>
    <hr>
    <div id="completetext">
        <p>A confirmation e-mail regarding your order has just been sent.</p>
        <p>In the event that a response mail is not received, please make another inquiry or inquire by phone.</p>
        <p>We look forward to doing business with you again in the future.</p>
        <div class="btn_area">
            <a href="<!--{$smarty.const.TOP_URLPATH}-->" class="btn_toppage btn_sub" rel="external">Home</a>
        </div>
    </div>
    <hr>
    <div class="shopInformation">
        <p><!--{$arrInfo.shop_name|h}--></p>
        <p>Phone:<!--{$arrInfo.tel01}-->-<!--{$arrInfo.tel02}-->-<!--{$arrInfo.tel03}--><br />
            E-mail:<a href="mailto:<!--{$arrInfo.email02|escape:'hex'}-->" rel="external"><!--{$arrInfo.email02|escape:'hexentity'}--></a></p>
    </div>
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
