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
use Doctrine\ORM\EntityManagerInterface;
use Eccube\Annotation\Inject;
use Eccube\Application;
use Eccube\Common\Constant;
use Eccube\Controller\AbstractController;
use Eccube\Entity\Plugin;
use Eccube\Repository\PluginRepository;
use Eccube\Service\PluginService;
use Eccube\Service\ComposerProcessService;
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
     * @Inject(ComposerProcessService::class)
     * @var ComposerProcessService
     */
    protected $composerService;

    /**
     * @var EntityManager
     * @Inject("orm.em")
     */
    protected $em;

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
                        $item['depend'] = $app['eccube.service.plugin']->getRequirePluginName($items, $item);
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
        $url = $this->appConfig['owners_store_url'].'?method=list';
        list($json, $info) = $this->getRequestApi($url, $app);
        $data = json_decode($json, true);
        $items = $data['item'];

        // Find plugin in api
        $index = array_search($id, array_column($items, 'product_id'));
        if ($index === false) {
            throw new NotFoundHttpException();
        }

        $pluginCode = $items[$index]['product_code'];

        /**
         * @var PluginService $pluginService
         */
        $pluginService =  $app['eccube.service.plugin'];
        $plugin = $pluginService->buildInfo($items, $pluginCode);

        // Prevent infinity loop: A -> B -> A.
        $arrDependency[] = $plugin;
        $arrDependency = $pluginService->getDependency($items, $plugin, $arrDependency);
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
        $url = $this->appConfig['owners_store_url'].'?eccube_version='.$eccubeVersion.'&plugin_code='.$pluginCode.'&version='.$version;
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

        /**
         * Mysql lock in transaction
         * @link https://dev.mysql.com/doc/refman/5.7/en/lock-tables.html
         * @var EntityManagerInterface $em
         */
        $em = $this->em;
        if ($em->getConnection()->isTransactionActive()) {
            $em->getConnection()->commit();
            $em->getConnection()->beginTransaction();
        }

        $return = $this->composerService->execRequire($pluginCode);
        if ($return) {
            $app->addSuccess('admin.plugin.install.complete', 'admin');

            return $app->redirect($app->url('admin_store_plugin'));
        }
        $app->addError('admin.plugin.install.fail', 'admin');

        return $app->redirect($app->url('admin_store_plugin_owners_search'));
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

        if ($Plugin->getEnable() == Constant::ENABLED) {
            $app->addError('admin.plugin.uninstall.error.not_disable', 'admin');

            return $app->redirect($app->url('admin_store_plugin'));
        }

        $pluginCode = $Plugin->getCode();


        /**
         * Mysql lock in transaction
         * @link https://dev.mysql.com/doc/refman/5.7/en/lock-tables.html
         * @var EntityManagerInterface $em
         */
        $em = $this->em;
        if ($em->getConnection()->isTransactionActive()) {
            $em->getConnection()->commit();
            $em->getConnection()->beginTransaction();
        }

        $return = $this->composerService->execRemove($pluginCode);
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
