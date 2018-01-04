<?php

namespace Eccube\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * ログ出力リスナー
 *
 * @package Eccube\EventListener
 */
class PageLayoutListener implements EventSubscriberInterface
{
    /**
     * @var \Twig_Environment
     */
    protected $twig;
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $em;

    public function __construct(\Twig_Environment $twig, \Doctrine\ORM\EntityManager $em)
    {
        $this->em = $em;
        $this->twig = $twig;
    }

    /**
     * @param GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        $route = $event->getRequest()->attributes->get('_route');

        // TODO
        $menus = array('', '', '');
        $this->twig->addGlobal('menus', $menus);
        $this->twig->addGlobal('AuthorityRoles', []);

        $this->twig->addGlobal(
            'BaseInfo',
            $this->em->find(\Eccube\Entity\BaseInfo::class, 1));

        try {
        //     // $device_type_id = $this->app['mobile_detect.device_type'];

        //     // // TODO デバッグ用
        //     // if ($request->query->has('device_type_id')) {
        //     //     $device_type_id = $request->get('device_type_id', \Eccube\Entity\Master\DeviceType::DEVICE_TYPE_PC);
        //     // }

            $DeviceType = $this->em->find(\Eccube\Entity\Master\DeviceType::class, \Eccube\Entity\Master\DeviceType::DEVICE_TYPE_PC);
            $qb = $this->em->getRepository(\Eccube\Entity\Page::class)->createQueryBuilder('p');
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
        } catch (\Doctrine\ORM\NoResultException $e) {
            $Page = $this->em->getRepository(\Eccube\Entity\Page::class)->newPage($DeviceType);
        }
        $this->twig->addGlobal('Page', $Page);
        $this->twig->addGlobal('title', $Page->getName());
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => [
                // SecurityServiceProviderで、認証処理が完了した後に実行.
                ['onKernelRequest', 6]
            ]
        ];
    }

    /**
     * ルーティング名を取得する.
     *
     * @param $request
     * @return string
     */
    private function getRoute($request)
    {
        return $request->attributes->get('_route');
    }
}
