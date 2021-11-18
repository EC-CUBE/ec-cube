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

namespace Eccube\Controller\Admin\Setting\System;

use Eccube\Controller\AbstractController;
use Eccube\Form\Type\Admin\SearchLoginHistoryType;
use Eccube\Repository\LoginHistoryRepository;
use Eccube\Repository\Master\PageMaxRepository;
use Eccube\Util\FormUtil;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class LoginHistoryController
 */
class LoginHistoryController extends AbstractController
{
    /**
     * @var PageMaxRepository
     */
    protected $pageMaxRepository;

    /**
     * @var LoginHistoryRepository
     */
    protected $loginHistoryRepository;

    /**
     * LoginHistoryController constructor.
     */
    public function __construct(
        PageMaxRepository $pageMaxRepository,
        LoginHistoryRepository $loginHistoryRepository
    ) {
        $this->pageMaxRepository = $pageMaxRepository;
        $this->loginHistoryRepository = $loginHistoryRepository;
    }

    /**
     * ログイン履歴検索画面を表示する.
     * 左ナビゲーションの選択はGETで遷移する.
     *
     * @Route("/%eccube_admin_route%/setting/system/login_history", name="admin_setting_system_login_history", methods={"GET", "POST"})
     * @Route("/%eccube_admin_route%/setting/system/login_history/{page_no}", requirements={"page_no" = "\d+"}, name="admin_setting_system_login_history_page", methods={"GET", "POST"})
     * @Template("@admin/Setting/System/login_history.twig")
     *
     * @param integer $page_no
     *
     * @return \Symfony\Component\HttpFoundation\Response|array
     */
    public function index(Request $request, PaginatorInterface $paginator, $page_no = null)
    {
        $session = $request->getSession();
        $pageNo = $page_no;
        $pageMaxis = $this->pageMaxRepository->findAll();
        $pageCount = $session->get('eccube.admin.login_history.search.page_count', $this->eccubeConfig['eccube_default_page_count']);
        $pageCountParam = $request->get('page_count');
        if ($pageCountParam && is_numeric($pageCountParam)) {
            foreach ($pageMaxis as $pageMax) {
                if ($pageCountParam == $pageMax->getName()) {
                    $pageCount = $pageMax->getName();
                    $session->set('eccube.admin.login_history.search.page_count', $pageCount);
                    break;
                }
            }
        }

        $pagination = null;
        $searchForm = $this->formFactory
            ->createBuilder(SearchLoginHistoryType::class)
            ->getForm();

        if ('POST' === $request->getMethod()) {
            $searchForm->handleRequest($request);
            if ($searchForm->isSubmitted() && $searchForm->isValid()) {
                $searchData = $searchForm->getData();
                $pageNo = 1;
                $session->set('eccube.admin.login_history.search', FormUtil::getViewData($searchForm));
                $session->set('eccube.admin.login_history.search.page_no', $pageNo);
            } else {
                return [
                    'searchForm' => $searchForm->createView(),
                    'pagination' => [],
                    'pageMaxis' => $pageMaxis,
                    'page_no' => $pageNo ? $pageNo : 1,
                    'page_count' => $pageCount,
                    'has_errors' => true,
                ];
            }
        } else {
            if (null !== $pageNo || $request->get('resume')) {
                if ($pageNo) {
                    $session->set('eccube.admin.login_history.search.page_no', (int) $pageNo);
                } else {
                    $pageNo = $session->get('eccube.admin.login_history.search.page_no', 1);
                }
                $viewData = $session->get('eccube.admin.login_history.search', []);
            } else {
                $pageNo = 1;
                $viewData = FormUtil::getViewData($searchForm);
                $session->set('eccube.admin.login_history.search', $viewData);
                $session->set('eccube.admin.login_history.search.page_no', $pageNo);
            }
            $searchData = FormUtil::submitAndGetData($searchForm, $viewData);
        }

        $qb = $this->loginHistoryRepository->getQueryBuilderBySearchDataForAdmin($searchData);
        $pagination = $paginator->paginate(
            $qb,
            $pageNo,
            $pageCount
        );

        return [
            'searchForm' => $searchForm->createView(),
            'pagination' => $pagination,
            'pageMaxis' => $pageMaxis,
            'page_count' => $pageCount,
            'has_errors' => false,
        ];
    }
}
