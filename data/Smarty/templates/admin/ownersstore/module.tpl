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

<!--{*
オーナーズストア通信処理用のjsとcssを読み込む
onclickなどで
OwnersStore.download();やOwnersStore.products_list();を呼び出すことで
配信サーバーとの通信処理を行う

購入商品一覧はid="ownersstore_products_list"に出力される

*}-->

<link rel="stylesheet" href="<!--{$TPL_URLPATH}-->css/thickbox.css" type="text/css" media="screen" />
<script type="text/javascript" src="<!--{$TPL_URLPATH}-->js/ownersstore.js.php"></script>

<form name="form1" id="form1" method="post" action="?">
<input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
<div id="ownersstore" class="contents-main">

    <!--購入商品一覧ここから-->
    <div id="ownersstore_products_list"></div>
    <!--購入商品一覧ここまで-->

    <div class="btn">
        <a class="btn-normal" href="javascript:;" onclick="OwnersStore.products_list();return false;"><span>モジュール一覧を取得する</span></a>
    </div>

    <div id="ownersstore_index">
        <iframe src="<!--{$smarty.const.OSTORE_SSLURL}-->store_info/" style="width:950px;height:600px;" scrolling="no" marginwidth="0" marginheight="0" frameborder="0"></iframe>
    </div>
</div>
</form>
