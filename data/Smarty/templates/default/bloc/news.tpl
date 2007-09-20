<!--{*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *}-->
<div id="newsarea">
  <h2>
    <img src="<!--{$TPL_DIR}-->img/top/news.jpg" width="400" height="29" alt="新着情報">
  </h2>

  <p>☆★☆ 新着情報は<a href="<!--{$smarty.const.URL_DIR}-->rss/index.php" target="_blank">RSS</a>で配信しています。★☆★</p>

<!--{section name=data loop=$arrNews}-->
  <dl>
    <dt><!--{$arrNews[data].news_date_disp|date_format:"%Y年%m月%d日"}--></dt>
    <dd>
      <!--{if $arrNews[data].news_url}-->
      <a href="<!--{$arrNews[data].news_url}-->"
        <!--{if $arrNews[data].link_method eq "2"}-->
        target="_blank"
        <!--{/if}-->>
      <!--{/if}-->
      <!--{$arrNews[data].news_title|escape|nl2br}-->
        <!--{if $arrNews[data].news_url}-->
      </a>
        <!--{/if}--><br/>
        <!--{$arrNews[data].news_comment|escape|nl2br}-->
     </dd>
  </dl>
<!--{/section}-->
</div>
