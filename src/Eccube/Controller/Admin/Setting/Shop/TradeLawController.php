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
use Eccube\Event\EccubeEvents;
use Eccube\Event\EventArgs;
use Eccube\Form\Type\Admin\TradeLawType;
use Eccube\Repository\TradeLawRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class TradeLawController extends AbstractController
{
    protected TradeLawRepository $tradeLawRepository;

    /**
     * @param TradeLawRepository $tradeLawRepository
     */
    public function __construct(
        TradeLawRepository $tradeLawRepository
    ) {
        $this->tradeLawRepository = $tradeLawRepository;
    }

    /**
     * 特定商取引法設定の初期表示・登録
     *
     * @Route("/%eccube_admin_route%/setting/shop/tradelaw", name="admin_setting_shop_tradelaw", methods={"GET", "POST"})
     * @Template("@admin/Setting/Shop/tradelaw.twig")
     *
     * @param Request $request
     */
    public function index(Request $request)
    {
        $tradeLawDetails = $this->tradeLawRepository->findBy([], ['sortNo' => 'ASC']);
        $builder = $this->formFactory->createBuilder();
        $builder
            ->add(
                'TradeLaws',
                CollectionType::class,
                [
                    'entry_type' => TradeLawType::class,
                    'data' => $tradeLawDetails,
                ]
            );
        $form = $builder->getForm();
        $form->handleRequest($request);

        $event = new EventArgs(
            [
                'TradeLaw' => $tradeLawDetails,
            ],
            $request
        );

        $form = $builder->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            foreach ($form['TradeLaws'] as $child) {
                $TradeLaw = $child->getData();
                $this->entityManager->persist($TradeLaw);
            }
            $this->entityManager->flush();

            $this->addSuccess('admin.common.save_complete', 'admin');

            $this->eventDispatcher->dispatch($event, EccubeEvents::ADMIN_SETTING_SHOP_TRADE_LAW_POST_COMPLETE);

            return $this->redirectToRoute('admin_setting_shop_tradelaw');
        }

        $this->eventDispatcher->dispatch($event, EccubeEvents::ADMIN_SETTING_SHOP_TRADE_LAW_INDEX_COMPLETE);

        return [
            'form' => $form->createView(),
            'tradeLawDetails' => $tradeLawDetails,
        ];
    }
}
