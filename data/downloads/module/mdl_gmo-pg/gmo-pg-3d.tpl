<span class="fs14">3-Dセキュア認証を続けます。</span>
<FORM name="downloadForm" action="<!--{$ACSUrl}-->" method="POST">
<NOSCRIPT>
  <BR><BR>
  <CENTER>
  <H2>
  ボタンをクリックしてください。
  </H2>
  <INPUT type="submit" value="OK">
  </CENTER>
</NOSCRIPT>
  <INPUT type="hidden" name="PaReq" value="<!--{$PaReq}-->">
  <INPUT type="hidden" name="TermUrl" value="<!--{$TermUrl}-->">
  <INPUT type="hidden" name="MD" value="<!--{$MD}-->">
</FORM>
<SCRIPT language="Javascript">
<!--
  function OnLoadEvent() {
    document.downloadForm.submit();
  }
//-->
</SCRIPT>
