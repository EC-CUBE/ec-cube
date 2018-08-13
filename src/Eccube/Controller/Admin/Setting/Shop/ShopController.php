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
use Eccube\Entity\BaseInfo;
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
     * @var BaseInfo
     */
    protected $BaseInfo;

    /**
     * ShopController constructor.
     *
     * @param Twig_Environment $twig
     * @param BaseInfoRepository $baseInfoRepository
     */
    public function __construct(Twig_Environment $twig, BaseInfoRepository $baseInfoRepository)
    {
        $this->BaseInfo = $baseInfoRepository->get();
        $this->twig = $twig;
    }

    /**
     * @Route("/%eccube_admin_route%/setting/shop", name="admin_setting_shop")
     * @Template("@admin/Setting/Shop/shop_master.twig")
     *
     * @param Request $request
     *
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function index(Request $request, CacheUtil $cacheUtil)
    {
        $builder = $this->formFactory
            ->createBuilder(ShopMasterType::class, $this->BaseInfo);

        $CloneInfo = clone $this->BaseInfo;
        $this->entityManager->detach($CloneInfo);

        $event = new EventArgs(
            [
                'builder' => $builder,
                'BaseInfo' => $this->BaseInfo,
            ],
            $request
        );
        $this->eventDispatcher->dispatch(EccubeEvents::ADMIN_SETTING_SHOP_SHOP_INDEX_INITIALIZE, $event);

        $form = $builder->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $this->entityManager->persist($this->BaseInfo);
                $this->entityManager->flush();

                $event = new EventArgs(
                    [
                        'form' => $form,
                        'BaseInfo' => $this->BaseInfo,
                    ],
                    $request
                );
                $this->eventDispatcher->dispatch(
                    EccubeEvents::ADMIN_SETTING_SHOP_SHOP_INDEX_COMPLETE,
                    $event
                );

                $cacheUtil->clearCache();

                $this->addSuccess('admin.flash.register_completed', 'admin');

                return $this->redirectToRoute('admin_setting_shop');
            } else {
                $this->addError('admin.flash.register_failed', 'admin');
            }
        }

        $this->twig->addGlobal('BaseInfo', $CloneInfo);

        return [
            'form' => $form->createView(),
        ];
    }
}
