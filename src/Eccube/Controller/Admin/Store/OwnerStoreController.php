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

namespace Eccube\Controller\Admin\Store;

use Eccube\Common\Constant;
use Eccube\Controller\AbstractController;
use Eccube\Entity\Master\PageMax;
use Eccube\Entity\Plugin;
use Eccube\Form\Type\Admin\SearchPluginApiType;
use Eccube\Repository\PluginRepository;
use Eccube\Service\Composer\ComposerApiService;
use Eccube\Service\Composer\ComposerProcessService;
use Eccube\Service\Composer\ComposerServiceInterface;
use Eccube\Service\PluginApiService;
use Eccube\Service\PluginService;
use Eccube\Service\SystemService;
use Eccube\Util\FormUtil;
use Knp\Component\Pager\Paginator;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/%eccube_admin_route%/store/plugin/api")
 */
class OwnerStoreController extends AbstractController
{
    /**
     * @var PluginRepository
     */
    protected $pluginRepository;

    /**
     * @var PluginService
     */
    protected $pluginService;

    /**
     * @var ComposerServiceInterface
     */
    protected $composerService;

    /**
     * @var SystemService
     */
    protected $systemService;

    /**
     * @var PluginApiService
     */
    protected $pluginApiService;

    private static $vendorName = 'ec-cube';

    /**
     * OwnerStoreController constructor.
     *
     * @param PluginRepository $pluginRepository
     * @param PluginService $pluginService
     * @param ComposerProcessService $composerProcessService
     * @param ComposerApiService $composerApiService
     * @param SystemService $systemService
     * @param PluginApiService $pluginApiService
     */
    public function __construct(
        PluginRepository $pluginRepository,
        PluginService $pluginService,
        ComposerProcessService $composerProcessService,
        ComposerApiService $composerApiService,
        SystemService $systemService,
        PluginApiService $pluginApiService
    ) {
        $this->pluginRepository = $pluginRepository;
        $this->pluginService = $pluginService;
        $this->systemService = $systemService;
        $this->pluginApiService = $pluginApiService;

        // TODO: Check the flow of the composer service below
        $memoryLimit = $this->systemService->getMemoryLimit();
        if ($memoryLimit == -1 or $memoryLimit >= $this->eccubeConfig['eccube_composer_memory_limit']) {
            $this->composerService = $composerApiService;
        } else {
            $this->composerService = $composerProcessService;
        }
    }

