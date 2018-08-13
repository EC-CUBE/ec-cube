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


use Eccube\Common\EccubeConfig;

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
     * PluginApiService constructor.
     * @param EccubeConfig $eccubeConfig
     */
    public function __construct(EccubeConfig $eccubeConfig)
    {
        $this->eccubeConfig = $eccubeConfig;
    }

    /**
     * @return mixed
     */
    public function getApiUrl()
    {
        if (empty($this->apiUrl)) {
            return $this->eccubeConfig->get('eccube_package_repo_url');
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

    public function getCategory()
    {
        $urlCategory = $this->getApiUrl() . '/category';

        return $this->getRequestApi($urlCategory);
    }

    public function getPlugins($data = array())
    {
        $url = $this->getApiUrl() . '/plugins';
        $params['category_id'] = $data['category_id'];
        $params['price_type'] = empty($data['price_type']) ? 'all' : $data['price_type'];
        $params['keyword'] = $data['keyword'];
        $params['sort'] = $data['sort'];
        $params['page'] = (isset($data['page_no']) && !empty($data['page_no'])) ? $data['page_no'] : 1;
        $params['per_page'] = (isset($data['page_count']) && !empty($data['page_count'])) ? $data['page_count'] : $this->eccubeConfig->get('eccube_default_page_count');

        return $this->getRequestApi($url, $params);
    }

    /**
     * API request processing
     *
     * @param string  $url
     * @param array $data
     *
     * @return array
     */
    public function getRequestApi($url, $data = array())
    {
        if (count($data) > 0) {
            $url .=  '?' . http_build_query($data);
        }

        $curl = curl_init($url);

        // Option array
        $options = [
            // HEADER
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

        return [$result, $info];
    }

    /**
     * Get message
     *
     * @param $info
     *
     * @return string
     */
    public function getResponseErrorMessage($info)
    {
        if (!empty($info)) {
            $statusCode = $info['http_code'];
            $message = $info['message'];
            $message = $statusCode.' : '.$message;
        } else {
            $message = trans('ownerstore.text.error.timeout');
        }

        return $message;
    }
}
