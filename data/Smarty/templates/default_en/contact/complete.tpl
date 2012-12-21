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

<div id="undercolumn">
    <h2 class="title"><!--{$tpl_title|h}--></h2>
    <div id="undercolumn_contact">
        <div id="complete_area">
            <p class="message">The details of your inquiry have been sent.</p>
            <p>
                In the event that a response mail is not received, there may be a problem.<br /> Please make another inquiry or inquire by phone.<br />
                We look forward to doing business with you again in the future.
            </p>
            <div class="shop_information">
            <p class="name"><!--{$arrSiteInfo.company_name|h}--><br />
            <p>TEL:<!--{$arrSiteInfo.tel01}-->-<!--{$arrSiteInfo.tel02}-->-<!--{$arrSiteInfo.tel03}-->
                <!--{if $arrSiteInfo.business_hour != ""}-->
                (Time of receipt/<!--{$arrSiteInfo.business_hour}-->)
                <!--{/if}--><br />
                E-mail:<a href="mailto:<!--{$arrSiteInfo.email02|escape:'hex'}-->"><!--{$arrSiteInfo.email02|escape:'hexentity'}--></a></p>
            </p>
            </div>

            <div class="btn_area">
                <ul>
                <li>
                    <a href="<!--{$smarty.const.TOP_URLPATH}-->" onmouseover="chgImg('<!--{$TPL_URLPATH}-->img/button/btn_toppage_on.jpg','b_toppage');" onmouseout="chgImg('<!--{$TPL_URLPATH}-->img/button/btn_toppage.jpg','b_toppage');"><img src="<!--{$TPL_URLPATH}-->img/button/btn_toppage.jpg" alt="To top page" border="0" name="b_toppage" id="b_toppage" /></a>
                </li>
                </ul>
            </div>
        </div>
    </div>
</div>
