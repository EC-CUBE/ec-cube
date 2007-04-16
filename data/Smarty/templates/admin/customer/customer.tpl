<!--{*
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
*}-->
<script type="text/javascript">
<!--
    
    function fnReturn() {
        document.form_search.action = './index.php';
        document.form_search.submit();
        return false;
    }
    
//-->
</script>

<!--★★メインコンテンツ★★-->
<table width="878" border="0" cellspacing="0" cellpadding="0" summary=" ">
    <tr valign="top">
        <td background="<!--{$smarty.const.URL_DIR}-->img/contents/navi_bg.gif" height="402">
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
                        <form name="form1" id="form1" method="post" action="<!--{$smarty.server.PHP_SELF|escape}-->">
                        <input type="hidden" name="mode" value="confirm">
                            <tr><td height="14"></td></tr>
                            <tr>
                                <td colspan="3"><img src="<!--{$smarty.const.URL_DIR}-->img/contents/main_top.jpg" width="706" height="14" alt=""></td>
                            </tr>
                            <tr>
                                <td background="<!--{$smarty.const.URL_DIR}-->img/contents/main_left.jpg"><img src="<!--{$smarty.const.URL_DIR}-->img/common/_.gif" width="14" height="1" alt=""></td>
                                <td bgcolor="#cccccc">
                                <table width="678" border="0" cellspacing="0" cellpadding="0" summary=" ">
                                    <tr>
                                        <td colspan="3"><img src="<!--{$smarty.const.URL_DIR}-->img/contents/contents_title_top.gif" width="678" height="7" alt=""></td>
                                    </tr>
                                    <tr>
                                        <td background="<!--{$smarty.const.URL_DIR}-->img/contents/contents_title_left_bg.gif"><img src="<!--{$smarty.const.URL_DIR}-->img/contents/contents_title_left.gif" width="22" height="12" alt=""></td>
                                        <td bgcolor="#636469" width="638" class="fs14n"><span class="white"><!--コンテンツタイトル-->顧客登録</span></td>
                                        <td background="<!--{$smarty.const.URL_DIR}-->img/contents/contents_title_right_bg.gif"><img src="<!--{$smarty.const.URL_DIR}-->img/common/_.gif" width="18" height="1" alt=""></td>
                                    </tr>
                                    <tr>
                                        <td colspan="3"><img src="<!--{$smarty.const.URL_DIR}-->img/contents/contents_title_bottom.gif" width="678" height="7" alt=""></td>
                                    </tr>
                                    <tr>
                                        <td colspan="3"><img src="<!--{$smarty.const.URL_DIR}-->img/contents/main_bar.jpg" width="678" height="10" alt=""></td>
                                    </tr>
                                </table>

                                <table width="678" border="0" cellspacing="1" cellpadding="8" summary=" ">

                                    <tr class="fs12n">
                                        <td bgcolor="#f2f1ec" width="190">会員状態<span class="red"> *</span></td>
                                        <td bgcolor="#ffffff" width="527">
                                            <span class="red12"><!--{$arrErr.status}--></span>
                                            <input type="radio" name="status"value=1 id="no_mem" <!--{if $status == 1}--> checked="checked" <!--{/if}-->><label for="no_mem">仮会員</label>
                                            <input type="radio" name="status"value=2 id="mem"<!--{if $status == 2}--> checked="checked" <!--{/if}-->><label for="mem">本会員</label>
                                        </td>
                                    </tr>
                                    <tr class="fs12n">
                                        <td bgcolor="#f2f1ec" width="190">お名前<span class="red"> *</span></td>
                                        <td bgcolor="#ffffff" width="527"><span class="red12"><!--{$arrErr.name01}--><!--{$arrErr.name02}--></span>姓&nbsp;<input type="text" name="name01" value="<!--{$name01|escape}-->" maxlength="<!--{$smarty.const.STEXT_LEN}-->" size="30" class="box30" <!--{if $arrErr.name01 != ""}--><!--{sfSetErrorStyle}--><!--{/if}--> />　名&nbsp;<input type="text" name="name02" value="<!--{$name02|escape}-->" maxlength="<!--{$smarty.const.STEXT_LEN}-->" size="30" class="box30" <!--{if $arrErr.name02 != ""}--><!--{sfSetErrorStyle}--><!--{/if}--> /></td>
                                    </tr>
                                    <tr class="fs12n">
                                        <td bgcolor="#f2f1ec" width="190">フリガナ<span class="red"> *</span></td>
                                        <td bgcolor="#ffffff" width="527"><span class="red12"><!--{$arrErr.kana01}--><!--{$arrErr.kana02}--></span>セイ&nbsp;<input type="text" name="kana01" value="<!--{$kana01|escape}-->" maxlength="<!--{$smarty.const.STEXT_LEN}-->" size="30" class="box30"  <!--{if $arrErr.kana01 != ""}--><!--{sfSetErrorStyle}--><!--{/if}--> />　メイ&nbsp;<input type="text" name="kana02" value="<!--{$kana02|escape}-->" maxlength="<!--{$smarty.const.STEXT_LEN}-->" size="30" class="box30"  <!--{if $arrErr.kana02 != ""}--><!--{sfSetErrorStyle}--><!--{/if}--> /></td>
                                    </tr>
                                    <tr class="fs12n">
                                        <td bgcolor="#f2f1ec" width="190">郵便番号<span class="red"> *</span></td>
                                        <td bgcolor="#ffffff" width="527"><span class="red12"><!--{$arrErr.zip01}--><!--{$arrErr.zip02}--></span>〒 <input type="text" name="zip01" value="<!--{$zip01|escape}-->" maxlength="<!--{$smarty.const.ZIP01_LEN}-->" size="6" class="box6" maxlength="3"  <!--{if $arrErr.zip01 != ""}--><!--{sfSetErrorStyle}--><!--{/if}--> /> - <input type="text" name="zip02" value="<!--{$zip02|escape}-->" maxlength="<!--{$smarty.const.ZIP02_LEN}-->" size="6" class="box6" maxlength="4"  <!--{if $arrErr.zip02 != ""}--><!--{sfSetErrorStyle}--><!--{/if}--> />
                                            <input type="button" name="address_input" value="住所入力" onclick="fnCallAddress('<!--{$smarty.const.URL_INPUT_ZIP}-->', 'zip01', 'zip02', 'pref', 'addr01');" />
                                        </td>
                                    </tr>
                                    <tr>
                                        <td bgcolor="#f2f1ec" width="190" class="fs12">ご住所<span class="red"> *</span></td>
                                        <td bgcolor="#ffffff" width="527">
                                        <table width="527" border="0" cellspacing="0" cellpadding="0" summary=" ">
                                            <tr>
                                                <td>
                                                    <span class="red12"><!--{$arrErr.pref}--><!--{$arrErr.addr01}--><!--{$arrErr.addr02}--></span>
                                                    <select name="pref"  <!--{if $arrErr.pref != ""}--><!--{sfSetErrorStyle}--><!--{/if}-->>
                                                    <option value="" selected="selected">都道府県を選択</option>
                                                    <!--{html_options options=$arrPref selected=$pref}-->
                                                    </select>
                                                </td>
                                            </tr>
                                            <tr><td height="5"></td></tr>
                                            <tr class="fs10n">
                                                <td><input type="text" name="addr01" value="<!--{$addr01|escape}-->" size="60" class="box60" <!--{if $arrErr.addr01 != ""}--><!--{sfSetErrorStyle}--><!--{/if}--> /><br />
                                                <!--{$smarty.const.SAMPLE_ADDRESS1}--></td>
                                            </tr>
                                            <tr><td height="5"></td></tr>
                                            <tr class="fs10n">
                                                <td><input type="text" name="addr02" value="<!--{$addr02|escape}-->" size="60" class="box60" <!--{if $arrErr.addr02 != ""}--><!--{sfSetErrorStyle}--><!--{/if}--> /><br />
                                                <!--{$smarty.const.SAMPLE_ADDRESS2}--></td>
                                            </tr>
                                        </table>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td bgcolor="#f2f1ec" width="190" class="fs12n">メールアドレス<span class="red"> *</span></td>
                                        <td bgcolor="#ffffff" width="527" class="fs10n"><span class="red12"><!--{$arrErr.email}--></span><input type="text" name="email" value="<!--{$email|escape}-->" size="60" class="box60" <!--{if $arrErr.email != ""}--><!--{sfSetErrorStyle}--><!--{/if}--> /></td>
                                    </tr>
                                    <tr>
                                        <td bgcolor="#f2f1ec" width="190" class="fs12n">メールアドレス(モバイル)</td>
                                        <td bgcolor="#ffffff" width="527" class="fs10n"><span class="red12"><!--{$arrErr.email_mobile}--></span><input type="text" name="email_mobile" value="<!--{$email_mobile|escape}-->" size="60" class="box60" <!--{if $arrErr.email_mobile != ""}--><!--{sfSetErrorStyle}--><!--{/if}--> /></td>
                                    </tr>
                                    <tr class="fs12n">
                                        <td bgcolor="#f2f1ec" width="190">電話番号<span class="red"> *</span></td>
                                        <td bgcolor="#ffffff" width="527"><span class="red12"><!--{$arrErr.tel01}--><!--{$arrErr.tel02}--><!--{$arrErr.tel03}--></span><input type="text" name="tel01" value="<!--{$tel01|escape}-->" maxlength="<!--{$smarty.const.TEL_ITEM_LEN}-->" size="6" class="box6" <!--{if $arrErr.tel01 != ""}--><!--{sfSetErrorStyle}--><!--{/if}--> /> - <input type="text" name="tel02" value="<!--{$tel02|escape}-->" maxlength="<!--{$smarty.const.TEL_ITEM_LEN}-->" size="6" class="box6" <!--{if $arrErr.tel01 != "" || $arrErr.tel02 != ""}--><!--{sfSetErrorStyle}--><!--{/if}--> /> - <input type="text" name="tel03" value="<!--{$tel03|escape}-->" maxlength="<!--{$smarty.const.TEL_ITEM_LEN}-->" size="6" class="box6" <!--{if $arrErr.tel01 != "" || $arrErr.tel03 != ""}--><!--{sfSetErrorStyle}--><!--{/if}--> /></td>
                                    </tr>
                                    <tr class="fs12n">
                                        <td bgcolor="#f2f1ec" width="190">FAX</td>
                                        <td bgcolor="#ffffff" width="527"><span class="red12"><!--{$arrErr.fax01}--><!--{$arrErr.fax02}--><!--{$arrErr.fax03}--></span><input type="text" name="fax01" value="<!--{$fax01|escape}-->" maxlength="<!--{$smarty.const.TEL_ITEM_LEN}-->" size="6" class="box6" <!--{if $arrErr.fax01 != ""}--><!--{sfSetErrorStyle}--><!--{/if}--> /> - <input type="text" name="fax02" value="<!--{$fax02|escape}-->" maxlength="<!--{$smarty.const.TEL_ITEM_LEN}-->" size="6" class="box6" <!--{if $arrErr.fax01 != "" || $arrErr.fax02 != ""}--><!--{sfSetErrorStyle}--><!--{/if}--> /> - <input type="text" name="fax03" value="<!--{$fax03|escape}-->" maxlength="<!--{$smarty.const.TEL_ITEM_LEN}-->" size="6" class="box6" <!--{if $arrErr.fax01 != "" || $arrErr.fax03 != ""}--><!--{sfSetErrorStyle}--><!--{/if}--> /></td>
                                    </tr>
                                    <tr class="fs12n">
                                        <td bgcolor="#f2f1ec" width="190">ご性別<span class="red"> *</span></td>
                                        <td bgcolor="#ffffff" width="527"><span class="red12"><!--{$arrErr.sex}--></span><input type="radio" name="sex" value="1" <!--{if $arrErr.sex != ""}--><!--{sfSetErrorStyle}--><!--{/if}--> <!--{if $sex eq 1}-->checked<!--{/if}--> />男性 <input type="radio" name="sex" value="2" <!--{if $arrErr.sex != ""}--><!--{sfSetErrorStyle}--><!--{/if}--> <!--{if $sex eq 2}-->checked<!--{/if}--> />女性</td>
                                    </tr>
                                    <tr class="fs12n">
                                        <td bgcolor="#f2f1ec" width="190">ご職業</td>
                                        <td bgcolor="#ffffff" width="527"><span class="red12"><!--{$arrErr.job}--></span>
                                            <select name="job" <!--{if $arrErr.job != ""}--><!--{sfSetErrorStyle}--><!--{/if}--> >
                                            <option value="" selected="selected">選択してください</option>
                                            <!--{html_options options=$arrJob selected=$job}-->
                                            </select>
                                        </td>
                                    </tr>
                                    <tr class="fs12n">
                                        <td bgcolor="#f2f1ec" width="190">生年月日</td>
                                        <td bgcolor="#ffffff" width="527"><span class="red12"><!--{$arrErr.year}--></span>
                                            <select name="year" <!--{if $arrErr.year != ""}--><!--{sfSetErrorStyle}--><!--{/if}--> >
                                                <option value="" selected="selected">------</option>
                                                <!--{html_options options=$arrYear selected=$year}-->
                                            </select>年
                                            <select name="month" <!--{if $arrErr.year != ""}--><!--{sfSetErrorStyle}--><!--{/if}--> >
                                                <option value="" selected="selected">----</option>
                                                <!--{html_options options=$arrMonth selected=$month}-->
                                            </select>月
                                            <select name="day" <!--{if $arrErr.year != ""}--><!--{sfSetErrorStyle}--><!--{/if}--> >
                                                <option value="" selected="selected">----</option>
                                                <!--{html_options options=$arrDay selected=$day"}-->        
                                            </select>日
                                        </td>
                                    </tr>
                                    <tr class="fs12n">
                                        <td bgcolor="#f2f1ec" width="190">パスワード<span class="red"> *</span></td>
                                        <td class="red10" bgcolor="#ffffff" width="527"><span class="red12"><!--{$arrErr.password}--></span><input type="password" name="password" value="<!--{$password|escape}-->" size="30" class="box30" <!--{if $arrErr.password != ""}--><!--{sfSetErrorStyle}--><!--{/if}--> />　半角英数小文字4〜10文字（記号不可）</td>
                                    </tr>
                                    <tr>
                                        <td bgcolor="#f2f1ec" width="190" class="fs12">パスワードを忘れたときのヒント<span class="red"> *</span></td>
                                        <td bgcolor="#ffffff" width="527">
                                        <table width="527" border="0" cellspacing="0" cellpadding="0" summary=" ">
                                            <tr class="fs12n">
                                                <td><span class="red12"><!--{$arrErr.reminder}--><!--{$arrErr.reminder_answer}--></span>質問： <select name="reminder" <!--{if $arrErr.reminder != ""}--><!--{sfSetErrorStyle}--><!--{/if}--> >
                                                <option value="" selected="selected">選択してください</option>
                                                <!--{html_options options=$arrReminder selected=$reminder}-->
                                                </select>
                                                </td>
                                            </tr>
                                            <tr><td height="5"></td></tr>
                                            <tr class="fs12n">
                                                <td>答え： <input type="text" name="reminder_answer" value="<!--{$reminder_answer|escape}-->" size="30" class="box30" <!--{if $arrErr.reminder_answer != ""}--><!--{sfSetErrorStyle}--><!--{/if}--> /></td>
                                            </tr>
                                        </table>
                                        </td>
                                    </tr>
                                    <tr class="fs12n">
                                        <td bgcolor="#f2f1ec" width="190">メールマガジン<span class="red"> *</span></td>
                                        <td bgcolor="#ffffff" width="527"><span class="red12"><!--{$arrErr.mailmaga_flg}--></span>
                                            <input type="radio" name="mailmaga_flg" value="1" <!--{if $arrErr.mailmaga_flg != ""}--><!--{sfSetErrorStyle}--><!--{/if}--> <!--{if $mailmaga_flg eq 1 or $mailmaga_flg eq 4}-->checked<!--{/if}--> />HTML　
                                            <input type="radio" name="mailmaga_flg" value="2" <!--{if $arrErr.mailmaga_flg != ""}--><!--{sfSetErrorStyle}--><!--{/if}--> <!--{if $mailmaga_flg eq 2 or $mailmaga_flg eq 5}-->checked<!--{/if}--> />テキスト　
                                            <input type="radio" name="mailmaga_flg" value="3" <!--{if $arrErr.mailmaga_flg != ""}--><!--{sfSetErrorStyle}--><!--{/if}--> <!--{if $mailmaga_flg eq "" or $mailmaga_flg eq 3 or $mailmaga_flg eq 6}-->checked<!--{/if}--> />希望しない</td>
                                    </tr>
                                    <tr>
                                        <td bgcolor="#f2f1ec" width="960" class="fs12n">SHOP用メモ</td>
                                        <td bgcolor="#ffffff" width="527" class="fs10n"><span class="red12"><!--{$arrErr.note}--></span><textarea name="note" maxlength="<!--{$smarty.const.LTEXT_LEN}-->" <!--{if $arrErr.note != ""}--><!--{sfSetErrorStyle}--><!--{/if}--> cols="60" rows="8" class="area60"><!--{$note|escape}--></textarea></td>
                                    </tr>
                                    <tr class="fs12n">
                                        <td bgcolor="#f2f1ec" width="190">所持ポイント</td>
                                        <td bgcolor="#ffffff" width="527"><span class="red12"><!--{$arrErr.point}--></span><input type="text" name="point" value="<!--{$point|escape}-->" maxlength="<!--{$smarty.const.TEL_LEN}-->" <!--{if $arrErr.point != ""}--><!--{sfSetErrorStyle}--><!--{/if}--> size="6" class="box6" <!--{if $arrErr.point != ""}--><!--{sfSetErrorStyle}--><!--{/if}--> /> pt</td>
                                    </tr>
                                    <tr class="fs12n">
                                        <td bgcolor="#f2f1ec" width="190">登録内容確認メール</td>
                                        <td bgcolor="#ffffff" width="527"><input type="checkbox" name="mail_send" value="1" <!--{if $mail_send eq 1}--> checked <!--{/if}--> >顧客登録完了メールを送信する</td>
                                </table>

                                <table width="678" border="0" cellspacing="0" cellpadding="0" summary=" ">
                                    <tr>
                                        <td bgcolor="#cccccc"><img src="<!--{$smarty.const.URL_DIR}-->img/common/_.gif" width="1" height="5" alt=""></td>
                                        <td><img src="<!--{$smarty.const.URL_DIR}-->img/contents/tbl_top.gif" width="676" height="7" alt=""></td>
                                        <td bgcolor="#cccccc"><img src="<!--{$smarty.const.URL_DIR}-->img/common/_.gif" width="1" height="5" alt=""></td>
                                    </tr>
                                    <tr>
                                        <td bgcolor="#cccccc"><img src="<!--{$smarty.const.URL_DIR}-->img/common/_.gif" width="1" height="10" alt=""></td>
                                        <td bgcolor="#e9e7de" align="center">
                                        <table border="0" cellspacing="0" cellpadding="0" summary=" ">
                                            <tr>
                                                <td>
                                                    <input type="image" onMouseover="chgImgImageSubmit('<!--{$smarty.const.URL_DIR}-->img/contents/btn_confirm_on.jpg',this)" onMouseout="chgImgImageSubmit('<!--{$smarty.const.URL_DIR}-->img/contents/btn_confirm.jpg',this)" src="<!--{$smarty.const.URL_DIR}-->img/contents/btn_confirm.jpg" width="123" height="24" alt="確認ページへ" border="0" name="confirm" id="confirm" />
                                                </td>
                                            </tr>
                                        </table>
                                        </td>
                                        <td bgcolor="#cccccc"><img src="<!--{$smarty.const.URL_DIR}-->img/common/_.gif" width="1" height="10" alt=""></td>
                                    </tr>
                                    <tr>
                                        <td colspan="3"><img src="<!--{$smarty.const.URL_DIR}-->img/contents/tbl_bottom.gif" width="678" height="8" alt=""></td>
                                    </tr>
                                </table>

                                </td>
                                <td background="<!--{$smarty.const.URL_DIR}-->img/contents/main_right.jpg"><img src="<!--{$smarty.const.URL_DIR}-->img/common/_.gif" width="14" height="1" alt=""></td>
                            </tr>
                            <tr>
                                <td colspan="3"><img src="<!--{$smarty.const.URL_DIR}-->img/contents/main_bottom.jpg" width="706" height="14" alt=""></td>
                            </tr>
                            <tr><td height="30"></td></tr>
                            </from>
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
