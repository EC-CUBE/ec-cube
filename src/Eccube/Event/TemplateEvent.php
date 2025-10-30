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

namespace Eccube\Event;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * Class TemplateEvent
 */
class TemplateEvent extends Event
{
    /**
     * @var string|null
     */
    private ?string $view;

    /**
     * @var string|null
     */
    private ?string $source;

    /**
     * @var array<mixed>
     */
    private array $parameters;

    /**
     * @var Response|null
     */
    private ?Response $response;

    /**
     * @var array<mixed>
     */
    private array $assets = [];

    /**
     * @var array<mixed>
     */
    private array $snippets = [];

    /**
     * TemplateEvent constructor.
     *
     * @param array<mixed> $parameters
     */
    public function __construct(?string $view, ?string $source, array $parameters = [], ?Response $response = null)
    {
        $this->view = $view;
        $this->source = $source;
        $this->parameters = $parameters;
        $this->response = $response;
    }

    public function getView(): ?string
    {
        return $this->view;
    }

    public function setView(string $view): void
    {
        $this->view = $view;
    }

    public function getSource(): ?string
    {
        return $this->source;
    }

    public function setSource(string $source): void
    {
        $this->source = $source;
    }

    public function getParameter(string $key): mixed
    {
        return $this->parameters[$key];
    }

    public function setParameter(string $key, mixed $value): void
    {
        $this->parameters[$key] = $value;
    }

    public function hasParameter(string $key): bool
    {
        return isset($this->parameters[$key]);
    }

    /**
     * @return array<mixed>
     */
    public function getParameters(): array
    {
        return $this->parameters;
    }

    /**
     * @param array<mixed> $parameters
     */
    public function setParameters(array $parameters): void
    {
        $this->parameters = $parameters;
    }

    public function getResponse(): ?Response
    {
        return $this->response;
    }

    public function setResponse(?Response $response): void
    {
        $this->response = $response;
    }

    /**
     * アセットを追加する
     *
     * ここで追加したコードは, <head></head>内に出力される
     * javascriptの読み込みやcssの読み込みに利用する.
     *
     * @param bool $include twigファイルとしてincludeするかどうか
     *
     * @return $this
     */
    public function addAsset(string $asset, bool $include = true): static
    {
        $this->assets[$asset] = $include;

        $this->setParameter('plugin_assets', $this->assets);

        return $this;
    }

    /**
     * スニペットを追加する.
     *
     * ここで追加したコードは, </body>タグ直前に出力される
     *
     * @param bool $include twigファイルとしてincludeするかどうか
     *
     * @return $this
     */
    public function addSnippet(string $snippet, bool $include = true): static
    {
        $this->snippets[$snippet] = $include;

        $this->setParameter('plugin_snippets', $this->snippets);

        return $this;
    }
}
