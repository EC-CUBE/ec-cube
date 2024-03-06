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

namespace Eccube\Controller\Admin\Customer;

use Eccube\Controller\AbstractController;
use Eccube\Entity\Customer;
use Eccube\Entity\Master\CustomerStatus;
use Eccube\Event\EccubeEvents;
use Eccube\Event\EventArgs;
use Eccube\Form\Type\Admin\CustomerType;
use Eccube\Repository\CustomerRepository;
use Eccube\Repository\Master\PageMaxRepository;
use Eccube\Repository\OrderRepository;
use Eccube\Util\StringUtil;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class CustomerEditController extends AbstractController
{
    /**
     * @var CustomerRepository
     */
    protected $customerRepository;

    /**
     * @var UserPasswordHasherInterface
     */
    protected $passwordHasher;

    /**
     * @var OrderRepository
     */
    protected $orderRepository;

    /**
     * @var PageMaxRepository
     */
    protected $pageMaxRepository;

    public function __construct(
        CustomerRepository $customerRepository,
        UserPasswordHasherInterface $passwordHasher,
        OrderRepository $orderRepository,
        PageMaxRepository $pageMaxRepository
    ) {
        $this->customerRepository = $customerRepository;
        $this->orderRepository = $orderRepository;
        $this->passwordHasher = $passwordHasher;
        $this->pageMaxRepository = $pageMaxRepository;
    }

    /**
     * @Route("/%eccube_admin_route%/customer/new", name="admin_customer_new", methods={"GET", "POST"})
     * @Route("/%eccube_admin_route%/customer/{id}/edit", requirements={"id" = "\d+"}, name="admin_customer_edit", methods={"GET", "POST"})
     * @Template("@admin/Customer/edit.twig")
     */
    public function index(Request $request, PaginatorInterface $paginator, $id = null)
    {
        $this->entityManager->getFilters()->enable('incomplete_order_status_hidden');
        // 編集
        if ($id) {
            /** @var Customer $Customer */
            $Customer = $this->customerRepository
                ->find($id);

            if (is_null($Customer)) {
                throw new NotFoundHttpException();
            }

            $oldStatusId = $Customer->getStatus()->getId();
            $Customer->setPlainPassword($this->eccubeConfig['eccube_default_password']);
        // 新規登録
        } else {
            $Customer = $this->customerRepository->newCustomer();

            $oldStatusId = null;
        }

        // 会員登録フォーム
        $builder = $this->formFactory
            ->createBuilder(CustomerType::class, $Customer);

        $event = new EventArgs(
            [
                'builder' => $builder,
                'Customer' => $Customer,
            ],
            $request
        );
        $this->eventDispatcher->dispatch($event, EccubeEvents::ADMIN_CUSTOMER_EDIT_INDEX_INITIALIZE);

        $form = $builder->getForm();

        $form->handleRequest($request);
        $page_count = (int) $this->session->get('eccube.admin.customer_edit.order.page_count',
            $this->eccubeConfig->get('eccube_default_page_count'));

        $page_count_param = (int) $request->get('page_count');
        $pageMaxis = $this->pageMaxRepository->findAll();

        if ($page_count_param) {
            foreach ($pageMaxis as $pageMax) {
                if ($page_count_param == $pageMax->getName()) {
                    $page_count = $pageMax->getName();
                    $this->session->set('eccube.admin.customer_edit.order.page_count', $page_count);
                    break;
                }
            }
        }
        $page_no = (int) $request->get('page_no', 1);
        $qb = $this->orderRepository->getQueryBuilderByCustomer($Customer);
        $pagination = [];
        if (!is_null($Customer->getId())) {
            $pagination = $paginator->paginate(
                $qb,
                $page_no > 0 ? $page_no : 1,
                $page_count
            );
        }

        if ($form->isSubmitted() && $form->isValid()) {
            log_info('会員登録開始', [$Customer->getId()]);

            if ($Customer->getPlainPassword() !== $this->eccubeConfig['eccube_default_password']) {
                $password = $this->passwordHasher->hashPassword($Customer, $Customer->getPlainPassword());
                $Customer->setPassword($password);
            }

            // 退会ステータスに更新の場合、ダミーのアドレスに更新
            $newStatusId = $Customer->getStatus()->getId();
            if ($oldStatusId != $newStatusId && $newStatusId == CustomerStatus::WITHDRAWING) {
                $Customer->setEmail(StringUtil::random(60).'@dummy.dummy');
            }

            $this->entityManager->persist($Customer);
            $this->entityManager->flush();

            log_info('会員登録完了', [$Customer->getId()]);

            $event = new EventArgs(
                [
                    'form' => $form,
                    'Customer' => $Customer,
                ],
                $request
            );
            $this->eventDispatcher->dispatch($event, EccubeEvents::ADMIN_CUSTOMER_EDIT_INDEX_COMPLETE);

            $this->addSuccess('admin.common.save_complete', 'admin');

            return $this->redirectToRoute('admin_customer_edit', [
                'id' => $Customer->getId(),
            ]);
        }

        return [
            'form' => $form->createView(),
            'Customer' => $Customer,
            'pagination' => $pagination,
            'pageMaxis' => $pageMaxis,
            'page_no' => $page_no,
            'page_count' => $page_count,
        ];
    }
}
