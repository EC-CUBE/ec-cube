<!--{*
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
*}-->
<table width="141" border="0" cellspacing="0" cellpadding="0" summary=" " id="menu_navi">
	<!--ナビ-->
	<tr><td class=<!--{if $tpl_subno != 'index'}-->"navi"<!--{else}-->"navi-on"<!--{/if}-->><a href="./index.php" onMouseOver="naviStyleChange('index', '#a5a5a5')" <!--{if $tpl_subno != 'index'}-->onMouseOut="naviStyleChange('index', '#636469')"<!--{/if}--> id="index"><img src="<!--{$TPL_DIR}-->img/contents/icon.jpg" width="15" height="9" alt="" border="0"><span class="navi_text">商品マスタ</span></a></td></tr>
	<tr><td><img src="<!--{$TPL_DIR}-->img/contents/navi_line.gif" width="140" height="2" alt=""></td></tr>
	<tr><td class=<!--{if $tpl_subno != 'product'}-->"navi"<!--{else}-->"navi-on"<!--{/if}-->><a href="./product.php" onMouseOver="naviStyleChange('product', '#a5a5a5')" <!--{if $tpl_subno != 'product'}-->onMouseOut="naviStyleChange('product', '#636469')"<!--{/if}--> id="product"><img src="<!--{$TPL_DIR}-->img/contents/icon.jpg" width="15" height="9" alt="" border="0"><span class="navi_text">商品登録</span></a></td></tr>
	<tr><td><img src="<!--{$TPL_DIR}-->img/contents/navi_line.gif" width="140" height="2" alt=""></td></tr>
	<tr><td class=<!--{if $tpl_subno != 'upload_csv'}-->"navi"<!--{else}-->"navi-on"<!--{/if}-->><a href="./upload_csv.php" onMouseOver="naviStyleChange('upload_csv', '#a5a5a5')" <!--{if $tpl_subno != 'upload_csv'}-->onMouseOut="naviStyleChange('upload_csv', '#636469')"<!--{/if}--> id="upload_csv"><img src="<!--{$TPL_DIR}-->img/contents/icon.jpg" width="15" height="9" alt="" border="0"><span class="navi_text">商品登録CSV</span></a></td></tr>
	<tr><td><img src="<!--{$TPL_DIR}-->img/contents/navi_line.gif" width="140" height="2" alt=""></td></tr>
	<tr><td class=<!--{if $tpl_subno != 'class'}-->"navi"<!--{else}-->"navi-on"<!--{/if}-->><a href="./class.php" onMouseOver="naviStyleChange('class', '#a5a5a5')" <!--{if $tpl_subno != 'class'}-->onMouseOut="naviStyleChange('class', '#636469')"<!--{/if}--> id="class"><img src="<!--{$TPL_DIR}-->img/contents/icon.jpg" width="15" height="9" alt="" border="0"><span class="navi_text">規格管理</span></a></td></tr>
	<tr><td><img src="<!--{$TPL_DIR}-->img/contents/navi_line.gif" width="140" height="2" alt=""></td></tr>
	<tr><td class=<!--{if $tpl_subno != 'category'}-->"navi"<!--{else}-->"navi-on"<!--{/if}-->><a href="./category.php" onMouseOver="naviStyleChange('category', '#a5a5a5')" <!--{if $tpl_subno != 'category'}-->onMouseOut="naviStyleChange('category', '#636469')"<!--{/if}--> id="category"><img src="<!--{$TPL_DIR}-->img/contents/icon.jpg" width="15" height="9" alt="" border="0"><span class="navi_text">カテゴリー管理</span></a></td></tr>
	<tr><td><img src="<!--{$TPL_DIR}-->img/contents/navi_line.gif" width="140" height="2" alt=""></td></tr>
	<tr><td class=<!--{if $tpl_subno != 'product_rank'}-->"navi"<!--{else}-->"navi-on"<!--{/if}-->><a href="./product_rank.php" onMouseOver="naviStyleChange('product_rank', '#a5a5a5')" <!--{if $tpl_subno != 'product_rank'}-->onMouseOut="naviStyleChange('product_rank', '#636469')"<!--{/if}--> id="product_rank"><img src="<!--{$TPL_DIR}-->img/contents/icon.jpg" width="15" height="9" alt="" border="0"><span class="navi_text">商品並び替え</span></a></td></tr>
	<tr><td><img src="<!--{$TPL_DIR}-->img/contents/navi_line.gif" width="140" height="2" alt=""></td></tr>
	<tr><td class=<!--{if $tpl_subno != 'review'}-->"navi"<!--{else}-->"navi-on"<!--{/if}-->><a href="./review.php" onMouseOver="naviStyleChange('review', '#a5a5a5')" <!--{if $tpl_subno != 'review'}-->onMouseOut="naviStyleChange('review', '#636469')"<!--{/if}--> id="review"><img src="<!--{$TPL_DIR}-->img/contents/icon.jpg" width="15" height="9" alt="" border="0"><span class="navi_text">レビュー管理</span></a></td></tr>
	<tr><td><img src="<!--{$TPL_DIR}-->img/contents/navi_line.gif" width="140" height="2" alt=""></td></tr>
	<tr><td class=<!--{if $tpl_subno != 'trackback'}-->"navi"<!--{else}-->"navi-on"<!--{/if}-->><a href="./trackback.php" onMouseOver="naviStyleChange('trackback', '#a5a5a5')" <!--{if $tpl_subno != 'trackback'}-->onMouseOut="naviStyleChange('trackback', '#636469')"<!--{/if}--> id="trackback"><img src="<!--{$TPL_DIR}-->img/contents/icon.jpg" width="15" height="9" alt="" border="0"><span class="navi_text">トラックバック管理</span></a></td></tr>
	<tr><td><img src="<!--{$TPL_DIR}-->img/contents/navi_line.gif" width="140" height="2" alt=""></td></tr>
	<!--ナビ-->
</table>