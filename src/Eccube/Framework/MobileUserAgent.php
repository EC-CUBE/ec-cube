<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2014 LOCKON CO.,LTD. All Rights Reserved.
 * http://www.lockon.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eccube\Framework;

use Eccube\Application;
use Eccube\Framework\Util\GcUtils;

/**
 * 携帯端末の情報を扱うクラス
 *
 * 対象とする携帯端末は $_SERVER から決定する。
 * 全てのメソッドはクラスメソッド。
 */
class MobileUserAgent
{
    /**
     * 携帯端末のキャリアを表す文字列を取得する。
     *
     * 文字列は docomo, ezweb, softbank のいずれか。
     *
     * @return string|false 携帯端末のキャリアを表す文字列を返す。
     *                      携帯端末ではない場合は false を返す。
     */
    public static function getCarrier()
    {
        $objAgent =& \Net_UserAgent_Mobile::singleton();
        if (\Net_UserAgent_Mobile::isError($objAgent)) {
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
    public static function getId()
    {
        $objAgent =& \Net_UserAgent_Mobile::singleton();
        if (\Net_UserAgent_Mobile::isError($objAgent)) {
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
    public static function getModel()
    {
        $objAgent =& \Net_UserAgent_Mobile::singleton();
        if (\Net_UserAgent_Mobile::isError($objAgent)) {
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
    public static function isSupported()
    {
        $objAgent =& \Net_UserAgent_Mobile::singleton();

        // 携帯端末だと認識されたが、User-Agent の形式が未知の場合
        if (\Net_UserAgent_Mobile::isError($objAgent)) {
            GcUtils::gfPrintLog($objAgent->toString());

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
    public static function isMobile()
    {
        $objAgent =& \Net_UserAgent_Mobile::singleton();
        if (\Net_UserAgent_Mobile::isError($objAgent)) {
            return false;
        } else {
            return $objAgent->isDoCoMo() || $objAgent->isEZweb() || $objAgent->isVodafone();
        }
    }
}
