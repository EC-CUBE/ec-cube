<!--{*
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
*}-->
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
