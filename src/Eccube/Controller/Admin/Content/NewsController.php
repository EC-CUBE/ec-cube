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

namespace Eccube\Controller\Admin\Content;

use Eccube\Annotation\Inject;
use Eccube\Application;
use Eccube\Controller\AbstractController;
use Eccube\Entity\News;
use Eccube\Event\EccubeEvents;
use Eccube\Event\EventArgs;
use Eccube\Form\Type\Admin\NewsType;
use Eccube\Repository\NewsRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * 新着情報のコントローラクラス
 *
 * @Route(service=NewsController::class)
 */
class NewsController extends AbstractController
{
    /**
     * @Inject("form.factory")
     * @var FormFactory
     */
    protected $formFactory;

    /**
     * @Inject("eccube.event.dispatcher")
     * @var EventDispatcher
     */
    protected $eventDispatcher;

    /**
     * @Inject(NewsRepository::class)
     * @var NewsRepository
     */
    protected $newsRepository;

    /**
     * 新着情報一覧を表示する。
     *
     * @Route("/%admin_route%/content/news", name="admin_content_news")
     * @Template("Content/news.twig")
     *
     * @param Application $app
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index(Application $app, Request $request)
    {
        $NewsList = $this->newsRepository->findBy(array(), array('rank' => 'DESC'));

        $builder = $this->formFactory->createBuilder();

        $event = new EventArgs(
            array(
                'builder' => $builder,
                'NewsList' => $NewsList,
            ),
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
     * @Route("/%admin_route%/content/news/new", name="admin_content_news_new")
     * @Route("/%admin_route%/content/news/{id}/edit", requirements={"id" = "\d+"}, name="admin_content_news_edit")
     * @Template("Content/news_edit.twig")
     *
     * @param Application $app
     * @param Request $request
     * @param null $id
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function edit(Application $app, Request $request, $id = null)
    {
        if ($id) {
            $News = $this->newsRepository->find($id);
            if (!$News) {
                throw new NotFoundHttpException();
            }
        } else {
            $News = new \Eccube\Entity\News();
        }

        $News->setLinkMethod($News->isLinkMethod());

        $builder = $this->formFactory
            ->createBuilder(NewsType::class, $News);

        $event = new EventArgs(
            array(
                'builder' => $builder,
                'News' => $News,
            ),
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
                array(
                    'form' => $form,
                    'News' => $News,
                ),
                $request
            );
            $this->eventDispatcher->dispatch(EccubeEvents::ADMIN_CONTENT_NEWS_EDIT_COMPLETE, $event);

            $app->addSuccess('admin.news.save.complete', 'admin');

            return $app->redirect($app->url('admin_content_news'));
        }

        return [
            'form' => $form->createView(),
            'News' => $News,
        ];
    }

    /**
     * 指定した新着情報の表示順を1つ上げる。
     *
     * @Method("PUT")
     * @Route("/%admin_route%/content/news/{id}/up", requirements={"id" = "\d+"}, name="admin_content_news_up")
     *
     * @param Application $app
     * @param Request $request
     * @param News $News
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function up(Application $app, Request $request, News $News)
    {
        $this->isTokenValid($app);

        try {
            $this->newsRepository->up($News);

            $app->addSuccess('admin.news.up.complete', 'admin');
        } catch (\Exception $e) {

            log_error('新着情報表示順更新エラー', [$News->getId(), $e]);

            $app->addError('admin.news.up.error', 'admin');
        }

        return $app->redirect($app->url('admin_content_news'));
    }

    /**
     * 指定した新着情報の表示順を1つ下げる。
     *
     * @Method("PUT")
     * @Route("/%admin_route%/content/news/{id}/down", requirements={"id" = "\d+"}, name="admin_content_news_down")
     *
     * @param Application $app
     * @param Request $request
     * @param News $News
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function down(Application $app, Request $request, News $News)
    {
        $this->isTokenValid($app);

        try {
            $this->newsRepository->down($News);

            $app->addSuccess('admin.news.down.complete', 'admin');
        } catch (\Exception $e) {

            log_error('新着情報表示順更新エラー', [$News->getId(), $e]);

            $app->addError('admin.news.down.error', 'admin');
        }

        return $app->redirect($app->url('admin_content_news'));
    }

    /**
     * 指定した新着情報を削除する。
     *
     * @Method("DELETE")
     * @Route("/%admin_route%/content/news/{id}/delete", requirements={"id" = "\d+"}, name="admin_content_news_delete")
     *
     * @param Application $app
     * @param Request $request
     * @param News $News
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function delete(Application $app, Request $request, News $News)
    {
        $this->isTokenValid($app);

        log_info('新着情報削除開始', [$News->getId()]);

        try {
            $this->newsRepository->delete($News);

            $event = new EventArgs(['News' => $News], $request);
            $this->eventDispatcher->dispatch(EccubeEvents::ADMIN_CONTENT_NEWS_DELETE_COMPLETE, $event);

            $app->addSuccess('admin.news.delete.complete', 'admin');

            log_info('新着情報削除完了', [$News->getId()]);

        } catch (\Exception $e) {

            $message = $app->trans('admin.delete.failed.foreign_key', ['%name%' => '新着情報']);
            $app->addError($message, 'admin');

            log_error('新着情報削除エラー', [$News->getId(), $e]);
        }

        return $app->redirect($app->url('admin_content_news'));
    }
}
