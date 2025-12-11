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

namespace Eccube\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Eccube\Common\Constant;
use Eccube\Common\EccubeConfig;
use Eccube\Session\Session;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController as Controller;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\FlashBagAwareSessionInterface;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Service\Attribute\Required;
use Symfony\Contracts\Translation\TranslatorInterface;

class AbstractController extends Controller
{
    protected EccubeConfig $eccubeConfig;

    protected EntityManagerInterface $entityManager;

    protected TranslatorInterface $translator;

    protected FormFactoryInterface $formFactory;

    protected EventDispatcherInterface $eventDispatcher;

    protected FlashBagAwareSessionInterface $session;

    protected RouterInterface $router;

    #[Required]
    public function setEccubeConfig(EccubeConfig $eccubeConfig): void
    {
        $this->eccubeConfig = $eccubeConfig;
    }

    #[Required]
    public function setEntityManager(EntityManagerInterface $entityManager): void
    {
        $this->entityManager = $entityManager;
    }

    #[Required]
    public function setTranslator(TranslatorInterface $translator): void
    {
        $this->translator = $translator;
    }

    #[Required]
    public function setSession(Session $session): void
    {
        $this->session = $session;
    }

    #[Required]
    public function setFormFactory(FormFactoryInterface $formFactory): void
    {
        $this->formFactory = $formFactory;
    }

    #[Required]
    public function setEventDispatcher(EventDispatcherInterface $eventDispatcher): void
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    #[Required]
    public function setRouter(RouterInterface $router): void
    {
        $this->router = $router;
    }

    public function addSuccess(string $message, string $namespace = 'front'): void
    {
        $this->addFlash('eccube.'.$namespace.'.success', $message);
    }

    public function addSuccessOnce(string $message, string $namespace = 'front'): void
    {
        $this->addFlashOnce('eccube.'.$namespace.'.success', $message);
    }

    public function addError(string $message, string $namespace = 'front'): void
    {
        $this->addFlash('eccube.'.$namespace.'.error', $message);
    }

    public function addErrorOnce(string $message, string $namespace = 'front'): void
    {
        $this->addFlashOnce('eccube.'.$namespace.'.error', $message);
    }

    public function addDanger(string $message, string $namespace = 'front'): void
    {
        $this->addFlash('eccube.'.$namespace.'.danger', $message);
    }

    public function addDangerOnce(string $message, string $namespace = 'front'): void
    {
        $this->addFlashOnce('eccube.'.$namespace.'.danger', $message);
    }

    public function addWarning(string $message, string $namespace = 'front'): void
    {
        $this->addFlash('eccube.'.$namespace.'.warning', $message);
    }

    public function addWarningOnce(string $message, string $namespace = 'front'): void
    {
        $this->addFlashOnce('eccube.'.$namespace.'.warning', $message);
    }

    public function addInfo(string $message, string $namespace = 'front'): void
    {
        $this->addFlash('eccube.'.$namespace.'.info', $message);
    }

    public function addInfoOnce(string $message, string $namespace = 'front'): void
    {
        $this->addFlashOnce('eccube.'.$namespace.'.info', $message);
    }

    public function addRequestError(string $message, string $namespace = 'front'): void
    {
        $this->addFlash('eccube.'.$namespace.'.request.error', $message);
    }

    public function addRequestErrorOnce(string $message, string $namespace = 'front'): void
    {
        $this->addFlashOnce('eccube.'.$namespace.'.request.error', $message);
    }

    public function clearMessage(): void
    {
        /** @var Session $session */
        $session = $this->session;
        $session->getFlashBag()->clear();
    }

    public function deleteMessage(): void
    {
        $this->clearMessage();
        $this->addWarning('admin.common.delete_error_already_deleted', 'admin');
    }

    public function hasMessage(string $type): bool
    {
        /** @var Session $session */
        $session = $this->session;

        return $session->getFlashBag()->has($type);
    }

    public function addFlashOnce(string $type, string $message): void
    {
        if (!$this->hasMessage($type)) {
            $this->addFlash($type, $message);
        }
    }

    /**
     * {@inheritdoc}
     *
     * @param string $message
     */
    #[\Override]
    protected function addFlash(string $type, $message): void
    {
        try {
            parent::addFlash($type, $message);
        } catch (\LogicException) {
            // fallback session
            /** @var Session $session */
            $session = $this->session;
            $session->getFlashBag()->add($type, $message);
        }
    }

    public function setLoginTargetPath(string $targetPath, ?string $namespace = null): void
    {
        if (is_null($namespace)) {
            /** @var Session $session */
            $session = $this->session;
            $session->getFlashBag()->set('eccube.login.target.path', $targetPath);
        } else {
            /** @var Session $session */
            $session = $this->session;
            $session->getFlashBag()->set('eccube.'.$namespace.'.login.target.path', $targetPath);
        }
    }

    /**
     * Forwards the request to another controller.
     *
     * @param string $route The name of the route
     * @param array<string, string>  $path An array of path parameters
     * @param array<string, string>  $query An array of query parameters
     *
     * @return Response A Response instance
     */
    public function forwardToRoute(string $route, array $path = [], array $query = []): Response
    {
        $Route = $this->router->getRouteCollection()->get($route);
        if (!$Route) {
            throw new RouteNotFoundException(sprintf('The named route "%s" as such route does not exist.', $route));
        }

        return $this->forward($Route->getDefault('_controller'), $path, $query);
    }

    /**
     * Checks the validity of a CSRF token.
     *
     * if token is invalid, throws AccessDeniedHttpException.
     *
     * @throws AccessDeniedHttpException
     */
    protected function isTokenValid(): bool
    {
        /** @var Request $request */
        $request = $this->container->get('request_stack')->getCurrentRequest();
        $token = $request->get(Constant::TOKEN_NAME) ?: $request->headers->get('ECCUBE-CSRF-TOKEN');

        if (!$this->isCsrfTokenValid(Constant::TOKEN_NAME, $token)) {
            throw new AccessDeniedHttpException('CSRF token is invalid.');
        }

        return true;
    }
}
