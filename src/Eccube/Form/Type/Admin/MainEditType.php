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
use Doctrine\ORM\EntityRepository;
use Eccube\Common\EccubeConfig;
use Eccube\Entity\Layout;
use Eccube\Entity\Master\DeviceType;
use Eccube\Entity\Page;
use Eccube\Form\Validator\TwigLint;
use Eccube\Repository\Master\DeviceTypeRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class MainEditType extends AbstractType
{
    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * @var DeviceTypeRepository
     */
    protected $deviceTypeRepository;

    /**
     * @var EccubeConfig
     */
    protected $eccubeConfig;

    /**
     * MainEditType constructor.
     *
     * @param EntityManagerInterface $entityManager
     * @param DeviceTypeRepository $deviceTypeRepository
     * @param EccubeConfig $eccubeConfig
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        DeviceTypeRepository $deviceTypeRepository,
        EccubeConfig $eccubeConfig
    ) {
        $this->entityManager = $entityManager;
        $this->deviceTypeRepository = $deviceTypeRepository;
        $this->eccubeConfig = $eccubeConfig;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'required' => true,
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length([
                        'max' => $this->eccubeConfig['eccube_stext_len'],
                    ]),
                ],
            ])
            ->add('url', TextType::class, [
                'required' => true,
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length([
                        'max' => $this->eccubeConfig['eccube_stext_len'],
                    ]),
                    new Assert\Regex([
                        'pattern' => '/^([0-9a-zA-Z_\-]+\/?)+(?<!\/)$/',
                    ]),
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
            ->add('tpl_data', TextareaType::class, [
                'label' => false,
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new Assert\NotBlank(),
                    new TwigLint(),
                ],
            ])
            ->add('author', TextType::class, [
                'required' => false,
                'constraints' => [
                    new Assert\Length([
                        'max' => $this->eccubeConfig['eccube_stext_len'],
                    ]),
                ],
            ])
            ->add('description', TextType::class, [
                'required' => false,
                'constraints' => [
                    new Assert\Length([
                        'max' => $this->eccubeConfig['eccube_stext_len'],
                    ]),
                ],
            ])
            ->add('keyword', TextType::class, [
                'required' => false,
                'constraints' => [
                    new Assert\Length([
                        'max' => $this->eccubeConfig['eccube_stext_len'],
                    ]),
                ],
            ])
            ->add('meta_robots', TextType::class, [
                'required' => false,
                'constraints' => [
                    new Assert\Length([
                        'max' => $this->eccubeConfig['eccube_stext_len'],
                    ]),
                ],
            ])->add('meta_tags', TextAreaType::class, [
                'required' => false,
                'constraints' => [
                    new Assert\Length([
                        'max' => $this->eccubeConfig['eccube_ltext_len'],
                    ]),
                ],
            ])
            ->add('PcLayout', EntityType::class, [
                'mapped' => false,
                'placeholder' => '---',
                'required' => false,
                'class' => Layout::class,
                'query_builder' => function (EntityRepository $er) {
                    $DeviceType = $this->deviceTypeRepository->find(DeviceType::DEVICE_TYPE_PC);

                    return $er->createQueryBuilder('l')
                        ->where('l.id != :DefaultLayoutPreviewPage')
                        ->andWhere('l.DeviceType = :DeviceType')
                        ->setParameter('DeviceType', $DeviceType)
                        ->setParameter('DefaultLayoutPreviewPage', Layout::DEFAULT_LAYOUT_PREVIEW_PAGE)
                        ->orderBy('l.id', 'DESC');
                },
            ])
            ->add('SpLayout', EntityType::class, [
                'mapped' => false,
                'placeholder' => '---',
                'required' => false,
                'class' => Layout::class,
                'query_builder' => function (EntityRepository $er) {
                    $DeviceType = $this->deviceTypeRepository->find(DeviceType::DEVICE_TYPE_MB);

                    return $er->createQueryBuilder('l')
                        ->where('l.id != :DefaultLayoutPreviewPage')
                        ->andWhere('l.DeviceType = :DeviceType')
                        ->setParameter('DeviceType', $DeviceType)
                        ->setParameter('DefaultLayoutPreviewPage', Layout::DEFAULT_LAYOUT_PREVIEW_PAGE)
                        ->orderBy('l.id', 'DESC');
                },
            ])
            ->addEventListener(FormEvents::POST_SET_DATA, function (FormEvent $event) {
                $Page = $event->getData();
                if (is_null($Page->getId())) {
                    return;
                }
                $form = $event->getForm();
                $Layouts = $Page->getLayouts();
                foreach ($Layouts as $Layout) {
                    if ($Layout->getDeviceType()->getId() == DeviceType::DEVICE_TYPE_PC) {
                        $form['PcLayout']->setData($Layout);
                    }
                    if ($Layout->getDeviceType()->getId() == DeviceType::DEVICE_TYPE_MB) {
                        $form['SpLayout']->setData($Layout);
                    }
                }
            })
            ->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) {
                $form = $event->getForm();

                /** @var Page $Page */
                $Page = $event->getData();

                // urlの重複チェック
                $qb = $this->entityManager->createQueryBuilder();
                $qb->select('count(p)')
                    ->from('Eccube\\Entity\\Page', 'p')
                    ->where('p.url = :url')
                    ->setParameter('url', $Page->getUrl());

                // 更新の場合は自身のデータを重複チェックから除外する
                if (!is_null($Page->getId())) {
                    $qb
                        ->andWhere('p.id <> :page_id')
                        ->setParameter('page_id', $Page->getId());
                }

                // 確認ページの編集ページ存在している場合
                if ($Page->getEditType() == Page::EDIT_TYPE_DEFAULT_CONFIRM && $Page->getMasterPage()) {
                    $qb
                        ->andWhere('p.id <> :master_page_id')
                        ->setParameter('master_page_id', $Page->getMasterPage()->getId());
                }

                $count = $qb->getQuery()->getSingleScalarResult();
                if ($count > 0) {
                    $form['url']->addError(new FormError(trans('admin.content.page_url_exists')));
                }

                // Page::EDIT_TYPE_USER ファイルの重複チェック
                $qb = $this->entityManager->createQueryBuilder();
                $qb->select('count(p)')
                    ->from('Eccube\\Entity\\Page', 'p')
                    ->where('p.file_name = :file_name')
                    ->andWhere('p.edit_type = :edit_type')
                    ->setParameter('file_name', $Page->getFileName())
                    ->setParameter('edit_type', Page::EDIT_TYPE_USER);

                // 更新の場合は自身のデータを重複チェックから除外する
                if (!is_null($Page->getId())) {
                    $qb
                        ->andWhere('p.id <> :page_id')
                        ->setParameter('page_id', $Page->getId());
                }

                $count = $qb->getQuery()->getSingleScalarResult();
                if ($count > 0) {
                    $form['file_name']->addError(new FormError(trans('admin.content.page_file_name_exists')));
                }

                // Page::EDIT_TYPE_USER を修正した場合は、 Page::EDIT_TYPE_DEFAULT とファイルの重複チェック
                if (Page::EDIT_TYPE_USER === $Page->getEditType()) {
                    $qb = $this->entityManager->createQueryBuilder();
                    $qb->select('count(p)')
                        ->from('Eccube\\Entity\\Page', 'p')
                        ->where('p.file_name = :file_name')
                        ->andWhere('p.edit_type >= :edit_type')
                        ->setParameter('file_name', $Page->getFileName())
                        ->setParameter('edit_type', Page::EDIT_TYPE_DEFAULT);

                    // 更新の場合は自身のデータを重複チェックから除外する
                    if (!is_null($Page->getId())) {
                        $qb
                            ->andWhere('p.id <> :page_id')
                            ->setParameter('page_id', $Page->getId());
                    }

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
            'data_class' => Page::class,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'main_edit';
    }
}
