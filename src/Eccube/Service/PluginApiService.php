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

namespace Eccube\Service;

use Eccube\Common\Constant;
use Eccube\Common\EccubeConfig;
use Eccube\Entity\BaseInfo;
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
     * @var BaseInfo
     */
    private $BaseInfo;

    /**
     * @var PluginRepository
     */
    private $pluginRepository;

    /**
     * @var PluginService
     */
    private $pluginService;

    /**
     * PluginApiService constructor.
     *
     * @param EccubeConfig $eccubeConfig
     * @param RequestStack $requestStack
     * @param BaseInfoRepository $baseInfoRepository
     * @param PluginRepository $pluginRepository
     * @param PluginService $pluginService
     * 
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function __construct(EccubeConfig $eccubeConfig, RequestStack $requestStack, BaseInfoRepository $baseInfoRepository, PluginRepository $pluginRepository, PluginService $pluginService)
    {
        $this->eccubeConfig = $eccubeConfig;
        $this->requestStack = $requestStack;
        $this->BaseInfo = $baseInfoRepository->get();
        $this->pluginRepository = $pluginRepository;
        $this->pluginService = $pluginService;
    }

    /**
     * @return mixed
     */
    public function getApiUrl()
    {
        if (empty($this->apiUrl)) {
            return $this->eccubeConfig->get('eccube_package_api_url');
        }

        return $this->apiUrl;
    }

    /**
     * @param mixed $apiUrl
     */
    public function setApiUrl($apiUrl)
    {
        $this->apiUrl = $apiUrl;
    }

    /**
     * Get master data: category
     *
     * @return array
     */
    public function getCategory()
    {
        try {
            $urlCategory = $this->getApiUrl().'/category';

            return $this->getRequestApi($urlCategory);
        } catch (PluginApiException $e) {
            return [];
        }
    }

    /**
     * Get plugins list
     *
     * @param $data
     *
     * @return array
     *
     * @throws PluginApiException
     */
    public function getPlugins($data)
    {
        $url = $this->getApiUrl().'/plugins';
        $params['category_id'] = $data['category_id'];
        $params['price_type'] = empty($data['price_type']) ? 'all' : $data['price_type'];
        $params['keyword'] = $data['keyword'];
        $params['sort'] = $data['sort'];
        $params['page'] = (isset($data['page_no']) && !empty($data['page_no'])) ? $data['page_no'] : 1;
        $params['per_page'] = (isset($data['page_count']) && !empty($data['page_count'])) ? $data['page_count'] : $this->eccubeConfig->get('eccube_default_page_count');

        $payload = $this->getRequestApi($url, $params);
        $data = json_decode($payload, true);

        if (isset($data['plugins'])) {
            $this->buildPlugins($data['plugins']);
        }

        return $data;
    }

    /**
     * Get purchased plugins list
     *
     * @return array
     *
     * @throws PluginApiException
     */
    public function getPurchased()
    {
        $url = $this->getApiUrl().'/plugins/purchased';

        $payload = $this->getRequestApi($url);
        $plugins = json_decode($payload, true);

        return $this->buildPlugins($plugins);
    }

    /**
     * Get recommended plugins list
     *
     * @return array($result, $info)
     *
     * @throws PluginApiException
     */
    public function getRecommended()
    {
        $url = $this->getApiUrl().'/plugins/recommended';

        $payload = $this->getRequestApi($url);
        $plugins = json_decode($payload, true);

        return $this->buildPlugins($plugins);
    }

    private function buildPlugins(&$plugins)
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
                    if ($this->pluginService->isUpdate($plugin->getVersion(), $item['version'])) {
                        // Need update
                        $item['update_status'] = 3;
                    }
                }
            }
            if ($item['purchased'] == false && (isset($item['purchase_required']) && $item['purchase_required'] == true)) {
                // Not purchased with paid items
                $item['update_status'] = 4;
            }

            $item = $this->buildInfo($item);
            $items[] = $item;
        }

        return $plugins;
    }

    /**
     * Get a plugin
     *
     * @param int|string $id Id or plugin code
     *
     * @return array
     *
     * @throws PluginApiException
     */
    public function getPlugin($id)
    {
        $url = $this->getApiUrl().'/plugin/'.$id;

        $payload = $this->getRequestApi($url);
        $json = json_decode($payload, true);

        return $this->buildInfo($json);
    }

    /**
     * API request processing
     *
     * @param string $url
     * @param array $data
     *
     * @return array
     *
     * @throws PluginApiException
     */
    public function getRequestApi($url, $data = [])
    {
        if (count($data) > 0) {
            $url .= '?'.http_build_query($data);
        }

        $curl = curl_init($url);

        $key = $this->BaseInfo->getAuthenticationKey();
        $baseUrl = $this->requestStack->getCurrentRequest()->getSchemeAndHttpHost().$this->requestStack->getCurrentRequest()->getBasePath();

        // Option array
        $options = [
            // HEADER
            CURLOPT_HTTPHEADER => [
                'X-ECCUBE-KEY: '.$key,
                'X-ECCUBE-URL: '.$baseUrl,
                'X-ECCUBE-VERSION: '.Constant::VERSION,
            ],
            CURLOPT_HTTPGET => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FAILONERROR => true,
            CURLOPT_CAINFO => \Composer\CaBundle\CaBundle::getSystemCaRootBundlePath(),
        ];

        // Set option value
        curl_setopt_array($curl, $options);
        $result = curl_exec($curl);
        $info = curl_getinfo($curl);
        $message = curl_error($curl);
        $info['message'] = $message;
        curl_close($curl);

        log_info('http get_info', $info);

        if ($info['http_code'] !== 200) {
            throw new PluginApiException($info);
        }

        return $result;
    }

    /**
     * Get plugin information
     *
     * @param array $plugin
     *
     * @return array|null
     */
    public function buildInfo(&$plugin)
    {
        $this->supportedVersion($plugin);

        return $plugin;
    }

    /**
     * Check support version
     *
     * @param $plugin
     */
    public function supportedVersion(&$plugin)
    {
        // Check the eccube version that the plugin supports.
        $plugin['version_check'] = false;
        if (in_array(Constant::VERSION, $plugin['supported_versions'])) {
            // Match version
            $plugin['version_check'] = true;
        }
    }
}
