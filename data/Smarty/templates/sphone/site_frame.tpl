<!DOCTYPE HTML>
<!--{*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2011 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 *}-->

<html lang="ja">
<head>
<meta charset="UTF-8"> 
<meta http-equiv="Content-Type" content="text/html; charset=<!--{$smarty.const.CHAR_CODE}-->" />

<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1, user-scalable=0" />
<meta name="format-detection" content="telephone=no">
<meta http-equiv="Content-Script-Type" content="text/javascript" />
<meta http-equiv="Content-Style-Type" content="text/css" />
<!--{* 共通CSS *}-->
<link rel="stylesheet" media="only screen" href="<!--{$TPL_URLPATH}-->css/import.css" />

<!--{if $tpl_page_category == "abouts"}-->
<!--{if ($smarty.server.HTTPS != "") && ($smarty.server.HTTPS != "off")}-->
<script src="https://maps.google.com/maps/api/js?sensor=false"></script>
<!--{else}-->
<script src="http://maps.google.com/maps/api/js?sensor=false"></script>
<!--{/if}-->
<!--{/if}-->
<script src="<!--{$smarty.const.ROOT_URLPATH}-->js/navi.js"></script>
<script src="<!--{$smarty.const.ROOT_URLPATH}-->js/win_op.js"></script>
<script src="<!--{$smarty.const.ROOT_URLPATH}-->js/site.js"></script>
<script src="<!--{$TPL_URLPATH}-->js/jquery-1.5.2.min.js"></script>
<script src="<!--{$TPL_URLPATH}-->js/jquery.biggerlink.js"></script>
<script type="text/javascript">    
    $(function(){
        $('.recomendblock, .list_area, .newslist li, .bubbleBox, .arrowBox, .category_body, .navBox li').biggerlink();
        //$('.recomendblock, .newslist li,.bubbleBox,.arrowBox,.navBox li').biggerlink();
    });
</script>
<script type="text/javascript" src="<!--{$TPL_URLPATH}-->js/btn.js"></script>
<script type="text/javascript" src="<!--{$TPL_URLPATH}-->js/barbutton.js"></script>
<script type="text/javascript" src="<!--{$TPL_URLPATH}-->js/category.js"></script>
<script type="text/javascript" src="<!--{$TPL_URLPATH}-->js/news.js"></script>

<!--{* スマートフォンカスタマイズ用CSS *}-->
<link rel="stylesheet" media="only screen" href="<!--{$TPL_URLPATH}-->css/jquery.mobile-1.0a3.min.css" />
<link rel="stylesheet" media="only screen" href="<!--{$TPL_URLPATH}-->css/jquery.lightbox-0.5.css" />

<!--{* スマートフォンカスタマイズ用JS *}-->
<script src="<!--{$TPL_URLPATH}-->js/config.js"></script>
<script src="<!--{$TPL_URLPATH}-->js/jquery.mobile-1.0a3.min.js"></script>
<script src="<!--{$TPL_URLPATH}-->js/autoresize.jquery.min.js"></script>
<script src="<!--{$TPL_URLPATH}-->js/jquery.flickslide.js"></script>
<script src="<!--{$TPL_URLPATH}-->js/jquery.lightbox-0.5.min.js"></script>
<script src="<!--{$TPL_URLPATH}-->js/favorite.js"></script>

<title><!--{$arrSiteInfo.shop_name|h}--><!--{if $tpl_subtitle|strlen >= 1}--> / <!--{$tpl_subtitle|h}--><!--{elseif $tpl_title|strlen >= 1}--> / <!--{$tpl_title|h}--><!--{/if}--></title>
<!--{if $arrPageLayout.author|strlen >= 1}-->
    <meta name="author" content="<!--{$arrPageLayout.author|h}-->" />
<!--{/if}-->
<!--{if $arrPageLayout.description|strlen >= 1}-->
    <meta name="description" content="<!--{$arrPageLayout.description|h}-->" />
<!--{/if}-->
<!--{if $arrPageLayout.keyword|strlen >= 1}-->
    <meta name="keywords" content="<!--{$arrPageLayout.keyword|h}-->" />
<!--{/if}-->
<!--{* iPhone用アイコン画像 *}-->
<link rel="apple-touch-icon" href="<!--{$TPL_URLPATH}-->img/common/apple-touch-icon.png" />

<script type="text/javascript">//<![CDATA[
    <!--{$tpl_javascript}-->
    $(function(){
    <!--{$tpl_onload}-->
    <!--{* lightbox準備 *}-->
    $('a[rel*=lightbox]').lightBox({
        imageLoading: '<!--{$TPL_URLPATH}-->img/lightboximages/lightbox-ico-loading.gif', 
        imageBtnPrev: '<!--{$TPL_URLPATH}-->img/lightboximages/lightbox-btn-prev.gif', 
        imageBtnNext: '<!--{$TPL_URLPATH}-->img/lightboximages/lightbox-btn-next.gif', 
        imageBtnClose: '<!--{$TPL_URLPATH}-->img/lightboximages/lightbox-btn-close.gif', 
        imageBlank: '<!--{$TPL_URLPATH}-->img/lightboximages/lightbox-blank.gif' 
    });
});
//]]>
</script>



<!--{* ▼Head COLUMN*}-->
  <!--{if $arrPageLayout.HeadNavi|@count > 0}-->
    <!--{* ▼上ナビ *}-->
      <!--{foreach key=HeadNaviKey item=HeadNaviItem from=$arrPageLayout.HeadNavi}-->
        <!--{* ▼<!--{$HeadNaviItem.bloc_name}--> ここから*}-->
          <!--{if $HeadNaviItem.php_path != ""}-->
            <!--{include_php file=$HeadNaviItem.php_path items=$HeadNaviItem}-->
          <!--{else}-->
            <!--{include file=$HeadNaviItem.tpl_path items=$HeadNaviItem}-->
          <!--{/if}-->
        <!--{* ▲<!--{$HeadNaviItem.bloc_name}--> ここまで*}-->
      <!--{/foreach}-->
    <!--{* ▲上ナビ *}-->
  <!--{/if}-->
<!--{* ▲Head COLUMN*}-->
</head>

<!-- ▼BODY部 スタート -->
<!--{include file='./site_main.tpl'}-->
<!-- ▲BODY部 エンド -->

</html>