    /**
     * Owner's Store Plugin Installation Screen - Search function
     *
     * @Route("/search", name="admin_store_plugin_owners_search")
     * @Route("/search/page/{page_no}", name="admin_store_plugin_owners_search_page", requirements={"page_no" = "\d+"})
     * @Template("@admin/Store/plugin_search.twig")
     *
     * @param Request     $request
     * @param int $page_no
     * @param Paginator $paginator
     *
     * @return array
     */
    public function search(Request $request, $page_no = null, Paginator $paginator)
    {
        // Acquire downloadable plug-in information from owners store
        $items = [];
        $message = '';
        $total = 0;
        $category = [];

        list($json, $info) = $this->pluginApiService->getCategory();
        if (!empty($json)) {
            $data = json_decode($json, true);
            $category = array_column($data, 'name', 'id');
        }

        // build form with master data
        $builder = $this->formFactory
            ->createBuilder(SearchPluginApiType::class, null, ['category' => $category]);
        $searchForm = $builder->getForm();

        $searchForm->handleRequest($request);
        $searchData = $searchForm->getData();
        if ($searchForm->isSubmitted()) {
            if ($searchForm->isValid()) {
                $page_no = 1;
                $searchData = $searchForm->getData();
                $this->session->set('eccube.admin.plugin_api.search', FormUtil::getViewData($searchForm));
                $this->session->set('eccube.admin.plugin_api.search.page_no', $page_no);
            }
        } else {
            // quick search
            if (is_numeric($categoryId = $request->get('category_id')) && array_key_exists($categoryId, $category)) {
                $searchForm['category_id']->setData($categoryId);
            }
            // reset page count
            $this->session->set('eccube.admin.plugin_api.search.page_count', $this->eccubeConfig->get('eccube_default_page_count'));
            if (null !== $page_no || $request->get('resume')) {
                if ($page_no) {
                    $this->session->set('eccube.admin.plugin_api.search.page_no', (int) $page_no);
                } else {
                    $page_no = $this->session->get('eccube.admin.plugin_api.search.page_no', 1);
                }
                $viewData = $this->session->get('eccube.admin.plugin_api.search', []);
                $searchData = FormUtil::submitAndGetData($searchForm, $viewData);
            } else {
                $page_no = 1;
                // submit default value
                $viewData = FormUtil::getViewData($searchForm);
                $searchData = FormUtil::submitAndGetData($searchForm, $viewData);
                $this->session->set('eccube.admin.plugin_api.search', $searchData);
                $this->session->set('eccube.admin.plugin_api.search.page_no', $page_no);
            }
        }

        // set page count
        $pageCount = $this->session->get('eccube.admin.plugin_api.search.page_count', $this->eccubeConfig->get('eccube_default_page_count'));
        if (($PageMax = $searchForm['page_count']->getData()) instanceof PageMax) {
            $pageCount = $PageMax->getId();
            $this->session->set('eccube.admin.plugin_api.search.page_count', $pageCount);
        }

        // Owner's store communication
        $searchData['page_no'] = $page_no;
        $searchData['page_count'] = $pageCount;
        list($json, $info) = $this->pluginApiService->getPlugins($searchData);
        if (empty($json)) {
            $message = $this->pluginApiService->getResponseErrorMessage($info);
        } else {
            $data = json_decode($json, true);
            $total = $data['total'];
            if (isset($data['plugins']) && count($data['plugins']) > 0) {
                // Check plugin installed
                $pluginInstalled = $this->pluginRepository->findAll();
                // Update_status 1 : not install/purchased 、2 : Installed、 3 : Update、4 : not purchased
                foreach ($data['plugins'] as $item) {
                    // Not install/purchased
                    $item['update_status'] = 1;
                    /** @var Plugin $plugin */
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
                    $items[] = $item;
                }

                // EC-CUBE version check
                foreach ($items as &$item) {
                    // Not applicable version
                    $item['version_check'] = 0;
                    if (in_array(Constant::VERSION, $item['supported_versions'])) {
                        // Match version
                        $item['version_check'] = 1;
                    }
                    // Add plugin dependency
                    $item['depend'] = $this->pluginService->getRequirePluginName($items, $item);
                }
                unset($item);

                // Todo: news api will remove this?
                // Promotion item
//                $i = 0;
//                foreach ($items as $item) {
//                    if ($item['promotion'] == 1) {
//                        $promotionItems[] = $item;
//                        unset($items[$i]);
//                    }
//                    $i++;
//                }
            } else {
                $message = trans('ownerstore.text.error.ec_cube_error');
            }
        }

        // The usage is set because `$items` are already paged.
        // virtual paging
        $pagination = $paginator->paginate($items, 1, $pageCount);
        $pagination->setTotalItemCount($total);
        $pagination->setCurrentPageNumber($page_no);
        $pagination->setItemNumberPerPage($pageCount);

        return [
            'pagination' => $pagination,
            'total' => $total,
            'searchForm' => $searchForm->createView(),
            'page_no' => $page_no,
            'message' => $message,
            'Categories' => $category
        ];
    }

