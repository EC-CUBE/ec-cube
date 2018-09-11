<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eccube\Controller\Admin\Setting\Shop;

use Eccube\Controller\AbstractController;
use Eccube\Entity\Master\CsvType;
use Eccube\Event\EccubeEvents;
use Eccube\Event\EventArgs;
use Eccube\Repository\CsvRepository;
use Eccube\Repository\Master\CsvTypeRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class CsvController
 */
class CsvController extends AbstractController
{
    /**
     * @var CsvRepository
     */
    protected $csvRepository;

    /**
     * @var CsvTypeRepository
     */
    protected $csvTypeRepository;

    /**
     * CsvController constructor.
     *
     * @param CsvRepository $csvRepository
     * @param CsvTypeRepository $csvTypeRepository
     */
    public function __construct(CsvRepository $csvRepository, CsvTypeRepository $csvTypeRepository)
    {
        $this->csvRepository = $csvRepository;
        $this->csvTypeRepository = $csvTypeRepository;
    }

    /**
     * @Route("/%eccube_admin_route%/setting/shop/csv/{id}",
     *     requirements={"id" = "\d+"},
     *     defaults={"id" = CsvType::CSV_TYPE_ORDER},
     *     name="admin_setting_shop_csv"
     * )
     * @Template("@admin/Setting/Shop/csv.twig")
     */
    public function index(Request $request, CsvType $CsvType)
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
                'class' => 'Eccube\Entity\Csv',
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
                'class' => 'Eccube\Entity\Csv',
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
        $this->eventDispatcher->dispatch(EccubeEvents::ADMIN_SETTING_SHOP_CSV_INDEX_INITIALIZE, $event);

        $form = $builder->getForm();

        if ('POST' === $request->getMethod()) {
            $data = $request->get('form');
            if (isset($data['csv_not_output'])) {
                $Csvs = $data['csv_not_output'];
                $sortNo = 1;
                foreach ($Csvs as $csv) {
                    $c = $this->csvRepository->find($csv);
                    $c->setSortNo($sortNo);
                    $c->setEnabled(false);
                    $sortNo++;
                }
            }

            if (isset($data['csv_output'])) {
                $Csvs = $data['csv_output'];
                $sortNo = 1;
                foreach ($Csvs as $csv) {
                    $c = $this->csvRepository->find($csv);
                    $c->setSortNo($sortNo);
                    $c->setEnabled(true);
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
            $this->eventDispatcher->dispatch(EccubeEvents::ADMIN_SETTING_SHOP_CSV_INDEX_COMPLETE, $event);

            $this->addSuccess('admin.common.save_complete', 'admin');

            return $this->redirectToRoute('admin_setting_shop_csv', ['id' => $CsvType->getId()]);
        }

        return [
            'form' => $form->createView(),
            'id' => $CsvType->getId(),
        ];
    }
}
