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

namespace Eccube\Twig\Extension;

use Symfony\Component\Filesystem\Filesystem;
use \Twig_Token;
use \Twig_Node_Include;
use Eccube\Event\TemplateEvent;
use Monolog\Logger;

class Twig_TokenParser_Extends extends \Twig_TokenParser
{
    public function __construct($app)
    {
        $this->app = $app;
    }

    public function parse(Twig_Token $token)
    {
        $stream = $this->parser->getStream();

        if (!$this->parser->isMainScope()) {
            throw new Twig_Error_Syntax('Cannot extend from a block.', $token->getLine(), $stream->getSourceContext());
        }

        if (null !== $this->parser->getParent()) {
            throw new Twig_Error_Syntax('Multiple extends tags are forbidden.', $token->getLine(), $stream->getSourceContext());
        }

        // Begin create event for extend
        $expr = $this->parser->getExpressionParser()->parseExpression();
        $view = $expr->getAttribute('value'); 

        $source = $this->app['twig']->getLoader()->getSource($view);

        $event = new TemplateEvent($view, $source);
        $eventName = $view;
        $path = $this->app['config']['template_realdir'];
        if ($this->app->isAdminRequest()) {
            // 管理画面の場合、event名に「Admin/」を付ける
            $eventName = 'Admin/' . $view;
            $path = $path.'/../admin';
        }

        $this->app['monolog']->debug('Template Event Name : ' . $eventName);
        $this->app['eccube.event.dispatcher']->dispatch($eventName, $event);
        // Begin create event for extend
        $this->createFile($path, $view, $event, $expr);
        $expr->setAttribute('value', $this->getTag().'/'.$view);

        $this->parser->setParent($expr);

        $stream->expect(Twig_Token::BLOCK_END_TYPE);
    }

    public function getTag()
    {
        return 'extends';
    }

    private function createFile($path, $view, $event)
    {
        // 一時ディレクトリ
        $tmpFile = $path.'/'.$this->getTag().'/'.$view;
        // 該当テンプレート
        $fs = new Filesystem();
        $fs->dumpFile($tmpFile, $event->getSource());
    }
}
