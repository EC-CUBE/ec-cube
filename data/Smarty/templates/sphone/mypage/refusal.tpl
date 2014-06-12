<!--{*
/*
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
 */
*}-->

<section id="mypagecolumn">
    <h2 class="title"><!--{$tpl_title|h}--></h2>
    <!--{include file=$tpl_navi}-->

    <h3 class="title_mypage"><!--{$tpl_subtitle|h}--></h3>
    <form name="form1" id="form1" method="post" action="<!--{$smarty.const.HTTPS_URL}-->mypage/refusal.php">
        <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
        <input type="hidden" name="mode" value="confirm" />

        <!--★インフォメーション★-->
        <div class="refusetxt">
            <p>会員を退会された場合には、現在保存されている購入履歴や、お届け先などの情報は、全て削除されますがよろしいでしょうか？</p>
            <div class="btn_area">
                <p><input class="btn data-role-none" type="submit" value="会員退会手続き" name="refusal" id="refusal" /></p>
            </div>
        </div>
    </form>
</section>

<!--{include file= 'frontparts/search_area.tpl'}-->

