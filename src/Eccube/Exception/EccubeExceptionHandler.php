<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) EC-CUBE CO.,LTD. All Rights Reserved.
 *
 * http://www.ec-cube.co.jp/
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


namespace Eccube\Exception;

use Eccube\Application;
use Symfony\Component\Debug\Exception\FlattenException;
use Symfony\Component\Debug\ExceptionHandler;
use Symfony\Component\HttpFoundation\Response;

class EccubeExceptionHandler extends ExceptionHandler
{

    public function handle(\Exception $exception)
    {

        $app = Application::getInstance();

        if (!$exception instanceof FlattenException) {
            $exception = FlattenException::create($exception);
        }

        log_error($exception->getMessage(), $exception->getTrace());

        $view = $app->renderView('error.twig', array(
            'error_title' => 'システムエラーが発生しました。',
            'error_message' => '大変お手数ですが、サイト管理者までご連絡ください。',
        ));

        $response = Response::create($view, 500);
        $response->sendHeaders();
        $response->sendContent();

        $app->terminate($app['request'], $response);
    }
}