    /**
     * Do confirm page
     *
     * @Route("/install/{id}/confirm", requirements={"id" = "\d+"}, name="admin_store_plugin_install_confirm")
     * @Template("@admin/Store/plugin_confirm.twig")
     *
     * @param Request $request
     * @param string $id
     *
     * @return array
     */
    public function doConfirm(Request $request, $id)
    {
        // Owner's store communication
        $url = $this->eccubeConfig['eccube_package_repo_url'].'/search/packages.json';
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
     * @Route("/install", name="admin_store_plugin_api_install")
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function apiInstall(Request $request)
    {
        $pluginCode = $request->get('pluginCode');
        $eccubeVersion = $request->get('eccubeVersion');
        $version = $request->get('version');

        // Check plugin code
        $url = $this->eccubeConfig['eccube_package_repo_url'].'/search/packages.json'.'?eccube_version='.$eccubeVersion.'&plugin_code='.$pluginCode.'&version='.$version;
        list($json, $info) = $this->getRequestApi($url);
        $existFlg = false;
        $data = json_decode($json, true);
        if (isset($data['item']) && !empty($data['item'])) {
            $existFlg = $this->pluginService->checkPluginExist($data['item'], $pluginCode);
        }
        if ($existFlg === false) {
            log_info(sprintf('%s plugin not found!', $pluginCode));
            $this->addError('admin.plugin.not.found', 'admin');

            return $this->redirectToRoute('admin_store_plugin_owners_search');
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
            $url = $this->eccubeConfig['eccube_package_repo_url'].'/report';
            $this->postRequestApi($url, $data);
            $this->addSuccess('admin.plugin.install.complete', 'admin');

            return $this->redirectToRoute('admin_store_plugin');
        } catch (\Exception $exception) {
            log_info($exception);
        }

        // Do report to package repo
        $url = $this->eccubeConfig['eccube_package_repo_url'].'/report/fail';
        $this->postRequestApi($url, $data);
        $this->addError('admin.plugin.install.fail', 'admin');

        return $this->redirectToRoute('admin_store_plugin_owners_search');
    }

    /**
     * Do confirm page
     *
     * @Route("/delete/{id}/confirm", requirements={"id" = "\d+"}, name="admin_store_plugin_delete_confirm")
     * @Template("@admin/Store/plugin_confirm_uninstall.twig")
     *
     * @param Plugin $Plugin
     *
     * @return array|RedirectResponse
     */
    public function deleteConfirm(Plugin $Plugin)
    {
        // Owner's store communication
        $url = $this->eccubeConfig['eccube_package_repo_url'].'/search/packages.json';
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
            $this->addError($message, 'admin');

            return $this->redirectToRoute('admin_store_plugin');
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
     * @Route("/delete/{id}/uninstall", requirements={"id" = "\d+"}, name="admin_store_plugin_api_uninstall", methods={"DELETE"})
     *
     * @param Plugin $Plugin
     *
     * @return RedirectResponse
     */
    public function apiUninstall(Plugin $Plugin)
    {
        $this->isTokenValid();

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
     * @Route("/upgrade/{pluginCode}/{version}", name="admin_store_plugin_api_upgrade", methods={"PUT"})
     *
     * @param string $pluginCode
     * @param string $version
     *
     * @return RedirectResponse
     */
    public function apiUpgrade($pluginCode, $version)
    {
        $this->isTokenValid();
        // Run install plugin
        $this->forward($this->generateUrl('admin_store_plugin_api_install', ['pluginCode' => $pluginCode, 'eccubeVersion' => Constant::VERSION, 'version' => $version]));

        if ($this->session->getFlashBag()->has('eccube.admin.error')) {
            $this->session->getFlashBag()->clear();
            $this->addError('admin.plugin.update.error', 'admin');

            return $this->redirectToRoute('admin_store_plugin');
        }
        $this->session->getFlashBag()->clear();
        $this->addSuccess('admin.plugin.update.complete', 'admin');

        return $this->redirectToRoute('admin_store_plugin');
    }

    /**
     * Do confirm update page
     *
     * @Route("/upgrade/{id}/confirm", requirements={"id" = "\d+"}, name="admin_store_plugin_update_confirm")
     * @Template("@admin/Store/plugin_confirm.twig")
     *
     * @param Plugin $plugin
     *
     * @return Response
     */
    public function doUpdateConfirm(Plugin $plugin)
    {
        $source = $plugin->getSource();
        $url = $this->generateUrl('admin_store_plugin_install_confirm', ['id' => $source, 'is_update' => true]);

        return $this->forward($url);
    }

    /**
     * API request processing
     *
     * @param string $url
     *
     * @return array
     * @deprecated since release, please preference PluginApiService
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
     * @param string $url
     * @param array $data
     *
     * @return array
     * @deprecated since release, please preference PluginApiService
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
}
