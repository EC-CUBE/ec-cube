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

namespace Eccube\Form\Type\Shopping;

use Eccube\Entity\Customer;
use Eccube\Entity\CustomerAddress;
use Eccube\Entity\Shipping;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class CustomerAddressType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // 会員住所とお届け先住所をマージして選択肢を作成
        /** @var Customer $Customer */
        $Customer = $options['customer'];
        $CustomerAddress = new CustomerAddress();
        $CustomerAddress->setFromCustomer($Customer);
        $Addresses = array_merge([$CustomerAddress], $Customer->getCustomerAddresses()->toArray());

        // 注文のお届け先住所とマッチするものを初期選択とする
        /** @var Shipping $Shipping */
        $Shipping = $options['shipping'];
        $Checked = null;
        foreach ($Addresses as $Address) {
            if ($Address->getShippingMultipleDefaultName() === $Shipping->getShippingMultipleDefaultName()) {
                $Checked = $Address;
            }
        }

        $builder->add('addresses', ChoiceType::class, [
            'choices' => $Addresses,
            'data' => $Checked,
            'constraints' => [
                new NotBlank(),
            ],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(['customer' => null, 'shipping' => null]);
    }
}
