<!--{*
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
 *}-->
<!--▼CONTENTS-->
<div id="undercolumn">
  <div id="undercolumn_entry">
    <h2 class="title"><!--{$tpl_title|h}--></h2>
    <p>下記の内容で送信してもよろしいでしょうか？<br />
      よろしければ、一番下の「会員登録完了へ」ボタンをクリックしてください。</p>
    <form name="form1" id="form1" method="post" action="?">
      <input type="hidden" name="mode" value="complete">
      <!--{foreach from=$list_data key=key item=item}-->
        <input type="hidden" name="<!--{$key|h}-->" value="<!--{$item|h}-->" />
      <!--{/foreach}-->
        <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />

      <table summary="入力内容確認">
        <tr>
          <th>お名前<span class="attention">※</span></th>
          <td>
            <!--{$list_data.name01|h}-->&nbsp;
            <!--{$list_data.name02|h}-->
          </td>
        </tr>
        <tr>
          <th>お名前(フリガナ)<span class="attention">※</span></th>
          <td>
            <!--{$list_data.kana01|h}-->&nbsp;
            <!--{$list_data.kana02|h}-->
          </td>
        </tr>
        <tr>
          <th>郵便番号<span class="attention">※</span></th>
          <td>
            〒<!--{$list_data.zip01|h}--> - <!--{$list_data.zip02|h}-->
          </td>
        </tr>
        <tr>
          <th>住所<span class="attention">※</span></th>
          <td>
            <!--{$arrPref[$list_data.pref]|h}--><!--{$list_data.addr01|h}--><!--{$list_data.addr02|h}-->
          </td>
        </tr>
        <tr>
          <th>電話番号<span class="attention">※</span></th>
          <td>
            <!--{$list_data.tel01|h}--> - <!--{$list_data.tel02|h}--> - <!--{$list_data.tel03|h}-->
          </td>
        </tr>
        <tr>
          <th>FAX</th>
          <td>
            <!--{if strlen($list_data.fax01) > 0 && strlen($list_data.fax02) > 0 && strlen($list_data.fax03) > 0}-->
              <!--{$list_data.fax01|h}--> - <!--{$list_data.fax02|h}--> - <!--{$list_data.fax03|h}-->
            <!--{else}-->
              未登録
            <!--{/if}-->
          </td>
        </tr>
        <tr>
          <th>メールアドレス<span class="attention">※</span></th>
          <td>
            <a href="mailto:<!--{$list_data.email|escape:'hex'}-->"><!--{$list_data.email|escape:'hexentity'}--></a>
          </td>
        </tr>
        <tr>
          <th>性別<span class="attention">※</span></th>
          <td>
            <!--{if $list_data.sex eq 1}-->
            男性
            <!--{else}-->
            女性
            <!--{/if}-->
          </td>
        </tr>
        <tr>
          <th>職業</th>
          <td><!--{$arrJob[$list_data.job]|default:"未登録"|h}--></td>
        </tr>
        <tr>
          <th>生年月日</th>
          <td>
            <!--{if strlen($list_data.year) > 0 && strlen($list_data.month) > 0 && strlen($list_data.day) > 0}-->
              <!--{$list_data.year|h}-->年<!--{$list_data.month|h}-->月<!--{$list_data.day|h}-->日
            <!--{else}-->
            未登録
            <!--{/if}-->
          </td>
        </tr>
        <tr>
          <th>希望するパスワード<span class="attention">※</span><br />
          </th>
          <td><!--{$passlen}--></td>
        </tr>
        <tr>
          <th>パスワードを忘れた時のヒント<span class="attention">※</span></th>
          <td>
              質問：<!--{$arrReminder[$list_data.reminder]|h}--><br />
              答え：<!--{$list_data.reminder_answer|h}-->
          </td>
        </tr>
        <tr>
          <th>メールマガジン送付について<span class="attention">※</span></th>
          <td>
            <!--{if $list_data.mailmaga_flg eq 1}-->
            HTMLメール＋テキストメールを受け取る
            <!--{elseif $list_data.mailmaga_flg eq 2}-->
            テキストメールを受け取る
            <!--{else}-->
            受け取らない
            <!--{/if}-->
          </td>
        </tr>
      </table>

      <div class="tblareabtn">
        <a href="?" onclick="fnModeSubmit('return', '', ''); return false;" onmouseover="chgImg('<!--{$TPL_DIR}-->img/button/btn_back_on.gif','back')" onmouseout="chgImg('<!--{$TPL_DIR}-->img/button/btn_back.gif','back')"><img src="<!--{$TPL_DIR}-->img/button/btn_back.gif" width="150" height="30" alt="戻る" border="0" name="back" id="back" /></a>&nbsp;
        <input type="submit" value="送信" class="spbtn spbtn-shopping" width="130" height="30" alt="送信" name="send" id="send" />
      </div>
    </form>
  </div>
</div>
<!--▲CONTENTS-->
