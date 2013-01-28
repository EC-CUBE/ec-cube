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

<!--{foreach key=key item=item from=$arrHidden}-->
    <input type="hidden" name="<!--{$key}-->" value="<!--{$item|h}-->" />
<!--{/foreach}-->

<div class="contents">
    <h2><!--{t string="tpl_Check results_01"}--></h2>
    <div class="result-info01">
        <textarea name="disp_area" cols="50" rows="20" class="box470"><!--{$mess}--></textarea>
    </div>
    <div class="result-info02">
        <!--{if $hasErr}-->
            <p class="action-message"><!--{t string="tpl_When [Next] is clicked, a check will be executed once again._01"}--></p>
            <div><input type="checkbox" name="mode_overwrite" value="step0" id="mode_overwrite" /> <label for="mode_overwrite"><!--{t string="tpl_Ignore problems and proceed (for advanced users)_01"}--></label></div>
            <div class="red"><!--{t string="tpl_* If you proceed without resolving the problem, this may lead to problems._01"}--></div>
        <!--{else}-->
            <!--{t string="tpl_Copying of necessary files will begin._01"}-->
        <!--{/if}-->
    </div>
</div>
<div class="btn-area-top"></div>
<div class="btn-area">
    <ul>
        <li><a class="btn-action" href="javascript:;" onclick="document.form1['mode'].value='return_welcome';document.form1.submit(); return false;"><span class="btn-prev"><!--{t string="tpl_Go back_01"}--></span></a></li>
        <li><a class="btn-action" href="javascript:;" onclick="document.form1.submit(); return false;"><span class="btn-next"><!--{t string="tpl_Next_01"}--></span></a></li>
    </ul>
</div>
<div class="btn-area-bottom"></div>
</form>
