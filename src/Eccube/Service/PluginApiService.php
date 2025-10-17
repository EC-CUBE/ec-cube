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

namespace Eccube\Service;

use Composer\CaBundle\CaBundle;
use Eccube\Common\Constant;
use Eccube\Common\EccubeConfig;
use Eccube\Entity\Plugin;
use Eccube\Exception\PluginApiException;
use Eccube\Repository\BaseInfoRepository;
use Eccube\Repository\PluginRepository;
use Symfony\Component\HttpFoundation\RequestStack;

class PluginApiService
{
    /**
     * Url for Api
     *
     * @var string
     */
    private $apiUrl;

    /**
     * @var EccubeConfig
     */
    private $eccubeConfig;

    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @var BaseInfoRepository
     */
    private $baseInfoRepository;

    /**
     * @var PluginRepository
     */
    private $pluginRepository;

    /**
     * PluginApiService constructor.
     *
     * @param EccubeConfig $eccubeConfig
     * @param RequestStack $requestStack
     * @param BaseInfoRepository $baseInfoRepository
     * @param PluginRepository $pluginRepository
     */
    public function __construct(EccubeConfig $eccubeConfig, RequestStack $requestStack, BaseInfoRepository $baseInfoRepository, PluginRepository $pluginRepository)
    {
        $this->eccubeConfig = $eccubeConfig;
        $this->requestStack = $requestStack;
        $this->baseInfoRepository = $baseInfoRepository;
        $this->pluginRepository = $pluginRepository;
    }

    /**
     * @return string
     */
    public function getApiUrl(): string
    {
        if (empty($this->apiUrl)) {
            return $this->eccubeConfig->get('eccube_package_api_url');
        }

        return $this->apiUrl;
    }

    /**
     * @param string $apiUrl
     *
     * @return void
     */
    public function setApiUrl($apiUrl): void
    {
        $this->apiUrl = $apiUrl;
    }

    /**
     * Get master data: category
     *
     * @return string|bool|array<string, string|int|array<int, string>>
     */
    public function getCategory(): string|bool|array
    {
        try {
            $urlCategory = $this->getApiUrl().'/category';

            return $this->requestApi($urlCategory);
        } catch (PluginApiException) {
            return [];
        }
    }

    /**
     * Get plugins list
     *
     * @param array<string, string|int> $data
     *
     * @return array<string, string|int>
     *
     * @throws PluginApiException
     */
    public function getPlugins($data): array
    {
        $url = $this->getApiUrl().'/plugins';
        $params['category_id'] = $data['category_id'];
        $params['price_type'] = empty($data['price_type']) ? 'all' : $data['price_type'];
        $params['keyword'] = $data['keyword'];
        $params['sort'] = $data['sort'];
        $params['page'] = (isset($data['page_no']) && !empty($data['page_no'])) ? $data['page_no'] : 1;
        $params['per_page'] = (isset($data['page_count']) && !empty($data['page_count'])) ? $data['page_count'] : $this->eccubeConfig->get('eccube_default_page_count');

        $payload = $this->requestApi($url, $params);
        $data = json_decode($payload, true);

        if (isset($data['plugins'])) {
            $this->buildPlugins($data['plugins']);
        }

        return $data;
    }

    /**
     * Get purchased plugins list
     *
     * @return array<int, array<string, string|int>>
     *
     * @throws PluginApiException
     */
    public function getPurchased(): array
    {
        $url = $this->getApiUrl().'/plugins/purchased';

        $payload = $this->requestApi($url);
        $plugins = json_decode($payload, true);

        return $this->buildPlugins($plugins);
    }

    /**
     * Get recommended plugins list
     *
     * @return array<int, array<string, string|int>>
     *
     * @throws PluginApiException
     */
    public function getRecommended(): array
    {
        $url = $this->getApiUrl().'/plugins/recommended';

        $payload = $this->requestApi($url);
        $plugins = json_decode($payload, true);

        return $this->buildPlugins($plugins);
    }

    /**
     * @param array<int, array<string, string|int>> $plugins
     *
     * @return array<int, array<string, string|int>>
     */
    private function buildPlugins(&$plugins): array
    {
        /** @var Plugin[] $pluginInstalled */
        $pluginInstalled = $this->pluginRepository->findAll();
        // Update_status 1 : not install/purchased 、2 : Installed、 3 : Update、4 : not purchased
        foreach ($plugins as &$item) {
            // Not install/purchased
            $item['update_status'] = 1;
            foreach ($pluginInstalled as $plugin) {
                if ($plugin->getSource() == $item['id']) {
                    // Installed
                    $item['update_status'] = 2;
                    if ($this->isUpdate($plugin->getVersion(), $item['version'])) {
                        // Need update
                        $item['update_status'] = 3;
                    }
                }
            }
            if ($item['purchased'] == false && (isset($item['purchase_required']) && $item['purchase_required'] == true)) {
                // Not purchased with paid items
                $item['update_status'] = 4;
            }

            $this->buildInfo($item);
        }

        return $plugins;
    }

