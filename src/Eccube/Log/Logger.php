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

namespace Eccube\Log;

use Eccube\Application;
use Psr\Log\AbstractLogger;

class Logger extends AbstractLogger
{
    protected $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * ログ出力を行う。アクセスされている画面によりログ出力先を分けている。
     *
     * @param mixed $level
     * @param string $message
     * @param array $context
     */
    public function log($level, $message, array $context = array())
    {
        if ($this->app->isFrontRequest()) {
            // フロント画面用のログ出力
            $this->app['monolog.logger.front']->log($level, $message, $context);
        } elseif ($this->app->isAdminRequest()) {
            // 管理画面用のログ出力
            $this->app['monolog.logger.admin']->log($level, $message, $context);
        } else {
            // 両方に当てはまらない場合、monolog用へログ出力
            $this->app['monolog']->log($level, $message, $context);
        }

        if ($this->app['debug']) {
            // debugが有効時はフロント、管理両方のログをmonologにも出力
            $this->app['monolog']->log($level, $message, $context);
        }

    }

}
