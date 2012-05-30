<!--{printXMLDeclaration}--><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
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

<html xmlns="http://www.w3.org/1999/xhtml" lang="ja" xml:lang="ja">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<!--{$smarty.const.CHAR_CODE}-->" />
<meta http-equiv="content-script-type" content="text/javascript" />
<meta http-equiv="content-style-type" content="text/css" />
<meta name="robots" content="noindex,nofollow" />
<link rel="stylesheet" href="<!--{$TPL_URLPATH}-->css/reset.css" type="text/css" media="all" />
<link rel="stylesheet" href="<!--{$TPL_URLPATH}-->css/admin_contents.css" type="text/css" media="all" />
<link rel="stylesheet" href="<!--{$TPL_URLPATH}-->css/admin_file_manager.css" type="text/css" media="all" />
<!--{if $tpl_mainno eq "basis" && $tpl_subno eq "index"}-->
<!--{if ($smarty.server.HTTPS != "") && ($smarty.server.HTTPS != "off")}-->
<script type="text/javascript" src="https://maps-api-ssl.google.com/maps/api/js?sensor=false"></script>
<!--{else}-->
<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>
<!--{/if}-->
<!--{/if}-->
<script type="text/javascript" src="<!--{$smarty.const.ROOT_URLPATH}-->js/navi.js"></script>
<script type="text/javascript" src="<!--{$smarty.const.ROOT_URLPATH}-->js/win_op.js"></script>
<script type="text/javascript" src="<!--{$smarty.const.ROOT_URLPATH}-->js/site.js"></script>
<script type="text/javascript" src="<!--{$smarty.const.ROOT_URLPATH}-->js/jquery-1.4.2.min.js"></script>
<script type="text/javascript" src="<!--{$TPL_URLPATH}-->js/admin.js"></script>
<script type="text/javascript" src="<!--{$smarty.const.ROOT_URLPATH}-->js/css.js"></script>
<script type="text/javascript" src="<!--{$TPL_URLPATH}-->js/file_manager.js"></script>
<title><!--{$smarty.const.ADMIN_TITLE}--></title>
<link rel="shortcut icon" href="<!--{$TPL_URLPATH}-->img/common/favicon.ico" />
<link rel="icon" type="image/vnd.microsoft.icon" href="<!--{$TPL_URLPATH}-->img/common/favicon.ico" />
<script type="text/javascript">//<![CDATA[
    <!--{$tpl_javascript}-->
    $(function(){
        <!--{$tpl_onload}-->
    });
//]]></script>
<!--{* ▼Head COLUMN*}-->
<!--{if $arrPageLayout.HeadNavi|@count > 0}-->
    <!--{foreach key=HeadNaviKey item=HeadNaviItem from=$arrPageLayout.HeadNavi}-->
        <!--{if $HeadNaviItem.php_path != ""}-->
            <!--{include_php file=$HeadNaviItem.php_path}-->
        <!--{/if}-->
    <!--{/foreach}-->
<!--{/if}-->
<!--{* ▲Head COLUMN*}-->
</head>

<body class="<!--{if strlen($tpl_authority) >= 1}-->authority_<!--{$tpl_authority}--><!--{/if}-->">
<!--{$GLOBAL_ERR}-->
<noscript>
    <p>JavaScript を有効にしてご利用下さい.</p>
</noscript>

<div id="container">
<a name="top"></a>

<!--{if $smarty.const.ADMIN_MODE == '1'}-->
<div id="admin-mode-on">ADMIN_MODE ON</div>
<!--{/if}-->

<!--{* ▼HEADER *}-->
<div id="header">
    <div id="header-contents">
        <div id="logo"><a href="<!--{$smarty.const.ADMIN_HOME_URLPATH}-->"><img src="<!--{$TPL_URLPATH}-->img/header/logo.jpg" width="172" height="25" alt="EC-CUBE" /></a></div>
        <div id="site-check">
            <p class="info"><span>ログイン&nbsp;:&nbsp;<!--{* ログイン名 *}--><!--{$smarty.session.login_name|h}--></span>&nbsp;様&nbsp;</p>
            <ul>
                <li><a href="<!--{$smarty.const.HTTP_URL}--><!--{$smarty.const.DIR_INDEX_PATH}-->" class="btn-tool-format" target="_blank"><span>SITE CHECK</span></a></li>
                <li><a href="<!--{$smarty.const.ADMIN_LOGOUT_URLPATH}-->" class="btn-tool-format">LOGOUT</a></li>
            </ul>
        </div>
    </div>
</div>
<!--{* ▲HEADER *}-->

