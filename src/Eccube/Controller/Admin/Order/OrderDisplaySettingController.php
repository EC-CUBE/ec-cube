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

namespace Eccube\Controller\Admin\Order;

use Eccube\Controller\AbstractController;
use Eccube\Entity\OrderDisplaySetting;
use Eccube\Repository\OrderDisplaySettingRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bridge\Twig\Attribute\Template;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

/**
 * 受注一覧画面の表示項目設定.
 *
 * CSV出力項目設定（CsvController）と同じ2カラム DnD 方式を踏襲する.
 */
class OrderDisplaySettingController extends AbstractController
{
    public function __construct(protected OrderDisplaySettingRepository $orderDisplaySettingRepository)
    {
    }

    /**
     * 表示項目設定画面.
     *
     * @return array<string, mixed>|RedirectResponse
     */
    #[Route(path: '/%eccube_admin_route%/order/display_setting', name: 'admin_order_display_setting', methods: ['GET', 'POST'])]
    #[Template(template: '@admin/Order/display_setting.twig')]
    public function index(Request $request): array|RedirectResponse
    {
        $builder = $this->formFactory->createBuilder();

        $nonDisplayItems = $this->orderDisplaySettingRepository->findBy(
            ['enabled' => false],
            ['sort_no' => 'ASC']
        );

        $builder->add('non_display_items', EntityType::class, [
            'class' => OrderDisplaySetting::class,
            'choice_label' => 'disp_name',
            'required' => false,
            'expanded' => false,
            'multiple' => true,
            'choices' => $nonDisplayItems,
        ]);

        $displayItems = $this->orderDisplaySettingRepository->findBy(
            ['enabled' => true],
            ['sort_no' => 'ASC']
        );

        $builder->add('display_items', EntityType::class, [
            'class' => OrderDisplaySetting::class,
            'choice_label' => 'disp_name',
            'required' => false,
            'expanded' => false,
            'multiple' => true,
            'choices' => $displayItems,
        ]);

        $form = $builder->getForm();

        // display_items/non_display_items のチェックに引っかかるため, token チェックは個別に行う
        if ('POST' === $request->getMethod() && $this->isTokenValid()) {
            $data = $request->get('form');

            if (isset($data['non_display_items'])) {
                $sortNo = 1;
                foreach ($data['non_display_items'] as $id) {
                    $setting = $this->orderDisplaySettingRepository->find($id);
                    if ($setting) {
                        $setting->setSortNo($sortNo);
                        $setting->setEnabled(false);
                    }
                    $sortNo++;
                }
            }

            if (isset($data['display_items'])) {
                $sortNo = 1;
                foreach ($data['display_items'] as $id) {
                    $setting = $this->orderDisplaySettingRepository->find($id);
                    if ($setting) {
                        $setting->setSortNo($sortNo);
                        $setting->setEnabled(true);
                    }
                    $sortNo++;
                }
            }

            $this->entityManager->flush();

            $this->addSuccess('admin.common.save_complete', 'admin');

            return $this->redirectToRoute('admin_order_display_setting');
        }

        return [
            'form' => $form->createView(),
        ];
    }
}
