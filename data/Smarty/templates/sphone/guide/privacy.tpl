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

<section id="undercolumn">
    <!--☆プライバシーポリシー -->
    <h2 class="title"><!--{$tpl_title|h}--></h2>
    <div class="inner">
        <p>
            <!--{if $arrSiteInfo.company_name != ''}--><!--{$arrSiteInfo.company_name|h}-->は、<!--{/if}-->
            個人情報保護の重要性に鑑み、「個人情報の保護に関する法律」及び本プライバシーポリシーを遵守し、お客さまのプライバシー保護に努めます。
        </p>
    </div>

    <hr>

    <div class="inner">
        <p><em>個人情報の定義</em><br />
            お客さま個人に関する情報(以下「個人情報」といいます)であって、お客さまのお名前、住所、電話番号など当該お客さま個人を識別することができる情報をさします。他の情報と組み合わせて照合することにより個人を識別することができる情報も含まれます。</p>
    </div>
</section>

<!--▼検索バー -->
<section id="search_area">
    <form method="get" action="<!--{$smarty.const.ROOT_URLPATH}-->products/list.php">
        <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
        <input type="hidden" name="mode" value="search" />
        <input type="search" name="name" id="search" value="" placeholder="キーワードを入力" class="searchbox" >
    </form>
</section>
<!--▲検索バー -->
