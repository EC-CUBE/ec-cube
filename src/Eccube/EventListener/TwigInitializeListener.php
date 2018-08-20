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
use SunCat\MobileDetectBundle\DeviceDetector\MobileDetector;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
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

    /**
     * @var MobileDetector
     */
    private $mobileDetector;

    /**
     * @var UrlGeneratorInterface
     */
    private $router;

    /**
     * TwigInitializeListener constructor.
     *
     * @param Environment $twig
     * @param BaseInfoRepository $baseInfoRepository
     * @param PageRepository $pageRepository
     * @param DeviceTypeRepository $deviceTypeRepository
     * @param AuthorityRoleRepository $authorityRoleRepository
     * @param EccubeConfig $eccubeConfig
     * @param Context $context
     * @param MobileDetector $mobileDetector
     * @param UrlGeneratorInterface $router
     */
    public function __construct(
        Environment $twig,
        BaseInfoRepository $baseInfoRepository,
        PageRepository $pageRepository,
        DeviceTypeRepository $deviceTypeRepository,
        AuthorityRoleRepository $authorityRoleRepository,
        EccubeConfig $eccubeConfig,
        Context $context,
        MobileDetector $mobileDetector,
        UrlGeneratorInterface $router
    ) {
        $this->twig = $twig;
        $this->baseInfoRepository = $baseInfoRepository;
        $this->pageRepository = $pageRepository;
        $this->deviceTypeRepository = $deviceTypeRepository;
        $this->authorityRoleRepository = $authorityRoleRepository;
        $this->eccubeConfig = $eccubeConfig;
        $this->requestContext = $context;
        $this->mobileDetector = $mobileDetector;
        $this->router = $router;
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

        if ($this->requestContext->isAdmin()) {
            $this->setAdminGlobals($event);
        } else {
            $this->setFrontVaribales($event);
        }
        
        if (!$event->isMasterRequest()) {
            return;
        }
    }

    /**
     * @param GetResponseEvent $event
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
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

        $type = DeviceType::DEVICE_TYPE_PC;
        if ($this->mobileDetector->isMobile()) {
            $type = DeviceType::DEVICE_TYPE_MB;
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
        $eccubeNav = $this->eccubeConfig['eccube_nav'];

        $Member = $this->requestContext->getCurrentUser();
        if ($Member instanceof Member) {
            $AuthorityRoles = $this->authorityRoleRepository->findBy(['Authority' => $Member->getAuthority()]);
            $baseUrl = $event->getRequest()->getBaseUrl() . '/' . $this->eccubeConfig['eccube_admin_route'];
            $eccubeNav = $this->getDisplayEccubeNav($eccubeNav, $AuthorityRoles, $baseUrl);
        }
        $this->twig->addGlobal('eccubeNav', $eccubeNav);
    }

    /**
     * URLに対する権限有無チェックして表示するNavを返す
     *
     * @param array $parentNav
     * @param AuthorityRole[] $AuthorityRoles
     * @param string $baseUrl
     *
     * @return array
     */
    private function getDisplayEccubeNav($parentNav, $AuthorityRoles, $baseUrl)
    {
        foreach ($parentNav as $key => $childNav) {
            if (array_key_exists('children', $childNav) && count($childNav['children']) > 0) {
                // 子のメニューがある場合は子の権限チェック
                $parentNav[$key]['children'] = $this->getDisplayEccubeNav($childNav['children'], $AuthorityRoles, $baseUrl);

                if (count($parentNav[$key]['children']) <= 0) {
                    // 子が存在しない場合は配列から削除
                    unset($parentNav[$key]);
                }
            } elseif (array_key_exists('url', $childNav)) {
                // 子のメニューがなく、URLが設定されている場合は権限があるURLか確認
                $param = array_key_exists('param', $childNav) ? $childNav['param'] : [];
                $url = $this->router->generate($childNav['url'], $param);
                foreach ($AuthorityRoles as $AuthorityRole) {
                    $denyUrl = str_replace('/', '\/', $baseUrl . $AuthorityRole->getDenyUrl());
                    if (preg_match("/^({$denyUrl})/i", $url)) {
                        // 権限がないURLの場合は配列から削除
                        unset($parentNav[$key]);
                        break;
                    }
                }
            }
        }

        return $parentNav;
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
