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

use Doctrine\ORM\NoResultException;
use Eccube\Application;
use Eccube\Controller\AbstractController;
use Eccube\Entity\BlockPosition;
use Eccube\Entity\Layout;
use Eccube\Form\Type\Master\DeviceTypeType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Validator\Constraints\NotBlank;

// todo プレビュー実装
class LayoutController extends AbstractController
{
    public function index(Application $app, Request $request)
    {
        $Layouts = $app['eccube.repository.layout']->findBy([], ['id' => 'DESC']);

        return $app->render(
            'Content/layout_list.twig',
            [
                'Layouts' => $Layouts,
            ]
        );
    }

    public function delete(Application $app, Request $request, $id)
    {
        $this->isTokenValid($app);

        $Layout = $app['eccube.repository.layout']->find($id);
        if (!$Layout) {
            $app->deleteMessage();

            return $app->redirect($app->url('admin_content_layout'));
        }

        $app['orm.em']->remove($Layout);
        $app['orm.em']->flush($Layout);


        $app->addSuccess('admin.delete.complete', 'admin');

        return $app->redirect($app->url('admin_content_layout'));
    }

    public function edit(Application $app, Request $request, $id = null)
    {
        if (is_null($id)) {
            $Layout = new Layout();
        } else {
            // todo レポジトリへ移動
            try {
                $Layout = $app['eccube.repository.layout']->createQueryBuilder('l')
                    ->select('l, bp, b')
                    ->leftJoin('l.BlockPositions', 'bp')
                    ->leftJoin('bp.Block', 'b')
                    ->where('l.id = :layout_id')
                    ->orderBy('bp.block_row', 'ASC')
                    ->setParameter('layout_id', $id)
                    ->getQuery()
                    ->getSingleResult();
            } catch (NoResultException $e) {
                throw new NotFoundHttpException();
            }
        }

        // todo レポジトリへ移動
        // 未使用ブロックの取得
        $Blocks = $Layout->getBlocks();
        if (empty($Blocks)) {
            $UnusedBlocks = $app['eccube.repository.block']->findAll();
        } else {
            $UnusedBlocks = $app['eccube.repository.block']
                ->createQueryBuilder('b')
                ->select('b')
                ->where('b not in (:blocks)')
                ->setParameter('blocks', $Blocks)
                ->getQuery()
                ->getResult();
        }

        $builder = $app->form($Layout);
        $builder
            ->add(
                'name',
                TextType::class,
                [
                    'constraints' => [
                        new NotBlank(),
                    ],
                    'required' => false,
                    'label' => 'レイアウト名',
                ]
            )->add(
                'DeviceType',
                DeviceTypeType::class,
                [
                    'constraints' => [
                        new NotBlank(),
                    ],
                    'required' => false,
                ]
            );

        $form = $builder->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Layoutの更新
            $Layout = $form->getData();
            $app['orm.em']->persist($Layout);
            $app['orm.em']->flush($Layout);

            // BlockPositionの更新
            // delete/insertのため、一度削除する.
            $BlockPositions = $Layout->getBlockPositions();
            foreach ($BlockPositions as $BlockPosition) {
                $Layout->removeBlockPosition($BlockPosition);
                $app['orm.em']->remove($BlockPosition);
                $app['orm.em']->flush($BlockPosition);
            }

            // ブロックの個数分登録を行う.
            $max = count($Blocks) + count($UnusedBlocks);
            $data = $request->request->all();
            for ($i = 0; $i < $max; $i++) {
                // block_idが取得できない場合はinsertしない
                if (!isset($data['block_id_'.$i])) {
                    continue;
                }
                // 未使用ブロックはinsertしない
                if ($data['target_id_'.$i] == \Eccube\Entity\PageLayout::TARGET_ID_UNUSED) {
                    continue;
                }
                $Block = $app['eccube.repository.block']->find($data['block_id_'.$i]);
                $BlockPosition = new BlockPosition();
                $BlockPosition
                    ->setBlockId($data['block_id_'.$i])
                    ->setLayoutId($Layout->getId())
                    ->setBlockRow($data['block_row_'.$i])
                    ->setTargetId($data['target_id_'.$i])
                    ->setBlock($Block)
                    ->setLayout($Layout);
                $Layout->addBlockPosition($BlockPosition);
                $app['orm.em']->persist($BlockPosition);
                $app['orm.em']->flush($BlockPosition);
            }

            $app->addSuccess('admin.register.complete', 'admin');

            return $app->redirect($app->url('admin_content_layout_edit', array('id' => $Layout->getId())));
        }

        return $app->render(
            'Content/layout.twig',
            array(
                'form' => $form->createView(),
                'Layout' => $Layout,
                'UnusedBlocks' => $UnusedBlocks,
            )
        );
    }

    public function viewBlock(Application $app, Request $request)
    {
        if (!$request->isXmlHttpRequest()) {
            throw new BadRequestHttpException();
        }

        $id = $request->get('id');

        if (is_null($id)) {
            throw new BadRequestHttpException();
        }

        $Block = $app['eccube.repository.block']->find($id);

        if (is_null($Block)) {
            return $app->json('みつかりませんでした');
        }

        // ブロックのソースコードの取得.
        $file = $app['eccube.repository.block']->getReadTemplateFile($Block->getFileName());
        $source = $file['tpl_data'];

        return $app->json([
            'id' => $Block->getId(),
            'source' => $source,
        ]);
    }
}
