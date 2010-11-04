<!--{include file="`$smarty.const.TEMPLATE_DIR`popup_header.tpl" subtitle="アンケート　`$QUESTION.title`/確認画面"}-->

<script type="text/javascript">
<!--
function func_return(){
  document.form1.mode.value = "return";
  document.form1.submit();
}
//-->
</script>

<div id="inquiry">
  <h1><!--{$QUESTION.title|escape}--></h1>
  <form name="form1" method="post" action="?">
    <input type="hidden" name="mode" value="regist">
    <!--{foreach key=key item=item from=$arrHidden}-->
    <input type="hidden" name="<!--{$key}-->" value="<!--{$item|escape}-->" />
    <!--{/foreach}-->
    <div id="inquiry-contents"><!--{$QUESTION.contents|escape|nl2br}--></div>
    <!--{if $errmsg}--><p><span class="attention">入力エラーが発生致しました。各項目のエラーメッセージをご確認の上、正しく入力してください。</span></p><!--{/if}-->
    <table id="inquiry-body">
      <!--{section name=question loop=$QUESTION.question}-->
      <!--{if $QUESTION.question[question].kind ne '0' }-->
      <tr>
        <th>質問<!--{$smarty.section.question.iteration}-->：<!--{$QUESTION.question[question].name|escape}--></th>
      </tr>
      <tr>
      <!--{if $QUESTION.question[question].kind eq 1}-->
        <td>
        <!--{$smarty.post.option[$smarty.section.question.index]|nl2br}-->
        </td>
      <!--{elseif $QUESTION.question[question].kind eq 2}-->
        <td>
        <!--{$smarty.post.option[$smarty.section.question.index]|escape}-->
        </td>
      <!--{elseif $QUESTION.question[question].kind eq 4}-->
        <td>
        <!--{lfArray_Search_key_Smarty arr=$QUESTION.question[question].option val=$smarty.post.option[$smarty.section.question.index] }-->
        </td>
      <!--{elseif $QUESTION.question[question].kind eq 3}-->
        <td>
        <!--{foreach item=sub from=$smarty.post.option[question]}-->  
          <!--{if strlen($sub) > 0}-->
            <!--{lfArray_Search_key_Smarty arr=$QUESTION.question[question].option val=$sub }--><br>
          <!--{/if}-->
        <!--{/foreach}-->
        </td>
      <!--{/if}-->
      </tr>
      <!--{/if}-->
      <!--{/section}-->
    </table>
    <p><span class="attention">※</span>印は入力必須項目です。</p>
    <table id="inquiry-personal">
      <tr>
        <th><span class="attention">※</span>お名前</th>
        <td>
          <!--{$arrForm.name01|escape}-->&nbsp;<!--{$arrForm.name02|escape}-->
          <input type="hidden" name="name01" value="<!--{$arrForm.name01|escape}-->" />
          <input type="hidden" name="name02" value="<!--{$arrForm.name02|escape}-->" />
        </td>
      </tr>
      <tr>
        <th><span class="attention">※</span>お名前(フリガナ)</th>
        <td>
          <!--{$arrForm.kana01|escape}-->&nbsp;<!--{$arrForm.kana02|escape}-->
          <input type="hidden" name="kana01" value="<!--{$arrForm.kana01|escape}-->" />
          <input type="hidden" name="kana02" value="<!--{$arrForm.kana02|escape}-->" />
        </td>
      </tr>
      <tr>
        <th><span class="attention">※</span>郵便番号</th>
        <td>
          〒<!--{$arrForm.zip01|escape}-->-<!--{$arrForm.zip02|escape}-->
          <input type="hidden" name="zip01" value="<!--{$arrForm.zip01|escape}-->" />
          <input type="hidden" name="zip02" value="<!--{$arrForm.zip02|escape}-->" />
        </td>
      </tr>
      <tr>
        <th><span class="attention">※</span>ご住所</th>
        <td>
          <input type="hidden" name="pref" value="<!--{$arrForm.pref|escape}-->" />
          <input type="hidden" name="addr01" value="<!--{$arrForm.addr01|escape}-->" />
          <input type="hidden" name="addr02" value="<!--{$arrForm.addr02|escape}-->" />
          <!--{$arrPref[$arrForm.pref]|escape}--><!--{$arrForm.addr01|escape}--> <!--{$arrForm.addr02|escape}-->
        </td>
      </tr>
      <tr>
        <th><span class="attention">※</span>お電話番号</th>
        <td>
          <!--{$arrForm.tel01|escape}-->-<!--{$arrForm.tel02|escape}-->-<!--{$arrForm.tel03|escape}-->
          <input type="hidden" name="tel01" value="<!--{$arrForm.tel01|escape}-->" />
          <input type="hidden" name="tel02" value="<!--{$arrForm.tel02|escape}-->" />
          <input type="hidden" name="tel03" value="<!--{$arrForm.tel03|escape}-->" />
        </td>
      </tr>
      <tr>
        <th><span class="attention">※</span>メールアドレス</th>
        <td>
          <!--{$arrForm.email|escape}-->
          <input type="hidden" name="email" value="<!--{$arrForm.email|escape}-->" />
          <input type="hidden" name="email02" value="<!--{$arrForm.email02|escape}-->" />
        </td>
      </tr>
    </table>
    <div class="btn">
      <button type="button" name="subm1" onclick="return func_return();"><span>戻る</span></button>
      <button type="submit" name="subm2"><span>送信</span></button>
    </div>
  </form>
</div>

<!--{include file="`$smarty.const.TEMPLATE_DIR`popup_footer.tpl"}-->
