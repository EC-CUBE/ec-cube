<!--{include file="`$smarty.const.TEMPLATE_DIR`popup_header.tpl" subtitle="アンケート　`$QUESTION.title`/完了画面"}-->

<div id="inquiry">
  <h1><!--{$QUESTION.title|escape}--></h1>
  <form name="form1" id="form1" method="post" action="">
    <div class="message">
      <p>
        アンケートの送信が完了いたしました。<br />
        ご協力ありがとうございました。
      </p>
      <button type="submit" name="subm1" onClick="window.close()"><span>閉じる</span></button>
    </div>
  </form>
</div>

<!--{include file="`$smarty.const.TEMPLATE_DIR`popup_footer.tpl"}-->
