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

namespace Eccube\DataCollector;

use Eccube\Common\Constant;
use Eccube\Plugin\ConfigManager;
use Eccube\Repository\PluginRepository;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\DataCollector;

/**
 * EccubeDataCollector.
 *
 * @see https://github.com/Sylius/SyliusCoreBundle/blob/master/Collector/SyliusCollector.php
 */
class EccubeDataCollector extends DataCollector
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var ConfigManager
     */
    protected $configManager;

    /**
     * @var PluginRepository
     */
    protected $pluginRepository;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container, ConfigManager $configManager, PluginRepository $pluginRepository)
    {
        $this->data = [
            'version' => Constant::VERSION,
            'base_currency_code' => null,
            'currency_code' => null,
            'default_locale_code' => null,
            'locale_code' => null,
            'plugins' => [],
        ];
        $this->container = $container;
        $this->configManager = $configManager;
        $this->pluginRepository = $pluginRepository;
    }

    /**
     * @return string
     */
    public function getVersion()
    {
        return $this->data['version'];
    }

    /**
     * @return array
     */
    public function getPlugins()
    {
        return $this->data['plugins'];
    }

    /**
     * @return string
     */
    public function getCurrencyCode()
    {
        return $this->data['currency_code'];
    }

    /**
     * @return string
     */
    public function getLocaleCode()
    {
        return $this->data['locale_code'];
    }

    /**
     * @return string
     */
    public function getDefaultCurrencyCode()
    {
        return $this->data['base_currency_code'];
    }

    /**
     * @return string
     */
    public function getDefaultLocaleCode()
    {
        return $this->data['default_locale_code'];
    }

    /**
     * {@inheritdoc}
     */
    public function collect(Request $request, Response $response, \Exception $exception = null)
    {
        $this->data['base_currency_code'] = $this->container->getParameter('currency');
        $this->data['currency_code'] = $this->container->getParameter('currency');

        try {
            $this->data['locale_code'] = $this->container->getParameter('locale');
        } catch (LocaleNotFoundException $exception) {
        }

        try {
            $this->data['plugins'] = $this->configManager->getPluginConfigAll();
            $Plugins = $this->pluginRepository->findBy([], ['code' => 'ASC']);

            foreach (array_keys($this->data['plugins']) as $pluginCode) {
                $Plugin = array_filter($Plugins, function ($Plugin) use ($pluginCode) {
                    return $Plugin->getCode() == $pluginCode;
                });
                if (!empty($Plugin) && count($Plugin) > 0) {
                    $this->data['plugins'][$pluginCode]['enabled'] = current($Plugin)->isEnabled();
                } else {
                    $this->data['plugins'][$pluginCode]['enabled'] = false;
                }
            }
        } catch (\Exception $exception) {
        }
    }

    public function reset()
    {
        $this->data = [];
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'eccube_core';
    }
}
