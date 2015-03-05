<!--{*
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2015 LOCKON CO.,LTD. All Rights Reserved.
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
<!--{include file="`$smarty.const.TEMPLATE_REALDIR`popup_header.tpl" subtitle="新しいお届け先の追加・変更"}-->

<article id="popup_delivery_addr" class="window_area">
    <h1 class="title"><!--{$tpl_title|h}--></h1>
    <p>下記項目にご入力ください。「<span class="attention">※</span>」印は入力必須項目です。<br />
        入力後、一番下の「登録する」ボタンをクリックしてください。
	</p>

    <form name="form1" id="form1" method="post" action="?">
        <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
        <input type="hidden" name="mode" value="edit" />
        <input type="hidden" name="other_deliv_id" value="<!--{$smarty.session.other_deliv_id|h}-->" />
        <input type="hidden" name="ParentPage" value="<!--{$ParentPage}-->" />

        <dl class="table" title="お届け先登録">
            <!--{include file="`$smarty.const.TEMPLATE_REALDIR`frontparts/form_personal_input.tpl" flgFields=1 emailMobile=false prefix=""}-->
        </dl>

        <div class="btn_area">
            <ul>
                <li>
                    <a class="btn-action" href="javascript:" onclick="eccube.setValueAndSubmit('form1', 'mode', 'edit'); return false;">
                        <img class="hover_change_submit" src="<!--{$TPL_URLPATH}-->img/button/btn_add_address_complete.jpg" alt="登録する" />
                    </a>
                </li>
            </ul>
        </div>
    </form>
</article>

<!--{include file="`$smarty.const.TEMPLATE_REALDIR`popup_footer.tpl"}-->