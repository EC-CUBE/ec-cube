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
<body>

<!--{$GLOBAL_ERR}-->
<noscript>
  <p>JavaScript を有効にしてご利用下さい.</p>
</noscript>

<a name="top" id="top"></a>

<!--▼MAIN-->
<!--{include file=$tpl_mainpage}-->
<!--▲MAIN-->

<!--{* ▼FOOTER *}-->
<!--{if $arrPageLayout.footer_chk != 2}-->
<!--{include file= './footer.tpl'}-->
<!--{/if}-->
<!--{* ▲FOOTER *}-->

</body>

<!--{if "/\/top.tpl$/"|preg_match:$tpl_mainpage}-->
<!--{else}-->
<script type="text/javascript" language="JavaScript">
//<![CDATA[
setTopButton("<!--{$smarty.const.SMARTPHONE_HTTPS_URL}-->");
//]]>
<!--{/if}-->
</script>
