<!--{if count($arrNews) > 0}-->
<center>
―――――――――<br>
<!--{marquee}-->
<!--{if $arrNews[0].news_url}--><a href="<!--{$arrNews[0].news_url|escape}-->"><!--{/if}-->
<!--{$arrNews[0].news_title|escape}-->
<!--{if $arrNews[0].news_url}--></a><!--{/if}-->
<!--{/marquee}-->
―――――――――<br>
</center>
<!--{/if}-->
