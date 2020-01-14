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

use Eccube\Request\Context;
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
     * @var Context
     */
    protected $requestContext;

    /**
     * ExceptionListener constructor.
     */
    public function __construct(\Twig_Environment $twig, Context $requestContext)
    {
        $this->twig = $twig;
        $this->requestContext = $requestContext;
    }

    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        $title = trans('exception.error_title');
        $message = trans('exception.error_message');
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
                    $infoMess = 'アクセスできません。';
                    $title = trans('exception.error_title_can_not_access');
                    if ($exception->getMessage()) {
                        $message = $exception->getMessage();
                    } else {
                        $message = trans('exception.error_message_can_not_access');
                    }
                    break;
                case 404:
                    $infoMess = 'ページがみつかりません。';
                    $title = trans('exception.error_title_not_found');
                    $message = trans('exception.error_message_not_found');
                    break;
                default:
                    break;
            }
        }

        if (isset($infoMess)) {
            log_info($infoMess, [
                $exception->getMessage(),
                $exception->getFile(),
                $exception->getLine(),
            ]);
        } else {
            log_error('システムエラーが発生しました。', [
                $exception->getMessage(),
                $exception->getFile(),
                $exception->getLine(),
                $exception->getTraceAsString(),
            ]);
        }

        try {
            $file = $this->requestContext->isAdmin() ? '@admin/error.twig' : 'error.twig';
            $content = $this->twig->render($file, [
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
