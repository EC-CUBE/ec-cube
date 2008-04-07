<h2>本人認証サービスを開始します。このままでしばらくお待ちください。</h2>
<form name="ACSCall" action="<!--{$AcsUrl}-->" method="POST">

<noscript>
<br />
<br />
<center>
<h2>
画面が移動しない場合は「OK」ボタンをクリックしてください。
</h2>
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
