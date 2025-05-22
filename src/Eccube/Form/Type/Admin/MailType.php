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
use Eccube\Entity\MailTemplate;
use Eccube\Form\Type\Master\MailTemplateType;
use Eccube\Form\Validator\TwigLint;
use Eccube\Repository\MailTemplateRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class MailType extends AbstractType
{
    private MailTemplateRepository $mailTemplateRepository;

    private EccubeConfig $eccubeConfig;

    public function __construct(MailTemplateRepository $mailTemplateRepository, EccubeConfig $eccubeConfig)
    {
        $this->mailTemplateRepository = $mailTemplateRepository;
        $this->eccubeConfig = $eccubeConfig;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('template', MailTemplateType::class, [
                'required' => false,
                'mapped' => false,
            ])
            ->add('name', TextType::class, [
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length(['max' => $this->eccubeConfig['eccube_stext_len']]),
                ],
            ])
            ->add('mail_subject', TextType::class, [
                'required' => true,
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length(['max' => $this->eccubeConfig['eccube_stext_len']]),
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

        $builder->addEventListener(FormEvents::POST_SET_DATA, function (FormEvent $event) {
            $data = $event->getData();
            if (null === $data->getId()) {
                $form = $event->getForm();
                $form->add('file_name', TextType::class, [
                    'constraints' => [
                        new Assert\NotBlank(),
                        new Assert\Regex(['pattern' => '/^[0-9a-z_-]+$/']),
                        new Assert\Length(['max' => $this->eccubeConfig['eccube_stext_len']]),
                    ],
                ]);
            }
        });

        $builder->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) {
            $data = $event->getData();
            if (null === $data->getId()) {
                $filename = 'Mail/'.$data->getFileName().'.twig';
                $MailTemplate = $this->mailTemplateRepository->findBy(['file_name' => $filename]);
                if ($MailTemplate) {
                    $form = $event->getForm();
                    $form['file_name']->addError(new FormError(trans('admin.setting.shop.mail.file_exists')));
                } else {
                    $data->setFileName('Mail/'.$data->getFileName().'.twig');
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
