<!--{*
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2008 LOCKON CO.,LTD. All Rights Reserved.
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
<!--{include file="`$smarty.const.TEMPLATE_DIR`popup_header.tpl" subtitle="お客様の声書き込み（入力ページ）"}-->

  <div id="windowarea">
    <h2><img src="<!--{$TPL_DIR}-->img/products/review_title.jpg" width="500" height="40" alt="お客様の声書き込み" /></h2>
    <p class="windowtext">以下の商品について、お客様のご意見、ご感想をどしどしお寄せください。<br />
      「<span class="attention">※</span>」印は入力必須項目です。<br />
       ご入力後、一番下の「確認ページへ」ボタンをクリックしてください。</p>
        <form name="form1" method="post" action="<!--{$smarty.server.PHP_SELF|escape}-->">
        <input type="hidden" name="mode" value="confirm" />
        <input type="hidden" name="product_id" value="<!--{$arrForm.product_id}-->" />
        <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
        <table summary="お客様の声書き込み">
          <tr>
            <th>商品名</th>
            <td><!--{$arrForm.name|escape}--></td>
          </tr>
          <tr>
            <th>投稿者名<span class="attention">※</span></th>
            <td><span class="attention"><!--{$arrErr.reviewer_name}--></span><input type="text" name="reviewer_name" value="<!--{$arrForm.reviewer_name|escape}-->" maxlength="<!--{$smarty.const.STEXT_LEN}-->" style="<!--{$arrErr.reviewer_name|sfGetErrorColor}-->" size="40" class="box350" /></td>
          </tr>
          <tr>
            <th>ホームページアドレス</th>
            <td><span class="attention"><!--{$arrErr.reviewer_url}--></span><input type="text" name="reviewer_url" value="<!--{$arrForm.reviewer_url|escape}-->" maxlength="<!--{$smarty.const.MTEXT_LEN}-->" style="<!--{$arrErr.reviewer_url|sfGetErrorColor}-->" size="40" class="box350" /></td>
          </tr>
          <tr>
            <th>性別</th>
            <td>
              <input type="radio" name="sex" id="man" value="1" <!--{if $arrForm.sex eq 1}--> checked="checked"<!--{/if}--> /><label for="man">男性</label>&nbsp;
              <input type="radio" name="sex" id="woman" value="2" <!--{if $arrForm.sex eq 2}--> checked="checked"<!--{/if}--> /><label for="woman">女性</label>
            </td>
          </tr>
          <tr>
            <th>おすすめレベル<span class="attention">※</span></th>
            <td>
              <span class="attention"><!--{$arrErr.recommend_level}--></span>
              <select name="recommend_level" style="<!--{$arrErr.recommend_level|sfGetErrorColor}-->">
                <option value="" selected="selected">選択してください</option>
                  <!--{html_options options=$arrRECOMMEND selected=$arrForm.recommend_level}-->
              </select>
            </td>
          </tr>
          <tr>
            <th>タイトル<span class="attention">※</span></th>
            <td>
              <span class="attention"><!--{$arrErr.title}--></span>
              <input type="text" name="title" value="<!--{$arrForm.title|escape}-->" maxlength="<!--{$smarty.const.STEXT_LEN}-->" style="<!--{$arrErr.title|sfGetErrorColor}-->" size="40" class="box350" />
            </td>
          </tr>
          <tr>
            <th>コメント<span class="attention">※</span></th>
            <td>
              <span class="attention"><!--{$arrErr.comment}--></span>
              <textarea name="comment" cols="50" rows="10" style="<!--{$arrErr.comment|sfGetErrorColor}-->" class="area350"><!--{$arrForm.comment|escape}--></textarea>
            </td>
          </tr>
        </table>
        <div class="btn">
          <input type="image" onmouseover="chgImgImageSubmit('<!--{$TPL_DIR}-->img/common/b_confirm_on.gif',this)" onmouseout="chgImgImageSubmit('<!--{$TPL_DIR}-->img/common/b_confirm.gif',this)" src="<!--{$TPL_DIR}-->img/common/b_confirm.gif" class="box150" alt="確認ページへ" name="conf" id="conf" />
        </div>
      </form>
    </div>

<!--{include file="`$smarty.const.TEMPLATE_DIR`popup_footer.tpl"}-->
