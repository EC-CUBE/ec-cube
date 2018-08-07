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
use Eccube\Repository\PageLayoutRepository;
use Eccube\Repository\BlockPositionRepository;
use Eccube\Request\Context;
use SunCat\MobileDetectBundle\DeviceDetector\MobileDetector;
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
     * @var PageLayoutRepository
     */
    protected $pageLayoutRepository;

    /**
     * @var BlockPositionRepository
     */
    protected $blockPositionRepository;

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

    /**
     * @var MobileDetector
     */
    private $mobileDetector;

    /**
     * TwigInitializeListener constructor.
     *
     * @param Environment $twig
     * @param BaseInfoRepository $baseInfoRepository
     * @param PageRepository $pageRepository
     * @param PageLayoutRepository $pageLayoutRepository
     * @param BlockPositionRepository $blockPositionRepository
     * @param DeviceTypeRepository $deviceTypeRepository
     * @param AuthorityRoleRepository $authorityRoleRepository
     * @param EccubeConfig $eccubeConfig
     * @param Context $context
     * @param MobileDetector $mobileDetector
     */
    public function __construct(
        Environment $twig,
        BaseInfoRepository $baseInfoRepository,
        PageRepository $pageRepository,
        PageLayoutRepository $pageLayoutRepository,
        BlockPositionRepository $blockPositionRepository,
        DeviceTypeRepository $deviceTypeRepository,
        AuthorityRoleRepository $authorityRoleRepository,
        EccubeConfig $eccubeConfig,
        Context $context,
        MobileDetector $mobileDetector
    ) {
        $this->twig = $twig;
        $this->baseInfoRepository = $baseInfoRepository;
        $this->pageRepository = $pageRepository;
        $this->pageLayoutRepository = $pageLayoutRepository;
        $this->blockPositionRepository = $blockPositionRepository;
        $this->deviceTypeRepository = $deviceTypeRepository;
        $this->authorityRoleRepository = $authorityRoleRepository;
        $this->eccubeConfig = $eccubeConfig;
        $this->requestContext = $context;
        $this->mobileDetector = $mobileDetector;
    }

    /**
     * @param GetResponseEvent $event
     *
     * @throws NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
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
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function setFrontVaribales(GetResponseEvent $event)
    {
        $request = $event->getRequest();
        /** @var \Symfony\Component\HttpFoundation\ParameterBag $attributes */
        $attributes = $request->attributes;
        $route = $attributes->get('_route');
        if ($route == 'user_data') {
            $routeParams = $attributes->get('_route_params', []);
            $route = isset($routeParams['route']) ? $routeParams['route'] : $attributes->get('route', '');
        }

        $type = DeviceType::DEVICE_TYPE_PC;
        if ($this->mobileDetector->isMobile()) {
            $type = DeviceType::DEVICE_TYPE_SP;
        }
        $DeviceType = $this->deviceTypeRepository->find($type);

        try {
            $Page = $this->pageRepository->getByUrl($DeviceType, $route);
        } catch (NoResultException $e) {
            try {
                log_info('fallback to PC layout');
                $DeviceType = $this->deviceTypeRepository->find(DeviceType::DEVICE_TYPE_PC);
                $Page = $this->pageRepository->getByUrl($DeviceType, $route);
            } catch (NoResultException $e) {
                $Page = $this->pageRepository->newPage($DeviceType);
            }
        }

        if ($request->get('preview')) {
            $Page->getPageLayouts()->clear();
            $PageLayouts = $this->pageLayoutRepository->findBy(['layout_id' => 0]);
            foreach ($PageLayouts as $PageLayout) {
                $Page->addPageLayout($PageLayout);
            }

            $Page->getBlockPositions()->clear();
            $BlockPositions = $this->blockPositionRepository->findBy(['layout_id' => 0]);
            foreach ($BlockPositions as $BlockPosition) {
                $Page->addBlockPosition($BlockPosition);
            }
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
