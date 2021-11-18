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

use Doctrine\Common\Persistence\Mapping\Driver\MappingDriver;
use Doctrine\Common\Persistence\Mapping\Driver\MappingDriverChain;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
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
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $masterdata = [];

        /** @var MappingDriverChain $driverChain */
        $driverChain = $this->entityManager->getConfiguration()->getMetadataDriverImpl();
        /** @var MappingDriver[] $drivers */
        $drivers = $driverChain->getDrivers();

        foreach ($drivers as $namespace => $driver) {
            if ($namespace == 'Eccube\Entity') {
                $classNames = $driver->getAllClassNames();
                foreach ($classNames as $className) {
                    /** @var ClassMetadata $meta */
                    $meta = $this->entityManager->getMetadataFactory()->getMetadataFor($className);

                    // OrderStatus/OrderStatusColorは対象外
                    // @see https://github.com/EC-CUBE/ec-cube/pull/4844
                    if (in_array($meta->getName(), [OrderStatus::class, OrderStatusColor::class, CustomerOrderStatus::class])) {
                        continue;
                    }

                    if (strpos($meta->rootEntityName, 'Master') !== false
                        && $meta->hasField('id')
                        && $meta->hasField('name')
                        && $meta->hasField('sort_no')
                    ) {
                        $metadataName = str_replace('\\', '-', $meta->getName());
                        $masterdata[$metadataName] = $meta->getTableName();
                    }
                }
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
    public function getBlockPrefix()
    {
        return 'admin_system_masterdata';
    }
}
