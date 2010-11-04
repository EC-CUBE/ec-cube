<!--{include file="`$smarty.const.TEMPLATE_DIR`popup_header.tpl" subtitle="アンケート　`$QUESTION.title`"}-->

<div id="inquiry">
  <h1><!--{$QUESTION.title|escape}--></h1>

  <form name="form1" id="form1" method="post" action="?">
    <input type="hidden" name="question_id" value="<!--{$question_id}-->" />
    <input type="hidden" name="mode" value="confirm" />
    <div id="inquiry-contents"><!--{$QUESTION.contents|escape|nl2br}--></div>

    <!--{if $errmsg}--><p><span class="attention">入力エラーが発生致しました。各項目のエラーメッセージをご確認の上、正しく入力してください。</span></p><!--{/if}-->
    <table id="inquiry-body">
    <!--{include file=inquiry/inquiry.tpl}-->
    </table>

    <p><span class="attention">※</span>印は入力必須項目です。</p>

    <table id="inquiry-personal">
      <tr>
        <th><span class="attention">※</span>お名前</th>
        <td>
          <!--{if $arrErr.name01 || $arrErr.name02}-->
          <span class="attention"><!--{$arrErr.name01}--><!--{$arrErr.name02}--></span>
          <!--{/if}-->
          姓&nbsp;<input type="text" name="name01" value="<!--{$arrForm.name01|escape}-->" maxlength="<!--{$smarty.const.STEXT_LEN}-->" size="20" class="box20" <!--{if $arrErr.name01}--><!--{sfSetErrorStyle}--><!--{/if}--> />&nbsp;
          名&nbsp;<input type="text" name="name02" value="<!--{$arrForm.name02|escape}-->" maxlength="<!--{$smarty.const.STEXT_LEN}-->" size="20" class="box20" <!--{if $arrErr.name02}--><!--{sfSetErrorStyle}--><!--{/if}--> />
        </td>
      </tr>
      <tr>
        <th><span class="attention">※</span>お名前(フリガナ)</th>
        <td>
          <!--{if $arrErr.kana01 || $arrErr.kana02}-->
          <span class="attention"><!--{$arrErr.kana01}--><!--{$arrErr.kana02}--></span>
          <!--{/if}-->
          セイ&nbsp;<input type="text" name="kana01" value="<!--{$arrForm.kana01|escape}-->" maxlength="<!--{$smarty.const.STEXT_LEN}-->" size="20" class="box20" <!--{if $arrErr.kana01}--><!--{sfSetErrorStyle}--><!--{/if}--> />&nbsp;
          メイ&nbsp;<input type="text" name="kana02" value="<!--{$arrForm.kana02|escape}-->" maxlength="<!--{$smarty.const.STEXT_LEN}-->" size="20" class="box20" <!--{if $arrErr.kana02}--><!--{sfSetErrorStyle}--><!--{/if}--> />
        </td>
      </tr>
      <tr>
        <th><span class="attention">※</span>郵便番号</th>
        <td>
          <!--{if $arrErr.zip01 || $arrErr.zip02}-->
          <span class="attention"><!--{$arrErr.zip01}--><!--{$arrErr.zip02}--></span>
          <!--{/if}-->
          〒&nbsp;<input type="text" name="zip01" value="<!--{$arrForm.zip01|escape}-->" maxlength="<!--{$smarty.const.ZIP01_LEN}-->" size="6" class="box6" <!--{if $arrErr.zip01}--><!--{sfSetErrorStyle}--><!--{/if}--> />
          &nbsp;-&nbsp;
          <input type="text" name="zip02" value="<!--{$arrForm.zip02|escape}-->" maxlength="<!--{$smarty.const.ZIP02_LEN}-->" size="6" class="box6" <!--{if $arrErr.zip02}--><!--{sfSetErrorStyle}--><!--{/if}--> />&nbsp;
          <button type="button" name="address_input" onclick="fnCallAddress('<!--{$smarty.const.URL_INPUT_ZIP}-->', 'zip01', 'zip02', 'pref', 'addr01'); return false;"><span>住所入力</span></button>
        </td>
      </tr>
      <tr>
        <th><span class="attention">※</span>ご住所</th>
        <td>
          <!--{if $arrErr.pref || $arrErr.addr01 || $arrErr.addr02}-->
          <span class="attention"><!--{$arrErr.pref}--><!--{$arrErr.addr01}--><!--{$arrErr.addr02}--></span>
          <!--{/if}-->
          <select name="pref" <!--{if $arrErr.pref}--><!--{sfSetErrorStyle}--><!--{/if}-->>
            <option value="" selected>選択してください</option>
            <!--{html_options options=$arrPref selected=$arrForm.pref}-->
          </select><br />
          <input type="text" name="addr01" value="<!--{$arrForm.addr01|escape}-->" size="35" class="box35" <!--{if $arrErr.addr01}--><!--{sfSetErrorStyle}--><!--{/if}--> /><br />
          ご住所1（市区町村名）<br />
          <input type="text" name="addr02" value="<!--{$arrForm.addr02|escape}-->" size="35" class="box35" <!--{if $arrErr.addr02}--><!--{sfSetErrorStyle}--><!--{/if}--> /><br />
          ご住所2（番地、建物、マンション名）<br />
          <span class="attention">住所は必ず2つに分けて入力してください。マンション名は必ず入力してください。</span>
        </td>
      </tr>
      <tr>
        <th><span class="attention">※</span>お電話番号</th>
        <td>
          <!--{if $arrErr.tel01 || $arrErr.tel02 || $arrErr.tel03}-->
          <span class="attention"><!--{$arrErr.tel01}--><!--{$arrErr.tel02}--><!--{$arrErr.tel03}--></span>
          <!--{/if}-->
          <input type="text" name="tel01" value="<!--{$arrForm.tel01|escape}-->" maxlength="<!--{$smarty.const.TEL_ITEM_LEN}-->" size="6" class="box6" <!--{if $arrErr.tel01}--><!--{sfSetErrorStyle}--><!--{/if}--> />&nbsp;-&nbsp;
          <input type="text" name="tel02" value="<!--{$arrForm.tel02|escape}-->" maxlength="<!--{$smarty.const.TEL_ITEM_LEN}-->" size="6" class="box6" <!--{if $arrErr.tel02}--><!--{sfSetErrorStyle}--><!--{/if}--> />&nbsp;-&nbsp;
          <input type="text" name="tel03" value="<!--{$arrForm.tel03|escape}-->" maxlength="<!--{$smarty.const.TEL_ITEM_LEN}-->" size="6" class="box6" <!--{if $arrErr.tel03}--><!--{sfSetErrorStyle}--><!--{/if}--> />
        </td>
      </tr>
      <tr>
        <th><span class="attention">※</span>メールアドレス</th>
        <td>
          <!--{if $arrErr.email}--><span class="attention"><!--{$arrErr.email}--></span><!--{/if}-->
          <input type="text" name="email" value="<!--{$arrForm.email|escape}-->" size="35" class="box35" <!--{if $arrErr.email}--><!--{sfSetErrorStyle}--><!--{/if}--> /><br />
          <!--{if $arrErr.email02}--><span class="attention"><!--{$arrErr.email02}--></span><!--{/if}-->
          <input type="text" name="email02" value="<!--{$arrForm.email02|escape}-->" size="35" class="box35" <!--{if $arrErr.email02}--><!--{sfSetErrorStyle}--><!--{/if}--> /><br />
          <span class="attention">確認のため2度入力してください。</span>
        </td>
      </tr>
    </table>
    <div class="btn"><button type="submit"><span>確認ページへ</span></button></div>
  </form>
</div>

<!--{include file="`$smarty.const.TEMPLATE_DIR`popup_footer.tpl"}-->
