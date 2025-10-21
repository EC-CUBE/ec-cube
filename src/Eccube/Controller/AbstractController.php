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
    /**
     * @var EccubeConfig
     */
    protected $eccubeConfig;

    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * @var FormFactoryInterface
     */
    protected $formFactory;

    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * @var FlashBagAwareSessionInterface
     */
    protected $session;

    /**
     * @var RouterInterface
     */
    protected $router;

    /**
     * @param EccubeConfig $eccubeConfig
     *
     * @return void
     */
    #[Required]
    public function setEccubeConfig(EccubeConfig $eccubeConfig): void
    {
        $this->eccubeConfig = $eccubeConfig;
    }

    /**
     * @param EntityManagerInterface $entityManager
     *
     * @return void
     */
    #[Required]
    public function setEntityManager(EntityManagerInterface $entityManager): void
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param TranslatorInterface $translator
     *
     * @return void
     */
    #[Required]
    public function setTranslator(TranslatorInterface $translator): void
    {
        $this->translator = $translator;
    }

    /**
     * @param Session $session
     *
     * @return void
     */
    #[Required]
    public function setSession(Session $session): void
    {
        $this->session = $session;
    }

    /**
     * @param FormFactoryInterface $formFactory
     *
     * @return void
     */
    #[Required]
    public function setFormFactory(FormFactoryInterface $formFactory): void
    {
        $this->formFactory = $formFactory;
    }

    /**
     * @param EventDispatcherInterface $eventDispatcher
     *
     * @return void
     */
    #[Required]
    public function setEventDispatcher(EventDispatcherInterface $eventDispatcher): void
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @param RouterInterface $router
     *
     * @return void
     */
    #[Required]
    public function setRouter(RouterInterface $router): void
    {
        $this->router = $router;
    }

    /**
     * @param string $message
     * @param string $namespace
     *
     * @return void
     */
    public function addSuccess($message, $namespace = 'front'): void
    {
        $this->addFlash('eccube.'.$namespace.'.success', $message);
    }

    /**
     * @param string $message
     * @param string $namespace
     *
     * @return void
     */
    public function addSuccessOnce($message, $namespace = 'front'): void
    {
        $this->addFlashOnce('eccube.'.$namespace.'.success', $message);
    }

    /**
     * @param string $message
     * @param string $namespace
     *
     * @return void
     */
    public function addError($message, $namespace = 'front'): void
    {
        $this->addFlash('eccube.'.$namespace.'.error', $message);
    }

    /**
     * @param string $message
     * @param string $namespace
     *
     * @return void
     */
    public function addErrorOnce($message, $namespace = 'front'): void
    {
        $this->addFlashOnce('eccube.'.$namespace.'.error', $message);
    }

    /**
     * @param string $message
     * @param string $namespace
     *
     * @return void
     */
    public function addDanger($message, $namespace = 'front'): void
    {
        $this->addFlash('eccube.'.$namespace.'.danger', $message);
    }

    /**
     * @param string $message
     * @param string $namespace
     *
     * @return void
     */
    public function addDangerOnce($message, $namespace = 'front'): void
    {
        $this->addFlashOnce('eccube.'.$namespace.'.danger', $message);
    }

    /**
     * @param string $message
     * @param string $namespace
     *
     * @return void
     */
    public function addWarning($message, $namespace = 'front'): void
    {
        $this->addFlash('eccube.'.$namespace.'.warning', $message);
    }

    /**
     * @param string $message
     * @param string $namespace
     *
     * @return void
     */
    public function addWarningOnce($message, $namespace = 'front'): void
    {
        $this->addFlashOnce('eccube.'.$namespace.'.warning', $message);
    }

    /**
     * @param string $message
     * @param string $namespace
     *
     * @return void
     */
    public function addInfo($message, $namespace = 'front'): void
    {
        $this->addFlash('eccube.'.$namespace.'.info', $message);
    }

    /**
     * @param string $message
     * @param string $namespace
     *
     * @return void
     */
    public function addInfoOnce($message, $namespace = 'front'): void
    {
        $this->addFlashOnce('eccube.'.$namespace.'.info', $message);
    }

    /**
     * @param string $message
     * @param string $namespace
     *
     * @return void
     */
    public function addRequestError($message, $namespace = 'front'): void
    {
        $this->addFlash('eccube.'.$namespace.'.request.error', $message);
    }

    /**
     * @param string $message
     * @param string $namespace
     *
     * @return void
     */
    public function addRequestErrorOnce($message, $namespace = 'front'): void
    {
        $this->addFlashOnce('eccube.'.$namespace.'.request.error', $message);
    }

    /**
     * @return void
     */
    public function clearMessage(): void
    {
        /** @var Session $session */
        $session = $this->session;
        $session->getFlashBag()->clear();
    }

    /**
     * @return void
     */
    public function deleteMessage(): void
    {
        $this->clearMessage();
        $this->addWarning('admin.common.delete_error_already_deleted', 'admin');
    }

    /**
     * @param string $type
     *
     * @return bool
     */
    public function hasMessage(string $type): bool
    {
        /** @var Session $session */
        $session = $this->session;

        return $session->getFlashBag()->has($type);
    }

    /**
     * @param string $type
     * @param string $message
     *
     * @return void
     */
    public function addFlashOnce(string $type, $message): void
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

    /**
     * @param string $targetPath
     * @param string|null $namespace
     *
     * @return void
     */
    public function setLoginTargetPath($targetPath, $namespace = null): void
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
    public function forwardToRoute($route, array $path = [], array $query = []): Response
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
     * @return bool
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
