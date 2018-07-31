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

namespace Eccube\Form\Type\Admin;

use Doctrine\ORM\EntityManagerInterface;
use Eccube\Common\EccubeConfig;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class OrderPdfType.
 */
class OrderPdfType extends AbstractType
{
    /** @var EccubeConfig */
    private $eccubeConfig;

    /** @var EntityManagerInterface */
    private $entityManager;

    /**
     * OrderPdfType constructor.
     *
     * @param EccubeConfig $eccubeConfig
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EccubeConfig $eccubeConfig, EntityManagerInterface $entityManager)
    {
        $this->eccubeConfig = $eccubeConfig;
        $this->entityManager = $entityManager;
    }

    /**
     * Build config type form.
     *
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $config = $this->eccubeConfig;
        $builder
            ->add('ids', TextType::class, [
                'label' => 'admin.order.export.pdf.label.001',
                'required' => false,
                'attr' => ['readonly' => 'readonly'],
                'constraints' => [
                    new Assert\NotBlank(),
                ],
            ])
            ->add('issue_date', DateType::class, [
                'label' => 'admin.order.export.pdf.label.002',
                'widget' => 'single_text',
                'required' => true,
                'data' => new \DateTime(),
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\DateTime(),
                ],
            ])
            ->add('title', TextType::class, [
                'label' => 'admin.order.export.pdf.label.003',
                'required' => false,
                'attr' => ['maxlength' => $config['eccube_stext_len']],
                'constraints' => [
                    new Assert\Length(['max' => $config['eccube_stext_len']]),
                ],
            ])
            ->add('download_kind', ChoiceType::class, [
                'label' => 'admin.order.export.pdf.label.download_kind',
                'choices' => [
                    'admin.order.export.pdf.label.file' => 1,
                    'admin.order.export.pdf.label.browser' => 2,
                ],
                'expanded' => false,
                'multiple' => false,
                'required' => false,
                'mapped' => false,
                'placeholder' => null,
            ])
            // メッセージ
            ->add('message1', TextType::class, [
                'label' => 'admin.order.export.pdf.label.004',
                'required' => false,
                'attr' => ['maxlength' => $config['eccube_order_pdf_message_len']],
                'constraints' => [
                    new Assert\Length(['max' => $config['eccube_order_pdf_message_len']]),
                ],
                'trim' => false,
            ])
            ->add('message2', TextType::class, [
                'label' => 'admin.order.export.pdf.label.005',
                'required' => false,
                'attr' => ['maxlength' => $config['eccube_order_pdf_message_len']],
                'constraints' => [
                    new Assert\Length(['max' => $config['eccube_order_pdf_message_len']]),
                ],
                'trim' => false,
            ])
            ->add('message3', TextType::class, [
                'label' => 'admin.order.export.pdf.label.006',
                'required' => false,
                'attr' => ['maxlength' => $config['eccube_order_pdf_message_len']],
                'constraints' => [
                    new Assert\Length(['max' => $config['eccube_order_pdf_message_len']]),
                ],
                'trim' => false,
            ])
            // 備考
            ->add('note1', TextType::class, [
                'label' => 'admin.order.export.pdf.label.007',
                'required' => false,
                'attr' => ['maxlength' => $config['eccube_stext_len']],
                'constraints' => [
                    new Assert\Length(['max' => $config['eccube_stext_len']]),
                ],
            ])
            ->add('note2', TextType::class, [
                'label' => 'admin.order.export.pdf.label.008',
                'required' => false,
                'attr' => ['maxlength' => $config['eccube_stext_len']],
                'constraints' => [
                    new Assert\Length(['max' => $config['eccube_stext_len']]),
                ],
            ])
            ->add('note3', TextType::class, [
                'label' => 'admin.order.export.pdf.label.009',
                'required' => false,
                'attr' => ['maxlength' => $config['eccube_stext_len']],
                'constraints' => [
                    new Assert\Length(['max' => $config['eccube_stext_len']]),
                ],
            ])
            ->add('default', CheckboxType::class, [
                'required' => false,
                'label' => 'admin.order.export.pdf.label.010',
            ])
            ->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) {
                $form = $event->getForm();
                $data = $form->getData();
                if (!isset($data['ids']) || !is_string($data['ids'])) {
                    return;
                }
                $ids = explode(',', $data['ids']);

                $qb = $this->entityManager->createQueryBuilder();
                $qb->select('count(s.id)')
                    ->from('Eccube\\Entity\\Shipping', 's')
                    ->where($qb->expr()->in('s.id', ':ids'))
                    ->setParameter('ids', $ids);
                $actual = $qb->getQuery()->getSingleScalarResult();
                $expected = count($ids);
                if ($actual != $expected) {
                    $form['ids']->addError(
                        new FormError(trans('admin.order.export.pdf.parameter.not.found'))
                    );
                }
            });
    }

    /**
     * Get name method (form factory name).
     *
     * @return string
     */
    public function getName()
    {
        return 'admin_order_pdf';
    }
}
