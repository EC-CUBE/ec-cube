<!--{*
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
 *}-->
<form name="form1" id="form1" method="post" action="?">
<input type="hidden" name="mode" value="<!--{$tpl_mode}-->" />
<input type="hidden" name="step" value="0" />
<input type="hidden" name="db_skip" value="<!--{$tpl_db_skip}-->" />
<input type="hidden" name="senddata_site_url" value="<!--{$tpl_site_url}-->" />
<input type="hidden" name="senddata_shop_name" value="<!--{$tpl_shop_name}-->" />
<input type="hidden" name="senddata_cube_ver" value="<!--{$tpl_cube_ver}-->" />
<input type="hidden" name="senddata_php_ver" value="<!--{$tpl_php_ver}-->" />
<input type="hidden" name="senddata_db_ver" value="<!--{$tpl_db_ver}-->" />
<input type="hidden" name="senddata_os_type" value="<!--{""|php_uname|h}--> <!--{$smarty.server.SERVER_SOFTWARE|h}-->" />
<!--{foreach key=key item=item from=$arrHidden}-->
    <input type="hidden" name="<!--{$key}-->" value="<!--{$item|h}-->" />
<!--{/foreach}-->


<div class="contents">
    <div class="message">
        <h2><!--{t string="tpl_Site information_01"}--></h2>
         <p><!--{t string="tpl_Please provide the information below for the purpose of debugging and improving the EC-CUBE system._01"}--></p>
    </div>
    <div class="result-info01">
        <ul class="site-info-list">
            <li><span class="bold"><!--{t string="tpl_Site URL: _01"}--></span><!--{$tpl_site_url}--></li>
            <li><span class="bold"><!--{t string="tpl_Store name: _01"}--></span><!--{$tpl_shop_name}--></li>
            <li><span class="bold"><!--{t string="tpl_EC-CUBE version: _01"}--></span><!--{$tpl_cube_ver}--></li>
            <li><span class="bold"><!--{t string="tpl_PHP information: _01"}--></span><!--{$tpl_php_ver}--></li>
            <li><span class="bold"><!--{t string="tpl_DB information: _01"}--></span><!--{$tpl_db_ver}--></li>
            <li><span class="bold"><!--{t string="tpl_OS information: _01"}--></span><!--{""|php_uname|h}--> <!--{$smarty.server.SERVER_SOFTWARE|h}--></li>
        </ul>
    </div>
    <div class="result-info02">
        <input type="radio" id="ok" name="send_info" checked value="true" /><label for="ok"><!--{t string="tpl_Yes (recommended)_01"}--></label>&nbsp;
        <input type="radio" id="ng" name="send_info" value="false" /><label for="ng"><!--{t string="tpl_No_02"}--></label>
    </div>
    <div class="btn-area-top"></div>
    <div class="btn-area">
        <ul>
            <li><a class="btn-action" href="#" onclick="document.form1['mode'].value='return_step3';document.form1.submit();return false;">
                <span class="btn-prev"><!--{t string="tpl_Go back_01"}--></span></a></li>
            <li><a class="btn-action" href="javascript:;" onclick="document.form1.submit(); return false;">
                <span class="btn-next"><!--{t string="tpl_Next_01"}--></span></a></li>
        </ul>
    <div class="btn-area-bottom"></div>
</div>
</form>
