<p>本人認証サービスを開始します。このままでしばらくお待ちください。</p>
<form name="ACSCall" action="<!--{$AcsUrl}-->" method="POST">

<noscript>
<br />
<br />
<center>
<p>
画面が移動しない場合は「OK」ボタンをクリックしてください。
</p>
<input type="submit" value="OK">
</center>
</noscript>

<input type="hidden" name="PaReq" value="<!--{$PaReq}-->">
<input type="hidden" name="TermUrl" value="<!--{$TermUrl}-->">
<input type="hidden" name="MD" value="<!--{$MD}-->">

</form>

<script>
<!--
function OnLoadEvent() {
    document.ACSCall.submit();
}
//-->
</script>
