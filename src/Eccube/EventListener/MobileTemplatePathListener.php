<?php

namespace Eccube\EventListener;

use Eccube\Request\Context;
use SunCat\MobileDetectBundle\DeviceDetector\MobileDetector;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Twig\Environment;

class MobileTemplatePathListener implements EventSubscriberInterface
{
    /**
     * @var Context
     */
    protected $context;

    /**
     * @var Environment
     */
    protected $twig;

    /**
     * @var MobileDetector
     */
    protected $detector;

    /**
     * @var string
     */
    protected $projectRoot;

    public function __construct(Context $context, Environment $twig, MobileDetector $detector, $projectRoot)
    {
        $this->context = $context;
        $this->twig = $twig;
        $this->detector = $detector;
        $this->projectRoot = $projectRoot;
    }

    public function onKernelRequest(GetResponseEvent $event)
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        // 管理画面の場合は実行しない.
        if ($this->context->isAdmin()) {
            return;
        }

        if (!$this->detector->isMobile()) {
            return;
        }

        // TODO テンプレートパスの設定箇所を調整.
        $paths = [
            $this->projectRoot.'/src/Eccube/Resource/template/smartphone',
        ];

        $loader = new \Twig_Loader_Chain(array(
            new \Twig_Loader_Filesystem($paths),
            $this->twig->getLoader(),
        ));

        $this->twig->setLoader($loader);
    }

    public static function getSubscribedEvents()
    {
        return [
            'kernel.request' => ['onKernelRequest', 512],
        ];
    }
}
