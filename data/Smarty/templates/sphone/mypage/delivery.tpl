<!--{*
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2012 LOCKON CO.,LTD. All Rights Reserved.
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
 */
*}-->

<section id="mypagecolumn">
    <h2 class="title"><!--{$tpl_title|h}--></h2>
    <!--{include file=$tpl_navi}-->

    <h3 class="title_mypage"><!--{$tpl_subtitle|h}--></h3>
    <!--★インフォメーション★-->
    <div class="information">
        <p>登録住所一覧です。<p>
        <p>最大<!--{$smarty.const.DELIV_ADDR_MAX|h}-->件まで登録できます。</p>
    </div>
    <!--{if $tpl_linemax < $smarty.const.DELIV_ADDR_MAX}-->
        <!--{* 退会時非表示 *}-->
        <!--{if $tpl_login}-->
            <!--★ボタン★-->
            <div class="btn_area_top">
                <a href="<!--{$smarty.const.ROOT_URLPATH}-->mypage/delivery_addr.php" class="btn_sub addbtn" rel="external" target="_blank">新しいお届け先を追加</a>
            </div>
        <!--{/if}-->
    <!--{/if}-->

    <div class="form_area">
        <!--{if $tpl_linemax > 0}-->
            <form name="form1" id="form1" method="post" action="<!--{$smarty.const.HTTPS_URL|sfTrimURL}-->/mypage/delivery.php" >
                <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
                <input type="hidden" name="mode" value="" />
                <input type="hidden" name="other_deliv_id" value="" />
                <input type="hidden" name="pageno" value="<!--{$tpl_pageno}-->" />

                <!--▼フォームボックスここから -->
                <div class="formBox">

                    <!--{section name=cnt loop=$arrOtherDeliv max=$dispNumber}-->
                        <!--▼お届け先 -->
                        <div class="delivBox">
                            <!--{assign var=OtherPref value="`$arrOtherDeliv[cnt].pref`"}-->
                            <p><em><span class="zip_title">お届け先住所<!--{$smarty.section.cnt.iteration}--></span></em>：<br />
                                〒<span class="zip01"><!--{$arrOtherDeliv[cnt].zip01}--></span>-<span class="zip02"><!--{$arrOtherDeliv[cnt].zip02}--></span><br />
                                <span class="address"><!--{$arrPref[$OtherPref]|h}--><!--{$arrOtherDeliv[cnt].addr01|h}--><!--{$arrOtherDeliv[cnt].addr02|h}--></span><br />
                                <span class="name01"><!--{$arrOtherDeliv[cnt].name01|h}--></span>&nbsp;<span class="name02"><!--{$arrOtherDeliv[cnt].name02|h}--></span></p>

                            <ul class="edit">
                                <li><a href="#" onClick="win02('./delivery_addr.php?other_deliv_id=<!--{$arrOtherDeliv[cnt].other_deliv_id}-->','deliv_disp','600','640'); return false;" class="b_edit deliv_edit" rel="external">編集</a></li>
                                <li><a href="#" onClick="fnModeSubmit('delete','other_deliv_id','<!--{$arrOtherDeliv[cnt].other_deliv_id}-->'); return false;" class="deliv_delete" rel="external"><img src="<!--{$TPL_URLPATH}-->img/button/btn_delete.png" class="pointer" width="21" height="20" alt="削除" /></a></li>
                            </ul>
                        </div>
                        <!--▲お届け先-->
                    <!--{/section}-->

                </div><!--▲formBox -->
            </form>
        <!--{else}-->
            <p class="delivempty"><strong>新しいお届け先はありません。</strong></p>
        <!--{/if}-->

        <!--{if count($arrOtherDeliv) > $dispNumber}-->
            <p><a rel="external" href="javascript: void(0);" class="btn_more" id="btn_more_delivery" onClick="getDelivery(<!--{$dispNumber}-->); return false;" rel="external">もっとみる(＋<!--{$dispNumber}-->件)</a></p>
        <!--{/if}-->

    </div><!-- /.form_area -->
</section>

<!--▼検索バー -->
<section id="search_area">
    <form method="get" action="<!--{$smarty.const.ROOT_URLPATH}-->products/list.php">
        <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
        <input type="hidden" name="mode" value="search" />
        <input type="search" name="name" id="search" value="" placeholder="キーワードを入力" class="searchbox" >
    </form>
</section>
<!--▲検索バー -->

<script>
    var pageNo = 2;

    function getDelivery(limit) {
        $.mobile.showPageLoadingMsg();
        var i = limit;
        //送信データを準備
        var postData = {};
        $('#form1').find(':input').each(function(){
            postData[$(this).attr('name')] = $(this).val();
        });
        postData["mode"] = "getList";
        postData["pageno"] = pageNo;
        postData["disp_number"] = i;

        $.ajax({
            type: "POST",
            url: "<!--{$smarty.const.ROOT_URLPATH}-->mypage/delivery.php",
            data: postData,
            cache: false,
            dataType: "json",
            error: function(XMLHttpRequest, textStatus, errorThrown){
                alert(textStatus);
                $.mobile.hidePageLoadingMsg();
            },
            success: function(result){
                var count = ((pageNo - 1) * i + 1); //お届け先住所の番号
                for (var j = 0; j < i; j++) {
                    if (result[j] != null) {
                        var delivery = result[j];
                        var deliveryHtml = "";
                        var maxCnt = $(".delivBox").length - 1;
                        var deliveryEl = $(".delivBox").get(maxCnt);
                        deliveryEl = $(deliveryEl).clone(true).insertAfter(deliveryEl);
                        maxCnt++;

                        //住所タイトルをセット
                        $($(".delivBox span.zip_title").get(maxCnt)).text('お届け先住所' + count);
                        //郵便番号1をセット
                        $($(".delivBox span.zip01").get(maxCnt)).text(delivery.zip01);
                        //郵便番号2をセット
                        $($(".delivBox span.zip02").get(maxCnt)).text(delivery.zip02);
                        //住所をセット
                        $($(".delivBox span.address").get(maxCnt)).text(delivery.prefname + delivery.addr01 + delivery.addr02);
                        //姓をセット
                        $($(".delivBox span.name01").get(maxCnt)).text(delivery.name01);
                        //名前をセット
                        $($(".delivBox span.name02").get(maxCnt)).text(delivery.name02);
                        //編集ボタンをセット
                        $($(".delivBox a.deliv_edit").get(maxCnt)).attr("onClick", "win02('./delivery_addr.php?other_deliv_id=" + delivery.other_deliv_id + "','deliv_disp','600','640'); return false;");
                        //削除ボタンをセット
                        $($(".delivBox a.deliv_delete").get(maxCnt)).attr("onClick", "fnModeSubmit('delete','other_deliv_id','" + delivery.other_deliv_id + "'); return false;");
                        count++;
                    }
                }
                pageNo++;

                //すべてのお届け先を表示したか判定
                if (parseInt(result.delivCount) <= $(".delivBox").length) {
                    $("#btn_more_delivery").hide();
                }
                $.mobile.hidePageLoadingMsg();
            }
        });
    }
</script>
