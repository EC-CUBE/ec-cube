<!--{printXMLDeclaration}--><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<!--{*
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2010 LOCKON CO.,LTD. All Rights Reserved.
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
<link rel="stylesheet" href="<!--{$TPL_DIR}-->css/admin_contents.css" type="text/css" media="all" />
<link rel="stylesheet" href="<!--{$TPL_DIR}-->css/admin_file_manager.css" type="text/css" media="all" />
<script type="text/javascript" src="<!--{$TPL_DIR}-->js/navi.js"></script>
<script type="text/javascript" src="<!--{$TPL_DIR}-->js/win_op.js"></script>
<script type="text/javascript" src="<!--{$TPL_DIR}-->js/site.js"></script>
<script type="text/javascript" src="<!--{$TPL_DIR}-->js/jquery-1.3.2.min.js"></script>
<script type="text/javascript" src="<!--{$TPL_DIR}-->js/admin.js"></script>
<script type="text/javascript" src="<!--{$TPL_DIR}-->js/css.js"></script>
<script type="text/javascript" src="<!--{$TPL_DIR}-->js/file_manager.js"></script>
<title><!--{$smarty.const.ADMIN_TITLE}--></title>
<script type="text/javascript">
<!--
<!--{$tpl_javascript}-->
//-->
</script>

</head>

<body onload="preLoadImg('<!--{$TPL_DIR}-->'); <!--{$tpl_onload}-->" class="<!--{if strlen($tpl_authority) >= 1}-->authority_<!--{$tpl_authority}--><!--{/if}-->">
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
  <div id="logo"><a href="<!--{$smarty.const.URL_DIR}-->admin/home.php"><img src="<!--{$TPL_DIR}-->img/admin/header/logo.jpg" width="230" height="50" alt="EC CUBE" /></a></div>
  <ul id="sites">
    <li><a href="<!--{$smarty.const.URL_HOME}-->"><span>MAIN PAGE</span></a></li>
    <li><a href="<!--{$smarty.const.SITE_URL}--><!--{$smarty.const.DIR_INDEX_URL}-->" target="_blank"><span>SITE CHECK</span></a></li>
  </ul>
  <p>
    WELCOME!&nbsp;<span><!--{* ログイン名 *}--><!--{$smarty.session.login_name|escape}--></span>&nbsp;様&nbsp;
    <a href="<!--{$smarty.const.URL_LOGOUT}-->">LOGOUT</a>
  </p>
</div>
<!--{* ▲HEADER *}-->
<!--{* ▼NAVI *}-->
<ul id="navi">
    <li id="navi-basis" class="<!--{if $tpl_mainno eq "basis"}-->on<!--{/if}-->">
        <a><span>基本情報管理</span></a>
        <!--{include file="`$smarty.const.TEMPLATE_ADMIN_DIR`basis/subnavi.tpl"}-->
    </li>
    <li id="navi-products" class="<!--{if $tpl_mainno eq "products"}-->on<!--{/if}-->">
        <a><span>商品管理</span></a>
        <!--{include file="`$smarty.const.TEMPLATE_ADMIN_DIR`products/subnavi.tpl"}-->
    </li>
    <li id="navi-customer" class="<!--{if $tpl_mainno eq "customer"}-->on<!--{/if}-->">
        <a><span>顧客管理</span></a>
        <!--{include file="`$smarty.const.TEMPLATE_ADMIN_DIR`customer/subnavi.tpl"}-->
    </li>
    <li id="navi-order" class="<!--{if $tpl_mainno eq "order"}-->on<!--{/if}-->">
        <a><span>受注管理</span></a>
        <!--{include file="`$smarty.const.TEMPLATE_ADMIN_DIR`order/subnavi.tpl"}-->
    </li>
    <li id="navi-total" class="<!--{if $tpl_mainno eq "total"}-->on<!--{/if}-->">
        <a><span>売上集計</span></a>
        <!--{include file="`$smarty.const.TEMPLATE_ADMIN_DIR`total/subnavi.tpl"}-->
    </li>
    <li id="navi-mail" class="<!--{if $tpl_mainno eq "mail"}-->on<!--{/if}-->">
        <a><span>メルマガ管理</span></a>
        <!--{include file="`$smarty.const.TEMPLATE_ADMIN_DIR`mail/subnavi.tpl"}-->
    </li>
    <li id="navi-contents" class="<!--{if $tpl_mainno eq "contents"}-->on<!--{/if}-->">
        <a><span>コンテンツ管理</span></a>
        <!--{include file="`$smarty.const.TEMPLATE_ADMIN_DIR`contents/subnavi.tpl"}-->
    </li>
    <li id="navi-design" class="<!--{if $tpl_mainno eq "design"}-->on<!--{/if}-->">
        <a><span>デザイン管理</span></a>
        <!--{include file="`$smarty.const.TEMPLATE_ADMIN_DIR`design/subnavi.tpl"}-->
    </li>
    <li id="navi-system" class="<!--{if $tpl_mainno eq "system"}-->on<!--{/if}-->">
        <a><span>システム設定</span></a>
        <!--{include file="`$smarty.const.TEMPLATE_ADMIN_DIR`system/subnavi.tpl"}-->
    </li>
    <li id="navi-ownersstore" class="<!--{if $tpl_mainno eq "ownersstore"}-->on<!--{/if}-->">
        <a><span>OWNERS STORE</span></a>
        <!--{include file="`$smarty.const.TEMPLATE_ADMIN_DIR`ownersstore/subnavi.tpl"}-->
    </li>
    <!--{if "DEBUG_LOAD_PLUGIN"|defined && $smarty.const.DEBUG_LOAD_PLUGIN}-->
        <li id="navi-plugin" class="<!--{if $tpl_mainno eq "plugin"}-->on<!--{/if}-->">
            <a><span>プラグイン</span></a>
            <!--{include file="`$smarty.const.TEMPLATE_ADMIN_DIR`plugin/subnavi.tpl"}-->
        </li>
    <!--{/if}-->
</ul>
<div style="clear: both;"></div>
<!--{* ▲NAVI *}-->

<!--{if $smarty.server.PHP_SELF != $smarty.const.URL_HOME && $tpl_subtitle}-->
<h1><!--{$tpl_subtitle}--></h1>
<!--{/if}-->

<!--{* ▼CONTENTS *}-->
<div id="contents" class="clear-block">
  <!--{include file=$tpl_mainpage}-->
</div>
<!--{* ▲CONTENTS *}-->

<!--{* ▼FOOTER *}-->
<div id="footer">
  <div id="topagetop">
    <a href="#top">GO TO PAGE TOP</a>
  </div>
  <div id="copyright">
    Copyright &copy; 2000-2010 LOCKON CO.,LTD. All Rights Reserved.
  </div>
</div>
<!--{* ▲FOOTER *}-->

</div>
</body>
</html>
