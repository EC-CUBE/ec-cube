<!--{*
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
*}-->
<script type="text/javascript"><!--
function confirmSubmit(mode, msg) {
    var form = document.form1;
    form['mode'].value = mode;
    if (window.confirm(msg)) {
        form.submit();
    } else {
        form['mode'].value = '';
    }
}
//-->
</script>
<!--★★メインコンテンツ★★-->
<table width="878" border="0" cellspacing="0" cellpadding="0" summary=" ">
<form name="form1" method="post" action="" enctype="multipart/form-data">
<input type="hidden" name="mode" value="">
<input type="hidden" name="uniqid" value="<!--{$uniqid}-->">
    <tr valign="top">
        <td background="<!--{$TPL_DIR}-->img/contents/navi_bg.gif" height="402">
            <!--▼SUB NAVI-->
            <!--{include file=$tpl_subnavi}-->
            <!--▲SUB NAVI-->
        </td>
        <td class="mainbg">
            <!--▼登録テーブルここから-->
            <table width="737" border="0" cellspacing="0" cellpadding="0" summary=" ">
                <!--メインエリア-->
                <tr>
                    <td align="center">
                        <table width="706" border="0" cellspacing="0" cellpadding="0" summary=" ">
                            <tr><td height="14"></td></tr>
                            <tr>
                                <td colspan="3"><img src="<!--{$TPL_DIR}-->img/contents/main_top.jpg" width="706" height="14" alt=""></td>
                            </tr>
                            <tr>
                                <td background="<!--{$TPL_DIR}-->img/contents/main_left.jpg"><img src="<!--{$TPL_DIR}-->img/common/_.gif" width="14" height="1" alt=""></td>
                                <td bgcolor="#cccccc">
                            <!--▼▼▼アップロード▼▼▼-->
                                <!--アップロードタイトルバーここから-->
                                <table width="678" border="0" cellspacing="0" cellpadding="0" summary=" ">
                                    <tr>
                                        <td colspan="3"><img src="<!--{$TPL_DIR}-->img/contents/contents_title_top.gif" width="678" height="7" alt=""></td>
                                    </tr>
                                    <tr>
                                        <td background="<!--{$TPL_DIR}-->img/contents/contents_title_left_bg.gif"><img src="<!--{$TPL_DIR}-->img/contents/contents_title_left.gif" width="22" height="12" alt=""></td>
                                        <td bgcolor="#636469" width="638" class="fs14n"><span class="white"><!--コンテンツタイトル-->アップロード</span></td>
                                        <td background="<!--{$TPL_DIR}-->img/contents/contents_title_right_bg.gif"><img src="<!--{$TPL_DIR}-->img/common/_.gif" width="18" height="1" alt=""></td>
                                    </tr>
                                    <tr>
                                        <td colspan="3"><img src="<!--{$TPL_DIR}-->img/contents/contents_title_bottom.gif" width="678" height="7" alt=""></td>
                                    </tr>
                                    <tr>
                                        <td colspan="3"><img src="<!--{$TPL_DIR}-->img/contents/main_bar.jpg" width="678" height="10" alt=""></td>
                                    </tr>
                                </table>
                                <!--アップロードタイトルバーここまで-->
                                <!--アップロード入力フォームここから-->
                                <table width="678" border="0" cellspacing="1" cellpadding="8" summary=" ">
                                    <tr class="fs12n">
                                        <td colspan="2" bgcolor="#ffffff" align="left">
                                            テンプレートパッケージのアップロードを行います。<br/>
                                            アップロードしたパッケージは、「テンプレート設定」で選択できるようになります。
                                        </td>
                                    </tr>
                                    <!--{assign var=key value="template_code"}-->
                                    <tr class="fs12n">
                                        <td bgcolor="#f2f1ec">テンプレートコード</td>
                                        <td bgcolor="#ffffff">
                                            <span class="red"><!--{$arrErr[$key]}--></span>
                                            <input type="text" name="<!--{$key}-->" value="<!--{$arrForm[$key].value|escape}-->" maxlength="" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" class="box54">
                                        </td>
                                    </tr>
                                    <!--{assign var=key value="template_name"}-->
                                    <tr class="fs12n">
                                        <td bgcolor="#f2f1ec">テンプレート名</td>
                                        <td bgcolor="#ffffff">
                                            <span class="red"><!--{$arrErr[$key]}--></span>
                                            <input type="text" name="<!--{$key}-->" value="<!--{$arrForm[$key].value|escape}-->" maxlength="" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" class="box54">
                                        </td>
                                    </tr>
                                    <!--{assign var=key value="template_file"}-->
                                    <tr class="fs12n">
                                        <td bgcolor="#f2f1ec">テンプレートファイル<br/>
                                            <span class="red"><span class="fs14n">※ファイル形式は.tar/.tar.gzのみ</span></span>
                                        </td>
                                        <td bgcolor="#ffffff">
                                            <span class="red"><!--{$arrErr[$key]}--></span>
                                            <input type="file" name="<!--{$key}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" class="box54" size="64" <!--{if $arrErr[$key]}-->style="background-color:<!--{$smarty.const.ERR_COLOR|escape}-->"<!--{/if}-->>
                                        </td>
                                    </tr>
                                </table>
                                <!--アップロード入力フォームここまで-->
                                <!--アップロードボタンここから-->
                                <table width="678" border="0" cellspacing="0" cellpadding="0" summary=" ">
                                    <tr>
                                        <td bgcolor="#cccccc"><img src="<!--{$TPL_DIR}-->img/common/_.gif" width="1" height="5" alt=""></td>
                                        <td><img src="<!--{$TPL_DIR}-->img/contents/tbl_top.gif" width="676" height="7" alt=""></td>
                                        <td bgcolor="#cccccc"><img src="<!--{$TPL_DIR}-->img/common/_.gif" width="1" height="5" alt=""></td>
                                    </tr>
                                    <tr>
                                        <td bgcolor="#cccccc"><img src="<!--{$TPL_DIR}-->img/common/_.gif" width="1" height="10" alt=""></td>
                                        <td bgcolor="#e9e7de" align="center">
                                        <table border="0" cellspacing="0" cellpadding="0" summary=" ">
                                            <tr>
                                                <td>
                                                    <a href="" onClick="fnModeSubmit('upload', '', '');return false;">
                                                    <img onMouseover="chgImgImageSubmit('<!--{$TPL_DIR}-->img/contents/btn_upload_on.jpg',this)" onMouseout="chgImgImageSubmit('<!--{$TPL_DIR}-->img/contents/btn_upload.jpg',this)" src="<!--{$TPL_DIR}-->img/contents/btn_upload.jpg" width="123" height="24" alt="アップロード" border="0" name="subm">
                                                    </a>
                                                </td>
                                            </tr>
                                        </table>
                                        </td>
                                        <td bgcolor="#cccccc"><img src="<!--{$TPL_DIR}-->img/common/_.gif" width="1" height="10" alt=""></td>
                                    </tr>
                                    <tr>
                                        <td colspan="3"><img src="<!--{$TPL_DIR}-->img/contents/tbl_bottom.gif" width="678" height="8" alt=""></td>
                                    </tr>
                                </table>

                                <table width="678" border="0" cellspacing="0" cellpadding="0" summary=" ">
                                    <tr><td><img src="<!--{$TPL_DIR}-->img/contents/main_bar.jpg" width="678" height="10" alt=""></td></tr>
                                </table>
                                <!--アップロードボタンここまで-->
                            <!--▲▲▲アップロード▲▲▲-->
                                </td>
                                <td background="<!--{$TPL_DIR}-->img/contents/main_right.jpg"><img src="<!--{$TPL_DIR}-->img/common/_.gif" width="14" height="1" alt=""></td>
                            </tr>
                            <tr>
                                <td colspan="3"><img src="<!--{$TPL_DIR}-->img/contents/main_bottom.jpg" width="706" height="14" alt=""></td>
                            </tr>
                            <tr><td height="30"></td></tr>
                        </table>
                    </td>
                </tr>
                <!--メインエリア-->
            </table>
            <!--▲登録テーブルここまで-->
        </td>
    </tr>
</form>
</table>
<!--★★メインコンテンツ★★-->
