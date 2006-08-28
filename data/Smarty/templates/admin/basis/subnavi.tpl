<SCRIPT LANGUAGE="JavaScript">
<!--
function Change(c){
    eventSource = event.srcElement

    if (eventSource.tagName=="TR" || eventSource.tagName=="TABLE") {
        return;
    }

    while(eventSource.tagName!="TD") {
        eventSource=eventSource.parentElement;
    }

    eventSource.style.backgroundColor = c;
}

function Reset(c){
    eventSource.style.backgroundColor = c;
}
//-->
</SCRIPT>
<table width="141" border="0" cellspacing="0" cellpadding="0" summary=" " OnMouseOver="Change('orange')">
	<!--ナビ-->
	<tr><td class=<!--{if $tpl_subno != 'index'}-->"navi"<!--{else}-->"navi-on"<!--{/if}-->><a href="./index.php"><img src="/img/contents/icon.jpg" width="15" height="9" alt="" border="0"><span class="navi_text">SHOPマスタ</span></a></td></tr>
	<tr><td><img src="/img/contents/navi_line.gif" width="140" height="2" alt=""></td></tr>
	<tr><td class=<!--{if $tpl_subno != 'tradelaw'}-->"navi"<!--{else}-->"navi-on"<!--{/if}--> ><a href="./tradelaw.php"><img src="/img/contents/icon.jpg" width="15" height="9" alt="" border="0"><span class="navi_text">特定商取引法</span></a></td></tr>
	<tr><td><img src="/img/contents/navi_line.gif" width="140" height="2" alt=""></td></tr>
	<tr><td class=<!--{if $tpl_subno != 'delivery'}-->"navi"<!--{else}-->"navi-on"<!--{/if}-->><a href="./delivery.php"><img src="/img/contents/icon.jpg" width="15" height="9" alt="" border="0"><span class="navi_text">配送業者<!--{if $smarty.const.INPUT_DELIV_FEE}-->・配送料<!--{/if}-->設定</span></a></td></tr>
	<tr><td><img src="/img/contents/navi_line.gif" width="140" height="2" alt=""></td></tr>
	<tr><td class=<!--{if $tpl_subno != 'payment'}-->"navi"<!--{else}-->"navi-on"<!--{/if}-->><a href="./payment.php"><img src="/img/contents/icon.jpg" width="15" height="9" alt="" border="0"><span class="navi_text">支払方法設定</span></a></td></tr>
	<tr><td><img src="/img/contents/navi_line.gif" width="140" height="2" alt=""></td></tr>
	<tr><td class=<!--{if $tpl_subno != 'point'}-->"navi"<!--{else}-->"navi-on"<!--{/if}-->><a href="./point.php"><img src="/img/contents/icon.jpg" width="15" height="9" alt="" border="0"><span class="navi_text">ポイント設定</span></a></td></tr>
	<tr><td><img src="/img/contents/navi_line.gif" width="140" height="2" alt=""></td></tr>
	<tr><td class=<!--{if $tpl_subno != 'mail'}-->"navi"<!--{else}-->"navi-on"<!--{/if}-->><a href="./mail.php"><img src="/img/contents/icon.jpg" width="15" height="9" alt="" border="0"><span class="navi_text">メール設定</span></a></td></tr>
	<tr><td><img src="/img/contents/navi_line.gif" width="140" height="2" alt=""></td></tr>
	<tr><td class=<!--{if $tpl_subno != 'seo'}-->"navi"<!--{else}-->"navi-on"<!--{/if}-->><a href="./seo.php"><img src="/img/contents/icon.jpg" width="15" height="9" alt="" border="0"><span class="navi_text">SEO管理</span></a></td></tr>
	<tr><td><img src="/img/contents/navi_line.gif" width="140" height="2" alt=""></td></tr>
	<tr><td class=<!--{if $tpl_subno != 'kiyaku'}-->"navi"<!--{else}-->"navi-on"<!--{/if}-->><a href="./kiyaku.php"><img src="/img/contents/icon.jpg" width="15" height="9" alt="" border="0"><span class="navi_text">会員規約設定</span></a></td></tr>
	<tr><td><img src="/img/contents/navi_line.gif" width="140" height="2" alt=""></td></tr>
	<tr><td class=<!--{if $tpl_subno != 'zip_install'}-->"navi"<!--{else}-->"navi-on"<!--{/if}-->><a href="#" onclick="win03('/admin/basis/zip_install.php', 'install', '750', '350');"><img src="/img/contents/icon.jpg" width="15" height="9" alt="" border="0"><span class="navi_text">郵便番号インストール</span></a></td></tr>
	<tr><td><img src="/img/contents/navi_line.gif" width="140" height="2" alt=""></td></tr>
	<!--ナビ-->
</table>