<?php

namespace Eccube\EventListener;

use Doctrine\ORM\NoResultException;
use Eccube\Entity\Master\DeviceType;
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

    public function __construct(
        Environment $twig,
        BaseInfoRepository $baseInfoRepository,
        PageRepository $pageRepository,
        DeviceTypeRepository $deviceTypeRepository,
        Context $context
    ) {
        $this->twig = $twig;
        $this->baseInfoRepository = $baseInfoRepository;
        $this->pageRepository = $pageRepository;
        $this->deviceTypeRepository = $deviceTypeRepository;
        $this->requestContext = $context;
    }

    /**
     * @param GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        $this->twig->addGlobal('BaseInfo', $this->baseInfoRepository->get());

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
        $route = $event->getRequest()->attributes->get('_route');

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
        // TODO 要実装
        $this->twig->addGlobal('AuthorityRoles', []);
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
