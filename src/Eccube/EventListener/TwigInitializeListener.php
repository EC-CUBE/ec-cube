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

namespace Eccube\EventListener;

use Doctrine\ORM\NoResultException;
use Eccube\Common\EccubeConfig;
use Eccube\Entity\AuthorityRole;
use Eccube\Entity\Master\DeviceType;
use Eccube\Entity\Member;
use Eccube\Repository\AuthorityRoleRepository;
use Eccube\Repository\BaseInfoRepository;
use Eccube\Repository\Master\DeviceTypeRepository;
use Eccube\Repository\PageRepository;
use Eccube\Request\Context;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Twig\Environment;

class TwigInitializeListener implements EventSubscriberInterface
{
    /**
     * @var Environment
     */
    protected $twig;

    /**
     * @var BaseInfoRepository
     */
    protected $baseInfoRepository;

    /**
     * @var DeviceTypeRepository
     */
    protected $deviceTypeRepository;

    /**
     * @var PageRepository
     */
    protected $pageRepository;

    /**
     * @var Context
     */
    protected $requestContext;

    /**
     * @var AuthorityRoleRepository
     */
    private $authorityRoleRepository;

    /**
     * @var EccubeConfig
     */
    private $eccubeConfig;

    public function __construct(
        Environment $twig,
        BaseInfoRepository $baseInfoRepository,
        PageRepository $pageRepository,
        DeviceTypeRepository $deviceTypeRepository,
        AuthorityRoleRepository $authorityRoleRepository,
        EccubeConfig $eccubeConfig,
        Context $context
    ) {
        $this->twig = $twig;
        $this->baseInfoRepository = $baseInfoRepository;
        $this->pageRepository = $pageRepository;
        $this->deviceTypeRepository = $deviceTypeRepository;
        $this->authorityRoleRepository = $authorityRoleRepository;
        $this->eccubeConfig = $eccubeConfig;
        $this->requestContext = $context;
    }

    /**
     * @param GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        $globals = $this->twig->getGlobals();
        if (array_key_exists('BaseInfo', $globals) && $globals['BaseInfo'] === null) {
            $this->twig->addGlobal('BaseInfo', $this->baseInfoRepository->get());
        }

        if (!$event->isMasterRequest()) {
            return;
        }

        if ($this->requestContext->isAdmin()) {
            $this->setAdminGlobals($event);
        } else {
            $this->setFrontVaribales($event);
        }
    }

    /**
     * @param GetResponseEvent $event
     */
    public function setFrontVaribales(GetResponseEvent $event)
    {
        /** @var \Symfony\Component\HttpFoundation\ParameterBag $attributes */
        $attributes = $event->getRequest()->attributes;
        $route = $attributes->get('_route');
        if ($route == 'user_data') {
            $routeParams = $attributes->get('_route_params', []);
            $route = isset($routeParams['route']) ? $routeParams['route'] : $attributes->get('route', '');
        }

        // TODO レイアウトの端末判定を実装
        $DeviceType = $this->deviceTypeRepository->find(DeviceType::DEVICE_TYPE_PC);

        try {
            $qb = $this->pageRepository->createQueryBuilder('p');
            $Page = $qb->select('p, pll,l, bp, b')
                ->leftJoin('p.PageLayouts', 'pll')
                ->leftJoin('pll.Layout', 'l')
                ->leftJoin('l.BlockPositions', 'bp')
                ->leftJoin('bp.Block', 'b')
                ->where('p.url = :route')
                ->andWhere('l.DeviceType = :DeviceType')
                ->orderBy('bp.block_row', 'ASC')
                ->setParameter('route', $route)
                ->setParameter('DeviceType', $DeviceType)
                ->getQuery()
                ->getSingleResult();
        } catch (NoResultException $e) {
            $Page = $this->pageRepository->newPage($DeviceType);
        }

        $this->twig->addGlobal('Page', $Page);
        $this->twig->addGlobal('title', $Page->getName());
    }

    /**
     * @param GetResponseEvent $event
     */
    public function setAdminGlobals(GetResponseEvent $event)
    {
        // メニュー表示用配列.
        $menus = [];
        $this->twig->addGlobal('menus', $menus);

        // メニューの権限制御.
        $Member = $this->requestContext->getCurrentUser();
        $AuthorityRoles = [];
        if ($Member instanceof Member) {
            $AuthorityRoles = $this->authorityRoleRepository->findBy(['Authority' => $this->requestContext->getCurrentUser()->getAuthority()]);
        }
        $roles = array_map(function (AuthorityRole $AuthorityRole) use ($event) {
            return $event->getRequest()->getBaseUrl().'/'.$this->eccubeConfig['eccube_admin_route'].$AuthorityRole->getDenyUrl();
        }, $AuthorityRoles);
        $this->twig->addGlobal('AuthorityRoles', $roles);
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => [
                // SecurityServiceProviderで、認証処理が完了した後に実行.
                ['onKernelRequest', 6],
            ],
        ];
    }
}
