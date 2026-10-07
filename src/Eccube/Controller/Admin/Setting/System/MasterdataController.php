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
use Eccube\Util\IdUtil;
use Symfony\Bridge\Twig\Attribute\Template;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Attribute\Route;

class MasterdataController extends AbstractController
{
    /**
     * @param class-string|null $entity
     *
     * @return RedirectResponse|Response|array<string, mixed>
     */
    #[Route(path: '/%eccube_admin_route%/setting/system/masterdata', name: 'admin_setting_system_masterdata', methods: ['GET', 'POST'])]
    #[Route(path: '/%eccube_admin_route%/setting/system/masterdata/{entity}/edit', name: 'admin_setting_system_masterdata_view', methods: ['GET', 'POST'])]
    #[Template(template: '@admin/Setting/System/masterdata.twig')]
    public function index(Request $request, $entity = null): RedirectResponse|Response|array
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
                /** @var class-string $entityName */
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
                } catch (MappingException) {
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
     * @return RedirectResponse|array<string, mixed>
     */
    #[Route(path: '/%eccube_admin_route%/setting/system/masterdata/edit', name: 'admin_setting_system_masterdata_edit', methods: ['GET', 'POST'])]
    #[Template(template: '@admin/Setting/System/masterdata.twig')]
    public function edit(Request $request): RedirectResponse|array
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

            // 編集対象はマスタデータのエンティティに限る
            $masterdataName = $form2['masterdata_name']->getData();
            $entityName = is_string($masterdataName) ? $this->findMasterdataEntityName(str_replace('-', '\\', $masterdataName)) : null;
            if (null === $entityName) {
                throw new BadRequestHttpException();
            }

            // ID の上限はマスタの型に合わせる
            $maxId = IdUtil::maxForType($this->entityManager->getClassMetadata($entityName)->getTypeOfField('id')) ?? IdUtil::INTEGER_MAX;
            foreach ($form2['data'] as $row) {
                $id = $row['id']->getData();
                if (null !== $id && null === IdUtil::toId($id, $maxId)) {
                    $row['id']->addError(new FormError(
                        $this->translator->trans('This value should be between {{ min }} and {{ max }}.', ['{{ min }}' => 0, '{{ max }}' => $maxId], 'validators')
                    ));
                }
            }

            if ($form2->isValid()) {
                $data = $form2->getData();
                $sortNo = 0;
                $ids = array_filter(array_map(
                    fn ($v) => $v['id'],
                    $data['data']
                ));

                $repository = $this->entityManager->getRepository($entityName);

                foreach ($data['data'] as $key => $value) {
                    if ($value['id'] !== null && $value['name'] !== null) {
                        $entity = $repository->find($value['id']);
                        $entity ??= new $entityName();
                        $entity->setId($value['id']);
                        $entity->setName($value['name']);
                        $entity->setSortNo($sortNo++);
                        $this->entityManager->persist($entity);
                    } elseif (!in_array($key, $ids) && null !== IdUtil::toId($key, $maxId)) {
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
                } catch (\Exception) {
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

    /**
     * マスタデータ (Master を含み id・name・sort_no を持つ具象エンティティ) であれば、そのクラス名を返す.
     *
     * 一覧 (MasterdataType) で除外している受注ステータス等も、従来どおり編集の対象に含める.
     *
     * @return class-string|null
     */
    private function findMasterdataEntityName(string $entityName): ?string
    {
        foreach ($this->entityManager->getMetadataFactory()->getAllMetadata() as $meta) {
            if ($meta->getName() !== $entityName) {
                continue;
            }

            return !$meta->getReflectionClass()->isAbstract()
                && str_contains($meta->rootEntityName, 'Master')
                && $meta->hasField('id')
                && $meta->hasField('name')
                && $meta->hasField('sort_no')
                ? $meta->getName()
                : null;
        }

        return null;
    }
}
