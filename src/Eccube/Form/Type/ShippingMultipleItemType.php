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

namespace Eccube\Form\Type;

use Doctrine\ORM\EntityManagerInterface;
use Eccube\Common\EccubeConfig;
use Eccube\Entity\Customer;
use Eccube\Entity\CustomerAddress;
use Eccube\Repository\Master\PrefRepository;
use Eccube\Service\OrderHelper;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Validator\Constraints as Assert;

class ShippingMultipleItemType extends AbstractType
{
    /**
     * @var array
     */
    protected $eccubeConfig;

    /**
     * @var SessionInterface
     */
    protected $session;

    /**
     * @var AuthorizationCheckerInterface
     */
    protected $authorizationChecker;

    /**
     * @var TokenStorageInterface
     */
    protected $tokenStorage;

    /**
     * @var PrefRepository
     */
    protected $prefRepository;

    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * @var OrderHelper
     */
    protected $orderHelper;

    /**
     * ShippingMultipleItemType constructor.
     *
     * @param EccubeConfig $eccubeConfig
     * @param SessionInterface $session
     * @param AuthorizationCheckerInterface $authorizationChecker
     * @param TokenStorageInterface $tokenStorage
     */
    public function __construct(
        EccubeConfig $eccubeConfig,
        SessionInterface $session,
        AuthorizationCheckerInterface $authorizationChecker,
        TokenStorageInterface $tokenStorage,
        PrefRepository $prefRepository,
        EntityManagerInterface $entityManager,
        OrderHelper $orderHelper
    ) {
        $this->eccubeConfig = $eccubeConfig;
        $this->session = $session;
        $this->authorizationChecker = $authorizationChecker;
        $this->tokenStorage = $tokenStorage;
        $this->prefRepository = $prefRepository;
        $this->entityManager = $entityManager;
        $this->orderHelper = $orderHelper;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('quantity', IntegerType::class, [
                'attr' => [
                    'min' => 1,
                    'maxlength' => $this->eccubeConfig['eccube_int_len'],
                ],
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\GreaterThanOrEqual([
                        'value' => 1,
                    ]),
                    new Assert\Regex(['pattern' => '/^\d+$/']),
                ],
            ])
            ->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
                $form = $event->getForm();

                if ($this->authorizationChecker->isGranted('IS_AUTHENTICATED_FULLY')) {
                    // 会員の場合は、会員住所とお届け先住所をマージしてリストを作成
                    /** @var Customer $Customer */
                    $Customer = $this->tokenStorage->getToken()->getUser();
                    $CustomerAddress = new CustomerAddress();
                    $CustomerAddress->setFromCustomer($Customer);
                    $CustomerAddresses = array_merge([$CustomerAddress], $Customer->getCustomerAddresses()->toArray());
                } else {
                    $CustomerAddresses = [];
                    // 非会員の場合は、セッションに保持されている注文者住所とお届け先住所をマージしてリストを作成
                    if ($NonMember = $this->orderHelper->getNonMember('eccube.front.shopping.nonmember')) {
                        $CustomerAddress = new CustomerAddress();
                        $CustomerAddress->setFromCustomer($NonMember);

                        if ($CustomerAddresses = $this->session->get('eccube.front.shopping.nonmember.customeraddress')) {
                            $CustomerAddresses = unserialize($CustomerAddresses);
                            $CustomerAddresses = array_merge([$CustomerAddress], $CustomerAddresses);
                            foreach ($CustomerAddresses as $Address) {
                                $Pref = $this->prefRepository->find($Address->getPref()->getId());
                                $Address->setPref($Pref);
                            }
                        }
                    }
                }

                $form->add('customer_address', ChoiceType::class, [
                    'choices' => $CustomerAddresses,
                    'choice_label' => 'shippingMultipleDefaultName',
                    'constraints' => [
                        new Assert\NotBlank(),
                    ],
                ]);
            })
            ->addEventListener(FormEvents::POST_SET_DATA, function (FormEvent $event) {
                /** @var \Eccube\Entity\Shipping $data */
                $data = $event->getData();
                /** @var \Symfony\Component\Form\Form $form */
                $form = $event->getForm();

                if (is_null($data)) {
                    return;
                }

                $choices = $form['customer_address']->getConfig()->getOption('choices');

                /* @var CustomerAddress $CustomerAddress */
                foreach ($choices as $address) {
                    if ($address->getShippingMultipleDefaultName() === $data->getShippingMultipleDefaultName()) {
                        $form['customer_address']->setData($address);
                        break;
                    }
                }

                $quantity = 0;
                // Check all shipment items
                foreach ($data->getProductOrderItems() as $OrderItem) {
                    // Check item distinct for each quantity
                    if ($data->getProductClassOfTemp()->getId() == $OrderItem->getProductClass()->getId()) {
                        $quantity += $OrderItem->getQuantity();
                    }
                }
                $form['quantity']->setData($quantity);
            });
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'shipping_multiple_item';
    }
}
