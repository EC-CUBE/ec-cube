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

namespace Eccube\Controller\Admin\Setting\System;

use Eccube\Controller\AbstractController;
use Eccube\Form\Type\Admin\TwoFactorAuthType;
use Eccube\Repository\MemberRepository;
use Eccube\Service\TwoFactorAuthService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;

class TwoFactorAuthController extends AbstractController
{
    /**
     * @var TokenStorageInterface
     */
    protected $tokenStorage;

    /**
     * @var MemberRepository
     */
    protected $memberRepository;

    /**
     * @var EncoderFactoryInterface
     */
    protected $encoderFactory;

    /**
     * @var TwoFactorAuthService
     */
    protected $twoFactorAuthService;

    /**
     * TwoFactorAuthController constructor.
     *
     * @param MemberRepository $memberRepository
     * @param TokenStorageInterface $tokenStorage
     */
    public function __construct(
        EncoderFactoryInterface $encoderFactory,
        MemberRepository $memberRepository,
        TokenStorageInterface $tokenStorage,
        TwoFactorAuthService $twoFactorAuthService
    ) {
        $this->encoderFactory = $encoderFactory;
        $this->memberRepository = $memberRepository;
        $this->tokenStorage = $tokenStorage;
        $this->twoFactorAuthService = $twoFactorAuthService;
    }

    /**
     * @Route("/%eccube_admin_route%/two_factor_auth/auth", name="admin_two_factor_auth", methods={"GET", "POST"})
     * @Template("@admin/two_factor_auth.twig")
     */
    public function auth(Request $request)
    {
        $Member = $this->getUser();

        if (!$this->twoFactorAuthService->isEnabled() || $this->twoFactorAuthService->isAuth($Member)) {
            return $this->redirectToRoute('admin_homepage');
        }

        $error = null;
        $builder = $this->formFactory->createBuilder(TwoFactorAuthType::class);
        $builder->remove('auth_key');
        $form = $builder->getForm();

        if ('POST' === $request->getMethod()) {
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                if ($Member->getTwoFactorAuthKey()) {
                    if ($this->twoFactorAuthService->verifyCode($Member->getTwoFactorAuthKey(), $form->get('device_token')->getData())) {
                        $response = new RedirectResponse($this->generateUrl('admin_homepage'));
                        $response->headers->setCookie($this->twoFactorAuthService->createAuthedCookie($Member));

                        return $response;
                    } else {
                        $error = trans('admin.setting.system.two_factor_auth.invalid_message__reinput');
                    }
                } else {
                    return $this->redirectToRoute('admin_two_factor_auth_set');
                }
            } else {
                $error = trans('admin.setting.system.two_factor_auth.invalid_message__reinput');
            }
        }

        return [
            'form' => $form->createView(),
            'error' => $error,
        ];
    }

    /**
     * @Route("/%eccube_admin_route%/two_factor_auth/set", name="admin_two_factor_auth_set", methods={"GET", "POST"})
     * @Template("@admin/two_factor_auth_set.twig")
     */
    public function set(Request $request)
    {
        $Member = $this->getUser();
        if (!$this->twoFactorAuthService->isEnabled() || $this->twoFactorAuthService->isAuth($Member)) {
            return $this->redirectToRoute('admin_homepage');
        }
        $res = $this->createResponse($request);

        return $res;
    }

    /**
     * @Route("/%eccube_admin_route%/setting/system/two_factor_auth/edit", name="admin_setting_system_two_factor_auth_edit", methods={"GET", "POST"})
     * @Template("@admin/Setting/System/two_factor_auth_edit.twig")
     */
    public function edit(Request $request)
    {
        $Member = $this->getUser();
        if (!$this->twoFactorAuthService->isAuth($Member)) {
            return $this->redirectToRoute('admin_homepage');
        }
        $res = $this->createResponse($request);
        if (is_array($res) && isset($res['error'])) {
            $this->addError($res['error']);
        }

        return $res;
    }

    private function createResponse(Request $request)
    {
        $error = null;
        $Member = $this->getUser();
        $builder = $this->formFactory->createBuilder(TwoFactorAuthType::class);
        $form = null;
        $auth_key = null;

        if ('GET' === $request->getMethod()) {
            if ($Member->getTwoFactorAuthKey()) {
                $this->addWarning('admin.setting.system.two_factor_auth.configured_warning', 'admin');
            }
            $auth_key = $this->twoFactorAuthService->createSecret();
            $builder->get('auth_key')->setData($auth_key);
            $form = $builder->getForm();
        } elseif ('POST' === $request->getMethod()) {
            $form = $builder->getForm();
            $form->handleRequest($request);
            $auth_key = $form->get('auth_key')->getData();
            $device_token = $form->get('device_token')->getData();
            if ($form->isSubmitted() && $form->isValid()) {
                if ($this->twoFactorAuthService->verifyCode($auth_key, $device_token, 2)) {
                    $Member->setTwoFactorAuthKey($auth_key);
                    $this->memberRepository->save($Member);
                    $this->addSuccess('admin.setting.system.two_factor_auth.complete_message', 'admin');
                    $response = new RedirectResponse($this->generateUrl('admin_homepage'));
                    $response->headers->setCookie($this->twoFactorAuthService->createAuthedCookie($Member));

                    return $response;
                } else {
                    $error = trans('admin.setting.system.two_factor_auth.invalid_message__reinput');
                }
            } else {
                $error = trans('admin.setting.system.two_factor_auth.invalid_message__invalid');
            }
        }

        return [
            'form' => $form->createView(),
            'Member' => $Member,
            'auth_key' => $auth_key,
            'error' => $error,
        ];
    }
}
