<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html lang="ja">
<!--{*
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
 *}-->
<!--{assign var=default_dir value="`$smarty.const.USER_DIR``$smarty.const.USER_PACKAGE_DIR``$smarty.const.DEFAULT_TEMPLATE_NAME`/"}-->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<!--{$smarty.const.CHAR_CODE}-->">
<meta http-equiv="content-script-type" content="text/javascript">
<meta http-equiv="content-style-type" content="text/css">
<link rel="stylesheet" href="../admin/css/install.css" type="text/css" >
<script type="text/javascript" src="../<!--{$default_dir}-->js/css.js"></script>
<script type="text/javascript" src="../<!--{$default_dir}-->js/navi.js"></script>
<title>EC CUBE インストール画面</title>
</head>

<body bgcolor="#ffffff" text="#000000" link="#006699" vlink="#006699" alink="#006699" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
<noscript>
<link rel="stylesheet" href="../<!--{$default_dir}-->css/common.css" type="text/css" >
</noscript>
<div align="center">
<a name="top"></a>

<!--▼HEADER-->
<table width="912" border="0" cellspacing="0" cellpadding="0" summary=" ">
    <tr valign="top">
        <td><img src="../<!--{$default_dir}-->img/header/header_left.jpg" width="17" height="50" alt=""></td>
        <td>
        <table width="878" border="0" cellspacing="0" cellpadding="0" summary=" " background="../<!--{$default_dir}-->img/header/header_bg2.jpg">
            <tr valign="top">
                <td><img src="../<!--{$default_dir}-->img/admin/header/logo.jpg" width="230" height="50" alt="EC CUBE" border="0"></td>
                <td width="648" align="right"></td>
            </tr>
        </table>
        </td>
        <td><img src="../<!--{$default_dir}-->img/header/header_right.jpg" width="17" height="50" alt=""></td>
    </tr>
</table>
<!--▲HEADER-->

<!--▼CONTENTS-->
<table width="912" border="0" cellspacing="0" cellpadding="0" summary=" ">
    <tr valign="top">
        <td background="../<!--{$default_dir}-->img/common/left_bg.jpg"><img src="../<!--{$default_dir}-->img/common/left.jpg" width="17" height="443" alt=""></td>
        <td>
        <!--★★メインコンテンツ★★-->
        <table width="878" border="0" cellspacing="0" cellpadding="0" summary=" ">
            <tr valign="top">
                <td class="mainbg" align="center" height="450">
                <table width="562" border="0" cellspacing="0" cellpadding="0" summary=" ">
                    <tr><td height="40"></td></tr>
                    <tr>
                        <td colspan="3"><img src="../<!--{$default_dir}-->img/contents/error_top.jpg" width="562" height="14" alt=""></td>
                    </tr>
                    <tr>
                        <td background="../<!--{$default_dir}-->img/contents/main_left.jpg"><img src="../<!--{$default_dir}-->img/common/_.gif" width="14" height="1" alt=""></td>
                        <td bgcolor="#cccccc">
                        <!--検索条件設定テーブルここから-->
                        <table width="534" border="0" cellspacing="0" cellpadding="0" summary=" ">
                            <tr>
                                <td bgcolor="#ffffff" align="center">
                                <!--{include file=$tpl_mainpage}-->
                                </td>
                            </tr>
                        </table>
                        <!--検索条件設定テーブルここまで-->
                        </td>
                        <td background="../<!--{$default_dir}-->img/contents/main_right.jpg"><img src="../<!--{$default_dir}-->img/common/_.gif" width="14" height="1" alt=""></td>
                    </tr>
                    <tr>
                        <td colspan="3"><img src="../<!--{$default_dir}-->img/contents/error_bottom.jpg" width="562" height="14" alt=""></td>
                    </tr>
                    <tr><td height="40"></td></tr>
                </table>
                </td>
            </tr>
        </table>
        <!--★★メインコンテンツ★★-->
        </td>
        <td background="../<!--{$default_dir}-->img/common/right_bg.jpg"><div align="justify"><img src="../<!--{$default_dir}-->img/common/right.jpg" width="17" height="443" alt=""></div></td>
    </tr>
</table>
<!--▲CONTENTS-->

<!--▼FOOTER-->
<table width="912" border="0" cellspacing="0" cellpadding="0" summary=" ">
    <tr valign="top">
        <td background="../<!--{$default_dir}-->img/common/left_bg.jpg"><img src="../<!--{$default_dir}-->img/common/_.gif" width="17" height="1" alt=""></td>
        <td bgcolor="#636469">
        <table width="878" border="0" cellspacing="0" cellpadding="0" summary=" ">
            <tr>
                <td align="center" bgcolor="#f0f0f0">
                <table width="840" border="0" cellspacing="0" cellpadding="0" summary=" ">
                    <tr>
                        <td height="45" align="right"><a href="#top"><img src="../<!--{$default_dir}-->img/admin/common/pagetop.gif" width="105" height="17" alt="GO TO PAGE TOP" border="0"></a></td>
                    </tr>
                </table>
                </td>
            </tr>
        </table>
        <table width="878" border="0" cellspacing="0" cellpadding="10" summary=" ">
            <tr>
                <td class="fs10n"><span class="gray">&nbsp;Copyright &copy; 2000-2010 LOCKON CO.,LTD. All Rights Reserved.</span></td>
            </tr>
        </table>
        </td>
        <td background="../<!--{$default_dir}-->img/common/right_bg.jpg"><img src="../<!--{$default_dir}-->img/common/_.gif" width="17" height="1" alt=""></td>
    </tr>
    <tr>
        <td colspan="3"><img src="../<!--{$default_dir}-->img/common/fotter.jpg" width="912" height="19" alt=""></td>
    </tr>
    <tr><td height="10"></td></tr>
</table>
<!--▲FOOTER-->
</div>

</body>
</html>
