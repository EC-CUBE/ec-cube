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
<!--▼CONTENTS-->
<div id="mypagecolumn">
  <h2 class="title"><img src="<!--{$TPL_DIR}-->img/mypage/title.jpg" width="700" height="40" alt="MYページ" /></h2>
<!--{include file=$tpl_navi}-->
<div id="mycontentsarea">
  <h3><img src="<!--{$TPL_DIR}-->img/mypage/subtitle02.gif" width="515" height="32" alt="会員登録内容変更" /></h3>
  <p>下記項目にご入力ください。「<span class="attention">※</span>」印は入力必須項目です。<br />
    入力後、一番下の「確認ページへ」ボタンをクリックしてください。</p>

  <form name="form1" id="form1" method="post" action="<!--{$smarty.server.PHP_SELF|escape}-->">
  <input type="hidden" name="mode" value="confirm" />
  <input type="hidden" name="customer_id" value="<!--{$arrForm.customer_id|escape}-->" />
    <table summary="会員登録内容変更 " class="delivname">
      <!--{include file="`$smarty.const.TEMPLATE_DIR`frontparts/form_personal_input.tpl" flgFields=3 emailMobile=true prefix=""}-->
    </table>
    <!--{if 'sfGMOMypageDisplay'|function_exists}-->
      <!--{'sfGMOMypageDisplay'|call_user_func}-->
    <!--{/if}-->
    <div class="tblareabtn">
      <input type="image" onmouseover="chgImgImageSubmit('<!--{$TPL_DIR}-->img/common/b_confirm_on.gif',this)" onmouseout="chgImgImageSubmit('<!--{$TPL_DIR}-->img/common/b_confirm.gif',this)" src="<!--{$TPL_DIR}-->img/common/b_confirm.gif" class="box150" alt="確認ページへ" name="refusal" id="refusal" />
    </div>
  </form>
</div>

</div>
<!--▲CONTENTS-->
