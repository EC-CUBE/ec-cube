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
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Symfony\Component\Translation\TranslatorInterface;

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
     * @var Session
     */
    protected $session;

    /**
     * @param EccubeConfig $eccubeConfig
     * @required
     */
    public function setEccubeConfig(EccubeConfig $eccubeConfig)
    {
        $this->eccubeConfig = $eccubeConfig;
    }

    /**
     * @param EntityManagerInterface $entityManager
     * @required
     */
    public function setEntityManager(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param TranslatorInterface $translator
     * @required
     */
    public function setTranslator(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * @param SessionInterface $session
     * @required
     */
    public function setSession(SessionInterface $session)
    {
        $this->session = $session;
    }

    /**
     * @param FormFactoryInterface $formFactory
     * @required
     */
    public function setFormFactory(FormFactoryInterface $formFactory)
    {
        $this->formFactory = $formFactory;
    }

    /**
     * @param EventDispatcherInterface $eventDispatcher
     * @required
     */
    public function setEventDispatcher(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    public function addSuccess($message, $namespace = 'front')
    {
        $this->session->getFlashBag()->add('eccube.'.$namespace.'.success', $message);
    }

    public function addError($message, $namespace = 'front')
    {
        $this->session->getFlashBag()->add('eccube.'.$namespace.'.error', $message);
    }

    public function addDanger($message, $namespace = 'front')
    {
        $this->session->getFlashBag()->add('eccube.'.$namespace.'.danger', $message);
    }

    public function addWarning($message, $namespace = 'front')
    {
        $this->session->getFlashBag()->add('eccube.'.$namespace.'.warning', $message);
    }

    public function addInfo($message, $namespace = 'front')
    {
        $this->session->getFlashBag()->add('eccube.'.$namespace.'.info', $message);
    }

    public function addRequestError($message, $namespace = 'front')
    {
        $this->session->getFlashBag()->add('eccube.'.$namespace.'.request.error', $message);
    }

    public function clearMessage()
    {
        $this->session->getFlashBag()->clear();
    }

    public function deleteMessage()
    {
        $this->clearMessage();
        $this->addWarning('admin.common.delete_error_already_deleted', 'admin');
    }

    /**
     * @param string $targetPath
     */
    public function setLoginTargetPath($targetPath, $namespace = null)
    {
        if (is_null($namespace)) {
            $this->session->getFlashBag()->set('eccube.login.target.path', $targetPath);
        } else {
            $this->session->getFlashBag()->set('eccube.'.$namespace.'.login.target.path', $targetPath);
        }
    }

    /**
     * Forwards the request to another controller.
     *
     * @param string $route The name of the route
     * @param array  $path An array of path parameters
     * @param array  $query An array of query parameters
     *
     * @return \Symfony\Component\HttpFoundation\Response A Response instance
     */
    public function forwardToRoute($route, array $path = [], array $query = [])
    {
        $Route = $this->get('router')->getRouteCollection()->get($route);
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
    protected function isTokenValid()
    {
        /** @var Request $request */
        $request = $this->container->get('request_stack')->getCurrentRequest();
        $token = $request->get(Constant::TOKEN_NAME)
            ? $request->get(Constant::TOKEN_NAME)
            : $request->headers->get('ECCUBE-CSRF-TOKEN');

        if (!$this->isCsrfTokenValid(Constant::TOKEN_NAME, $token)) {
            throw new AccessDeniedHttpException('CSRF token is invalid.');
        }

        return true;
    }
}
