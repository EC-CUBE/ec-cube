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

namespace Eccube\Controller\Admin\Setting\Shop;

use Eccube\Controller\AbstractController;
use Eccube\Entity\Csv;
use Eccube\Entity\Master\CsvType;
use Eccube\Event\EccubeEvents;
use Eccube\Event\EventArgs;
use Eccube\Repository\CsvRepository;
use Eccube\Repository\Master\CsvTypeRepository;
use Eccube\Util\IdUtil;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bridge\Twig\Attribute\Template;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class CsvController
 */
class CsvController extends AbstractController
{
    /**
     * CsvController constructor.
     */
    public function __construct(protected CsvRepository $csvRepository, protected CsvTypeRepository $csvTypeRepository)
    {
    }

    /**
     * @return RedirectResponse|array<string, mixed>
     */
    #[Route(path: '/%eccube_admin_route%/setting/shop/csv/{id}', name: 'admin_setting_shop_csv', requirements: ['id' => '\d+'], defaults: ['id' => CsvType::CSV_TYPE_ORDER], methods: ['GET', 'POST'])]
    #[Template(template: '@admin/Setting/Shop/csv.twig')]
    public function index(Request $request, CsvType $CsvType): RedirectResponse|array
    {
        $builder = $this->createFormBuilder();

        $builder->add(
            'csv_type',
            \Eccube\Form\Type\Master\CsvType::class,
            [
                'label' => 'admin.setting.shop.csv.csv_columns',
                'required' => true,
                'constraints' => [
                    new Assert\NotBlank(),
                ],
                'data' => $CsvType,
            ]
        );

        $CsvNotOutput = $this->csvRepository->findBy(
            ['CsvType' => $CsvType, 'enabled' => false],
            ['sort_no' => 'ASC']
        );

        $builder->add(
            'csv_not_output',
            EntityType::class,
            [
                'class' => Csv::class,
                'choice_label' => 'disp_name',
                'required' => false,
                'expanded' => false,
                'multiple' => true,
                'choices' => $CsvNotOutput,
            ]
        );

        $CsvOutput = $this->csvRepository->findBy(
            ['CsvType' => $CsvType, 'enabled' => true],
            ['sort_no' => 'ASC']
        );

        $builder->add(
            'csv_output',
            EntityType::class,
            [
                'class' => Csv::class,
                'choice_label' => 'disp_name',
                'required' => false,
                'expanded' => false,
                'multiple' => true,
                'choices' => $CsvOutput,
            ]
        );

        $event = new EventArgs(
            [
                'builder' => $builder,
                'CsvOutput' => $CsvOutput,
                'CsvType' => $CsvType,
            ],
            $request
        );
        $this->eventDispatcher->dispatch($event, EccubeEvents::ADMIN_SETTING_SHOP_CSV_INDEX_INITIALIZE);

        $form = $builder->getForm();

        // csv_output/csv_not_outputのチェックに引っかかるため, tokenチェックは個別に行う
        if ('POST' === $request->getMethod() && $this->isTokenValid()) {
            $data = $request->get('form');

            // 選択中の CSV 種別の項目だけを更新対象にする
            $CsvsById = [];
            foreach (array_merge($CsvNotOutput, $CsvOutput) as $Csv) {
                $CsvsById[$Csv->getId()] = $Csv;
            }

            foreach (['csv_not_output' => false, 'csv_output' => true] as $key => $enabled) {
                if (!isset($data[$key]) || !is_array($data[$key])) {
                    continue;
                }
                $sortNo = 1;
                foreach ($data[$key] as $csv) {
                    $id = IdUtil::toId($csv);
                    if (null === $id || !isset($CsvsById[$id])) {
                        throw new BadRequestHttpException();
                    }
                    $c = $CsvsById[$id];
                    $c->setSortNo($sortNo);
                    $c->setEnabled($enabled);
                    $sortNo++;
                }
            }

            $this->entityManager->flush();

            $event = new EventArgs(
                [
                    'form' => $form,
                    'CsvOutput' => $CsvOutput,
                    'CsvType' => $CsvType,
                ],
                $request
            );
            $this->eventDispatcher->dispatch($event, EccubeEvents::ADMIN_SETTING_SHOP_CSV_INDEX_COMPLETE);

            $this->addSuccess('admin.common.save_complete', 'admin');

            return $this->redirectToRoute('admin_setting_shop_csv', ['id' => $CsvType->getId()]);
        }

        return [
            'form' => $form->createView(),
            'id' => $CsvType->getId(),
        ];
    }
}
