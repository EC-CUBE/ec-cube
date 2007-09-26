<!--{*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *}-->
<!--▼CONTENTS-->
<div id="undercolumn">
  <div id="undercolumn_entry">
    <h2 class="title"><img src="<!--{$TPL_DIR}-->img/entry/title.jpg" width="580" height="40" alt="会員登録" /></h2>
    <p>下記の内容で送信してもよろしいでしょうか？<br />
      よろしければ、一番下の「会員登録完了へ」ボタンをクリックしてください。</p>
    <form name="form1" id="form1" method="post" action="<!--{$smarty.server.PHP_SELF|escape}-->">
      <input type="hidden" name="mode" value="complete">
      <!--{foreach from=$list_data key=key item=item}-->
        <input type="hidden" name="<!--{$key|escape}-->" value="<!--{$item|escape}-->" />
      <!--{/foreach}-->
        <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />

      <table summary="入力内容確認">
        <tr>
          <th>お名前<span class="attention">※</span></th>
          <td>
            <!--{$list_data.name01|escape}-->&nbsp;
            <!--{$list_data.name02|escape}-->
          </td>
        </tr>
        <tr>
          <th>お名前（フリガナ）<span class="attention">※</span></th>
          <td>
            <!--{$list_data.kana01|escape}-->&nbsp;
            <!--{$list_data.kana02|escape}-->
          </td>
        </tr>
        <tr>
          <th>郵便番号<span class="attention">※</span></th>
          <td>
            〒<!--{$list_data.zip01|escape}--> - <!--{$list_data.zip02|escape}-->
          </td>
        </tr>
        <tr>
          <th>住所<span class="attention">※</span></th>
          <td>
            <!--{$arrPref[$list_data.pref]|escape}--><!--{$list_data.addr01|escape}--><!--{$list_data.addr02|escape}-->
          </td>
        </tr>
        <tr>
          <th>電話番号<span class="attention">※</span></th>
          <td>
            <!--{$list_data.tel01|escape}--> - <!--{$list_data.tel02|escape}--> - <!--{$list_data.tel03|escape}-->
          </td>
        </tr>
        <tr>
          <th>FAX</th>
          <td>
            <!--{if strlen($list_data.fax01) > 0 && strlen($list_data.fax02) > 0 && strlen($list_data.fax03) > 0}-->
              <!--{$list_data.fax01|escape}--> - <!--{$list_data.fax02|escape}--> - <!--{$list_data.fax03|escape}-->
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
          <td><!--{$arrJob[$list_data.job]|escape|default:"未登録"}--></td>
        </tr>
        <tr>
          <th>生年月日</th>
          <td>
            <!--{if strlen($list_data.year) > 0 && strlen($list_data.month) > 0 && strlen($list_data.day) > 0}-->
              <!--{$list_data.year|escape}-->年<!--{$list_data.month|escape}-->月<!--{$list_data.day|escape}-->日
            <!--{else}-->
            未登録
            <!--{/if}-->
          </td>
        </tr>
        <tr>
          <th>希望するパスワード<span class="attention">※</span><br />
            <span class="mini">パスワードは購入時に必要です</span>
          </th>
          <td><!--{$passlen}--></td>
        </tr>
        <tr>
          <th>パスワードを忘れた時のヒント<span class="attention">※</span></th>
          <td>
            <dl>
              <dt>質問：</dt>
              <dd><!--{$arrReminder[$list_data.reminder]|escape}--></dd>
              <dt>答え：</dt>
              <dd><!--{$list_data.reminder_answer|escape}--></dd>
            </dl>
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
        <a href="<!--{$smarty.server.PHP_SELF|escape}-->" onclick="fnModeSubmit('return', '', ''); return false;" onmouseover="chgImg('<!--{$TPL_DIR}-->img/common/b_back_on.gif','back')" onmouseout="chgImg('<!--{$TPL_DIR}-->img/common/b_back.gif','back')"><img src="<!--{$TPL_DIR}-->img/common/b_back.gif" width="150" height="30" alt="戻る" border="0" name="back" id="back" /></a>&nbsp;
        <input type="image" onmouseover="chgImgImageSubmit('<!--{$TPL_DIR}-->img/entry/b_entrycomp_on.gif',this)" onmouseout="chgImgImageSubmit('<!--{$TPL_DIR}-->img/entry/b_entrycomp.gif',this)" src="<!--{$TPL_DIR}-->img/entry/b_entrycomp.gif" class="box150" alt="送信" border="0" name="send" id="send" />
      </div>
    </form>
  </div>
</div>
<!--▲CONTENTS-->
