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

namespace Eccube\Controller\Admin\Content;

use Eccube\Controller\AbstractController;
use Eccube\Entity\News;
use Eccube\Event\EccubeEvents;
use Eccube\Event\EventArgs;
use Eccube\Form\Type\Admin\NewsType;
use Eccube\Repository\NewsRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class NewsController extends AbstractController
{
    /**
     * @var NewsRepository
     */
    protected $newsRepository;

    public function __construct(NewsRepository $newsRepository)
    {
        $this->newsRepository = $newsRepository;
    }

    /**
     * 新着情報一覧を表示する。
     *
     * @Route("/%eccube_admin_route%/content/news", name="admin_content_news")
     * @Template("@admin/Content/news.twig")
     *
     * @param Request $request
     *
     * @return array
     */
    public function index(Request $request)
    {
        $NewsList = $this->newsRepository->findBy([], ['sort_no' => 'DESC']);

        $builder = $this->formFactory->createBuilder();

        $event = new EventArgs(
            [
                'builder' => $builder,
                'NewsList' => $NewsList,
            ],
            $request
        );
        $this->eventDispatcher->dispatch(EccubeEvents::ADMIN_CONTENT_NEWS_INDEX_INITIALIZE, $event);

        $form = $builder->getForm();

        return [
            'form' => $form->createView(),
            'NewsList' => $NewsList,
        ];
    }

    /**
     * 新着情報を登録・編集する。
     *
     * @Route("/%eccube_admin_route%/content/news/new", name="admin_content_news_new")
     * @Route("/%eccube_admin_route%/content/news/{id}/edit", requirements={"id" = "\d+"}, name="admin_content_news_edit")
     * @Template("@admin/Content/news_edit.twig")
     *
     * @param Request $request
     * @param null $id
     *
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function edit(Request $request, $id = null)
    {
        if ($id) {
            $News = $this->newsRepository->find($id);
            if (!$News) {
                throw new NotFoundHttpException();
            }
        } else {
            $News = new \Eccube\Entity\News();
        }

        $builder = $this->formFactory
            ->createBuilder(NewsType::class, $News);

        $event = new EventArgs(
            [
                'builder' => $builder,
                'News' => $News,
            ],
            $request
        );
        $this->eventDispatcher->dispatch(EccubeEvents::ADMIN_CONTENT_NEWS_EDIT_INITIALIZE, $event);

        $form = $builder->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (!$News->getUrl()) {
                $News->setLinkMethod(false);
            }
            $this->newsRepository->save($News);

            $event = new EventArgs(
                [
                    'form' => $form,
                    'News' => $News,
                ],
                $request
            );
            $this->eventDispatcher->dispatch(EccubeEvents::ADMIN_CONTENT_NEWS_EDIT_COMPLETE, $event);

            $this->addSuccess('admin.news.save.complete', 'admin');

            return $this->redirectToRoute('admin_content_news');
        }

        return [
            'form' => $form->createView(),
            'News' => $News,
        ];
    }

    /**
     * @Method("POST")
     * @Route("/%eccube_admin_route%/content/news/sort_no/move", name="admin_content_news_sort_no_move")
     *
     * @param Request $request
     *
     * @return Response
     */
    public function moveSortNo(Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            $this->isTokenValid();
            $sortNos = $request->request->all();
            foreach ($sortNos as $newsId => $sortNo) {
                /** @var News $News */
                $News = $this->newsRepository
                    ->find($newsId);
                $News->setSortNo($sortNo);
                $this->entityManager->persist($News);
            }
            $this->entityManager->flush();
        }

        return new Response();
    }

    /**
     * 指定した新着情報を削除する。
     *
     * @Route("/%eccube_admin_route%/content/news/{id}/delete", requirements={"id" = "\d+"}, name="admin_content_news_delete", methods={"DELETE"})
     *
     * @param Request $request
     * @param News $News
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function delete(Request $request, News $News)
    {
        $this->isTokenValid();

        log_info('新着情報削除開始', [$News->getId()]);

        try {
            $this->newsRepository->delete($News);

            $event = new EventArgs(['News' => $News], $request);
            $this->eventDispatcher->dispatch(EccubeEvents::ADMIN_CONTENT_NEWS_DELETE_COMPLETE, $event);

            $this->addSuccess('admin.news.delete.complete', 'admin');

            log_info('新着情報削除完了', [$News->getId()]);
        } catch (\Exception $e) {
            $message = trans('admin.delete.failed.foreign_key', ['%name%' => trans('news.text.name')]);
            $this->addError($message, 'admin');

            log_error('新着情報削除エラー', [$News->getId(), $e]);
        }

        return $this->redirectToRoute('admin_content_news');
    }
}
