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

class Mail
{
    /**
     * メールの文字コード設定が ISO-2022-JP かどうかを返します。
     *
     * @param Application $app
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
     */
    public static function setParameterForCharaset(Application $app, \Swift_Message $message)
    {
        if (Mail::isISO2022JP($app)) {
            $message
                ->setCharset('iso-2022-jp')
                ->setEncoder(new \Swift_Mime_ContentEncoder_PlainContentEncoder('7bit'))
                ->setMaxLineLength(0);
        }
    }
}
