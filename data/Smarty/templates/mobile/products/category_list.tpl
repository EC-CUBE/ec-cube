<center><!--{$arrCategory.category_name|escape}--></center>

<hr>

<!--{foreach from=$arrChildren key=i item=arrChild}-->
<!--{if $arrChild.has_children}-->
<!--{assign var=path value="`$smarty.const.URL_DIR`products/category_list.php"}-->
<!--{else}-->
<!--{assign var=path value="`$smarty.const.URL_DIR`products/list.php"}-->
<!--{/if}-->
<!--{if $i+1<9}-->
[emoji:<!--{$i+125}-->]<a href="<!--{$path}-->?category_id=<!--{$arrChild.category_id}-->" accesskey="<!--{$i+1}-->"><!--{$arrChild.category_name|escape}-->(<!--{$arrChild.product_count}-->)</a><br>
<!--{else}-->
[<!--{$i+1}-->]<a href="<!--{$path}-->?category_id=<!--{$arrChild.category_id}-->"><!--{$arrChild.category_name|escape}-->(<!--{$arrChild.product_count}-->)</a><br>
<!--{/if}-->
<!--{/foreach}-->

<br>
<hr>

<!--XXX--><a href="#" accesskey="9">[emoji:133]かごを見る</a><br>
<a href="<!--{$smarty.const.URL_SITE_TOP}-->" accesskey="0">[emoji:134]TOPページへ</a><br>

<br>

<!-- ▼フッター ここから -->
<center>LOCKON CO.,LTD.</center>
<!-- ▲フッター ここまで -->
