<!--{*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2012 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * This program is free software; you can attentionistribute it and/or
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
<script type="text/javascript">
<!--
    // モードとキーを指定してSUBMITを行う。
    function fnModeSubmit(mode) {
        switch(mode) {
        case 'drop':
            if(!window.confirm('<!--{t string="tpl_Are you sure you want to remove this item?It can not be restored._01" escape="j"}-->')){
                return;
            }
            break;
        default:
            break;
        }
        document.form1['mode'].value = mode;
        document.form1.submit();
    }
//-->
</script>

<form name="form1" id="form1" method="post" action="?">
<input type="hidden" name="mode" value="<!--{$tpl_mode}-->" />
<input type="hidden" name="step" value="0" />

<!--{foreach key=key item=item from=$arrHidden}-->
    <input type="hidden" name="<!--{$key}-->" value="<!--{$item|h}-->" />
<!--{/foreach}-->

<div class="contents">
    <div class="message">
        <h2><!--{t string="tpl_Database initialization_01"}--></h2>
    </div>
    <div class="result-info02">
    <p class="action-message">
        <!--{if $tpl_db_version != ""}--><span class="bold"><!--{t string="tpl_Connection information_01"}--></span><br />
            <!--{$tpl_db_version}-->
        <!--{/if}-->
        <!--{t string="tpl_Database initialization will begin.<br />* Will be suspended if a table, etc. is already created._01" escape="none"}--></P>
        <!--{if $tpl_mode != 'complete'}-->
            <input type="checkbox" id="skip" name="db_skip" <!--{if $tpl_db_skip == "on"}-->checked="checked"<!--{/if}--> /> <label for="skip"><!--{t string="tpl_Do not carry out the database initialization process_01"}--></label>
        <!--{/if}-->
    </div>
    <div class="result-info02">
        <!--{if count($arrErr) > 0 || $tpl_message != ""}-->
            <!--{$tpl_message}--><br />
            <span class="attention top"><!--{$arrErr.all}--></span>
            <!--{if $arrErr.all != ""}-->
                <ul class="btn-area">
                    <li><a class="btn-normal" href="javascript:;" onclick="fnModeSubmit('drop'); return false;"><!--{t string="tpl_Delete all existing data_01"}--></a></li>
                </ul>
            <!--{/if}-->
        <!--{/if}-->
    </div>
</div>

<div class="btn-area-top"></div>
    <div class="btn-area">
        <ul>
            <li><a class="btn-action" href="javascript:;" onclick="document.form1['mode'].value='return_step2';document.form1.submit();return false;"><span class="btn-prev"><!--{t string="tpl_Go back_01"}--></span></a></li>
            <li><a class="btn-action" href="javascript:;" onclick="document.body.style.cursor='wait'; document.form1.submit(); return false;"><span class="btn-next"><!--{t string="tpl_Next_01"}--></span></a></li>
        </ul>
    </div>
    <div class="btn-area-bottom"></div>
</form>
