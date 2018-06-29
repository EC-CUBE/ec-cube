<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eccube\Event;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpFoundation\Response;

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
     * @var array
     */
    private $parameters;

    /**
     * @var null|Response
     */
    private $response;

    /**
     * @var array
     */
    private $plugin_javascripts = [];

    /**
     * @var array
     */
    private $plugin_assets = [];

    /**
     * @var array
     */
    private $plugin_snippets = [];

    /**
     * TemplateEvent constructor.
     *
     * @param string $view
     * @param string $source
     * @param array $parameters
     * @param Response|null $response
     */
    public function __construct($view, $source, array $parameters = [], Response $response = null)
    {
        $this->view = $view;
        $this->source = $source;
        $this->parameters = $parameters;
        $this->response = $response;
    }

    /**
     * @return string
     */
    public function getView()
    {
        return $this->view;
    }

    /**
     * @param string $view
     */
    public function setView($view)
    {
        $this->view = $view;
    }

    /**
     * @return string
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * @param string $source
     */
    public function setSource($source)
    {
        $this->source = $source;
    }

    /**
     * @param $key
     *
     * @return mixed
     */
    public function getParameter($key)
    {
        return $this->parameters[$key];
    }

    /**
     * @param $key
     * @param $value
     */
    public function setParameter($key, $value)
    {
        $this->parameters[$key] = $value;
    }

    /**
     * @param $key
     *
     * @return bool
     */
    public function hasParameter($key)
    {
        return isset($this->parameters[$key]);
    }

    /**
     * @return array
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * @param array $parameters
     */
    public function setParameters($parameters)
    {
        $this->parameters = $parameters;
    }

    /**
     * @return null|Response
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * @param null|Response $response
     */
    public function setResponse($response)
    {
        $this->response = $response;
    }

    /**
     * Add plugin_javascripts
     *
     * @param $plugin_javascript
     * @param bool $value
     *
     * @return $this
     */
    public function addPluginJavascripts($plugin_javascript, $value = true)
    {
        $this->plugin_javascripts[$plugin_javascript] = $value;

        $this->setParameter('plugin_javascripts', $this->plugin_javascripts);

        return $this;
    }

    /**
     * Remove plugin_javascripts
     *
     * @param $plugin_javascript
     */
    public function removePluginJavascripts($plugin_javascript)
    {
        unset($this->plugin_javascripts[$plugin_javascript]);

        $this->setParameter('plugin_javascripts', $this->plugin_javascripts);
    }

    /**
     * Get plugin_javascripts
     *
     * @return array
     */
    public function getPluginJavascripts()
    {
        return $this->plugin_javascripts;
    }

    /**
     * Add plugin_assets
     *
     * @param $plugin_asset
     * @param bool $value
     *
     * @return $this
     */
    public function addPluginAssets($plugin_asset, $value = true)
    {
        $this->plugin_assets[$plugin_asset] = $value;

        $this->setParameter('plugin_assets', $this->plugin_assets);

        return $this;
    }

    /**
     * Remove plugin_assets
     *
     * @param $plugin_asset
     */
    public function removePluginAssets($plugin_asset)
    {
        unset($this->plugin_assets[$plugin_asset]);

        $this->setParameter('plugin_assets', $this->plugin_assets);
    }

    /**
     * Get plugin_assets
     *
     * @return array
     */
    public function getPluginAssets()
    {
        return $this->plugin_assets;
    }

    /**
     * Add plugin_snippets
     *
     * @param $plugin_snippet
     * @param bool $value
     *
     * @return $this
     */
    public function addPluginSnippets($plugin_snippet, $value = true)
    {
        $this->plugin_snippets[$plugin_snippet] = $value;

        $this->setParameter('plugin_snippets', $this->plugin_snippets);

        return $this;
    }

    /**
     * Remove plugin_snippets
     *
     * @param $plugin_snippet
     */
    public function removePluginSnippets($plugin_snippet)
    {
        unset($this->plugin_snippets[$plugin_snippet]);

        $this->setParameter('plugin_snippeet', $this->plugin_snippets);
    }

    /**
     * Get plugin_snippets
     *
     * @return array
     */
    public function getPluginSnippets()
    {
        return $this->plugin_snippets;
    }
}
