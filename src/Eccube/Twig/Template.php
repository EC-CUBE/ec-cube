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
use Twig\Source;

class Template extends \Twig\Template
{
    /**
     * Twig 3.2+ の yield ベース描画では render() が display() を経由しないため、
     * TemplateEvent はここで発火させる（従来は display() を上書きしていた）。
     *
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\SyntaxError
     */
    public function yield(array $context, array $blocks = []): iterable
    {
        $globals = $this->env->getGlobals();
        if (isset($globals['event_dispatcher']) && strpos($this->getTemplateName(), '__string_template__') !== 0) {
            /** @var EventDispatcherInterface $eventDispatcher */
            $eventDispatcher = $globals['event_dispatcher'];
            $templateName = $this->getTemplateName();
            $originCode = $this->env->getLoader()->getSourceContext($templateName)->getCode();
            $event = new TemplateEvent($templateName, $originCode, $context);
            $eventDispatcher->dispatch($event, $templateName);
            if ($event->getSource() !== $originCode) {
                $newTemplate = $this->env->createTemplate($event->getSource());

                return yield from $newTemplate->unwrap()->yield($event->getParameters(), $blocks);
            }

            return yield from parent::yield($event->getParameters(), $blocks);
        }

        return yield from parent::yield($context, $blocks);
    }

    public function getTemplateName(): string
    {
        // Templateのキャッシュ作成時に動的に作成されるメソッド
        // デバッグツールバーでエラーが発生するため空文字を返しておく。
        // @see https://github.com/EC-CUBE/ec-cube/issues/4529
        return '';
    }

    public function getDebugInfo(): array
    {
        // Templateのキャッシュ作成時に動的に作成されるメソッド
        return [];
    }

    protected function doDisplay(array $context, array $blocks = []): iterable
    {
        // Templateのキャッシュ作成時に動的に作成されるメソッド
        return [];
    }

    public function getSourceContext(): Source
    {
        // FIXME Twig\Loader\FilesystemLoader の実装を持ってきたが,これで問題ないか要確認
        return new Source('', $this->getTemplateName(), '');
    }
}
