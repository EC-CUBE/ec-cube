<!--{*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2014 LOCKON CO.,LTD. All Rights Reserved.
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
                <a id="windowcolumn<!--{$smarty.section.data.index}-->" href="javascript:getNewsDetail(<!--{$arrNews[data].news_id}-->);">
                <span class="news_title"><!--{$arrNews[data].news_title|h}--></span></a><br />
                <span class="news_date"><!--{$arrNews[data].cast_news_date|date_format:"%Y年 %m月 %d日"}--></span>
            </li>
        <!--{/section}-->
    </ul>

    <!--{if $newsCount > 3}-->
        <div class="btn_area">
            <p><a href="javascript:;" class="btn_more" id="btn_more_news" onclick="getNews(3); return false;">もっとみる(＋3件)</a></p>
        </div>
    <!--{/if}-->
</section>
<!-- ▲新着情報 -->


<script>
    var newsPageNo = 2;

    function getNews(limit) {
        eccube.showLoading();
        var i = limit;

        $.ajax({
            url: "<!--{$smarty.const.ROOT_URLPATH}-->frontparts/bloc/news.php",
            type: "POST",
            data: "mode=getList&pageno="+newsPageNo+"&disp_number="+i,
            cache: false,
            dataType: "json",
            error: function(XMLHttpRequest, textStatus, errorThrown){
                alert(textStatus);
                eccube.hideLoading();
            },
            success: function(result){
                if (result.error) {
                    alert(result.error);
                } else {
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
                            var newsDateDispArray = news.cast_news_date.split("-"); //ハイフンで年月日を分解
                            var newsDateDisp = newsDateDispArray[0] + "年 " + newsDateDispArray[1] + "月 " + newsDateDispArray[2] + "日";
                            $($("#news_area ul.newslist li span.news_date").get(maxCnt)).text(newsDateDisp);
                        }
                    }

                    //全ての新着情報を表示したか判定
                    var newsPageCount = result.news_page_count;
                    if (parseInt(newsPageCount) <= newsPageNo) {
                        $("#btn_more_news").hide();
                    }

                    newsPageNo++;
                }
                eccube.hideLoading();
            }
        });
    }

    var loadingState = 0;
    function getNewsDetail(newsId) {
        if (loadingState == 0) {
            loadingState = 1;
            eccube.showLoading();
            $.ajax({
                url: "<!--{$smarty.const.ROOT_URLPATH}-->frontparts/bloc/news.php",
                type: "GET",
                data: "mode=getDetail&news_id="+newsId,
                cache: false,
                async: false,
                dataType: "json",
                error: function(XMLHttpRequest, textStatus, errorThrown){
                    alert(textStatus);
                    eccube.hideLoading();
                    loadingState = 0;
                },
                success: function(result){
                    if (result.error) {
                        alert(result.error);
                        eccube.hideLoading();
                        loadingState = 0;
                    }
                    else if (result != null) {
                        var dialog = $("#news-dialog");

                        //件名をセット
                        $("#news-dialog-title").remove();
                        if (result.news_url != null) {
                            dialog.find(".dialog-content").append(
                                $('<h3 id="news-dialog-title">').append(
                                    $('<a>')
                                        .attr('href', result.news_url)
                                        .attr('rel', "external")
                                        .attr('target', "_blank")
                                        .text(result.news_title)
                                )
                            );
                        } else {
                            dialog.find(".dialog-content").append(
                                $('<h3 id="news-dialog-title">').text(result.news_title)
                            );
                        }

                        //本文をセット
                        $("#news-dialog-body").remove();
                        if (result.news_comment != null) {
                            dialog.find(".dialog-content").append(
                                $('<div id="news-dialog-body">').html(result.news_comment.replace(/\n/g,"<br />"))
                            );
                        }

                        //ダイアログをモーダルウィンドウで表示
                        $.colorbox({inline: true, href: dialog, onOpen: function(){
                            dialog.show().css('width', String($('body').width() * 0.9) + 'px');
                        }, onComplete: function(){
                            eccube.hideLoading();
                            loadingState = 0;
                        }, onClosed: function(){
                            dialog.hide();
                        }});
                    }
                    else {
                        eccube.hideLoading();
                        loadingState = 0;
                        alert('取得できませんでした。');
                    }
                }
            });
        }
    }
</script>

<!--{include file="`$smarty.const.SMARTPHONE_TEMPLATE_REALDIR`frontparts/dialog_modal.tpl" dialog_id="news-dialog" dialog_title="新着情報"}-->
