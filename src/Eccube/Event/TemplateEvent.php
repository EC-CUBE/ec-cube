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
     * @var string
     */
    private $view;

    /**
     * @var string
     */
    private $source;

    /**
     * @var array<mixed>
     */
    private $parameters;

    /**
     * @var Response|null
     */
    private $response;

    /**
     * @var array<mixed>
     */
    private $assets = [];

    /**
     * @var array<mixed>
     */
    private $snippets = [];

    /**
     * TemplateEvent constructor.
     *
     * @param string $view
     * @param string $source
     * @param array<mixed> $parameters
     * @param Response|null $response
     */
    public function __construct($view, $source, array $parameters = [], ?Response $response = null)
    {
        $this->view = $view;
        $this->source = $source;
        $this->parameters = $parameters;
        $this->response = $response;
    }

    /**
     * @return string|null
     */
    public function getView(): ?string
    {
        return $this->view;
    }

    /**
     * @param string $view
     *
     * @return void
     */
    public function setView($view): void
    {
        $this->view = $view;
    }

    /**
     * @return string|null
     */
    public function getSource(): ?string
    {
        return $this->source;
    }

    /**
     * @param string $source
     *
     * @return void
     */
    public function setSource($source): void
    {
        $this->source = $source;
    }

    /**
     * @param string $key
     *
     * @return mixed
     */
    public function getParameter($key): mixed
    {
        return $this->parameters[$key];
    }

    /**
     * @param string $key
     * @param mixed $value
     *
     * @return void
     */
    public function setParameter($key, $value): void
    {
        $this->parameters[$key] = $value;
    }

    /**
     * @param string $key
     *
     * @return bool
     */
    public function hasParameter($key): bool
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
     *
     * @return void
     */
    public function setParameters($parameters): void
    {
        $this->parameters = $parameters;
    }

    /**
     * @return Response|null
     */
    public function getResponse(): ?Response
    {
        return $this->response;
    }

    /**
     * @param Response|null $response
     *
     * @return void
     */
    public function setResponse($response): void
    {
        $this->response = $response;
    }

    /**
     * アセットを追加する
     *
     * ここで追加したコードは, <head></head>内に出力される
     * javascriptの読み込みやcssの読み込みに利用する.
     *
     * @param string $asset
     * @param bool $include twigファイルとしてincludeするかどうか
     *
     * @return $this
     */
    public function addAsset($asset, $include = true): static
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
     * @param string $snippet
     * @param bool $include twigファイルとしてincludeするかどうか
     *
     * @return $this
     */
    public function addSnippet($snippet, $include = true): static
    {
        $this->snippets[$snippet] = $include;

        $this->setParameter('plugin_snippets', $this->snippets);

        return $this;
    }
}
