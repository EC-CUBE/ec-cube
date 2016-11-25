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


namespace Eccube\Controller\Admin\Product;

use Eccube\Application;
use Eccube\Controller\AbstractController;
use Eccube\Entity\Master\CsvType;
use Eccube\Event\EccubeEvents;
use Eccube\Event\EventArgs;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CategoryController extends AbstractController
{
    public function index(Application $app, Request $request, $parent_id = null, $id = null)
    {
        if ($parent_id) {
            $Parent = $app['eccube.repository.category']->find($parent_id);
            if (!$Parent) {
                throw new NotFoundHttpException('親カテゴリが存在しません');
            }
        } else {
            $Parent = null;
        }
        if ($id) {
            $TargetCategory = $app['eccube.repository.category']->find($id);
            if (!$TargetCategory) {
                throw new NotFoundHttpException('カテゴリが存在しません');
            }
            $Parent = $TargetCategory->getParent();
        } else {
            $TargetCategory = new \Eccube\Entity\Category();
            $TargetCategory->setParent($Parent);
            if ($Parent) {
                $TargetCategory->setLevel($Parent->getLevel() + 1);
            } else {
                $TargetCategory->setLevel(1);
            }
        }

        //
        $builder = $app['form.factory']
            ->createBuilder('admin_category', $TargetCategory);

        $event = new EventArgs(
            array(
                'builder' => $builder,
                'Parent' => $Parent,
                'TargetCategory' => $TargetCategory,
            ),
            $request
        );
        $app['eccube.event.dispatcher']->dispatch(EccubeEvents::ADMIN_PRODUCT_CATEGORY_INDEX_INITIALIZE, $event);

        $form = $builder->getForm();

        //
        if ($request->getMethod() === 'POST') {
            $form->handleRequest($request);
            if ($form->isValid()) {
                if ($app['config']['category_nest_level'] < $TargetCategory->getLevel()) {
                    throw new BadRequestHttpException('リクエストが不正です');
                }
                log_info('カテゴリ登録開始', array($id));
                $status = $app['eccube.repository.category']->save($TargetCategory);

                if ($status) {

                    log_info('カテゴリ登録完了', array($id));

                    $event = new EventArgs(
                        array(
                            'form' => $form,
                            'Parent' => $Parent,
                            'TargetCategory' => $TargetCategory,
                        ),
                        $request
                    );
                    $app['eccube.event.dispatcher']->dispatch(EccubeEvents::ADMIN_PRODUCT_CATEGORY_INDEX_COMPLETE, $event);

                    $app->addSuccess('admin.category.save.complete', 'admin');

                    if ($Parent) {
                        return $app->redirect($app->url('admin_product_category_show', array('parent_id' => $Parent->getId())));
                    } else {
                        return $app->redirect($app->url('admin_product_category'));
                    }
                } else {
                    log_info('カテゴリ登録エラー', array($id));
                    $app->addError('admin.category.save.error', 'admin');
                }
            }
        }

        $Categories = $app['eccube.repository.category']->getList($Parent);

        // ツリー表示のため、ルートからのカテゴリを取得
        $TopCategories = $app['eccube.repository.category']->getList(null);

        return $app->render('Product/category.twig', array(
            'form' => $form->createView(),
            'Parent' => $Parent,
            'Categories' => $Categories,
            'TopCategories' => $TopCategories,
            'TargetCategory' => $TargetCategory,
        ));
    }

    public function delete(Application $app, Request $request, $id)
    {
        $this->isTokenValid($app);

        $TargetCategory = $app['eccube.repository.category']->find($id);
        if (!$TargetCategory) {
            $app->deleteMessage();
            return $app->redirect($app->url('admin_product_category'));
        }
        $Parent = $TargetCategory->getParent();

        log_info('カテゴリ削除開始', array($id));

        $status = $app['eccube.repository.category']->delete($TargetCategory);

        if ($status === true) {

            log_info('カテゴリ削除完了', array($id));

            $event = new EventArgs(
                array(
                    'Parent' => $Parent,
                    'TargetCategory' => $TargetCategory,
                ),
                $request
            );
            $app['eccube.event.dispatcher']->dispatch(EccubeEvents::ADMIN_PRODUCT_CATEGORY_DELETE_COMPLETE, $event);

            $app->addSuccess('admin.category.delete.complete', 'admin');
        } else {
            log_info('カテゴリ削除エラー', array($id));
            $app->addError('admin.category.delete.error', 'admin');
        }

        if ($Parent) {
            return $app->redirect($app->url('admin_product_category_show', array('parent_id' => $Parent->getId())));
        } else {
            return $app->redirect($app->url('admin_product_category'));
        }
    }

    public function moveRank(Application $app, Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            $ranks = $request->request->all();
            foreach ($ranks as $categoryId => $rank) {
                /* @var $Category \Eccube\Entity\Category */
                $Category = $app['eccube.repository.category']
                    ->find($categoryId);
                $Category->setRank($rank);
                $app['orm.em']->persist($Category);
            }
            $app['orm.em']->flush();
        }
        return true;
    }


    /**
     * カテゴリCSVの出力.
     *
     * @param Application $app
     * @param Request $request
     * @return StreamedResponse
     */
    public function export(Application $app, Request $request)
    {
        // タイムアウトを無効にする.
        set_time_limit(0);

        // sql loggerを無効にする.
        $em = $app['orm.em'];
        $em->getConfiguration()->setSQLLogger(null);

        $response = new StreamedResponse();
        $response->setCallback(function () use ($app, $request) {

            // CSV種別を元に初期化.
            $app['eccube.service.csv.export']->initCsvType(CsvType::CSV_TYPE_CATEGORY);

            // ヘッダ行の出力.
            $app['eccube.service.csv.export']->exportHeader();

            $qb = $app['eccube.repository.category']
                ->createQueryBuilder('c')
                ->orderBy('c.rank', 'DESC');

            // データ行の出力.
            $app['eccube.service.csv.export']->setExportQueryBuilder($qb);
            $app['eccube.service.csv.export']->exportData(function ($entity, $csvService) {

                $Csvs = $csvService->getCsvs();

                /** @var $Category \Eccube\Entity\Category */
                $Category = $entity;

                // CSV出力項目と合致するデータを取得.
                $row = array();
                foreach ($Csvs as $Csv) {
                    $row[] = $csvService->getData($Csv, $Category);
                }

                //$row[] = number_format(memory_get_usage(true));
                // 出力.
                $csvService->fputcsv($row);
            });
        });

        $now = new \DateTime();
        $filename = 'category_' . $now->format('YmdHis') . '.csv';
        $response->headers->set('Content-Type', 'application/octet-stream');
        $response->headers->set('Content-Disposition', 'attachment; filename=' . $filename);
        $response->send();

        log_info('カテゴリCSV出力ファイル名', array($filename));

        return $response;
    }
}
