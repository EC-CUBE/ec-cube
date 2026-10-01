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

use Eccube\Entity\OpeningHours;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * 営業時間（schema.org OpeningHoursSpecification の 1 エントリ）の入力フォーム.
 */
class OpeningHoursType extends AbstractType
{
    /**
     * schema.org DayOfWeek の値.
     */
    private const DAY_OF_WEEK_CHOICES = [
        'admin.setting.shop.opening_hours.monday' => 'Monday',
        'admin.setting.shop.opening_hours.tuesday' => 'Tuesday',
        'admin.setting.shop.opening_hours.wednesday' => 'Wednesday',
        'admin.setting.shop.opening_hours.thursday' => 'Thursday',
        'admin.setting.shop.opening_hours.friday' => 'Friday',
        'admin.setting.shop.opening_hours.saturday' => 'Saturday',
        'admin.setting.shop.opening_hours.sunday' => 'Sunday',
        'admin.setting.shop.opening_hours.public_holidays' => 'PublicHolidays',
    ];

    /**
     * {@inheritdoc}
     *
     * @param array<string, mixed> $options
     */
    #[\Override]
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('day_of_week', ChoiceType::class, [
                'required' => false,
                'multiple' => true,
                'expanded' => true,
                'choices' => self::DAY_OF_WEEK_CHOICES,
            ])
            ->add('opens', TimeType::class, [
                'required' => false,
                'input' => 'datetime',
                'widget' => 'single_text',
            ])
            ->add('closes', TimeType::class, [
                'required' => false,
                'input' => 'datetime',
                'widget' => 'single_text',
            ]);
    }

    /**
     * {@inheritdoc}
     */
    #[\Override]
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => OpeningHours::class,
        ]);
    }
}
