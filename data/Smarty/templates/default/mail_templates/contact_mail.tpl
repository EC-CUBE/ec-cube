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
<!--{$tpl_header}-->
　※本メールは自動配信メールです。
　等幅フォント(MSゴシック12ポイント、Osaka-等幅など)で
　最適にご覧になれます。

┏━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━┓
　※本メールは、
　<!--{$tpl_shopname}-->より、お問い合わせをされた方に
　お送りしています。
　もしお心当たりが無い場合は、このままこのメールを破棄して
　ください。
　またその旨、<!--{$tpl_infoemail}-->まで
　ご連絡いただければ幸いです。
┗━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━┛

<!--{$arrForm.name01}-->様

以下のお問い合わせを受付致しました。
確認次第ご連絡いたしますので、少々お待ちください。

■お名前　：<!--{$arrForm.name01}--> <!--{$arrForm.name02}--> (<!--{$arrForm.kana01}--> <!--{$arrForm.kana02}-->) 様
■郵便番号：<!--{if $arrForm.zip01 && $arrForm.zip02}-->〒<!--{$arrForm.zip01}-->-<!--{$arrForm.zip02}--><!--{/if}-->

■住所　　：<!--{$arrPref[$arrForm.pref]}--><!--{$arrForm.addr01}--><!--{$arrForm.addr02}-->
■電話番号：<!--{$arrForm.tel01}-->-<!--{$arrForm.tel02}-->-<!--{$arrForm.tel03}-->
■お問い合わせの内容
<!--{$arrForm.contents}-->
<!--{$tpl_footer}-->
