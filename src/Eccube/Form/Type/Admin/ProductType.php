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
use Eccube\Entity\Category;
use Eccube\Entity\Tag;
use Eccube\Form\Type\Master\ProductStatusType;
use Eccube\Form\Validator\TwigLint;
use Eccube\Repository\CategoryRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class ProductType.
 */
class ProductType extends AbstractType
{
    /**
     * ProductType constructor.
     */
    public function __construct(protected CategoryRepository $categoryRepository, protected EccubeConfig $eccubeConfig)
    {
    }

    /**
     * {@inheritdoc}
     *
     * @param array<string, mixed> $options
     */
    #[\Override]
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // 商品規格情報
            ->add('class', ProductClassType::class, [
                'mapped' => false,
            ])
            // 基本情報
            ->add('name', TextType::class, [
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length(['max' => $this->eccubeConfig['eccube_stext_len']]),
                ],
            ])
            ->add('product_image', FileType::class, [
                'multiple' => true,
                'required' => false,
                'mapped' => false,
            ])
            ->add('description_detail', TextareaType::class, [
                'purify_html' => true,
                'constraints' => [
                    new Assert\Length(['max' => $this->eccubeConfig['eccube_ltext_len']]),
                ],
            ])
            ->add('description_list', TextareaType::class, [
                'purify_html' => true,
                'required' => false,
                'constraints' => [
                    new Assert\Length(['max' => $this->eccubeConfig['eccube_ltext_len']]),
                ],
            ])
            ->add('Category', ChoiceType::class, [
                'choice_label' => 'Name',
                'multiple' => true,
                'mapped' => false,
                'expanded' => true,
                'choices' => $this->categoryRepository->getList(null, true),
                'choice_value' => fn (?Category $Category = null) => $Category ? $Category->getId() : null,
            ])

            // 詳細な説明
            ->add('Tag', EntityType::class, [
                'class' => Tag::class,
                'query_builder' => fn ($er) => $er->createQueryBuilder('t')
                ->orderBy('t.sort_no', 'DESC'),
                'required' => false,
                'multiple' => true,
                'expanded' => true,
                'mapped' => false,
            ])
            ->add('search_word', TextType::class, [
                'required' => false,
                'constraints' => [
                    new Assert\Length(['max' => $this->eccubeConfig['eccube_ltext_len']]),
                ],
            ])
            // サブ情報
            ->add('free_area', TextareaType::class, [
                'purify_html' => true,
                'required' => false,
                'constraints' => [
                    new TwigLint(),
                    new Assert\Length(['max' => $this->eccubeConfig['eccube_lltext_len']]),
                ],
            ])
            ->add('order_memo', TextareaType::class, [
                'required' => false,
                'constraints' => [
                    new Assert\Length(['max' => $this->eccubeConfig['eccube_lltext_len']]),
                ],
            ])

            // 右ブロック
            ->add('Status', ProductStatusType::class, [
                'constraints' => [
                    new Assert\NotBlank(),
                ],
            ])
            ->add('note', TextareaType::class, [
                'required' => false,
                'constraints' => [
                    new Assert\Length(['max' => $this->eccubeConfig['eccube_ltext_len']]),
                ],
            ])

            // タグ
            ->add('tags', CollectionType::class, [
                'entry_type' => HiddenType::class,
                'prototype' => true,
                'mapped' => false,
                'allow_add' => true,
                'allow_delete' => true,
            ])
            // 画像
            ->add('images', CollectionType::class, [
                'entry_type' => HiddenType::class,
                'prototype' => true,
                'mapped' => false,
                'allow_add' => true,
                'allow_delete' => true,
            ])
            ->add('add_images', CollectionType::class, [
                'entry_type' => HiddenType::class,
                'prototype' => true,
                'mapped' => false,
                'allow_add' => true,
                'allow_delete' => true,
            ])
            ->add('delete_images', CollectionType::class, [
                'entry_type' => HiddenType::class,
                'prototype' => true,
                'mapped' => false,
                'allow_add' => true,
                'allow_delete' => true,
            ])
            ->add('return_link', HiddenType::class, [
                'mapped' => false,
            ])
            // 商品ごとFAQ
            //
            // faqs_rendered は FAQ 欄が描画されたことを示すセンチネル。
            // @admin/Content/faq_collection.twig が必ず出力するため、テンプレートを上書きして
            // FAQ 欄を描画していない場合だけ送信データから欠落する。
            // 「上書きで未描画」と「UI で全行削除」はどちらも faqs キーが送られてこないため、
            // このセンチネルが無いと両者を区別できない（下の PRE_SUBMIT を参照）。
            ->add('faqs_rendered', HiddenType::class, [
                'mapped' => false,
                'data' => '1',
            ])
            ->add('faqs', CollectionType::class, [
                'entry_type' => FaqType::class,
                'entry_options' => ['sortable' => true],
                'prototype' => true,
                'mapped' => true,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
            ])
        ;

        // FAQ 欄が描画されていない（テンプレート上書き等）ときは faqs に一切触れない。
        // CollectionType は送信キーが無いと空コレクションとして扱い、allow_delete と
        // Product::$Faqs の orphanRemoval により既存 FAQ を無警告で全削除してしまうため、
        // フィールドごと取り除いて DataMapper が $Faqs に触れないようにする。
        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event): void {
            $data = $event->getData();
            if (!is_array($data) || empty($data['faqs_rendered'])) {
                $event->getForm()->remove('faqs');
            }
        });

        $builder->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event): void {
            /** @var FormInterface $form */
            $form = $event->getForm();
            $saveImgDir = $this->eccubeConfig['eccube_save_image_dir'];
            $tempImgDir = $this->eccubeConfig['eccube_temp_image_dir'];
            $this->validateFilePath($form->get('delete_images'), [$saveImgDir, $tempImgDir]);
            $this->validateFilePath($form->get('add_images'), [$tempImgDir]);
        });
    }

    /**
     * 指定された複数ディレクトリのうち、いずれかのディレクトリ以下にファイルが存在するかを確認。
     *
     * @param array<int, string> $dirs
     */
    private function validateFilePath(FormInterface $form, array $dirs): void
    {
        foreach ($form->getData() as $fileName) {
            if (str_contains((string) $fileName, '..')) {
                $form->getRoot()['product_image']->addError(new FormError(trans('admin.product.image__invalid_path')));
                break;
            }
            $fileInDir = array_filter($dirs, function ($dir) use ($fileName) {
                $filePath = realpath($dir.'/'.$fileName);
                $topDirPath = realpath($dir);

                return str_starts_with($filePath, (string) $topDirPath) && $filePath !== $topDirPath;
            });
            if (!$fileInDir) {
                $form->getRoot()['product_image']->addError(new FormError(trans('admin.product.image__invalid_path')));
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    #[\Override]
    public function configureOptions(OptionsResolver $resolver): void
    {
    }

    /**
     * {@inheritdoc}
     */
    #[\Override]
    public function getBlockPrefix(): string
    {
        return 'admin_product';
    }
}
