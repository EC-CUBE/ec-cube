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
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @Route("/{_admin}/store/plugin/api", service=OwnerStoreController::class)
 */
class OwnerStoreController extends AbstractController
{
    /**
     * @Inject("config")
     *
     * @var array
     */
    protected $eccubeConfig;

    /**
     * @Inject(PluginRepository::class)
     *
     * @var PluginRepository
     */
    protected $pluginRepository;

    /**
     * @Inject(PluginService::class)
     *
     * @var PluginService
     */
    protected $pluginService;

    /**
     * @Inject("eccube.service.composer")
     *
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
     *
     * @var SystemService
     */
    protected $systemService;

    private static $vendorName = 'ec-cube';

    /**
     * Owner's Store Plugin Installation Screen - Search function
     *
     * @Route("/search", name="admin_store_plugin_owners_search")
     * @Template("Store/plugin_search.twig")
     *
     * @param Application $app
     * @param Request     $request
     *
     * @return array
     */
    public function search(Application $app, Request $request)
    {
        // Acquire downloadable plug-in information from owners store
        $items = [];
        $promotionItems = [];
        $message = '';
        // Owner's store communication
        $url = $this->eccubeConfig['package_repo_url'].'/search/packages.json';
        list($json, $info) = $this->getRequestApi($url);
        if ($json === false) {
            $message = $this->getResponseErrorMessage($info);
        } else {
            $data = json_decode($json, true);
            if (isset($data['success']) && $data['success']) {
                // Check plugin installed
                $pluginInstalled = $this->pluginRepository->findAll();
                // Update_status 1 : not install/purchased 、2 : Installed、 3 : Update、4 : paid purchase
                foreach ($data['item'] as $item) {
                    // Not install/purchased
                    $item['update_status'] = 1;
                    /** @var Plugin $plugin */
                    foreach ($pluginInstalled as $plugin) {
                        if ($plugin->getSource() == $item['product_id']) {
                            // Installed
                            $item['update_status'] = 2;
                            if ($this->pluginService->isUpdate($plugin->getVersion(), $item['version'])) {
                                // Need update
                                $item['update_status'] = 3;
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
                $message = trans('ownerstore.text.error.ec_cube_error');
            }
        }

        return [
            'items' => $items,
            'promotionItems' => $promotionItems,
            'message' => $message,
        ];
    }

    /**
     * Do confirm page
     *
     * @Route("/install/{id}/confirm", requirements={"id" = "\d+"}, name="admin_store_plugin_install_confirm")
     * @Template("Store/plugin_confirm.twig")
     *
     * @param Application $app
     * @param Request     $request
     * @param string      $id
     *
     * @return array
     */
    public function doConfirm(Application $app, Request $request, $id)
    {
        // Owner's store communication
        $url = $this->eccubeConfig['package_repo_url'].'/search/packages.json';
        list($json, $info) = $this->getRequestApi($url);
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
        $dependents[] = $plugin;
        $dependents = $this->pluginService->getDependency($items, $plugin, $dependents);
        // Unset first param
        unset($dependents[0]);

        return [
            'item' => $plugin,
            'dependents' => $dependents,
            'is_update' => $request->get('is_update', false),
        ];
    }

    /**
     * Api Install plugin by composer connect with package repo
     *
     * @Route("/install/{pluginCode}/{eccubeVersion}/{version}" , name="admin_store_plugin_api_install")
     *
     * @param Application $app
     * @param Request     $request
     * @param string      $pluginCode
     * @param string      $eccubeVersion
     * @param string      $version
     *
     * @return RedirectResponse
     */
    public function apiInstall(Application $app, Request $request, $pluginCode, $eccubeVersion, $version)
    {
        // Check plugin code
        $url = $this->eccubeConfig['package_repo_url'].'/search/packages.json'.'?eccube_version='.$eccubeVersion.'&plugin_code='.$pluginCode.'&version='.$version;
        list($json, $info) = $this->getRequestApi($url);
        $existFlg = false;
        $data = json_decode($json, true);
        if (isset($data['item']) && !empty($data['item'])) {
            $existFlg = $this->pluginService->checkPluginExist($data['item'], $pluginCode);
        }
        if ($existFlg === false) {
            log_info(sprintf('%s plugin not found!', $pluginCode));
            $app->addError('admin.plugin.not.found', 'admin');

            return $app->redirect($app->url('admin_store_plugin_owners_search'));
        }

        $items = $data['item'];
        $plugin = $this->pluginService->buildInfo($items, $pluginCode);
        $dependents[] = $plugin;
        $dependents = $this->pluginService->getDependency($items, $plugin, $dependents);
        // Unset first param
        unset($dependents[0]);
        $dependentModifier = [];
        $packageNames = '';
        if (!empty($dependents)) {
            foreach ($dependents as $key => $item) {
                $pluginItem = [
                    'product_code' => $item['product_code'],
                    'version' => $item['version'],
                ];
                array_push($dependentModifier, $pluginItem);
                // Re-format plugin code
                $dependents[$key]['product_code'] = self::$vendorName.'/'.$item['product_code'];
            }
            $packages = array_column($dependents, 'version', 'product_code');
            $packageNames = $this->pluginService->parseToComposerCommand($packages);
        }
        $packageNames .= ' '.self::$vendorName.'/'.$pluginCode.':'.$version;
        $data = [
            'code' => $pluginCode,
            'version' => $version,
            'core_version' => $eccubeVersion,
            'php_version' => phpversion(),
            'db_version' => $this->systemService->getDbversion(),
            'os' => php_uname('s').' '.php_uname('r').' '.php_uname('v'),
            'host' => $request->getHost(),
            'web_server' => $request->server->get('SERVER_SOFTWARE'),
            'composer_version' => $this->composerService->composerVersion(),
            'composer_execute_mode' => $this->composerService->getMode(),
            'dependents' => json_encode($dependentModifier),
        ];

        try {
            $this->composerService->execRequire($packageNames);
            // Do report to package repo
            $url = $this->eccubeConfig['package_repo_url'].'/report';
            $this->postRequestApi($url, $data);
            $app->addSuccess('admin.plugin.install.complete', 'admin');

            return $app->redirect($app->url('admin_store_plugin'));
        } catch (\Exception $exception) {
            log_info($exception);
        }

        // Do report to package repo
        $url = $this->eccubeConfig['package_repo_url'].'/report/fail';
        $this->postRequestApi($url, $data);
        $app->addError('admin.plugin.install.fail', 'admin');

        return $app->redirect($app->url('admin_store_plugin_owners_search'));
    }

    /**
     * Do confirm page
     *
     * @Route("/delete/{id}/confirm", requirements={"id" = "\d+"}, name="admin_store_plugin_delete_confirm")
     * @Template("Store/plugin_confirm_uninstall.twig")
     *
     * @param Application $app
     * @param Plugin      $Plugin
     *
     * @return array|RedirectResponse
     */
    public function deleteConfirm(Application $app, Plugin $Plugin)
    {
        // Owner's store communication
        $url = $this->eccubeConfig['package_repo_url'].'/search/packages.json';
        list($json, $info) = $this->getRequestApi($url);
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
            $message = trans('admin.plugin.uninstall.depend', ['%name%' => $Plugin->getName(), '%depend_name%' => $dependName]);
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
     * @Route("/delete/{id}/uninstall", requirements={"id" = "\d+"}, name="admin_store_plugin_api_uninstall")
     *
     * @param Application $app
     * @param Plugin      $Plugin
     *
     * @return RedirectResponse
     */
    public function apiUninstall(Application $app, Plugin $Plugin)
    {
        $this->isTokenValid($app);

        if ($Plugin->isEnabled()) {
            $this->addError('admin.plugin.uninstall.error.not_disable', 'admin');

            return $this->redirectToRoute('admin_store_plugin');
        }

        $pluginCode = $Plugin->getCode();
        $packageName = self::$vendorName.'/'.$pluginCode;
        try {
            $this->composerService->execRemove($packageName);
            $this->addSuccess('admin.plugin.uninstall.complete', 'admin');
        } catch (\Exception $exception) {
            log_info($exception);
            $this->addError('admin.plugin.uninstall.error', 'admin');
        }

        return $this->redirectToRoute('admin_store_plugin');
    }

    /**
     * オーナーズブラグインインストール、アップデート
     *
     * @Method("PUT")
     * @Route("/upgrade/{pluginCode}/{version}", name="admin_store_plugin_api_upgrade")
     *
     * @param Application $app
     * @param string      $pluginCode
     * @param string      $version
     *
     * @return RedirectResponse
     */
    public function apiUpgrade(Application $app, $pluginCode, $version)
    {
        $this->isTokenValid($app);
        // Run install plugin
        $app->forward($app->url('admin_store_plugin_api_install', ['pluginCode' => $pluginCode, 'eccubeVersion' => Constant::VERSION, 'version' => $version]));

        /** @var Session $session */
        $session = $app['session'];
        if ($session->getFlashBag()->has('eccube.admin.error')) {
            $session->getFlashBag()->clear();
            $app->addError('admin.plugin.update.error', 'admin');

            return $app->redirect($app->url('admin_store_plugin'));
        }
        $session->getFlashBag()->clear();
        $app->addSuccess('admin.plugin.update.complete', 'admin');

        return $app->redirect($app->url('admin_store_plugin'));
    }

    /**
     * Do confirm update page
     *
     * @Route("/upgrade/{id}/confirm", requirements={"id" = "\d+"}, name="admin_store_plugin_update_confirm")
     * @Template("Store/plugin_confirm.twig")
     *
     * @param Application $app
     * @param Plugin      $plugin
     *
     * @return Response
     */
    public function doUpdateConfirm(Application $app, Plugin $plugin)
    {
        $source = $plugin->getSource();
        $url = $app->url('admin_store_plugin_install_confirm', ['id' => $source, 'is_update' => true]);

        return $app->forward($url);
    }

    /**
     * API request processing
     *
     * @param string  $url
     *
     * @return array
     */
    private function getRequestApi($url)
    {
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
     * API post request processing
     *
     * @param string  $url
     * @param array $data
     *
     * @return array
     */
    private function postRequestApi($url, $data)
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

        return [$result, $info];
    }

    /**
     * Get message
     *
     * @param $info
     *
     * @return string
     */
    private function getResponseErrorMessage($info)
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
