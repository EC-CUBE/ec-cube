<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2015 LOCKON CO.,LTD. All Rights Reserved.
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

namespace Eccube\Util;

use Eccube\Application;

class MailUtil
{
    /**
     * メールの文字コード設定が ISO-2022-JP かどうかを返します。
     *
     * @param Application $app
     * @return bool
     */
    public static function isISO2022JP(Application $app)
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
