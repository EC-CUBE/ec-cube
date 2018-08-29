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
use Eccube\Entity\BaseInfo;
use Eccube\Entity\Master\PageMax;
use Eccube\Entity\Plugin;
use Eccube\Form\Type\Admin\SearchPluginApiType;
use Eccube\Repository\BaseInfoRepository;
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

    /** @var BaseInfo */
    private $BaseInfo;

    /**
     * OwnerStoreController constructor.
     *
     * @param PluginRepository $pluginRepository
     * @param PluginService $pluginService
     * @param ComposerProcessService $composerProcessService
     * @param ComposerApiService $composerApiService
     * @param SystemService $systemService
     * @param PluginApiService $pluginApiService
     * @param BaseInfoRepository $baseInfoRepository
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function __construct(
        PluginRepository $pluginRepository,
        PluginService $pluginService,
        ComposerProcessService $composerProcessService,
        ComposerApiService $composerApiService,
        SystemService $systemService,
        PluginApiService $pluginApiService,
        BaseInfoRepository $baseInfoRepository
    ) {
        $this->pluginRepository = $pluginRepository;
        $this->pluginService = $pluginService;
        $this->systemService = $systemService;
        $this->pluginApiService = $pluginApiService;
        $this->BaseInfo = $baseInfoRepository->get();

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

        if (!$this->BaseInfo->getAuthenticationKey()) {
            $this->addWarning('認証キーを設定してください。', 'admin');
            return $this->redirectToRoute('admin_store_authentication_setting');
        }

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

                    $item = $this->pluginService->buildInfo($item);
                    $items[] = $item;
                }

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
            'Categories' => $category,
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
     * @throws \Eccube\Exception\PluginException
     */
    public function doConfirm(Request $request, $id)
    {
        list($json) = $this->pluginApiService->getPlugin($id);
        $plugin = [];
        if ($json) {
            $data = json_decode($json, true);
            $plugin = $this->pluginService->buildInfo($data);
        }

        if (empty($plugin)) {
            throw new NotFoundHttpException();
        }

        // Todo: need define plugin's dependency mechanism
        $requires = $this->pluginService->getPluginRequired($plugin);

        return [
            'item' => $plugin,
            'requires' => $requires,
            'is_update' => $request->get('is_update', false),
        ];
    }

    /**
     * Api Install plugin by composer connect with package repo
     *
     * @Route("/composer_install", name="admin_store_plugin_api_composer_install", methods={"POST"})
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function composerInstall(Request $request)
    {
        $this->isTokenValid();

        $pluginCode = $request->get('pluginCode');

        $log = null;
        try {
            $log = $this->composerService->execRequire("ec-cube/".$pluginCode);

            return $this->json(['success' => true, 'log' => $log]);
        } catch (\Exception $e) {
            $log = $e->getMessage();
            log_error($e);
        }

        return $this->json(['success' => false, 'log' => $log], 500);
    }


    /**
     * Api Install plugin by composer connect with package repo
     *
     * @Route("/install", name="admin_store_plugin_api_install", methods={"POST"})
     *
     * @param Request $request
     *
     * @return Response
     * @throws \Exception
     */
    public function install(Request $request)
    {
        $this->isTokenValid();

        $pluginCode = $request->get('pluginCode');

        if (!$pluginCode) {
            throw new NotFoundHttpException();
        }

        try {
            $this->pluginService->installWithCode($pluginCode);
            return new Response();
        } catch (\Exception $e) {
            log_error($e);
            throw $e;
        }

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
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function apiUninstall(Plugin $Plugin)
    {
        $this->isTokenValid();

        if ($Plugin->isEnabled()) {
            return $this->json(['success' => false, 'message' => trans('admin.plugin.uninstall.error.not_disable')], 400);
        }

        $pluginCode = $Plugin->getCode();
        $otherDepend = $this->pluginService->findDependentPlugin($pluginCode);

        if (!empty($otherDepend)) {
            $DependPlugin = $this->pluginRepository->findOneBy(['code' => $otherDepend[0]]);
            $dependName = $otherDepend[0];
            if ($DependPlugin) {
                $dependName = $DependPlugin->getName();
            }
            $message = trans('admin.plugin.uninstall.depend', ['%name%' => $Plugin->getName(), '%depend_name%' => $dependName]);

            return $this->json(['success' => false, 'message' => $message], 400);
        }

        $pluginCode = $Plugin->getCode();
        $packageName = self::$vendorName.'/'.$pluginCode;
        try {
            $log = $this->composerService->execRemove($packageName);
            return $this->json(['success' => false, 'log' => $log]);
        } catch (\Exception $e) {
            log_error($e);
            return $this->json(['success' => false, 'log' => $e->getMessage()], 500);
        }

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

        return $this->forwardToRoute('admin_store_plugin_install_confirm', ['id' => $source, 'is_update' => true]);
    }

    /**
     * API request processing
     *
     * @param string $url
     *
     * @return array
     *
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
     *
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
