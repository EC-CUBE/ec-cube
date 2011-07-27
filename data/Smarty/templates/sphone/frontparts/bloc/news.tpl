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
<!-- ▼新着情報 -->

<section id="news_area">
<h2 class="title_block">新着情報</h2>
<ul class="newslist">
  <!--{section name=data loop=$arrNews max=3}-->
  <li>
    <a #windowcolumn href="javascript:getNewsDetail(<!--{$arrNews[data].news_id}-->);">
    <span class="news_title"><!--{$arrNews[data].news_title|h}--></span></a><br />
    <span class="news_date"><!--{$arrNews[data].news_date_disp|date_format:"%Y年 %m月 %d日"}--></span>
  </li>
  </a>
  <!--{/section}-->
</ul>

<!--{if $newsCount > 3}-->
<div class="btn_area">
<p><a href="javascript: void(0);" class="btn_more" id="btn_more_news" onClick="getNews(3); return false;">もっとみる(＋3件)</a></p>
</div>
<!--{/if}-->
</section>
<!-- ▲新着情報 -->


<script>
var newsPageNo = 2;

function getNews(limit) {
    $.mobile.pageLoading();
    var i = limit;
    
    $.ajax({
        url: "<!--{$smarty.const.HTTP_URL}-->frontparts/bloc/news.php",
        type: "POST",
           data: "mode=getList&pageno="+newsPageNo+"&disp_number="+i,
           cache: false,
           dataType: "json",
           error: function(XMLHttpRequest, textStatus, errorThrown){
            alert(textStatus);
            $.mobile.pageLoading(true);
           },
           success: function(result){
             for (var j = 0; j < i; j++) {
                 if (result[j] != null) {
                    var news = result[j];
                    var maxCnt = $("#news_area ul.newslist li").length - 1;
                    var newsEl = $("#news_area ul.newslist li").get(maxCnt);
                    newsEl = $(newsEl).clone(true).insertAfter(newsEl);
                    maxCnt++;
                    
                     //件名をセット
                     $($("#news_area ul.newslist li a span.news_title").get(maxCnt)).text(news.news_title);
                     
                     //リンクをセット
                     $($("#news_area ul.newslist li a").get(maxCnt)).attr("href", "javascript:getNewsDetail(" + news.news_id + ");");
                     
                     //年月をセット
                     var newsDateDispArray = news.news_date_disp.split("-"); //ハイフンで年月日を分解
                     var newsDateDisp = newsDateDispArray[0] + "年 " + newsDateDispArray[1] + "月 " + newsDateDispArray[2] + "日";
                     $($("#news_area ul.newslist li span.news_date").get(maxCnt)).text(newsDateDisp);
                 }
             }

             //すべての新着情報を表示したか判定
             var newsPageCount = result.news_page_count;
             if (parseInt(newsPageCount) <= newsPageNo) {
                 $("#btn_more_news").hide();
             }
             
             newsPageNo++;
             
             $.mobile.pageLoading(true);
           }
    });
}

function getNewsDetail(newsId) {
    $.mobile.pageLoading();
    $.ajax({
        url: "<!--{$smarty.const.HTTP_URL}-->frontparts/bloc/news.php",
        type: "GET",
           data: "mode=getDetail&news_id="+newsId,
           cache: false,
           dataType: "json",
           error: function(XMLHttpRequest, textStatus, errorThrown){
               alert(textStatus);
               $.mobile.pageLoading(true);
           },
           success: function(result){
             if (result[0] != null) {
                 var news = result[0];
                var maxCnt = 0;
                
                 //件名をセット
                 $($("#windowcolumn dl.view_detail dt a").get(maxCnt)).text(news.news_title);
                 if (news.news_url != null) {
                     $($("#windowcolumn dl.view_detail dt a").get(maxCnt)).attr("href", news.news_url);
                 } else {
                     $($("#windowcolumn dl.view_detail dt a").get(maxCnt)).attr("href", "#");
                 }
                 
                 //年月をセット
                 //var newsDateDispArray = news.news_date_disp.split("-"); //ハイフンで年月日を分解
                 //var newsDateDisp = newsDateDispArray[0] + "年 " + newsDateDispArray[1] + "月 " + newsDateDispArray[2] + "日";
                 //$($("#windowcolumn dl.view_detail dt").get(maxCnt)).text(newsDateDisp);

                //コメントをセット
                 $("#newsComment").text(news.news_comment);
             }
             
             $.mobile.pageLoading(true);
             $.mobile.changePage('#windowcolumn', 'slideup');
           }
    });
}
</script>