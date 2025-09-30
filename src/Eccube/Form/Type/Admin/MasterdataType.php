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

namespace Eccube\Form\Type\Admin;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\Driver\AttributeDriver;
use Eccube\Entity\Master\CustomerOrderStatus;
use Eccube\Entity\Master\OrderStatus;
use Eccube\Entity\Master\OrderStatusColor;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class MasterdataType
 */
class MasterdataType extends AbstractType
{
    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * MasterdataType constructor.
     *
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * {@inheritdoc}
     */
    #[\Override]
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $masterdata = [];

        // AttributeDriver でも有効なメタデータ一括取得に変更
        foreach ($this->entityManager->getMetadataFactory()->getAllMetadata() as $meta) {
            // 抽象クラスは除外
            $rc = $meta->getReflectionClass();
            if ($rc && $rc->isAbstract()) {
                continue;
            }

            // OrderStatus/OrderStatusColor/CustomerOrderStatus は対象外
            if (in_array($meta->getName(), [OrderStatus::class, OrderStatusColor::class, CustomerOrderStatus::class], true)) {
                continue;
            }

            if (str_contains($meta->rootEntityName, 'Master')
                && $meta->hasField('id')
                && $meta->hasField('name')
                && $meta->hasField('sort_no')
            ) {
                $metadataName = str_replace('\\', '-', $meta->getName());
                $masterdata[$metadataName] = $meta->getTableName();
            }
        }

        $builder
            ->add('masterdata', ChoiceType::class, [
                'choices' => array_flip($masterdata),
                'expanded' => false,
                'multiple' => false,
                'constraints' => [
                    new Assert\NotBlank(),
                ],
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    #[\Override]
    public function getBlockPrefix()
    {
        return 'admin_system_masterdata';
    }
}
