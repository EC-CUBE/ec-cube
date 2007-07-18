<!-- EBiS tag version2.10 start -->
<script type="text/javascript">
if ( location.protocol == 'http:' ){ 
	strServerName = '<!--{$arrEbis.login_url|escape}-->'; 
} else { 
	strServerName = 'https://secure2.ebis.ne.jp/ver3';
}
cid = '<!--{$arrEbis.cid|escape}-->'; pid = '<!--{$arrEbis.pid|escape}-->'; m1id='<!--{$arrEbis.m1id|escape}-->'; a1id='<!--{$arrEbis.a1id|escape}-->'; o1id='<!--{$arrEbis.o1id|escape}-->'; o2id='<!--{$arrEbis.o2id|escape}-->'; o3id='<!--{$arrEbis.o3id|escape}-->'; o4id='<!--{$arrEbis.o4id|escape}-->'; o5id='<!--{$arrEbis.o5id|escape}-->';
document.write("<scr" + "ipt type=\"text\/javascript\" src=\"" + strServerName + "\/ebis_tag.php?cid=" + cid + "&pid=" + pid + "&m1id=" + m1id + "&a1id=" + a1id + "&o1id=" + o1id + "&o2id=" + o2id + "&o3id=" + o3id + "&o4id=" + o4id + "&o5id=" + o5id + "\"><\/scr" + "ipt>");
</script>
<noscript>
<img src="https://secure2.ebis.ne.jp/ver3/log.php?argument=<!--{$arrEbis.cid|escape}-->&ebisPageID=<!--{$arrEbis.pid|escape}-->&ebisMember=<!--{$arrEbis.m1id|escape}-->&ebisAmount=<!--{$arrEbis.a1id|escape}-->&ebisOther1=<!--{$arrEbis.o1id|escape}-->&ebisOther2=<!--{$arrEbis.o2id|escape}-->&ebisOther3=<!--{$arrEbis.o3id|escape}-->&ebisOther4=<!--{$arrEbis.o4id|escape}-->&ebisOther5=<!--{$arrEbis.o5id|escape}-->" width="0" height="0">
</noscript>
<!-- EBiS tag end -->