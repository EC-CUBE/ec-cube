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

namespace Eccube\Controller\Admin\Setting\System;

use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Eccube\Controller\AbstractController;
use Eccube\Entity\Member;
use Eccube\Event\EccubeEvents;
use Eccube\Event\EventArgs;
use Eccube\Form\Type\Admin\MemberType;
use Eccube\Repository\MemberRepository;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;

class MemberController extends AbstractController
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
     * MemberController constructor.
     *
     * @param EncoderFactoryInterface $encoderFactory
     * @param MemberRepository $memberRepository
     * @param TokenStorageInterface $tokenStorage
     */
    public function __construct(
        EncoderFactoryInterface $encoderFactory,
        MemberRepository $memberRepository,
        TokenStorageInterface $tokenStorage
    ) {
        $this->encoderFactory = $encoderFactory;
        $this->memberRepository = $memberRepository;
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * @Route("/%eccube_admin_route%/setting/system/member", name="admin_setting_system_member")
     * @Template("@admin/Setting/System/member.twig")
     */
    public function index(Request $request)
    {
        $Members = $this->memberRepository->findBy([], ['sort_no' => 'DESC']);

        $builder = $this->formFactory->createBuilder();

        $event = new EventArgs(
            [
                'builder' => $builder,
                'Members' => $Members,
            ],
            $request
        );
        $this->eventDispatcher->dispatch(EccubeEvents::ADMIN_SETTING_SYSTEM_MEMBER_INDEX_INITIALIZE, $event);

        $form = $builder->getForm();

        return [
            'form' => $form->createView(),
            'Members' => $Members,
        ];
    }

    /**
     * @Route("/%eccube_admin_route%/setting/system/member/new", name="admin_setting_system_member_new")
     * @Template("@admin/Setting/System/member_edit.twig")
     */
    public function create(Request $request)
    {
        $LoginMember = clone $this->tokenStorage->getToken()->getUser();
        $this->entityManager->detach($LoginMember);

        $Member = new Member();
        $builder = $this->formFactory
            ->createBuilder(MemberType::class, $Member);

        $event = new EventArgs([
            'builder' => $builder,
            'Member' => $Member,
        ], $request);
        $this->eventDispatcher->dispatch(EccubeEvents::ADMIN_SETTING_SYSTEM_MEMBER_EDIT_INITIALIZE, $event);

        $form = $builder->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $encoder = $this->encoderFactory->getEncoder($Member);
            $salt = $encoder->createSalt();
            $rawPassword = $Member->getPassword();
            $encodedPassword = $encoder->encodePassword($rawPassword, $salt);
            $Member
                ->setSalt($salt)
                ->setPassword($encodedPassword);

            $this->memberRepository->save($Member);

            $event = new EventArgs(
                [
                    'form' => $form,
                    'Member' => $Member,
                ],
                $request
            );
            $this->eventDispatcher->dispatch(EccubeEvents::ADMIN_SETTING_SYSTEM_MEMBER_EDIT_COMPLETE, $event);

            $this->addSuccess('admin.member.save.complete', 'admin');

            return $this->redirectToRoute('admin_setting_system_member');
        }

        $this->tokenStorage->getToken()->setUser($LoginMember);

        return [
            'form' => $form->createView(),
            'Member' => $Member,
        ];
    }

    /**
     * @Route("/%eccube_admin_route%/setting/system/member/{id}/edit", requirements={"id" = "\d+"}, name="admin_setting_system_member_edit")
     * @Template("@admin/Setting/System/member_edit.twig")
     */
    public function edit(Request $request, Member $Member)
    {
        $LoginMember = clone $this->tokenStorage->getToken()->getUser();
        $this->entityManager->detach($LoginMember);

        $previousPassword = $Member->getPassword();
        $Member->setPassword($this->eccubeConfig['eccube_default_password']);

        $builder = $this->formFactory
            ->createBuilder(MemberType::class, $Member);

        $event = new EventArgs(
            [
                'builder' => $builder,
                'Member' => $Member,
            ],
            $request
        );
        $this->eventDispatcher->dispatch(EccubeEvents::ADMIN_SETTING_SYSTEM_MEMBER_EDIT_INITIALIZE, $event);

        $form = $builder->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($Member->getpassword() === $this->eccubeConfig['eccube_default_password']) {
                // 編集時にパスワードを変更していなければ
                // 変更前のパスワード(暗号化済み)をセット
                $Member->setPassword($previousPassword);
            } else {
                $salt = $Member->getSalt();
                // 2系からのデータ移行でsaltがセットされていない場合はsaltを生成.
                if (empty($salt)) {
                    $salt = bin2hex(openssl_random_pseudo_bytes(5));
                    $Member->setSalt($salt);
                }

                $rawPassword = $Member->getPassword();
                $encoder = $this->encoderFactory->getEncoder($Member);
                $encodedPassword = $encoder->encodePassword($rawPassword, $salt);
                $Member->setPassword($encodedPassword);
            }

            $this->memberRepository->save($Member);

            $event = new EventArgs(
                [
                    'form' => $form,
                    'Member' => $Member,
                ],
                $request
            );
            $this->eventDispatcher->dispatch(EccubeEvents::ADMIN_SETTING_SYSTEM_MEMBER_EDIT_COMPLETE, $event);

            $this->addSuccess('admin.member.save.complete', 'admin');

            return $this->redirectToRoute('admin_setting_system_member');
        }

        $this->tokenStorage->getToken()->setUser($LoginMember);

        return [
            'form' => $form->createView(),
            'Member' => $Member,
        ];
    }

    /**
     * @Route("/%eccube_admin_route%/setting/system/member/{id}/up", requirements={"id" = "\d+"}, name="admin_setting_system_member_up", methods={"PUT"})
     */
    public function up(Request $request, Member $Member)
    {
        $this->isTokenValid();

        try {
            $this->memberRepository->up($Member);

            $this->addSuccess('admin.member.up.complete', 'admin');
        } catch (\Exception $e) {
            log_error('メンバー表示順更新エラー', [$Member->getId(), $e]);

            $this->addError('admin.member.up.error', 'admin');
        }

        return $this->redirectToRoute('admin_setting_system_member');
    }

    /**
     * @Route("/%eccube_admin_route%/setting/system/member/{id}/down", requirements={"id" = "\d+"}, name="admin_setting_system_member_down", methods={"PUT"})
     */
    public function down(Request $request, Member $Member)
    {
        $this->isTokenValid();

        try {
            $this->memberRepository->down($Member);

            $this->addSuccess('admin.member.down.complete', 'admin');
        } catch (\Exception $e) {
            log_error('メンバー表示順更新エラー', [$Member->getId(), $e]);

            $this->addError('admin.member.down.error', 'admin');
        }

        return $this->redirectToRoute('admin_setting_system_member');
    }

    /**
     * @Route("/%eccube_admin_route%/setting/system/member/{id}/delete", requirements={"id" = "\d+"}, name="admin_setting_system_member_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Member $Member)
    {
        $this->isTokenValid();

        log_info('メンバー削除開始', [$Member->getId()]);

        try {
            $this->memberRepository->delete($Member);

            $event = new EventArgs(
                [
                    'Member' => $Member,
                ],
                $request
            );
            $this->eventDispatcher->dispatch(EccubeEvents::ADMIN_SETTING_SYSTEM_MEMBER_DELETE_COMPLETE, $event);

            $this->addSuccess('admin.member.delete.complete', 'admin');

            log_info('メンバー削除完了', [$Member->getId()]);
        } catch (ForeignKeyConstraintViolationException $e) {
            log_info('メンバー削除エラー', [$Member->getId()]);

            $message = trans('admin.delete.failed.foreign_key', ['%name%' => $Member->getName()]);
            $this->addError($message, 'admin');
        } catch (\Exception $e) {
            log_info('メンバー削除エラー', [$Member->getId(), $e]);

            $message = trans('admin.delete.failed');
            $this->addError($message, 'admin');
        }

        return $this->redirectToRoute('admin_setting_system_member');
    }
}
