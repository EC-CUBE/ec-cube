<?php
/*
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
 */

/**
 * 携帯端末の情報を扱うクラス
 *
 * 対象とする携帯端末は $_SERVER から決定する。
 * すべてのメソッドはクラスメソッド。
 */
class SC_MobileUserAgent {
    /**
     * 携帯端末のキャリアを表す文字列を取得する。
     *
     * 文字列は docomo, ezweb, softbank のいずれか。
     *
     * @return string|false 携帯端末のキャリアを表す文字列を返す。
     *                      携帯端末ではない場合は false を返す。
     */
    function getCarrier() {
        $objAgent =& Net_UserAgent_Mobile::singleton();
        if (Net_UserAgent_Mobile::isError($objAgent)) {
            return false;
        }

        switch ($objAgent->getCarrierShortName()) {
            case 'I':
                return 'docomo';
            case 'E':
                return 'ezweb';
            case 'V':
                return 'softbank';
            case 'S':
                return 'softbank';
            default:
                return false;
        }
    }

    /**
     * 勝手サイトで利用可能な携帯端末/利用者のIDを取得する。
     *
     * 各キャリアで使用するIDの種類:
     * + docomo   ... UTN
     * + ezweb    ... EZ番号
     * + softbank ... 端末シリアル番号
     *
     * @return string|false 取得したIDを返す。取得できなかった場合は false を返す。
     */
    function getId() {
        $objAgent =& Net_UserAgent_Mobile::singleton();
        if (Net_UserAgent_Mobile::isError($objAgent)) {
            return false;
        } elseif ($objAgent->isDoCoMo() || $objAgent->isVodafone()) {
            $id = $objAgent->getSerialNumber();
        } elseif ($objAgent->isEZweb()) {
            $id = @$_SERVER['HTTP_X_UP_SUBNO'];
        }
        return isset($id) ? $id : false;
    }

    /**
     * 携帯端末の機種を表す文字列を取得する。
     * 携帯端末ではない場合はユーザーエージェントの名前を取得する。(例: 'Mozilla')
     *
     * @return string 携帯端末のモデルを表す文字列を返す。
     */
    function getModel() {
        $objAgent =& Net_UserAgent_Mobile::singleton();
        if (Net_UserAgent_Mobile::isError($objAgent)) {
            return 'Unknown';
        } elseif ($objAgent->isNonMobile()) {
            return $objAgent->getName();
        } else {
            return $objAgent->getModel();
        }
    }

    /**
     * EC-CUBE がサポートする携帯端末かどうかを判別する。
     *
     * 以下の条件に該当する場合は, false を返す.
     *
     * - 携帯端末だと判別されたが, ユーザーエージェントが解析不能な場合
     * - J-PHONE C4型(パケット非対応)
     * - EzWeb で WAP2 以外の端末
     * - DoCoMo 501i, 502i, 209i, 210i, SH821i, N821i, P821i, P651ps, R691i, F671i, SH251i, SH251iS
     *
     * @return boolean サポートしている場合は true、それ以外の場合は false を返す。
     */
    function isSupported() {
        $objAgent =& Net_UserAgent_Mobile::singleton();

        // 携帯端末だと認識されたが、User-Agent の形式が未知の場合
        if (Net_UserAgent_Mobile::isError($objAgent)) {
            GC_Utils_Ex::gfPrintLog($objAgent->toString());
            return false;
        }

        if ($objAgent->isDoCoMo()) {
            $arrUnsupportedSeries = array('501i', '502i', '209i', '210i');
            $arrUnsupportedModels = array('SH821i', 'N821i', 'P821i ', 'P651ps', 'R691i', 'F671i', 'SH251i', 'SH251iS');
            return !in_array($objAgent->getSeries(), $arrUnsupportedSeries) && !in_array($objAgent->getModel(), $arrUnsupportedModels);
        } elseif ($objAgent->isEZweb()) {
            return $objAgent->isWAP2();
        } elseif ($objAgent->isVodafone()) {
            return $objAgent->isPacketCompliant();
        } else {
            // 携帯端末ではない場合はサポートしていることにする。
            return true;
        }
    }

    /**
     * EC-CUBE がサポートする携帯キャリアかどうかを判別する。 
     * 
     * ※一部モジュールで使用。ただし、本メソッドは将来的に削除しますので新規ご利用は控えてください。
     * 
     * @return boolean サポートしている場合は true、それ以外の場合は false を返す。 
     */
    function isMobile() { 
        $objAgent =& Net_UserAgent_Mobile::singleton(); 
        if (Net_UserAgent_Mobile::isError($objAgent)) { 
            return false; 
        } else { 
            return $objAgent->isDoCoMo() || $objAgent->isEZweb() || $objAgent->isVodafone(); 
        } 
    }
}
