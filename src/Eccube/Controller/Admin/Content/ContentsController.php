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
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ContentsController extends AbstractController
{
    public function __construct()
    {
    }

    public function index(Application $app)
    {
        $NewsList = $app['eccube.repository.news']->findBy(array(), array('rank' => 'DESC'));

        $form = $app->form()->getForm();

        return $app->render('Content/index.twig', array(
            'form' => $form->createView(),
            'NewsList' => $NewsList,
        ));
    }

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

        $form = $app['form.factory']
            ->createBuilder('admin_news', $News)
            ->getForm();

        if ('POST' === $request->getMethod()) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $data = $form->getData();
                if (empty($data['url'])) {
                    $News->setLinkMethod(Constant::DISABLED);
                }
                $status = $app['eccube.repository.news']->save($News);
                if ($status) {
                    $app->addSuccess('admin.news.save.complete', 'admin');
                    return $app->redirect($app->url('admin_content'));
                } else {
                    $app->addError('admin.news.save.error', 'admin');
                }
            }
        }

        return $app->render('Content/edit.twig', array(
            'form' => $form->createView(),
            'News' => $News,
        ));

    }

    public function up(Application $app, Request $request, $id)
    {
        $TargetNews = $app['eccube.repository.news']->find($id);

        if (!$TargetNews) {
            throw new NotFoundHttpException();
        }

        $status = false;
        if ('POST' === $request->getMethod()) {
            $status = $app['eccube.repository.news']->up($TargetNews);
        }

        if ($status) {
            $app->addSuccess('admin.news.up.complete', 'admin');
        } else {
            $app->addError('admin.news.up.error', 'admin');
        }

        return $app->redirect($app->url('admin_content'));
    }

    public function down(Application $app, Request $request, $id)
    {
        $TargetNews = $app['eccube.repository.news']->find($id);

        if (!$TargetNews) {
            throw new NotFoundHttpException();
        }

        $status = false;
        if ('POST' === $request->getMethod()) {
            $status = $app['eccube.repository.news']->down($TargetNews);
        }

        if ($status) {
            $app->addSuccess('admin.news.down.complete', 'admin');
        } else {
            $app->addError('admin.news.down.error', 'admin');
        }

        return $app->redirect($app->url('admin_content'));
    }

    public function delete(Application $app, Request $request, $id)
    {
        $TargetNews = $app['eccube.repository.news']->find($id);
        if (!$TargetNews) {
            throw new NotFoundHttpException();
        }

        $status = false;
        if ('POST' === $request->getMethod()) {
            $status = $app['eccube.repository.news']->delete($TargetNews);
        }

        if ($status) {
            $app->addSuccess('admin.news.delete.complete', 'admin');
        } else {
            // fixme : キー名を英語にする
            $app->addSuccess('admin.news.delete.error', 'admin');
        }

        return $app->redirect($app->url('admin_content'));
    }
}