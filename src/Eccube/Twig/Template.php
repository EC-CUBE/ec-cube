<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) EC-CUBE CO.,LTD. All Rights Reserved.
 *
 * http://www.ec-cube.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eccube\Twig;

use Eccube\Event\TemplateEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class Template extends \Twig\Template
{
    /**
     * {@inheritdoc}
     *
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\SyntaxError
     */
    public function display(array $context, array $blocks = [])
    {
        $globals = $this->env->getGlobals();
        if (isset($globals['event_dispatcher']) && strpos($this->getTemplateName(), '__string_template__') !== 0) {
            /** @var EventDispatcherInterface $eventDispatcher */
            $eventDispatcher = $globals['event_dispatcher'];
            $originCode = $this->env->getLoader()->getSourceContext($this->getTemplateName())->getCode();
            $event = new TemplateEvent($this->getTemplateName(), $originCode, $context);
            $eventDispatcher->dispatch($this->getTemplateName(), $event);
            if ($event->getSource() !== $originCode) {
                $newTemplate = $this->env->createTemplate($event->getSource());
                $newTemplate->display($event->getParameters(), $blocks);
            } else {
                parent::display($event->getParameters(), $blocks);
            }
        } else {
            parent::display($context, $blocks);
        }
    }

    public function getTemplateName()
    {
        // Templateのキャッシュ作成時に動的に作成されるメソッド
        // デバッグツールバーでエラーが発生するため空文字を返しておく。
        // @see https://github.com/EC-CUBE/ec-cube/issues/4529
        return '';
    }

    public function getDebugInfo()
    {
        // Templateのキャッシュ作成時に動的に作成されるメソッド
        return [];
    }

    protected function doDisplay(array $context, array $blocks = [])
    {
        // Templateのキャッシュ作成時に動的に作成されるメソッド
    }
}
