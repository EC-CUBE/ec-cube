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
namespace Eccube\Controller\Admin\Store;

use Doctrine\ORM\EntityManager;
use Eccube\Annotation\Inject;
use Eccube\Application;
use Eccube\Common\Constant;
use Eccube\Controller\AbstractController;
use Eccube\Entity\Plugin;
use Eccube\Repository\PluginRepository;
use Eccube\Service\Composer\ComposerServiceInterface;
use Eccube\Service\PluginService;
use Eccube\Service\SystemService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
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
     * @Inject(PluginService::class)
     * @var PluginService
     */
    protected $pluginService;

    /**
     * @Inject("eccube.service.composer")
     * @var ComposerServiceInterface
     */
    protected $composerService;

    /**
     * @var EntityManager
     * @Inject("orm.em")
     */
    protected $em;

    /**
     * @Inject(SystemService::class)
     * @var SystemService
     */
    protected $systemService;

    private static $vendorName = 'ec-cube';

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
        $url = $this->appConfig['package_repo_url'].'/search/packages.json';
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
                    $arrDependency = [];
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

                        // Add plugin dependency
                        $item['depend'] = $this->pluginService->getRequirePluginName($items, $item);
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
                    $message = $data['error_code'].' : '.$data['error_message'];
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
     * Do confirm page
     *
     * @Route("/{_admin}/store/plugin/{id}/confirm", requirements={"id" = "\d+"}, name="admin_store_plugin_install_confirm")
     * @Template("Store/plugin_confirm.twig")
     * @param Application $app
     * @param Request     $request
     * @param string      $id
     * @return array
     */
    public function doConfirm(Application $app, Request $request, $id)
    {
        // Owner's store communication
        $url = $this->appConfig['package_repo_url'].'/search/packages.json';
        list($json, $info) = $this->getRequestApi($url, $app);
        $data = json_decode($json, true);
        $items = $data['item'];

        // Find plugin in api
        $index = array_search($id, array_column($items, 'product_id'));
        if ($index === false) {
            throw new NotFoundHttpException();
        }

        $pluginCode = $items[$index]['product_code'];

        $plugin = $this->pluginService->buildInfo($items, $pluginCode);

        // Prevent infinity loop: A -> B -> A.
        $arrDependency[] = $plugin;
        $arrDependency = $this->pluginService->getDependency($items, $plugin, $arrDependency);
        // Unset first param
        unset($arrDependency[0]);

        return [
            'item' => $plugin,
            'arrDependency' => $arrDependency,
        ];
    }

    /**
     * Api Install plugin by composer connect with package repo
     *
     * @Route("/{_admin}/store/plugin/api/{pluginCode}/{eccubeVersion}/{version}" , name="admin_store_plugin_api_install")
     *
     * @param Application $app
     * @param Request     $request
     * @param string      $pluginCode
     * @param string      $eccubeVersion
     * @param string      $version
     * @return RedirectResponse
     */
    public function apiInstall(Application $app, Request $request, $pluginCode, $eccubeVersion, $version)
    {
        // Check plugin code
        $url = $this->appConfig['package_repo_url'].'/search/packages.json'.'?eccube_version='.$eccubeVersion.'&plugin_code='.$pluginCode.'&version='.$version;
        list($json, $info) = $this->getRequestApi($url, $app);
        $existFlg = false;
        $data = json_decode($json, true);
        if ($data && isset($data['success'])) {
            $success = $data['success'];
            if ($success == '1' && isset($data['item'])) {
                foreach ($data['item'] as $item) {
                    if ($item['product_code'] == $pluginCode) {
                        $existFlg = true;
                        break;
                    }
                }
            }
        }
        if ($existFlg === false) {
            log_info(sprintf('%s plugin not found!', $pluginCode));
            $app->addError('admin.plugin.not.found', 'admin');

            return $app->redirect($app->url('admin_store_plugin_owners_search'));
        }
        $dependents = array();
        $items = $data['item'];
        $plugin = $this->pluginService->buildInfo($items, $pluginCode);
        $dependents[] = $plugin;
        $dependents = $this->pluginService->getDependency($items, $plugin, $dependents);

        // Unset first param
        unset($dependents[0]);
        $dependentModifier = [];
        $packageNames = '';
        if (!empty($dependents)) {
            foreach ($dependents as $item) {
                $packageNames .= self::$vendorName . '/' . $item['product_code'] . ' ';
                $pluginItem = [
                    "product_code" => $item['product_code'],
                    "version" => $item['version']
                ];
                array_push($dependentModifier, $pluginItem);
            }
        }
        $packageNames .= self::$vendorName . '/' . $pluginCode;
        $return = $this->composerService->execRequire($packageNames);
        $data = array(
            'code' => $pluginCode,
            'version' => $version,
            'core_version' => $eccubeVersion,
            'php_version' => phpversion(),
            'db_version' => $this->systemService->getDbversion(),
            'os' => php_uname('s') . ' ' . php_uname('r') . ' ' . php_uname('v'),
            'host' => $request->getHost(),
            'web_server' => $request->server->get("SERVER_SOFTWARE"),
            'composer_version' => $this->composerService->composerVersion(),
            'composer_execute_mode' => $this->composerService->getMode(),
            'dependents' => json_encode($dependentModifier)
        );
        if ($return) {
            $url = $this->appConfig['package_repo_url'] . '/report';
            $this->postRequestApi($url, $app, $data);
            $app->addSuccess('admin.plugin.install.complete', 'admin');

            return $app->redirect($app->url('admin_store_plugin'));
        }
        $url = $this->appConfig['package_repo_url'] . '/report/fail';
        $this->postRequestApi($url, $app, $data);
        $app->addError('admin.plugin.install.fail', 'admin');

        return $app->redirect($app->url('admin_store_plugin_owners_search'));
    }

    /**
     * Do confirm page
     *
     * @Route("/{_admin}/store/plugin/delete/{id}/confirm", requirements={"id" = "\d+"}, name="admin_store_plugin_delete_confirm")
     * @Template("Store/plugin_confirm_uninstall.twig")
     * @param Application $app
     * @param Plugin      $Plugin
     * @return array|RedirectResponse
     */
    public function deleteConfirm(Application $app, Plugin $Plugin)
    {
        // Owner's store communication
        $url = $this->appConfig['package_repo_url'].'/search/packages.json';
        list($json, $info) = $this->getRequestApi($url, $app);
        $data = json_decode($json, true);
        $items = $data['item'];

        // The plugin depends on it
        $pluginCode = $Plugin->getCode();
        $otherDepend = $this->pluginService->findDependentPlugin($pluginCode);

        if (!empty($otherDepend)) {
            $DependPlugin = $this->pluginRepository->findOneBy(['code' => $otherDepend[0]]);
            $dependName = $otherDepend[0];
            if ($DependPlugin) {
                $dependName = $DependPlugin->getName();
            }

            $message = $app->trans('admin.plugin.uninstall.depend', ['%name%' => $Plugin->getName(), '%depend_name%' => $dependName]);
            $app->addError($message, 'admin');

            return $app->redirect($app->url('admin_store_plugin'));
        }

        // Check plugin in api
        $pluginSource = $Plugin->getSource();
        $index = array_search($pluginSource, array_column($items, 'product_id'));
        if ($index === false) {
            throw new NotFoundHttpException();
        }

        // Build info
        $pluginCode = $Plugin->getCode();
        $plugin = $this->pluginService->buildInfo($items, $pluginCode);
        $plugin['id'] = $Plugin->getId();

        return [
            'item' => $plugin,
        ];
    }

    /**
     * New ways to remove plugin: using composer command
     *
     * @Method("DELETE")
     * @Route("/{_admin}/store/plugin/api/{id}/uninstall", requirements={"id" = "\d+"}, name="admin_store_plugin_api_uninstall")
     * @param Application $app
     * @param Plugin      $Plugin
     * @return RedirectResponse
     */
    public function apiUninstall(Application $app, Plugin $Plugin)
    {
        $this->isTokenValid($app);

        if ($Plugin->isEnable()) {
            $this->pluginService->disable($Plugin);
        }
        $pluginCode = $Plugin->getCode();
        $packageName = self::$vendorName.'/'.$pluginCode;
        $return = $this->composerService->execRemove($packageName);
        if ($return) {
            $app->addSuccess('admin.plugin.uninstall.complete', 'admin');
        } else {
            $app->addError('admin.plugin.uninstall.error', 'admin');
        }

        return $app->redirect($app->url('admin_store_plugin'));
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

        log_info('http get_info', $info);

        return array($result, $info);
    }

    /**
     * API post request processing
     *
     * @param string  $url
     * @param Application $app
     * @param array $data
     * @return array
     */
    private function postRequestApi($url, $app, $data)
    {
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        $result = curl_exec($curl);
        $info = curl_getinfo($curl);
        $message = curl_error($curl);
        $info['message'] = $message;
        curl_close($curl);

        log_info('http post_info', $info);
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
