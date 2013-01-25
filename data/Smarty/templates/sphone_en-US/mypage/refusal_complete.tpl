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
    <!--{include file = $tpl_navi}-->

    <h3 class="title_mypage"><!--{$tpl_subtitle|h}--></h3>

    <!--★インフォメーション★-->
    <div id="mycontentsarea">
        <div id="completetext">
            <p>The cancelation request is now complete.</p>
            <p>Thank you for using MY page. <br />
               We look forward to your business in the future.</p>
        </div>

        <hr>

        <div class="shopInformation">
            <p><!--{$arrSiteInfo.company_name|h}--></p>
            <p>Phone:<!--{$arrSiteInfo.tel01}-->-<!--{$arrSiteInfo.tel02}-->-<!--{$arrSiteInfo.tel03}--><br />
                E-mail:<a href="mailto:<!--{$arrSiteInfo.email02|escape:'hex'}-->" rel="external"><!--{$arrSiteInfo.email02|escape:'hexentity'}--></a></p>
        </div>
    </div><!-- /#mycontentsarea -->
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
