<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eccube\Util;

use Eccube\Application;
use Eccube\Common\EccubeConfig;

class MailUtil
{
    /**
     * メールの文字コード設定が ISO-2022-JP かどうかを返します。
     *
     * @param EccubeConfig $eccubeConfig
     *
     * @return bool
     */
    public static function isISO2022JP(EccubeConfig $eccubeConfig)
    {
        if (isset($app['config']['mail']['charset_iso_2022_jp'])
            && is_bool($app['config']['mail']['charset_iso_2022_jp'])) {
            return $app['config']['mail']['charset_iso_2022_jp'] === true;
        }

        return false;
    }

    /**
     * 文字コード別のパラメータを設定します。
     *
     * @param Application $app
     * @param \Swift_Message $message
     * @param string $fromEncoding
     * @param string $toEncoding
     */
    public static function setParameterForCharset(Application $app, \Swift_Message $message, $fromEncoding = 'UTF-8', $toEncoding = 'iso-2022-jp')
    {
        if (MailUtil::isISO2022JP($app)) {
            $message
                ->setCharset($toEncoding)
                ->setEncoder(new \Swift_Mime_ContentEncoder_PlainContentEncoder('7bit'));

            $body = mb_convert_encoding($message->getBody(), $toEncoding, $fromEncoding);
            $message->setBody($body);
        }
    }

    /**
     * iso-2022-jpがmail.ymlで設定されていた場合、messageのcharsetを指定された文字コードに変換する
     *
     * @param Application $app
     * @param \Swift_Message $message
     * @param string $fromEncoding
     * @param string $toEncoding
     */
    public static function convertMessage(Application $app, \Swift_Message $message, $fromEncoding = 'iso-2022-jp', $toEncoding = 'UTF-8')
    {
        if (MailUtil::isISO2022JP($app)) {
            $body = mb_convert_encoding($message->getBody(), $toEncoding, $fromEncoding);
            $message->setCharset($toEncoding);
            $message->setBody($body);
        }
    }
}
