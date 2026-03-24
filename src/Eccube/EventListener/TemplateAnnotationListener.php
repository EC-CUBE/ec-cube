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

use Doctrine\Common\Annotations\AnnotationReader;
use Symfony\Bridge\Twig\Attribute\Template;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Twig\Environment;

/**
 * Handles #[Template] attributes and @Template annotations for backward compatibility.
 *
 * This listener provides backward compatibility for plugins using
 * the deprecated @Template annotation from SensioFrameworkExtraBundle.
 * New code should use #[Template] from symfony/twig-bridge.
 */
class TemplateAnnotationListener implements EventSubscriberInterface
{
    private Environment $twig;

    public function __construct(Environment $twig)
    {
        $this->twig = $twig;
    }

    /**
     * Detect template from attribute or annotation and store it on the request.
     */
    public function onKernelController(ControllerEvent $event)
    {
        $controller = $event->getController();

        if (!is_array($controller) && !($controller instanceof \Closure)) {
            return;
        }

        if (is_array($controller)) {
            $reflMethod = new \ReflectionMethod($controller[0], $controller[1]);
        } else {
            return;
        }

        // Check for #[Template] PHP 8 attribute
        $attrs = $reflMethod->getAttributes(Template::class);
        if (!empty($attrs)) {
            $templateAttr = $attrs[0]->newInstance();
            $event->getRequest()->attributes->set('_template', $templateAttr->template);

            return;
        }

        // Fallback: check for @Template annotation from SensioFrameworkExtraBundle
        // This provides backward compatibility for plugins
        try {
            $reader = new AnnotationReader();
            $anno = $reader->getMethodAnnotation($reflMethod, 'Sensio\Bundle\FrameworkExtraBundle\Configuration\Template');
            if ($anno) {
                trigger_deprecation(
                    'ec-cube/ec-cube',
                    '4.3',
                    'Using @Template annotation from SensioFrameworkExtraBundle is deprecated, use #[Template] attribute from symfony/twig-bridge instead.'
                );
                // SensioFrameworkExtraBundle's Template annotation stores template in different ways
                $template = method_exists($anno, 'getTemplate') ? $anno->getTemplate() : (property_exists($anno, 'value') ? $anno->value : null);
                if ($template) {
                    $event->getRequest()->attributes->set('_template', $template);
                }
            }
        } catch (\Throwable $e) {
            // If Sensio classes are not available, silently skip
        }
    }

    /**
     * If the controller returned an array and a template was detected, render it.
     */
    public function onKernelView(ViewEvent $event)
    {
        $request = $event->getRequest();
        $template = $request->attributes->get('_template');

        if (!$template) {
            return;
        }

        $result = $event->getControllerResult();

        if (!is_array($result)) {
            return;
        }

        $response = new Response($this->twig->render($template, $result));
        $event->setResponse($response);
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::CONTROLLER => ['onKernelController', -128],
            KernelEvents::VIEW => ['onKernelView', 128],
        ];
    }
}
