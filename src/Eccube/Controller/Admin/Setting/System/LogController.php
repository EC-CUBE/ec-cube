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


namespace Eccube\Controller\Admin\Setting\System;

use Eccube\Application;
use Eccube\Common\Constant;
use Symfony\Component\HttpFoundation\Request;
use Dubture\Monolog\Reader\LogReader;

class LogController
{
    public function index(Application $app, Request $request)
    {
        $line = array();
        // default
        $formData['files'] = 'site.log';
        $formData['line_max'] = '50';

        $form = $app['form.factory']
            ->createBuilder('admin_system_log')
            ->getForm();

        if ('POST' === $request->getMethod()) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $formData = $form->getData();
            }
        }

        $logFile = $app['config']['root_dir'].'/app/log/'.$formData['files'];
        $reader = new LogReader($logFile);

        $count = $reader->count();
        $line_max = ($count < $formData['line_max']) ? $count : $formData['line_max']+1;

        // 空の1行が末尾に入るので2からスタート
        for ($i = 2; $i <= $line_max; $i++) {
            $count = $reader->count()-$i;
            $line[] = $reader->offsetGet($count);
        }

        return $app['view']->render('Setting/System/log.twig', array(
            'form' => $form->createView(),
            'line' => $line,
        ));
    }
}
