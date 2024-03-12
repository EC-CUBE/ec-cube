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

namespace Eccube\Controller\Admin\Store;

use Eccube\Controller\AbstractController;
use Eccube\Entity\BaseInfo;
use Eccube\Entity\Master\PageMax;
use Eccube\Entity\Plugin;
use Eccube\Exception\PluginApiException;
use Eccube\Form\Type\Admin\SearchPluginApiType;
use Eccube\Repository\BaseInfoRepository;
use Eccube\Repository\PluginRepository;
use Eccube\Service\Composer\ComposerServiceInterface;
use Eccube\Service\PluginApiService;
use Eccube\Service\PluginService;
use Eccube\Service\SystemService;
use Eccube\Util\CacheUtil;
use Eccube\Util\FormUtil;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
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

    /** @var CacheUtil */
    private $cacheUtil;

    /**
     * OwnerStoreController constructor.
     *
     * @param PluginRepository $pluginRepository
     * @param PluginService $pluginService
     * @param ComposerServiceInterface $composerService
     * @param SystemService $systemService
     * @param PluginApiService $pluginApiService
     * @param BaseInfoRepository $baseInfoRepository
     * @param CacheUtil $cacheUtil
     *
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function __construct(
        PluginRepository $pluginRepository,
        PluginService $pluginService,
        ComposerServiceInterface $composerService,
        SystemService $systemService,
        PluginApiService $pluginApiService,
        BaseInfoRepository $baseInfoRepository,
        CacheUtil $cacheUtil
    ) {
        $this->pluginRepository = $pluginRepository;
        $this->pluginService = $pluginService;
        $this->systemService = $systemService;
        $this->pluginApiService = $pluginApiService;
        $this->BaseInfo = $baseInfoRepository->get();
        $this->cacheUtil = $cacheUtil;

        // TODO: Check the flow of the composer service below
        $this->composerService = $composerService;
    }

    /**
     * Owner's Store Plugin Installation Screen - Search function
     *
     * @Route("/search", name="admin_store_plugin_owners_search", methods={"GET", "POST"})
     * @Route("/search/page/{page_no}", name="admin_store_plugin_owners_search_page", requirements={"page_no" = "\d+"}, methods={"GET", "POST"})
     * @Template("@admin/Store/plugin_search.twig")
     *
     * @param Request     $request
     * @param int $page_no
     * @param PaginatorInterface $paginator
     *
     * @return array
     */
    public function search(Request $request, PaginatorInterface $paginator, $page_no = null)
    {
        if (empty($this->BaseInfo->getAuthenticationKey())) {
            $this->addWarning('admin.store.plugin.search.not_auth', 'admin');

            return $this->redirectToRoute('admin_store_authentication_setting');
        }

        // Acquire downloadable plug-in information from owners store
        $category = [];

        $json = $this->pluginApiService->getCategory();
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

        $total = 0;
        $items = [];

        try {
            $data = $this->pluginApiService->getPlugins($searchData);
            $total = $data['total'];
            $items = $data['plugins'];
        } catch (PluginApiException $e) {
            $this->addError($e->getMessage(), 'admin');
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
            'Categories' => $category,
        ];
    }

    /**
     * Do confirm page
     *
     * @Route("/install/{id}/confirm", requirements={"id" = "\d+"}, name="admin_store_plugin_install_confirm", methods={"GET"})
     * @Template("@admin/Store/plugin_confirm.twig")
     *
     * @param Request $request
     *
     * @return array
     *
     * @throws \Eccube\Exception\PluginException
     */
    public function doConfirm(Request $request, $id)
    {
        try {
            $item = $this->pluginApiService->getPlugin($id);
            // Todo: need define item's dependency mechanism
            $requires = $this->pluginService->getPluginRequired($item);

            return [
                'item' => $item,
                'requires' => $requires,
                'is_update' => false,
            ];
        } catch (PluginApiException $e) {
            $this->addError($e->getMessage(), 'admin');

            return $this->redirectToRoute('admin_store_authentication_setting');
        }
    }

    /**
     * Api Install plugin by composer connect with package repo
     *
     * @Route("/install", name="admin_store_plugin_api_install", methods={"POST"})
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function apiInstall(Request $request)
    {
        $this->isTokenValid();

        // .maintenanceファイルを設置
        $this->systemService->switchMaintenance(true);

        $this->cacheUtil->clearCache();

        $pluginCode = $request->get('pluginCode');

        try {
            $log = $this->composerService->execRequire('ec-cube/'.$pluginCode);

            return $this->json(['success' => true, 'log' => $log]);
        } catch (\Exception $e) {
            $log = $e->getMessage();
            log_error($e);
        }

        return $this->json(['success' => false, 'log' => $log], 500);
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

        // .maintenanceファイルを設置
        $this->systemService->switchMaintenance(true);

        $this->cacheUtil->clearCache();

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
     * @Route("/upgrade", name="admin_store_plugin_api_upgrade", methods={"POST"})
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function apiUpgrade(Request $request)
    {
        $this->isTokenValid();

        $this->cacheUtil->clearCache();

        $pluginCode = $request->get('pluginCode');
        $version = $request->get('version');

        try {
            $log = $this->composerService->execRequire('ec-cube/'.$pluginCode.':'.$version);

            return $this->json(['success' => true, 'log' => $log]);
        } catch (\Exception $e) {
            $log = $e->getMessage();
            log_error($e);
        }

        return $this->json(['success' => false, 'log' => $log], 500);
    }

    /**
     * オーナーズブラグインインストール、スキーマ更新
     *
     * @Route("/schema_update", name="admin_store_plugin_api_schema_update", methods={"POST"})
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function apiSchemaUpdate(Request $request)
    {
        $this->isTokenValid();

        $this->cacheUtil->clearCache();

        $pluginCode = $request->get('pluginCode');

        try {
            $Plugin = $this->pluginRepository->findByCode($pluginCode);

            if (!$Plugin) {
                throw new NotFoundHttpException();
            }

            $config = $this->pluginService->readConfig($this->pluginService->calcPluginDir($Plugin->getCode()));

            ob_start();
            $this->pluginService->generateProxyAndUpdateSchema($Plugin, $config);

            // 初期化されていなければインストール処理を実行する
            if (!$Plugin->isInitialized()) {
                $this->pluginService->callPluginManagerMethod($config, 'install');
                $Plugin->setInitialized(true);
                $this->entityManager->persist($Plugin);
                $this->entityManager->flush();
            }

            $log = ob_get_clean();
            while (ob_get_level() > 0) {
                ob_end_flush();
            }

            return $this->json(['success' => true, 'log' => $log]);
        } catch (\Exception $e) {
            $log = $e->getMessage();
            log_error($e);

            return $this->json(['success' => false, 'log' => $log], 500);
        }
    }

    /**
     * オーナーズブラグインインストール、更新処理
     *
     * @Route("/update", name="admin_store_plugin_api_update", methods={"POST"})
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function apiUpdate(Request $request)
    {
        $this->isTokenValid();

        $this->cacheUtil->clearCache();

        $pluginCode = $request->get('pluginCode');

        try {
            $Plugin = $this->pluginRepository->findByCode($pluginCode);
            if (!$Plugin) {
                throw new NotFoundHttpException();
            }

            $config = $this->pluginService->readConfig($this->pluginService->calcPluginDir($Plugin->getCode()));
            ob_start();
            $this->pluginService->updatePlugin($Plugin, $config);
            $log = ob_get_clean();
            while (ob_get_level() > 0) {
                ob_end_flush();
            }

            return $this->json(['success' => true, 'log' => $log]);
        } catch (\Exception $e) {
            $log = $e->getMessage();
            log_error($e);
        }

        return $this->json(['success' => false, 'log' => $log], 500);
    }

    /**
     * Do confirm update page
     *
     * @Route("/upgrade/{id}/confirm", requirements={"id" = "\d+"}, name="admin_store_plugin_update_confirm", methods={"GET"})
     * @Template("@admin/Store/plugin_confirm.twig")
     *
     * @param Plugin $Plugin
     *
     * @return array
     */
    public function doUpdateConfirm(Plugin $Plugin)
    {
        try {
            $item = $this->pluginApiService->getPlugin($Plugin->getSource());

            return [
                'item' => $item,
                'requires' => [],
                'is_update' => true,
                'Plugin' => $Plugin,
            ];
        } catch (PluginApiException $e) {
            $this->addError($e->getMessage(), 'admin');

            return $this->redirectToRoute('admin_store_authentication_setting');
        }
    }
}
