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

namespace Eccube\Controller\Admin\Content;

use Eccube\Controller\AbstractController;
use Eccube\Entity\Faq;
use Eccube\Event\EccubeEvents;
use Eccube\Event\EventArgs;
use Eccube\Form\Type\Admin\FaqType;
use Eccube\Repository\FaqRepository;
use Eccube\Util\CacheUtil;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bridge\Twig\Attribute\Template;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;

class FaqController extends AbstractController
{
    public function __construct(
        protected FaqRepository $faqRepository,
        private readonly PaginatorInterface $paginator,
        private readonly CacheUtil $cacheUtil,
    ) {
    }

    /**
     * FAQ一覧を表示する。
     *
     * 商品ごと・カテゴリごとのFAQは各編集画面を開かないと存在が分からないため、
     * この一覧は3区分を横断して表示し、区分での絞り込みを提供する。
     *
     * @param int $page_no
     *
     * @return array<string, mixed>
     */
    #[Route(path: '/%eccube_admin_route%/content/faq', name: 'admin_content_faq', methods: ['GET'])]
    #[Route(path: '/%eccube_admin_route%/content/faq/page/{page_no}', name: 'admin_content_faq_page', requirements: ['page_no' => '\d+'], methods: ['GET'])]
    #[Template(template: '@admin/Content/faq.twig')]
    public function index(Request $request, $page_no = 1): array
    {
        $faqType = $this->resolveFaqType($request);
        $qb = $this->faqRepository->getQueryBuilderAll($faqType);

        $event = new EventArgs(
            [
                'qb' => $qb,
            ],
            $request
        );
        $this->eventDispatcher->dispatch($event, EccubeEvents::ADMIN_CONTENT_FAQ_INDEX_INITIALIZE);

        $pagination = $this->paginator->paginate(
            $qb,
            $page_no,
            $this->eccubeConfig->get('eccube_default_page_count')
        );

        return [
            'pagination' => $pagination,
            'faqType' => $faqType,
        ];
    }

    /**
     * リクエストの区分絞り込みパラメータを Faq::FAQ_TYPE_* に解決する。
     *
     * 未指定・不正値はすべて「絞り込みなし」として扱う。
     */
    private function resolveFaqType(Request $request): ?string
    {
        $faqType = $request->query->all()['faq_type'] ?? null;

        if (!is_string($faqType)) {
            return null;
        }

        return in_array($faqType, [Faq::FAQ_TYPE_COMMON, Faq::FAQ_TYPE_PRODUCT, Faq::FAQ_TYPE_CATEGORY], true)
            ? $faqType
            : null;
    }

    /**
     * サイト共通FAQの表示順を並び替える。
     *
     * 一覧は3区分を横断するが、並び替えは同一区分内でしか意味を持たないため対象は
     * サイト共通FAQのみ。商品ごと・カテゴリごとのFAQは各編集画面で並び替える。
     */
    #[Route(path: '/%eccube_admin_route%/content/faq/sort_no/move', name: 'admin_content_faq_sort_no_move', methods: ['POST'])]
    public function moveSortNo(Request $request): Response
    {
        if ($request->isXmlHttpRequest() && $this->isTokenValid()) {
            $sortNos = $request->request->all();
            foreach ($sortNos as $faqId => $sortNo) {
                $Faq = $this->faqRepository->find($faqId);
                if (!$Faq || $Faq->getFaqType() !== Faq::FAQ_TYPE_COMMON) {
                    continue;
                }
                $Faq->setSortNo((int) $sortNo);
                $this->entityManager->persist($Faq);
            }
            $this->entityManager->flush();

            // キャッシュの削除
            $this->cacheUtil->clearDoctrineCache();
        }

        return new Response();
    }

    /**
     * サイト共通FAQを登録・編集する。
     *
     * @param int|null $id
     *
     * @return array<string, mixed>|RedirectResponse
     */
    #[Route(path: '/%eccube_admin_route%/content/faq/new', name: 'admin_content_faq_new', methods: ['GET', 'POST'])]
    #[Route(path: '/%eccube_admin_route%/content/faq/{id}/edit', name: 'admin_content_faq_edit', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    #[Template(template: '@admin/Content/faq_edit.twig')]
    public function edit(Request $request, $id = null): array|RedirectResponse
    {
        if ($id) {
            $Faq = $this->faqRepository->find($id);
            // 専用画面ではサイト共通FAQ（商品・カテゴリに紐付かない）のみ扱う
            if (!$Faq || $Faq->getFaqType() !== Faq::FAQ_TYPE_COMMON) {
                throw new NotFoundHttpException();
            }
        } else {
            $Faq = new Faq();
            // 表示順を空欄で開き、未入力なら FaqRepository::save() の自動採番に任せる。
            // エンティティの既定値 0（NOT NULL 対策）のままだと入力欄に 0 が入り、
            // Assert\Range(min: 1) に掛かって登録できない。
            $Faq->setSortNo(null);
        }

        $builder = $this->formFactory
            ->createBuilder(FaqType::class, $Faq);

        $event = new EventArgs(
            [
                'builder' => $builder,
                'Faq' => $Faq,
            ],
            $request
        );
        $this->eventDispatcher->dispatch($event, EccubeEvents::ADMIN_CONTENT_FAQ_EDIT_INITIALIZE);

        $form = $builder->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->faqRepository->save($Faq);

            $event = new EventArgs(
                [
                    'form' => $form,
                    'Faq' => $Faq,
                ],
                $request
            );
            $this->eventDispatcher->dispatch($event, EccubeEvents::ADMIN_CONTENT_FAQ_EDIT_COMPLETE);

            $this->addSuccess('admin.common.save_complete', 'admin');

            // キャッシュの削除
            $this->cacheUtil->clearDoctrineCache();

            return $this->redirectToRoute('admin_content_faq_edit', ['id' => $Faq->getId()]);
        }

        return [
            'form' => $form->createView(),
            'Faq' => $Faq,
        ];
    }

    /**
     * 指定したサイト共通FAQを削除する。
     */
    #[Route(path: '/%eccube_admin_route%/content/faq/{id}/delete', name: 'admin_content_faq_delete', requirements: ['id' => '\d+'], methods: ['DELETE'])]
    public function delete(Request $request, Faq $Faq): RedirectResponse
    {
        $this->isTokenValid();

        if ($Faq->getFaqType() !== Faq::FAQ_TYPE_COMMON) {
            throw new NotFoundHttpException();
        }

        log_info('FAQ削除開始', [$Faq->getId()]);

        try {
            $this->faqRepository->delete($Faq);

            $event = new EventArgs(['Faq' => $Faq], $request);
            $this->eventDispatcher->dispatch($event, EccubeEvents::ADMIN_CONTENT_FAQ_DELETE_COMPLETE);

            $this->addSuccess('admin.common.delete_complete', 'admin');

            log_info('FAQ削除完了', [$Faq->getId()]);

            // キャッシュの削除
            $this->cacheUtil->clearDoctrineCache();
        } catch (\Exception $e) {
            $message = trans('admin.common.delete_error_foreign_key', ['%name%' => $Faq->getQuestion()]);
            $this->addError($message, 'admin');

            log_error('FAQ削除エラー', [$Faq->getId(), $e]);
        }

        return $this->redirectToRoute('admin_content_faq');
    }
}
