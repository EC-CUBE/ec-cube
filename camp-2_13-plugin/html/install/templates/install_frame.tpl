<!--{*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2013 LOCKON CO.,LTD. All Rights Reserved.
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
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ja" lang="ja">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=<!--{$smarty.const.CHAR_CODE}-->" />
        <meta http-equiv="content-script-type" content="text/javascript" />
        <meta http-equiv="content-style-type" content="text/css" />
        <link rel="stylesheet" href="css/admin_contents.css" type="text/css" media="all" />
        <script type="text/javascript" src="../js/css.js"></script>
        <script type="text/javascript" src="../js/navi.js"></script>
        <script type="text/javascript" src="../js/jquery-1.4.2.min.js"></script>
        <title>EC-CUBEインストール</title>
    </head>
    <body>
        <!--{$GLOBAL_ERR}-->
        <noscript>
            <p>JavaScript を有効にしてご利用下さい。</p>
        </noscript>
        <div id="outside">
            <div id="out-wrap">
                <div class="logo">
                    <img src="img/logo_resize.jpg" width="99" height="15" alt="EC-CUBE" />
                </div>
                <div id="out-area">
                    <div class="out-top"></div>
                    <!--{include file=$tpl_mainpage}-->
                </div>
                <!--{if strlen($install_info_url) != 0}-->
                <div id="info-area">
                            <iframe src="<!--{$install_info_url}-->" width="562" height="550" frameborder="no" scrolling="no">
                                                         こちらはEC-CUBEからのお知らせです。この部分は iframe対応ブラウザでご覧下さい。
                            </iframe>
                 </div>
                <!--{/if}-->
            </div>
        </div>
    </body>
</html>
