<?php


namespace Eccube\EventListener;


use Eccube\Common\EccubeConfig;
use Eccube\Request\Context;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\RouterInterface;

class MaintenanceListener implements EventSubscriberInterface
{
    /**
     * @var EccubeConfig
     */
    private $eccubeConfig;

    /**
     * @var Context
     */
    private $requestContext;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var SessionInterface
     */
    private $session;

    public function __construct(
        EccubeConfig $eccubeConfig,
        Context $requestContext,
        RouterInterface $router,
        SessionInterface $session
    )
    {
        $this->eccubeConfig = $eccubeConfig;
        $this->requestContext = $requestContext;
        $this->router = $router;
        $this->session = $session;
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => [
                ['onKernelRequest', -255]
            ]
        ];
    }

    public function onKernelRequest(GetResponseEvent $event)
    {
        if (false === $event->isMasterRequest()) {
            return;
        }

        if ($this->session->get('_security_admin')) {
            return;
        }

        $project_dir = $this->eccubeConfig->get('kernel.project_dir');
        $maintenanceFile = env('ECCUBE_MAINTENANCE_FILE_PATH', $project_dir . '/.maintenance');

        if (file_exists($maintenanceFile)) {
            if ($this->requestContext->isFront()) {
                ob_start();
                $locale = env('ECCUBE_LOCALE');
                $templateCode = env('ECCUBE_TEMPLATE_CODE');
                $baseUrl = \htmlspecialchars(\rawurldecode($event->getRequest()->getBaseUrl()), ENT_QUOTES);
                require($project_dir . '/maintenance.php');
                $maintenance = ob_get_clean();

                $event->setResponse(new Response($maintenance, 503));
            }
        }
    }
}
