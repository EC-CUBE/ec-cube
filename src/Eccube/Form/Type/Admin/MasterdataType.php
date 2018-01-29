<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2015 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */

namespace Eccube\Form\Type\Admin;

use Doctrine\Common\Persistence\Mapping\Driver\MappingDriver;
use Doctrine\Common\Persistence\Mapping\Driver\MappingDriverChain;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class MasterdataType
 * @package Eccube\Form\Type\Admin
 */
class MasterdataType extends AbstractType
{
    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * MasterdataType constructor.
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
        $masterdata = array();

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
            ->add('masterdata', ChoiceType::class, array(
                'choices' => array_flip($masterdata),
                'expanded' => false,
                'multiple' => false,
                'constraints' => array(
                    new Assert\NotBlank(),
                ),
            ))
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
