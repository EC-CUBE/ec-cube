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

namespace Eccube\EventListener;

use Eccube\Common\EccubeConfig;
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
     * @var EccubeConfig
     */
    protected $eccubeConfig;

    public function __construct(Context $context, Environment $twig, MobileDetector $detector, EccubeConfig $eccubeConfig)
    {
        $this->context = $context;
        $this->twig = $twig;
        $this->detector = $detector;
        $this->eccubeConfig = $eccubeConfig;
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

        $paths = [
            $this->eccubeConfig->get('eccube_theme_src_dir').'/smartphone',
        ];

        if (is_dir($this->eccubeConfig->get('eccube_theme_app_dir').'/smartphone')) {
            $paths = [
                $this->eccubeConfig->get('eccube_theme_app_dir').'/smartphone',
                $this->eccubeConfig->get('eccube_theme_src_dir').'/smartphone',
            ];
        }

        $loader = new \Twig_Loader_Chain([
            new \Twig_Loader_Filesystem($paths),
            $this->twig->getLoader(),
        ]);

        $this->twig->setLoader($loader);
    }

    public static function getSubscribedEvents()
    {
        return [
            'kernel.request' => ['onKernelRequest', 512],
        ];
    }
}
