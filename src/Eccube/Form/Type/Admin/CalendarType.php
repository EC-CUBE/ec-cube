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

use Eccube\Common\EccubeConfig;
use Eccube\Entity\Calendar;
use Eccube\Repository\CalendarRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class CalendarType
 */
class CalendarType extends AbstractType
{
    /**
     * @var EccubeConfig
     */
    protected $eccubeConfig;

    /**
     * @var CalendarRepository
     */
    protected $calendarRepository;

    /**
     * CalendarType constructor.
     */
    public function __construct(EccubeConfig $eccubeConfig, CalendarRepository $calendarRepository)
    {
        $this->eccubeConfig = $eccubeConfig;
        $this->calendarRepository = $calendarRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, [
                'required' => true,
                'constraints' => [
                    new Assert\Length([
                        'max' => $this->eccubeConfig['eccube_stext_len'],
                    ]),
                ],
            ])
            ->add('holiday', DateType::class, [
                'label' => 'admin.common.create_date__start',
                'required' => true,
                'input' => 'datetime',
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',
                'placeholder' => ['year' => '----', 'month' => '--', 'day' => '--'],
                'attr' => [
                    'class' => 'datetimepicker-input',
                    'data-target' => '#'.$this->getBlockPrefix().'_create_date_start',
                    'data-toggle' => 'datetimepicker',
                ],
            ])
        ;

        $builder->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) {
            // 日付重複チェック
            /** @var Calendar $Calendar */
            $Calendar = $event->getData();
            $qb = $this->calendarRepository->createQueryBuilder('c');
            $qb
                ->select('count(c.id)')
                ->where('c.holiday = :holiday')
                ->setParameter('holiday', $Calendar->getHoliday());
            if ($Calendar->getId()) {
                // 更新の場合は自IDを除外してチェック
                $qb
                    ->andWhere('c.id <> :id')
                    ->setParameter('id', $Calendar->getId());
            }
            $count = $qb->getQuery()
                ->getSingleScalarResult();
            if ($count > 0) {
                $form = $event->getForm();
                $form['holiday']->addError(new FormError(trans('admin.setting.shop.calendar.holiday.available_error')));
            }
        });
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Calendar::class,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'calendar';
    }
}
