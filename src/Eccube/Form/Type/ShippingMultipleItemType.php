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

namespace Eccube\Form\Type;

use Eccube\Common\EccubeConfig;
use Eccube\Entity\Customer;
use Eccube\Entity\CustomerAddress;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\HttpFoundation\Session\Session;
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
     * @var Session
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
     * ShippingMultipleItemType constructor.
     *
     * @param array $eccubeConfig
     * @param Session $session
     * @param AuthorizationCheckerInterface $authorizationChecker
     * @param TokenStorageInterface $tokenStorage
     */
    public function __construct(
        EccubeConfig $eccubeConfig,
        Session $session,
        AuthorizationCheckerInterface $authorizationChecker,
        TokenStorageInterface $tokenStorage
    ) {
        $this->eccubeConfig = $eccubeConfig;
        $this->session = $session;
        $this->authorizationChecker = $authorizationChecker;
        $this->tokenStorage = $tokenStorage;
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
                    // 会員の場合、CustomerAddressを設定
                    /** @var Customer $Customer */
                    $Customer = $this->tokenStorage->getToken()->getUser();
                    $CustomerAddresses = $Customer->getCustomerAddresses();
                    $Addresses = array_reduce($CustomerAddresses->toArray(), function (array $result, CustomerAddress $CustomerAddress) {
                        $result[$CustomerAddress->getShippingMultipleDefaultName()] = $CustomerAddress->getId();

                        return $result;
                    }, []);

                    $form->add('customer_address', ChoiceType::class, [
                        'choices' => $Addresses,
                        'constraints' => [
                            new Assert\NotBlank(),
                        ],
                    ]);
                } else {
                    // 非会員の場合、セッションに設定されたCustomerAddressを設定
                    if ($this->session->has('eccube.front.shopping.nonmember.customeraddress')) {
                        $customerAddresses = $this->session->get('eccube.front.shopping.nonmember.customeraddress');
                        $customerAddresses = unserialize($customerAddresses);
                        $addresses = array_map(function (CustomerAddress $CustomerAddress) {
                            return $CustomerAddress->getShippingMultipleDefaultName();
                        }, $customerAddresses);

                        $form->add('customer_address', ChoiceType::class, [
                            'choices' => array_flip($addresses),
                            'constraints' => [
                                new Assert\NotBlank(),
                            ],
                        ]);
                    }
                }
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
                foreach ($choices as $address => $id) {
                    if ($address === $data->getShippingMultipleDefaultName()) {
                        $form['customer_address']->setData($id);
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
