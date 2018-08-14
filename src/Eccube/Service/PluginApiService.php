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
     * PluginApiService constructor.
     * @param EccubeConfig $eccubeConfig
     * @param RequestStack $requestStack
     */
    public function __construct(EccubeConfig $eccubeConfig, RequestStack $requestStack)
    {
        $this->eccubeConfig = $eccubeConfig;
        $this->requestStack = $requestStack;
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

    /**
     * Get master data: category
     *
     * @return array($result, $info)
     */
    public function getCategory()
    {
        $urlCategory = $this->getApiUrl() . '/category';

        return $this->getRequestApi($urlCategory);
    }

    /**
     * Get plugins list
     *
     * @param array $data
     * @return array($result, $info)
     */
    public function getPlugins($data)
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
     * Get captcha image
     *
     * @return array($result, $info)
     */
    public function getCaptcha()
    {
        $apiUrl = $this->getApiUrl().'/captcha';

        $requestApi = $this->getRequestApi($apiUrl);

        return $requestApi;
    }

    /**
     * Get api key from captcha image
     *
     * @param array $data
     * @return array($result, $info)
     */
    public function postApiKey($data)
    {
        $apiUrl = $this->getApiUrl().'/api_key';

        $baseUrl = $this->requestStack->getCurrentRequest()->getSchemeAndHttpHost() . $this->requestStack->getCurrentRequest()->getBasePath();
        $data['eccube_url'] = $baseUrl;
        $data['eccube_version'] = Constant::VERSION;

        $requestApi = $this->postRequestApi($apiUrl, $data);

        return $requestApi;
    }

    /**
     * API post
     *
     * @param string  $url
     * @param array $data
     *
     * @return array($result, $info)
     */
    public function postRequestApi($url, $data = array())
    {
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_POST, 1);

        if (count($data) > 0) {
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        }

        // Todo: will implement after server worked
        $key = null;
        $baseUrl = $this->requestStack->getCurrentRequest()->getSchemeAndHttpHost() . $this->requestStack->getCurrentRequest()->getBasePath();
        // Option array
        $options = [
            // HEADER
            CURLOPT_HTTPHEADER => [
                'X-ECCUBE-KEY: '.base64_encode($key),
                'X-ECCUBE-URL: '.base64_encode($baseUrl),
                'X-ECCUBE-VERSION: '.base64_encode(Constant::VERSION),
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

        return [$result, $info];
    }

    /**
     * API request processing
     *
     * @param string  $url
     * @param array $data
     *
     * @return array($result, $info)
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
