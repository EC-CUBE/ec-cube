<!-- EBiS tag version2.00 start -->
<script type="text/javascript">
if ( location.protocol == 'http:' ){ 
	strServerName = 'http://daikoku.ebis.ne.jp'; 
} else { 
	strServerName = 'https://secure2.ebis.ne.jp/ver3';
}
cid = '<!--{$arrEbis.cid}-->'; pid = '<!--{$arrEbis.pid}-->'; m1id='<!--{$arrEbis.m1id}-->'; a1id='<!--{$arrEbis.a1id}-->'; o1id='<!--{$arrEbis.o1id}-->'; o2id='<!--{$arrEbis.o2id}-->'; o3id='<!--{$arrEbis.o3id}-->'; o4id='<!--{$arrEbis.o4id}-->'; o5id='<!--{$arrEbis.o5id}-->';
document.write("<scr" + "ipt type=\"text\/javascript\" src=\"" + strServerName + "\/ebis_tag.php?cid=" + cid + "&pid=" + pid + "&m1id=" + m1id + "&a1id=" + a1id + "&o1id=" + o1id + "&o2id=" + o2id + "&o3id=" + o3id + "&o4id=" + o4id + "&o5id=" + o5id + "\"><\/scr" + "ipt>");
</script>
<noscript>
<img src="https://secure2.ebis.ne.jp/ver3/log.php?argument=<!--{$smarty.const.EBIS_CID}-->&ebisPageID=<!--{$arrEbis.pid}-->" width="0" height="0">
</noscript>
<!-- EBiS tag end -->
