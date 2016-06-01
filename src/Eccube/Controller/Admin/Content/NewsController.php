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

use Eccube\Application;
use Eccube\Common\Constant;
use Eccube\Controller\AbstractController;
use Eccube\Event\EccubeEvents;
use Eccube\Event\EventArgs;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * 新着情報のコントローラクラス
 */
class NewsController extends AbstractController
{
    /**
     * 新着情報一覧を表示する。
     *
     * @param Application $app
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index(Application $app, Request $request)
    {
        $NewsList = $app['eccube.repository.news']->findBy(array(), array('rank' => 'DESC'));

        $builder = $app->form();

        $event = new EventArgs(
            array(
                'builder' => $builder,
                'NewsList' => $NewsList,
            ),
            $request
        );
        $app['eccube.event.dispatcher']->dispatch(EccubeEvents::ADMIN_CONTENT_NEWS_INDEX_INITIALIZE, $event);

        $form = $builder->getForm();

        return $app->render('Content/news.twig', array(
            'form' => $form->createView(),
            'NewsList' => $NewsList,
        ));
    }

    /**
     * 新着情報を登録・編集する。
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
            $News = $app['eccube.repository.news']->find($id);
            if (!$News) {
                throw new NotFoundHttpException();
            }
            $News->setLinkMethod((bool) $News->getLinkMethod());
        } else {
            $News = new \Eccube\Entity\News();
        }

        $builder = $app['form.factory']
            ->createBuilder('admin_news', $News);

        $event = new EventArgs(
            array(
                'builder' => $builder,
                'News' => $News,
            ),
            $request
        );
        $app['eccube.event.dispatcher']->dispatch(EccubeEvents::ADMIN_CONTENT_NEWS_EDIT_INITIALIZE, $event);

        $form = $builder->getForm();

        if ('POST' === $request->getMethod()) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $data = $form->getData();
                if (empty($data['url'])) {
                    $News->setLinkMethod(Constant::DISABLED);
                }

                $status = $app['eccube.repository.news']->save($News);

                if ($status) {

                    $event = new EventArgs(
                        array(
                            'form' => $form,
                            'News' => $News,
                        ),
                        $request
                    );
                    $app['eccube.event.dispatcher']->dispatch(EccubeEvents::ADMIN_CONTENT_NEWS_EDIT_COMPLETE, $event);

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
     * @param Application $app
     * @param Request $request
     * @param integer $id
     * @throws NotFoundHttpException
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function up(Application $app, Request $request, $id)
    {
        $this->isTokenValid($app);

        $TargetNews = $app['eccube.repository.news']->find($id);
        if (!$TargetNews) {
            throw new NotFoundHttpException();
        }

        $status = $app['eccube.repository.news']->up($TargetNews);

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
     * @param Application $app
     * @param Request $request
     * @param integer $id
     * @throws NotFoundHttpException
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function down(Application $app, Request $request, $id)
    {
        $this->isTokenValid($app);

        $TargetNews = $app['eccube.repository.news']->find($id);
        if (!$TargetNews) {
            throw new NotFoundHttpException();
        }

        $status = $app['eccube.repository.news']->down($TargetNews);

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
     * @param Application $app
     * @param Request $request
     * @param integer $id
     * @throws NotFoundHttpException
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function delete(Application $app, Request $request, $id)
    {
        $this->isTokenValid($app);

        $TargetNews = $app['eccube.repository.news']->find($id);
        if (!$TargetNews) {
            throw new NotFoundHttpException();
        }

        $status = $app['eccube.repository.news']->delete($TargetNews);

        $event = new EventArgs(
            array(
                'TargetNews' => $TargetNews,
                'status' => $status,
            ),
            $request
        );
        $app['eccube.event.dispatcher']->dispatch(EccubeEvents::ADMIN_CONTENT_NEWS_DELETE_COMPLETE, $event);
        $status = $event->getArgument('status');

        if ($status) {
            $app->addSuccess('admin.news.delete.complete', 'admin');
        } else {
            $app->addSuccess('admin.news.delete.error', 'admin');
        }

        return $app->redirect($app->url('admin_content_news'));
    }
}