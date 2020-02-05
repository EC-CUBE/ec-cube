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

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

/**
 * Safariの一部のバージョンでSameSite=Noneを正しく扱われないバグ対応.
 *
 * @see https://bugs.webkit.org/show_bug.cgi?id=198181
 */
class SameSiteCookieHotfixListener implements EventSubscriberInterface
{
    private static $TARGET_UA_PATTERNS = [
        '/^.*iPhone; CPU iPhone OS 1[0-2].*$/',
        '/^.*iPad; CPU OS 1[0-2].*$/',
        '/^.*iPod touch; CPU iPhone OS 1[0-2].*$/',
        '/^.*Macintosh; Intel Mac OS X.*Version\/1[0-2].*Safari.*$/',
    ];

    public function onKernelRequest(GetResponseEvent $event)
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        $ua = $event->getRequest()->headers->get('User-Agent');
        $isUnsupported = array_filter(self::$TARGET_UA_PATTERNS, function ($pattern) use ($ua) {
            return preg_match($pattern, $ua);
        });

        if ($isUnsupported) {
            $event->setResponse(new Response('お使いのブラウザーではご利用いただけません。'));
        }
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            'kernel.request' => ['onKernelRequest', 256],
        ];
    }
}
