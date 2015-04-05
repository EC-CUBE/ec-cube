<!--{*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2014 LOCKON CO.,LTD. All Rights Reserved.
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
    <div class="thankstext">
        <p>本登録が完了いたしました。</p>
    </div>
    <hr>
    <div id="completetext">
        <p>それではショッピングをお楽しみください。</p>
        <p>今後ともご愛顧賜りますようよろしくお願い申し上げます。</p>
        <div class="btn_area">
            <a rel="external" href="<!--{$smarty.const.TOP_URL}-->" class="btn_toppage btn_sub">トップページへ</a>
        </div>
    </div>
    <hr>
    <div class="shopInformation">
        <p><!--{$arrSiteInfo.company_name|h}--></p>
        <p>TEL：<!--{$arrSiteInfo.tel01}-->-<!--{$arrSiteInfo.tel02}-->-<!--{$arrSiteInfo.tel03}--><br />
            E-mail：<a href="mailto:<!--{$arrSiteInfo.email02|escape:'hex'}-->"><!--{$arrSiteInfo.email02|escape:'hexentity'}--></a></p>
    </div>
</section>

<!--{include file= 'frontparts/search_area.tpl'}-->

