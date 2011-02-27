<div class="bloc_outer">
    <div id="header_login_area" class="clearfix">
        <form name="login_header_form" id="login_header_form" method="post" action="<!--{$smarty.const.HTTPS_URL}-->frontparts/login_check.php" onsubmit="return fnCheckLogin('login_header_form')">
            <input type="hidden" name="mode" value="login" />
            <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
            <input type="hidden" name="url" value="<!--{$smarty.server.PHP_SELF|h}-->" />
            <div class="bloc_body clearfix">
                <!--{if $tpl_login}-->
                    <!--{if !$tpl_disable_logout}-->
                    <p class="btn">
                      ようこそ
                      <span class="user_name"><!--{$tpl_name1|h}--> <!--{$tpl_name2|h}--> 様</span> /
                    <!--{if $smarty.const.USE_POINT !== false}-->
                        所持ポイント: <span class="point"> <!--{$tpl_user_point|number_format|default:0}--> pt</span>
                    <!--{/if}-->&nbsp;&nbsp;
                       <input type="image" onmouseover="chgImgImageSubmit('<!--{$TPL_URLPATH}-->img/common/btn_header_logout_on.jpg',this)" onmouseout="chgImgImageSubmit('<!--{$TPL_URLPATH}-->img/common/btn_header_logout.jpg',this)" src="<!--{$TPL_URLPATH}-->img/common/btn_header_logout.jpg" onclick="fnFormModeSubmit('login_header_form', 'logout', '', ''); return false;" alt="ログアウト" />
                     </p>
                    <!--{/if}-->
                <!--{else}-->
                <ul class="formlist clearfix">
                    <li class="mail">
                        <input type="text" class="box150" name="login_email" value="<!--{$tpl_login_email|h}-->" style="ime-mode: disabled;" />
                    </li>
                    <li class="login_memory">
                        <input type="checkbox" name="login_memory" id="login_memory" value="1" <!--{$tpl_login_memory|sfGetChecked:1}--> /><label for="login_memory"><span>記憶</span></label>
                    </li>
                    <li class="password"><input type="password" class="box100" name="login_pass" /></li>
                    <li class="btn">
                        <input type="image" onmouseover="chgImgImageSubmit('<!--{$TPL_URLPATH}-->img/common/btn_header_login_on.jpg',this)" onmouseout="chgImgImageSubmit('<!--{$TPL_URLPATH}-->img/common/btn_header_login.jpg',this)" src="<!--{$TPL_URLPATH}-->img/common/btn_header_login.jpg" />
                    </li>
                    <li class="forgot">
                        <a href="<!--{$smarty.const.HTTPS_URL|sfTrimURL}-->/forgot/<!--{$smarty.const.DIR_INDEX_PATH}-->" onclick="win01('<!--{$smarty.const.HTTPS_URL|sfTrimURL}-->/forgot/<!--{$smarty.const.DIR_INDEX_PATH}-->','forget','600','400'); return false;" target="_blank">パスワードを忘れた方</a>
                    </li>
                </ull>

              <!--{/if}-->
            </div>
        </form>
    </div>
</div>