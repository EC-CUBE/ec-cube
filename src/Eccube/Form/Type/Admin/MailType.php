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
use Eccube\Common\EccubeConfig;
use Eccube\Entity\MailTemplate;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Eccube\Form\Validator\TwigLint;

class MailType extends AbstractType
{

    /**
     * @var EccubeConfig
     */
    protected $eccubeConfig;

    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * MailType constructor.
     * @param EccubeConfig $eccubeConfig
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(
        EccubeConfig $eccubeConfig,
        EntityManagerInterface $entityManager
    )
    {
        $this->eccubeConfig = $eccubeConfig;
        $this->entityManager = $entityManager;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('mail_subject', TextType::class, [
                'required' => true,
                'constraints' => [
                    new Assert\NotBlank(),
                ],
            ])
            ->add('tpl_data', TextareaType::class, [
                'label' => false,
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new TwigLint(),
                ],
            ])
            ->add('html_tpl_data', TextareaType::class, [
                'label' => false,
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new TwigLint(),
                ],
            ])
        ;

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            /** @var MailTemplate $Mail */
            $Mail = $event->getData();
            if (!$Mail || !$Mail->getId()) {
                $form = $event->getForm();

                $form
                    ->add('name', TextType::class, [
                        'required' => true,
                        'constraints' => [
                            new Assert\Length([
                                'max' => $this->eccubeConfig['eccube_name_len'],
                            ]),
                            new Assert\NotBlank(),
                        ],
                    ])
                    ->add('file_name', TextType::class, [
                        'required' => true,
                        'constraints' => [
                            new Assert\NotBlank(),
                            new Assert\Length([
                                'max' => $this->eccubeConfig['eccube_stext_len'],
                            ]),
                            new Assert\Regex([
                                'pattern' => '/^([0-9a-zA-Z_\-]+\/?)+$/',
                            ]),
                        ],
                    ])
                ;
            }
        });

        $builder->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) {
            $form = $event->getForm();

            /** @var MailTemplate $Mail*/
            $Mail = $event->getData();

            if (!$Mail->getId()) {
                $qb = $this->entityManager->createQueryBuilder();
                $qb->select('count(m)')
                    ->from('Eccube\\Entity\\MailTemplate', 'm')
                    ->where('m.file_name = :file_name')
                    ->setParameter('file_name', $Mail['file_name']);

                $count = $qb->getQuery()->getSingleScalarResult();
                if ($count > 0) {
                    $form['file_name']->addError(new FormError(trans('admin.content.page_file_name_exists')));
                }
            }
        });
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => MailTemplate::class,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'mail';
    }
}
