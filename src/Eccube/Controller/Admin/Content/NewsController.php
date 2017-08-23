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
use Eccube\Annotation\Component;
use Eccube\Application;
use Eccube\Common\Constant;
use Eccube\Controller\AbstractController;
use Eccube\Event\EccubeEvents;
use Eccube\Event\EventArgs;
use Eccube\Form\Type\Admin\NewsType;
use Eccube\Repository\NewsRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * 新着情報のコントローラクラス
 *
 * @Component
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
     * @Route("/{_admin}/content/news", name="admin_content_news")
     * @Template("Content/news.twig")
     *
     * @param Application $app
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index(Application $app, Request $request)
    {
        $NewsList = $this->newsRepository->findBy(array(), array('rank' => 'DESC'));

        $builder = $app->form();

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
     * @Route("/{_admin}/content/news/new", name="admin_content_news_new")
     * @Route("/{_admin}/content/news/{id}/edit", requirements={"id" = "\d+"}, name="admin_content_news_edit")
     * @Template("Content/news_edit.twig")
     *
     * @param Application $app
     * @param Request $request
     * @param integer $id
     * @throws NotFoundHttpException
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
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

        $News->setLinkMethod((bool) $News->getLinkMethod());

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

        if ('POST' === $request->getMethod()) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $data = $form->getData();
                if (empty($data['url'])) {
                    $News->setLinkMethod(Constant::DISABLED);
                }

                $status = $this->newsRepository->save($News);

                if ($status) {

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
                $app->addError('admin.news.save.error', 'admin');
            }
        }

        return $app->render('Content/news_edit.twig', array(
            'form' => $form->createView(),
            'News' => $News,
        ));
    }

    /**
     * 指定した新着情報の表示順を1つ上げる。
     *
     * @Method("PUT")
     * @Route("/{_admin}/content/news/{id}/up", requirements={"id" = "\d+"}, name="admin_content_news_up")
     *
     * @param Application $app
     * @param Request $request
     * @param integer $id
     * @throws NotFoundHttpException
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function up(Application $app, Request $request, $id)
    {
        $this->isTokenValid($app);

        $TargetNews = $this->newsRepository->find($id);
        if (!$TargetNews) {
            throw new NotFoundHttpException();
        }

        $status = $this->newsRepository->up($TargetNews);

        if ($status) {
            $app->addSuccess('admin.news.up.complete', 'admin');
        } else {
            $app->addError('admin.news.up.error', 'admin');
        }

        return $app->redirect($app->url('admin_content_news'));
    }

    /**
     * 指定した新着情報の表示順を1つ下げる。
     *
     * @Method("PUT")
     * @Route("/{_admin}/content/news/{id}/down", requirements={"id" = "\d+"}, name="admin_content_news_down")
     *
     * @param Application $app
     * @param Request $request
     * @param integer $id
     * @throws NotFoundHttpException
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function down(Application $app, Request $request, $id)
    {
        $this->isTokenValid($app);

        $TargetNews = $this->newsRepository->find($id);
        if (!$TargetNews) {
            throw new NotFoundHttpException();
        }

        $status = $this->newsRepository->down($TargetNews);

        if ($status) {
            $app->addSuccess('admin.news.down.complete', 'admin');
        } else {
            $app->addError('admin.news.down.error', 'admin');
        }

        return $app->redirect($app->url('admin_content_news'));
    }

    /**
     * 指定した新着情報を削除する。
     *
     * @Method("DELETE")
     * @Route("/{_admin}/content/news/{id}/delete", requirements={"id" = "\d+"}, name="admin_content_news_delete")
     *
     * @param Application $app
     * @param Request $request
     * @param integer $id
     * @throws NotFoundHttpException
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function delete(Application $app, Request $request, $id)
    {
        $this->isTokenValid($app);

        $TargetNews = $this->newsRepository->find($id);
        if (!$TargetNews) {
            throw new NotFoundHttpException();
        }

        $status = $this->newsRepository->delete($TargetNews);

        $event = new EventArgs(
            array(
                'TargetNews' => $TargetNews,
                'status' => $status,
            ),
            $request
        );
        $this->eventDispatcher->dispatch(EccubeEvents::ADMIN_CONTENT_NEWS_DELETE_COMPLETE, $event);
        $status = $event->getArgument('status');

        if ($status) {
            $app->addSuccess('admin.news.delete.complete', 'admin');
        } else {
            $app->addSuccess('admin.news.delete.error', 'admin');
        }

        return $app->redirect($app->url('admin_content_news'));
    }
}
