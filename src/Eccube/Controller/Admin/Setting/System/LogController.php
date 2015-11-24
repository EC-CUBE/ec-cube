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
use Symfony\Component\HttpFoundation\Request;

class LogController
{
    public function index(Application $app, Request $request)
    {
        $formData = array();
        // default
        $formData['files'] = 'site_'.date('Y-m-d').'.log';
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

        return $app['view']->render('Setting/System/log.twig', array(
            'form' => $form->createView(),
            'log' => $this->parseLogFile($logFile, $formData),
        ));
    }

    private function parseLogFile($logFile, $formData)
    {
        $pattern = '/\[(?P<date>.*)\] (?P<logger>\w+).(?P<level>\w+): (?P<message>.*)$/';

        $log = array();
        $message = array();

        foreach (array_reverse(file($logFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES)) as $line) {
            // 上限に達した場合、処理を抜ける
            if (count($log) >= $formData['line_max']) {
                break;
            }

            $line = chop($line);
            if (preg_match($pattern, $line, $data)) {
                $message[] = $data['message'];
                // messageをここで分解し直す
                // 改行コードにするとややこしいのでbrで改行する
                preg_match('/(?P<message>.*[^ ]+) (?P<context>[^ ]+) (?P<extra>[^ ]+)/', implode("<br>", array_reverse($message)), $res);
                $message = array();

                $log[] = array(
                    'date' => \DateTime::createFromFormat('Y-m-d H:i:s', $data['date']),
                    'logger' => $data['logger'],
                    'level' => $data['level'],
                    'message' => $res['message'],
                    'context' => var_export(json_decode($res['context'], true), true),
                    'extra' => var_export(json_decode($res['extra'], true), true),
                );
            } else {
                // 内容が改行されている場合がある
                $message[] = $line;
            }
        }
        return $log;
    }
}
