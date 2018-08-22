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

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\KernelEvents;

class ExceptionListener implements EventSubscriberInterface
{
    /**
     * @var \Twig_Environment
     */
    private $twig;

    /**
     * ExceptionListener constructor.
     */
    public function __construct(\Twig_Environment $twig)
    {
        $this->twig = $twig;
    }

    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        $title = 'システムエラーが発生しました。';
        $message = '大変お手数ですが、サイト管理者までご連絡ください。';
        $statusCode = Response::HTTP_INTERNAL_SERVER_ERROR;

        $exception = $event->getException();

        if ($exception instanceof HttpExceptionInterface) {
            $statusCode = $exception->getStatusCode();
            switch ($statusCode) {
                case 400:
                case 401:
                case 403:
                case 405:
                case 406:
                    $title = 'アクセスできません。';
                    if ($exception->getMessage()) {
                        $message = $exception->getMessage();
                    } else {
                        $message = 'お探しのページはアクセスができない状況にあるか、移動もしくは削除された可能性があります。';
                    }
                    break;
                case 404:
                    $title = 'ページがみつかりません。';
                    $message = 'URLに間違いがないかご確認ください。';
                    break;
                default:
                    break;
            }
        }

        try {
            $content = $this->twig->render('error.twig', [
                'error_title' => $title,
                'error_message' => $message,
            ]);
        } catch (\Exception $ignore) {
            $content = $title;
        }

        $event->setResponse(Response::create($content, $statusCode));
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * The array keys are event names and the value can be:
     *
     *  * The method name to call (priority defaults to 0)
     *  * An array composed of the method name to call and the priority
     *  * An array of arrays composed of the method names to call and respective
     *    priorities, or 0 if unset
     *
     * For instance:
     *
     *  * array('eventName' => 'methodName')
     *  * array('eventName' => array('methodName', $priority))
     *  * array('eventName' => array(array('methodName1', $priority), array('methodName2')))
     *
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::EXCEPTION => ['onKernelException'],
        ];
    }
}