<!--{* ▼NAVI *}-->
<div id="navi-wrap">
    <ul id="navi" class="clearfix">
        <li id="navi-basis" class="<!--{if $tpl_mainno eq "basis"}-->on<!--{/if}-->">
            <a href="<!--{$smarty.const.ROOT_URLPATH}--><!--{$smarty.const.ADMIN_DIR}-->basis/<!--{$smarty.const.DIR_INDEX_PATH}-->"><span class="level1">基本情報管理</span></a>
            <!--{include file="`$smarty.const.TEMPLATE_ADMIN_REALDIR`basis/subnavi.tpl"}-->
        </li>
        <li id="navi-products" class="<!--{if $tpl_mainno eq "products"}-->on<!--{/if}-->">
            <a href="<!--{$smarty.const.ROOT_URLPATH}--><!--{$smarty.const.ADMIN_DIR}-->products/<!--{$smarty.const.DIR_INDEX_PATH}-->"><span>商品管理</span></a>
            <!--{include file="`$smarty.const.TEMPLATE_ADMIN_REALDIR`products/subnavi.tpl"}-->
        </li>
        <li id="navi-customer" class="<!--{if $tpl_mainno eq "customer"}-->on<!--{/if}-->">
            <a href="<!--{$smarty.const.ROOT_URLPATH}--><!--{$smarty.const.ADMIN_DIR}-->customer/<!--{$smarty.const.DIR_INDEX_PATH}-->"><span>会員管理</span></a>
            <!--{include file="`$smarty.const.TEMPLATE_ADMIN_REALDIR`customer/subnavi.tpl"}-->
        </li>
        <li id="navi-order" class="<!--{if $tpl_mainno eq "order"}-->on<!--{/if}-->">
            <a href="<!--{$smarty.const.ROOT_URLPATH}--><!--{$smarty.const.ADMIN_DIR}-->order/<!--{$smarty.const.DIR_INDEX_PATH}-->"><span>受注管理</span></a>
            <!--{include file="`$smarty.const.TEMPLATE_ADMIN_REALDIR`order/subnavi.tpl"}-->
        </li>
        <li id="navi-total" class="<!--{if $tpl_mainno eq "total"}-->on<!--{/if}-->">
            <a href="<!--{$smarty.const.ROOT_URLPATH}--><!--{$smarty.const.ADMIN_DIR}-->total/<!--{$smarty.const.DIR_INDEX_PATH}-->"><span>売上集計</span></a>
            <!--{include file="`$smarty.const.TEMPLATE_ADMIN_REALDIR`total/subnavi.tpl"}-->
        </li>
        <li id="navi-mail" class="<!--{if $tpl_mainno eq "mail"}-->on<!--{/if}-->">
            <a href="<!--{$smarty.const.ROOT_URLPATH}--><!--{$smarty.const.ADMIN_DIR}-->mail/<!--{$smarty.const.DIR_INDEX_PATH}-->"><span>メルマガ管理</span></a>
            <!--{include file="`$smarty.const.TEMPLATE_ADMIN_REALDIR`mail/subnavi.tpl"}-->
        </li>
        <li id="navi-contents" class="<!--{if $tpl_mainno eq "contents"}-->on<!--{/if}-->">
            <a href="<!--{$smarty.const.ROOT_URLPATH}--><!--{$smarty.const.ADMIN_DIR}-->contents/<!--{$smarty.const.DIR_INDEX_PATH}-->"><span>コンテンツ管理</span></a>
            <!--{include file="`$smarty.const.TEMPLATE_ADMIN_REALDIR`contents/subnavi.tpl"}-->
        </li>
        <li id="navi-design" class="<!--{if $tpl_mainno eq "design"}-->on<!--{/if}-->">
            <a href="<!--{$smarty.const.ROOT_URLPATH}--><!--{$smarty.const.ADMIN_DIR}-->design/<!--{$smarty.const.DIR_INDEX_PATH}-->"><span>デザイン管理</span></a>
            <!--{include file="`$smarty.const.TEMPLATE_ADMIN_REALDIR`design/subnavi.tpl"}-->
        </li>
        <li id="navi-system" class="<!--{if $tpl_mainno eq "system"}-->on<!--{/if}-->">
            <a href="<!--{$smarty.const.ROOT_URLPATH}--><!--{$smarty.const.ADMIN_DIR}-->system/<!--{$smarty.const.DIR_INDEX_PATH}-->"><span>システム設定</span></a>
            <!--{include file="`$smarty.const.TEMPLATE_ADMIN_REALDIR`system/subnavi.tpl"}-->
        </li>
        <li id="navi-ownersstore" class="<!--{if $tpl_mainno eq "ownersstore"}-->on<!--{/if}-->">
            <a href="<!--{$smarty.const.ROOT_URLPATH}--><!--{$smarty.const.ADMIN_DIR}-->ownersstore/<!--{$smarty.const.DIR_INDEX_PATH}-->"><span>オーナーズストア</span></a>
            <!--{include file="`$smarty.const.TEMPLATE_ADMIN_REALDIR`ownersstore/subnavi.tpl"}-->
        </li>
    </ul>
</div>
<!--{* ▲NAVI *}-->

<!--{if $tpl_subtitle}-->
<h1><span class="title"><!--{$tpl_maintitle|h}--><!--{if strlen($tpl_maintitle) >= 1 && strlen($tpl_subtitle) >= 1}-->＞<!--{/if}--><!--{$tpl_subtitle|h}--></span></h1>
<!--{/if}-->

<div id="contents" class="clearfix">
    <!--{include file=$tpl_mainpage}-->
</div>

<!--{* ▼FOOTER *}-->
<div id="footer">
    <div id="footer-contents">
        <div id="copyright">Copyright &copy; 2000-<!--{$smarty.now|date_format:"%Y"}--> LOCKON CO.,LTD. All Rights Reserved.</div>
        <div id="topagetop">
            <ul class="sites">
                <li><a href="#top" class="btn-tool-format">PAGE TOP</a></li>
            </ul>
        </div>
    </div>
</div>
<!--{* ▲FOOTER *}-->

</div>
</body>
</html>