    /**
     * Is update
     *
     * @param string $pluginVersion
     * @param string $remoteVersion
     *
     * @return bool
     */
    private function isUpdate($pluginVersion, $remoteVersion): bool
    {
        return version_compare($pluginVersion, $remoteVersion, '<');
    }

    /**
     * Get a plugin
     *
     * @param int|string $id Id or plugin code
     *
     * @return array<string, string|int|array<int, string>>
     *
     * @throws PluginApiException
     */
    public function getPlugin($id): array
    {
        $url = $this->getApiUrl().'/plugin/'.$id;

        $payload = $this->requestApi($url);
        $json = json_decode($payload, true);

        return $this->buildInfo($json);
    }

    /**
     * @param Plugin $Plugin
     *
     * @return void
     */
    public function pluginInstalled(Plugin $Plugin): void
    {
        $this->updatePluginStatus('/status/installed', $Plugin);
    }

    /**
     * @param Plugin $Plugin
     *
     * @return void
     */
    public function pluginEnabled(Plugin $Plugin): void
    {
        $this->updatePluginStatus('/status/enabled', $Plugin);
    }

    /**
     * @param Plugin $Plugin
     *
     * @return void
     */
    public function pluginDisabled(Plugin $Plugin): void
    {
        $this->updatePluginStatus('/status/disabled', $Plugin);
    }

    /**
     * @param Plugin $Plugin
     *
     * @return void
     */
    public function pluginUninstalled(Plugin $Plugin): void
    {
        $this->updatePluginStatus('/status/uninstalled', $Plugin);
    }

    /**
     * @param string $url
     * @param Plugin $Plugin
     *
     * @return void
     */
    private function updatePluginStatus($url, Plugin $Plugin): void
    {
        if ($Plugin->getSource()) {
            try {
                $this->requestApi($this->getApiUrl().$url, ['id' => $Plugin->getSource()], true);
            } catch (PluginApiException) {
            }
        }
    }

    /**
     * API request processing
     *
     * @param string $url
     * @param array<string, mixed> $data
     * @param bool $post
     *
     * @return string|bool
     *
     * @throws PluginApiException
     */
    public function requestApi($url, $data = [], $post = false): string|bool
    {
        if ($post === false && count($data) > 0) {
            $url .= '?'.http_build_query($data);
        }

        $curl = curl_init($url);

        if ($post) {
            curl_setopt($curl, CURLOPT_POST, true);

            if (count($data) > 0) {
                curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
            }
        }

        $key = $this->baseInfoRepository->get()->getAuthenticationKey();

        $baseUrl = null;
        if ($this->requestStack->getCurrentRequest()) {
            $baseUrl = $this->requestStack->getCurrentRequest()->getSchemeAndHttpHost().$this->requestStack->getCurrentRequest()->getBasePath();
        }

        // Option array
        $options = [
            // HEADER
            CURLOPT_HTTPHEADER => [
                'X-ECCUBE-KEY: '.$key,
                'X-ECCUBE-URL: '.$baseUrl,
                'X-ECCUBE-VERSION: '.Constant::VERSION,
            ],
            CURLOPT_HTTPGET => $post === false,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FAILONERROR => true,
            CURLOPT_CAINFO => CaBundle::getSystemCaRootBundlePath(),
            CURLOPT_TIMEOUT_MS => 5000,
        ];

        // Set option value
        curl_setopt_array($curl, $options);
        $result = curl_exec($curl);
        $info = curl_getinfo($curl);
        $message = curl_error($curl);
        $info['message'] = $message;
        curl_close($curl);

        log_info('http get_info', $info);

        if ($info['http_code'] !== 200 || $result === false) {
            throw new PluginApiException($info);
        }

        return $result;
    }

    /**
     * Get plugin information
     *
     * @param array<string, string|int|array<int, string|float>|bool>  $plugin
     *
     * @return array<string, string|int|array<int, string|float>|bool>
     */
    public function buildInfo(&$plugin): array
    {
        $this->supportedVersion($plugin);

        return $plugin;
    }

    /**
     * Check support version
     *
     * @param array<string, string|int|array<int, string|float>|bool> $plugin
     *
     * @return void
     */
    public function supportedVersion(&$plugin): void
    {
        // Check the eccube version that the plugin supports.
        $plugin['version_check'] = false;
        if (in_array(Constant::VERSION, $plugin['supported_versions'])) {
            // Match version
            $plugin['version_check'] = true;
        }
    }
}
