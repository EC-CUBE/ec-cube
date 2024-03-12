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

use Doctrine\Persistence\Mapping\MappingException;
use Eccube\Controller\AbstractController;
use Eccube\Event\EccubeEvents;
use Eccube\Event\EventArgs;
use Eccube\Form\Type\Admin\MasterdataEditType;
use Eccube\Form\Type\Admin\MasterdataType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class MasterdataController extends AbstractController
{
    /**
     * @Route("/%eccube_admin_route%/setting/system/masterdata", name="admin_setting_system_masterdata", methods={"GET", "POST"})
     * @Route("/%eccube_admin_route%/setting/system/masterdata/{entity}/edit", name="admin_setting_system_masterdata_view", methods={"GET", "POST"})
     * @Template("@admin/Setting/System/masterdata.twig")
     */
    public function index(Request $request, $entity = null)
    {
        $data = [];

        $builder = $this->formFactory->createBuilder(MasterdataType::class);

        $event = new EventArgs(
            [
                'builder' => $builder,
            ],
            $request
        );
        $this->eventDispatcher->dispatch($event, EccubeEvents::ADMIN_SETTING_SYSTEM_MASTERDATA_INDEX_INITIALIZE);

        $form = $builder->getForm();

        if ('POST' === $request->getMethod()) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $event = new EventArgs(
                    [
                        'form' => $form,
                    ],
                    $request
                );
                $this->eventDispatcher->dispatch($event, EccubeEvents::ADMIN_SETTING_SYSTEM_MASTERDATA_INDEX_COMPLETE);

                if ($event->hasResponse()) {
                    return $event->getResponse();
                }

                return $this->redirectToRoute(
                    'admin_setting_system_masterdata_view', ['entity' => $form['masterdata']->getData()]
                );
            }
        } elseif (!is_null($entity)) {
            $form->submit(['masterdata' => $entity]);
            if ($form['masterdata']->isValid()) {
                $entityName = str_replace('-', '\\', $entity);
                try {
                    $masterdata = $this->entityManager->getRepository($entityName)->findBy(
                        [],
                        ['sort_no' => 'ASC']
                    );
                    $data['data'] = [];
                    $data['masterdata_name'] = $entity;
                    foreach ($masterdata as $value) {
                        $data['data'][$value['id']]['id'] = $value['id'];
                        $data['data'][$value['id']]['name'] = $value['name'];
                    }
                    $data['data'][] = [
                        'id' => '',
                        'name' => '',
                    ];
                } catch (MappingException $e) {
                }
            }
        }

        $builder2 = $this->formFactory->createBuilder(MasterdataEditType::class, $data);

        $event = new EventArgs(
            [
                'builder' => $builder2,
            ],
            $request
        );
        $this->eventDispatcher->dispatch($event, EccubeEvents::ADMIN_SETTING_SYSTEM_MASTERDATA_INDEX_FORM2_INITIALIZE);

        $form2 = $builder2->getForm();

        return [
            'form' => $form->createView(),
            'form2' => $form2->createView(),
        ];
    }

    /**
     * @Route("/%eccube_admin_route%/setting/system/masterdata/edit", name="admin_setting_system_masterdata_edit", methods={"GET", "POST"})
     * @Template("@admin/Setting/System/masterdata.twig")
     */
    public function edit(Request $request)
    {
        $builder2 = $this->formFactory->createBuilder(MasterdataEditType::class);

        $event = new EventArgs(
            [
                'builder' => $builder2,
            ],
            $request
        );
        $this->eventDispatcher->dispatch($event, EccubeEvents::ADMIN_SETTING_SYSTEM_MASTERDATA_EDIT_INITIALIZE);

        $form2 = $builder2->getForm();

        if ('POST' === $request->getMethod()) {
            $form2->handleRequest($request);

            if ($form2->isValid()) {
                $data = $form2->getData();

                $entityName = str_replace('-', '\\', $data['masterdata_name']);
                $sortNo = 0;
                $ids = array_filter(array_map(
                    function ($v) {
                        return $v['id'];
                    },
                    $data['data']
                ));

                $repository = $this->entityManager->getRepository($entityName);

                foreach ($data['data'] as $key => $value) {
                    if ($value['id'] !== null && $value['name'] !== null) {
                        $entity = $repository->find($value['id']);
                        if ($entity === null) {
                            $entity = new $entityName();
                        }
                        $entity->setId($value['id']);
                        $entity->setName($value['name']);
                        $entity->setSortNo($sortNo++);
                        $this->entityManager->persist($entity);
                    } elseif (!in_array($key, $ids)) {
                        // remove
                        $delKey = $this->entityManager->getRepository($entityName)->find($key);
                        if ($delKey) {
                            $this->entityManager->remove($delKey);
                        }
                    }
                }

                try {
                    $this->entityManager->flush();

                    $event = new EventArgs(
                        [
                            'form' => $form2,
                        ],
                        $request
                    );
                    $this->eventDispatcher->dispatch(
                        $event,
                        EccubeEvents::ADMIN_SETTING_SYSTEM_MASTERDATA_EDIT_COMPLETE
                    );

                    $this->addSuccess('admin.common.save_complete', 'admin');
                } catch (\Exception $e) {
                    // 外部キー制約などで削除できない場合に例外エラーになる
                    $this->addError('admin.common.save_error', 'admin');
                }

                return $this->redirectToRoute(
                    'admin_setting_system_masterdata_view', ['entity' => $data['masterdata_name']]
                );
            }
        }

        $builder = $this->formFactory->createBuilder(MasterdataType::class);

        $event = new EventArgs(
            [
                'builder' => $builder,
            ],
            $request
        );
        $this->eventDispatcher->dispatch($event, EccubeEvents::ADMIN_SETTING_SYSTEM_MASTERDATA_EDIT_FORM_INITIALIZE);

        $form = $builder->getForm();
        $parameter = array_merge($request->request->all(), ['masterdata' => $form2['masterdata_name']->getData()]);
        $form->submit($parameter);

        return [
            'form' => $form->createView(),
            'form2' => $form2->createView(),
        ];
    }
}
