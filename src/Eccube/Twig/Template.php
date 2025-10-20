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

use Eccube\Common\EccubeConfig;
use Eccube\Entity\BaseInfo;
use Eccube\Entity\Layout;
use Eccube\Entity\Page;
use Eccube\Event\TemplateEvent;
use Symfony\Bridge\Twig\AppVariable;
use Symfony\Component\EventDispatcher\Debug\TraceableEventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Twig\Error\LoaderError;
use Twig\Error\SyntaxError;
use Twig\Source;

class Template extends \Twig\Template
{
    /**
     * {@inheritdoc}
     *
     * @param array<string, AppVariable|BaseInfo|EccubeConfig|TraceableEventDispatcher|Layout|Page|string|bool> $context
     * @param array<string, array<int, string|object>>  $blocks
     *
     * @return void
     *
     * @throws LoaderError
     * @throws SyntaxError
     */
    #[\Override]
    public function display(array $context, array $blocks = []): void
    {
        $globals = $this->env->getGlobals();
        if (isset($globals['event_dispatcher']) && !str_starts_with($this->getTemplateName(), '__string_template__')) {
            /** @var EventDispatcherInterface $eventDispatcher */
            $eventDispatcher = $globals['event_dispatcher'];
            $originCode = $this->env->getLoader()->getSourceContext($this->getTemplateName())->getCode();
            $event = new TemplateEvent($this->getTemplateName(), $originCode, $context);
            $eventDispatcher->dispatch($event, $this->getTemplateName());
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

    #[\Override]
    public function getTemplateName(): string
    {
        // Templateのキャッシュ作成時に動的に作成されるメソッド
        // デバッグツールバーでエラーが発生するため空文字を返しておく。
        // @see https://github.com/EC-CUBE/ec-cube/issues/4529
        return '';
    }

    /**
     * @return array<empty>
     */
    #[\Override]
    public function getDebugInfo(): array
    {
        // Templateのキャッシュ作成時に動的に作成されるメソッド
        return [];
    }

    /**
     * @param array<mixed> $context
     * @param array<mixed> $blocks
     *
     * @return array<empty>
     */
    protected function doDisplay(array $context, array $blocks = []): array
    {
        // Templateのキャッシュ作成時に動的に作成されるメソッド
        return [];
    }

    #[\Override]
    public function getSourceContext(): Source
    {
        // FIXME Twig\Loader\FilesystemLoader の実装を持ってきたが,これで問題ないか要確認
        return new Source('', $this->getTemplateName(), '');
    }
}
