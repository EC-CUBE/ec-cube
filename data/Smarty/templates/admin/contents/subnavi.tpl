1<table width="141" border="0" cellspacing="0" cellpadding="0" summary=" " id="menu_navi">
	<!--ナビ-->

	<!--{if $tpl_subno == 'csv'}-->
		<tr><td class=<!--{if $tpl_subno_csv != 'product'}-->"subnavi"<!--{else}-->"subnavi-on"<!--{/if}-->><a href="./csv.php?tpl_subno_csv=<!--{$arrSubnavi.1}-->" onMouseOver="naviStyleChange('product_sub', '#b7b7b7')" <!--{if $tpl_subno_csv != 'product'}-->onMouseOut="naviStyleChange('product_sub', '#818287')"<!--{/if}--> id="product_sub"><span class="subnavi_text">商品管理</span></a></td></tr>
		<tr><td><img src="/img/contents/navi_subline.gif" width="140" height="2" alt=""></td></tr>
		<tr><td class=<!--{if $tpl_subno_csv != 'customer'}-->"subnavi"<!--{else}-->"subnavi-on"<!--{/if}-->><a href="./csv.php?tpl_subno_csv=<!--{$arrSubnavi.2}-->" onMouseOver="naviStyleChange('customer_sub', '#b7b7b7')" <!--{if $tpl_subno_csv != 'customer'}-->onMouseOut="naviStyleChange('customer_sub', '#818287')"<!--{/if}--> id="customer_sub"><span class="subnavi_text">顧客管理</span></a></td></tr>
		<tr><td><img src="/img/contents/navi_subline.gif" width="140" height="2" alt=""></td></tr>
		<tr><td class=<!--{if $tpl_subno_csv != 'order'}-->"subnavi"<!--{else}-->"subnavi-on"<!--{/if}-->><a href="./csv.php?tpl_subno_csv=<!--{$arrSubnavi.3}-->" onMouseOver="naviStyleChange('order_sub', '#b7b7b7')" <!--{if $tpl_subno_csv != 'order'}-->onMouseOut="naviStyleChange('order_sub', '#818287')"<!--{/if}--> id="order_sub"><span class="subnavi_text">受注管理</span></a></td></tr>
		<tr><td><img src="/img/contents/navi_subline.gif" width="140" height="2" alt=""></td></tr>
		<tr><td class=<!--{if $tpl_subno_csv != 'csv_sql'}-->"subnavi"<!--{else}-->"subnavi-on"<!--{/if}-->><a href="./csv_sql.php" onMouseOver="naviStyleChange('csv_sql', '#b7b7b7')" <!--{if $tpl_subno_csv != 'csv_sql'}-->onMouseOut="naviStyleChange('csv_sql', '#818287')"<!--{/if}--> id="csv_sql"><span class="subnavi_text">高度な設定</span></a></td></tr>
		<tr><td><img src="/img/contents/navi_subline.gif" width="140" height="2" alt=""></td></tr>
	<!--{/if}-->
	
	<!--ナビ-->
</table>
