<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2015 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */


namespace Acme\Controller\Admin\Store;

use Eccube\Annotation\Component;
use Eccube\Annotation\Inject;
use Eccube\Application;
use Eccube\Common\Constant;
use Eccube\Controller\AbstractController;
use Eccube\Entity\Plugin;
use Eccube\Repository\PluginRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Process\Process;

/**
 * @Component
 * @Route(service=OwnerStoreController::class)
 */
class OwnerStoreController extends AbstractController
{
    /**
     * @Inject("config")
     * @var array
     */
    protected $appConfig;

    /**
     * @Inject(PluginRepository::class)
     * @var PluginRepository
     */
    protected $pluginRepository;

    /**
     * Owner's Store Plugin Installation Screen - Search function
     *
     * @Route("/{_admin}/store/plugin/search", name="admin_store_plugin_owners_search")
     * @Template("Store/plugin_search.twig")
     * @param Application $app
     * @param Request     $request
     * @return array
     */
    public function search(Application $app, Request $request)
    {
        // Acquire downloadable plug-in information from owners store
        $success = 0;
        $items = array();
        $promotionItems = array();
        $message = '';
        // Owner's store communication
        $url = $this->appConfig['owners_store_url'].'?method=list';
        list($json, $info) = $this->getRequestApi($url, $app);
        if ($json === false) {
            $message = $this->getResponseErrorMessage($info);
        } else {
            $data = json_decode($json, true);
            if (isset($data['success'])) {
                $success = $data['success'];
                if ($success == '1') {
                    $items = array();
                    // Check plugin installed
                    $arrPluginInstalled = $this->pluginRepository->findAll();
                    // Update_status 1 : not install/purchased 、2 : Installed、 3 : Update、4 : paid purchase
                    foreach ($data['item'] as $item) {
                        // Not install/purchased
                        $item['update_status'] = 1;
                        /** @var Plugin $plugin */
                        foreach ($arrPluginInstalled as $plugin) {
                            if ($plugin->getSource() == $item['product_id']) {
                                // Need update
                                $item['update_status'] = 3;
                                if ($plugin->getVersion() == $item['version']) {
                                    // Installed
                                    $item['update_status'] = 2;
                                }
                            }
                        }
                        $items[] = $item;
                    }

                    // EC-CUBE version check
                    foreach ($items as &$item) {
                        // Not applicable version
                        $item['version_check'] = 0;
                        if (in_array(Constant::VERSION, $item['eccube_version'])) {
                            // Match version
                            $item['version_check'] = 1;
                        }
                        if ($item['price'] != '0' && $item['purchased'] == '0') {
                            // Not purchased with paid items
                            $item['update_status'] = 4;
                        }
                    }
                    unset($item);

                    // Promotion item
                    $i = 0;
                    foreach ($items as $item) {
                        if ($item['promotion'] == 1) {
                            $promotionItems[] = $item;
                            unset($items[$i]);
                        }
                        $i++;
                    }
                } else {
                    $message = $data['error_code'] . ' : ' . $data['error_message'];
                }
            } else {
                $success = 0;
                $message = "EC-CUBEオーナーズストアにエラーが発生しています。";
            }
        }

        return [
            'success' => $success,
            'items' => $items,
            'promotionItems' => $promotionItems,
            'message' => $message,
        ];
    }

    /**
     * Api Install plugin by composer connect with packagist
     *
     * @Route("/{_admin}/store/plugin/api/{pluginCode}" , name="admin_store_plugin_api_install")
     *
     * @param Application $app
     * @param Request     $request
     * @param string      $pluginCode
     * @return RedirectResponse
     */
    public function apiInstall(Application $app, Request $request, $pluginCode)
    {
        // Check plugin code
        $url = $this->appConfig['owners_store_url'].'?method=list';
        list($json, $info) = $this->getRequestApi($url, $app);
        $existFlg = false;
        $data = json_decode($json, true);
        if ($data && isset($data['success'])) {
            $success = $data['success'];
            if ($success == '1') {
                foreach ($data['item'] as $item) {
                    if ($item['product_code'] == $pluginCode) {
                        $existFlg = true;
                        break;
                    }
                }
            }
        }
        if ($existFlg === false) {
            $app->log(sprintf('%s plugin not found!', $pluginCode));
            $app->addError('admin.plugin.not.found', 'admin');

            return $app->redirect($app->url('admin_store_plugin_owners_search'));
        }

        try {
            $execute = sprintf('cd %s &&', $this->appConfig['root_dir']);
            $execute .= sprintf(' composer require ec-cube/%s', $pluginCode);

            $install = new Process($execute);
            $install->setTimeout(null);
            $install->run();
            if ($install->isSuccessful()) {
                $app->addSuccess('admin.plugin.install.complete', 'admin');
                $app->log(sprintf('Install %s plugin successful!', $pluginCode));

                return $app->redirect($app->url('admin_store_plugin'));
            }
            $app->addError('admin.plugin.install.fail', 'admin');
        } catch (Exception $exception) {
            $app->addError($exception->getMessage(), 'admin');
            $app->log($exception->getCode().' : '.$exception->getMessage());
        }
        $app->log(sprintf('Install %s plugin fail!', $pluginCode));

        return $app->redirect($app->url('admin_store_plugin_owners_search'));
    }

    /**
     * API request processing
     *
     * @param string  $url
     * @param Application $app
     * @return array
     */
    private function getRequestApi($url, $app)
    {
        $curl = curl_init($url);

        // Option array
        $options = array(
            // HEADER
            CURLOPT_HTTPGET => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FAILONERROR => true,
            CURLOPT_CAINFO => \Composer\CaBundle\CaBundle::getSystemCaRootBundlePath(),
        );

        // Set option value
        curl_setopt_array($curl, $options);
        $result = curl_exec($curl);
        $info = curl_getinfo($curl);
        $message = curl_error($curl);
        $info['message'] = $message;
        curl_close($curl);

        $app->log('http get_info', $info);

        return array($result, $info);
    }

    /**
     * Get message
     *
     * @param $info
     * @return string
     */
    private function getResponseErrorMessage($info)
    {
        if (!empty($info)) {
            $statusCode = $info['http_code'];
            $message = $info['message'];

            $message = $statusCode.' : '.$message;
        } else {
            $message = "タイムアウトエラーまたはURLの指定に誤りがあります。";
        }

        return $message;
    }
}
