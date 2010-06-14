<!--{*
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2010 LOCKON CO.,LTD. All Rights Reserved.
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
<!--{include file="`$smarty.const.TEMPLATE_DIR`popup_header.tpl" subtitle="新しいお届け先の追加・変更"}-->

<div id="windowarea">
    <h2 class="title"><!--{$tpl_title|escape}--></h2>
    <p>下記項目にご入力ください。「<span class="attention">※</span>」印は入力必須項目です。</p>
    <p>入力後、一番下の「登録する」ボタンをクリックしてください。</p>

    <form name="form1" id="form1" method="post" action="?">
        <input type="hidden" name="mode" value="edit" />
        <input type="hidden" name="other_deliv_id" value="<!--{$smarty.session.other_deliv_id}-->" />
        <input type="hidden" name="ParentPage" value="<!--{$ParentPage}-->" />

        <table summary="お届け先登録">
            <!--{include file="`$smarty.const.TEMPLATE_DIR`frontparts/form_personal_input.tpl" flgFields=1 emailMobile=false prefix=""}-->
        </table>

        <div class="btn">
            <input type="image" onmouseover="chgImgImageSubmit('<!--{$TPL_DIR}-->img/common/b_entry_on.gif',this);" onmouseout="chgImgImageSubmit('<!--{$TPL_DIR}-->img/common/b_entry.gif',this);" src="<!--{$TPL_DIR}-->img/common/b_entry.gif" class="box150" alt="登録する" name="register" id="register" />
        </div>
    </form>
</div>

<!--{include file="`$smarty.const.TEMPLATE_DIR`popup_footer.tpl"}-->
