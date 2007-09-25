<!--{*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *}-->

<div id="undercolumn">
  <div id="undercolumn_contact">
    <h2 class="title">
      <img src="<!--{$TPL_DIR}-->img/contact/title.jpg" width="580" height="40" alt="お問い合わせ" />
    </h2>
    <p>下記入力内容で送信してもよろしいでしょうか？<br />
      よろしければ、一番下の「送信」ボタンをクリックしてください。</p>
    <form name="form1" id="form1" method="post" action="<!--{$smarty.server.PHP_SELF|escape}-->">
      <input type="hidden" name="mode" value="complete" />
      <!--{foreach key=key item=item from=$arrForm}-->
        <!--{if $key ne 'mode'}-->
      <input type="hidden" name="<!--{$key}-->" value="<!--{$item|escape}-->" />
        <!--{/if}-->
      <!--{/foreach}-->
      <table summary="お問い合わせ内容確認">
        <tr>
          <th>お名前<span class="attention">※</span></th>
          <td><!--{$arrForm.name01|escape}-->　<!--{$arrForm.name02|escape}--></td>
        </tr>
        <tr>
          <th>お名前（フリガナ）<span class="attention">※</span></th>
          <td><!--{$arrForm.kana01|escape}-->　<!--{$arrForm.kana02|escape}--></td>
        </tr>
        <tr>
          <th>郵便番号</th>
          <td>
             <!--{if strlen($arrForm.zip01) > 0 && strlen($arrForm.zip02) > 0}-->
               〒<!--{$arrForm.zip01|escape}-->-<!--{$arrForm.zip02|escape}-->
             <!--{/if}-->
          </td>
        </tr>
        <tr>
          <th>住所</th>
          <td><!--{$arrPref[$arrForm.pref]}--><!--{$arrForm.addr01|escape}--><!--{$arrForm.addr02|escape}--></td>
        </tr>
        <tr>
          <th>電話番号</th>
          <td>
            <!--{if strlen($arrForm.tel01) > 0 && strlen($arrForm.tel02) > 0 && strlen($arrForm.tel03) > 0}-->
              <!--{$arrForm.tel01|escape}-->-<!--{$arrForm.tel02|escape}-->-<!--{$arrForm.tel03|escape}-->
            <!--{/if}-->
          </td>
        </tr>
        <tr>
          <th>メールアドレス<span class="attention">※</span></th>
          <td><a href="mailto:<!--{$arrForm.email|escape:'hex'}-->"><!--{$arrForm.email|escape:'hexentity'}--></a></td>
        </tr>
        <tr>
          <th>お問い合わせ内容<span class="attention">※</span><br />
             <span class="mini">（全角1000字以下）</span></th>
          <td><!--{$arrForm.contents|escape|nl2br}--></td>
        </tr>
      </table>
      <div class="tblareabtn">
      <a href="<!--{$smarty.server.PHP_SELF|escape}-->"
         onclick="fnModeSubmit('return', '', ''); return false;"
         onmouseover="chgImg('<!--{$TPL_DIR}-->img/common/b_back_on.gif','back02');"
         onmouseout="chgImg('<!--{$TPL_DIR}-->img/common/b_back.gif','back02');">
        <img src="<!--{$TPL_DIR}-->img/common/b_back.gif" width="150" height="30"
             alt="戻る" name="back02" id="back02" />
      </a>
      <input type="image"
             onmouseover="chgImgImageSubmit('<!--{$TPL_DIR}-->img/common/b_complete_on.gif',this)"
             onmouseout="chgImgImageSubmit('<!--{$TPL_DIR}-->img/common/b_complete.gif',this)"
             src="<!--{$TPL_DIR}-->img/common/b_complete.gif" alt="完了ページへ" name="send" id="send" class="box150" />
       </div>
     </form>
   </div>
</div>
<!--▲CONTENTS-->
