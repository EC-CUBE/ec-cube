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

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Eccube\Event\TemplateEvent;

abstract class Template extends \Twig\Template
{
    /**
     * {@inheritDoc}
     *
     * @param array $context
     * @param array $blocks
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
}
