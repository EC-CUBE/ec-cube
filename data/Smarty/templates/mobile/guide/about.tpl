<center>���Ĳ�ҾҲ�</center>

<hr>

<!-- ����ʸ �������� -->
<!--{if $arrSiteInfo.shop_name != ""}-->
[emoji:38]<font color="#800000">Ź̾</font><br>
<!--{$arrSiteInfo.shop_name|escape}--><br>
<!--{/if}-->
<!--{if $arrSiteInfo.company_name != ""}-->
[emoji:39]<font color="#800000">���̾</font><br>
<!--{$arrSiteInfo.company_name|escape}--><br>
<!--{/if}-->
<!--{if $arrSiteInfo.zip01 != ""}-->
[emoji:121]<font color="#800000">����</font><br>
��<!--{$arrSiteInfo.zip01|escape}-->-<!--{$arrSiteInfo.zip02|escape}--><br>
<!--{$arrSiteInfo.pref|escape}--><!--{$arrSiteInfo.addr01|escape}--><!--{$arrSiteInfo.addr02|escape}--><br>
<!--{/if}-->
<!--{if $arrSiteInfo.tel01 != ""}-->
[emoji:74]<font color="#800000">�����ֹ�</font><br>
<!--{$arrSiteInfo.tel01|escape}-->-<!--{$arrSiteInfo.tel02|escape}-->-<!--{$arrSiteInfo.tel03|escape}--><br>
<!--{/if}-->
<!--{if $arrSiteInfo.fax01 != ""}-->
[emoji:76]<font color="#800000">FAX�ֹ�</font><br>
<!--{$arrSiteInfo.fax01|escape}-->-<!--{$arrSiteInfo.fax02|escape}-->-<!--{$arrSiteInfo.fax03|escape}--><br>
<!--{/if}-->
<!--{if $arrSiteInfo.business_hour != ""}-->
[emoji:176]<font color="#800000">�ĶȻ���</font><br>
<!--{$arrSiteInfo.business_hour|escape}--><br>
<!--{/if}-->
<!--{if $arrSiteInfo.email02 != ""}-->
[emoji:110]<font color="#800000">�᡼�륢�ɥ쥹</font><br>
<a href="mailto:<!--{$arrSiteInfo.email02|escape}-->"><!--{$arrSiteInfo.email02|escape}--></a><br>
<!--{/if}-->
<!--{if $arrSiteInfo.good_traded != ""}-->
[emoji:72]<font color="#800000">�谷����</font><br>
<!--{$arrSiteInfo.good_traded|escape|nl2br}--><br>
<!--{/if}-->
<!--{if $arrSiteInfo.message != ""}-->
[emoji:70]<font color="#800000">��å�����</font><br>
<!--{$arrSiteInfo.message|escape|nl2br}--><br>
<!--{/if}-->
<!-- ����ʸ �����ޤ� -->

<br>
<hr>

<a href="<!--{$smarty.const.URL_CART_TOP}-->" accesskey="9"><!--{9|numeric_emoji}-->�����򸫤�</a><br>
<a href="<!--{$smarty.const.URL_SITE_TOP}-->" accesskey="0"><!--{0|numeric_emoji}-->TOP�ڡ�����</a><br>

<br>

<!-- ���եå��� �������� -->
<!--{include file='footer.tpl'}-->
<!-- ���եå��� �����ޤ� -->
