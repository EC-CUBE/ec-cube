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
use Eccube\Form\Type\Admin\ShopMasterType;
use Eccube\Repository\BaseInfoRepository;
use Eccube\Util\CacheUtil;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Twig_Environment;

/**
 * Class ShopController
 */
class ShopController extends AbstractController
{
    /**
     * @var Twig_Environment
     */
    protected $twig;

    /**
     * @var BaseInfoRepository
     */
    protected $baseInfoRepository;

    /**
     * ShopController constructor.
     *
     * @param Twig_Environment $twig
     * @param BaseInfoRepository $baseInfoRepository
     */
    public function __construct(Twig_Environment $twig, BaseInfoRepository $baseInfoRepository)
    {
        $this->baseInfoRepository = $baseInfoRepository;
        $this->twig = $twig;
    }

    /**
     * @Route("/%eccube_admin_route%/setting/shop", name="admin_setting_shop", methods={"GET", "POST"})
     * @Template("@admin/Setting/Shop/shop_master.twig")
     *
     * @param Request $request
     *
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function index(Request $request, CacheUtil $cacheUtil)
    {
        $BaseInfo = $this->baseInfoRepository->get();
        $builder = $this->formFactory
            ->createBuilder(ShopMasterType::class, $BaseInfo);

        $CloneInfo = clone $BaseInfo;
        $this->entityManager->detach($CloneInfo);

        $event = new EventArgs(
            [
                'builder' => $builder,
                'BaseInfo' => $BaseInfo,
            ],
            $request
        );
        $this->eventDispatcher->dispatch(EccubeEvents::ADMIN_SETTING_SHOP_SHOP_INDEX_INITIALIZE, $event);

        $form = $builder->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($BaseInfo);
            $this->entityManager->flush();

            $event = new EventArgs(
                [
                    'form' => $form,
                    'BaseInfo' => $BaseInfo,
                ],
                $request
            );
            $this->eventDispatcher->dispatch(
                EccubeEvents::ADMIN_SETTING_SHOP_SHOP_INDEX_COMPLETE,
                $event
            );

            // キャッシュの削除
            $cacheUtil->clearDoctrineCache();

            $this->addSuccess('admin.common.save_complete', 'admin');

            return $this->redirectToRoute('admin_setting_shop');
        }

        $this->twig->addGlobal('BaseInfo', $CloneInfo);

        return [
            'form' => $form->createView(),
        ];
    }
}
