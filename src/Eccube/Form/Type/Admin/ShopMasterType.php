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
use Eccube\Entity\BaseInfo;
use Eccube\Form\EventListener\ConvertKanaListener;
use Eccube\Form\Type\AddressType;
use Eccube\Form\Type\PhoneNumberType;
use Eccube\Form\Type\PostalType;
use Eccube\Form\Type\PriceType;
use Eccube\Form\Type\ToggleSwitchType;
use Eccube\Form\Validator\Email;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * Class ShopMasterType
 */
class ShopMasterType extends AbstractType
{
    /**
     * ShopMasterType constructor.
     */
    public function __construct(protected EccubeConfig $eccubeConfig)
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
            ->add('company_name', TextType::class, [
                'required' => false,
                'constraints' => [
                    new Assert\Length([
                        'max' => $this->eccubeConfig['eccube_stext_len'],
                    ]),
                ],
            ])
            ->add('shop_name', TextType::class, [
                'required' => true,
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length([
                        'max' => $this->eccubeConfig['eccube_stext_len'],
                    ]),
                ],
            ])
            ->add('shop_name_eng', TextType::class, [
                'required' => false,
                'constraints' => [
                    new Assert\Length([
                        'max' => $this->eccubeConfig['eccube_mtext_len'],
                    ]),
                    new Assert\Regex([
                        'pattern' => '/^[[:graph:][:space:]]+$/i',
                    ]),
                ],
            ])
            ->add('postal_code', PostalType::class, [
                'required' => false,
            ])
            ->add('address', AddressType::class, [
                'required' => false,
            ])
            ->add('phone_number', PhoneNumberType::class, [
                'required' => false,
            ])
            ->add('business_hour', TextType::class, [
                'required' => false,
                'constraints' => [
                    new Assert\Length([
                        'max' => $this->eccubeConfig['eccube_stext_len'],
                    ]),
                ],
            ])
            ->add('email01', EmailType::class, [
                'required' => false,
                'constraints' => [
                    new Assert\NotBlank(),
                    new Email(null, null, $this->eccubeConfig['eccube_rfc_email_check'] ? 'strict' : null),
                ],
            ])
            ->add('email02', EmailType::class, [
                'required' => false,
                'constraints' => [
                    new Assert\NotBlank(),
                    new Email(null, null, $this->eccubeConfig['eccube_rfc_email_check'] ? 'strict' : null),
                ],
            ])
            ->add('email03', EmailType::class, [
                'required' => false,
                'constraints' => [
                    new Assert\NotBlank(),
                    new Email(null, null, $this->eccubeConfig['eccube_rfc_email_check'] ? 'strict' : null),
                ],
            ])
            ->add('email04', EmailType::class, [
                'required' => false,
                'constraints' => [
                    new Assert\NotBlank(),
                    new Email(null, null, $this->eccubeConfig['eccube_rfc_email_check'] ? 'strict' : null),
                ],
            ])
            ->add('good_traded', TextareaType::class, [
                'required' => false,
                'constraints' => [
                    new Assert\Length([
                        'max' => $this->eccubeConfig['eccube_ltext_len'],
                    ]),
                ],
            ])
            ->add('message', TextareaType::class, [
                'required' => false,
                'constraints' => [
                    new Assert\Length([
                        'max' => $this->eccubeConfig['eccube_ltext_len'],
                    ]),
                ],
            ])
            // 構造化データ（JSON-LD / schema.org）
            ->add('same_as', TextareaType::class, [
                'required' => false,
                'constraints' => [
                    new Assert\Length([
                        'max' => $this->eccubeConfig['eccube_ltext_len'],
                    ]),
                    new Assert\Callback($this->validateSameAsUrls(...)),
                ],
            ])
            ->add('founding_date', DateType::class, [
                'required' => false,
                'input' => 'datetime',
                'widget' => 'single_text',
                'constraints' => [
                    new Assert\LessThanOrEqual([
                        'value' => 'today',
                        'message' => 'admin.setting.shop.founding_date.error.not_future',
                    ]),
                ],
            ])
            ->add('number_of_employees', IntegerType::class, [
                'required' => false,
                'constraints' => [
                    new Assert\PositiveOrZero(),
                    // DB カラムは INT。桁あふれによる保存時エラーを防ぐため上限を設ける
                    new Assert\LessThanOrEqual(2147483647),
                ],
            ])
            ->add('copyright_year', IntegerType::class, [
                'required' => false,
                'constraints' => [
                    new Assert\Range([
                        'min' => 1900,
                        'max' => 9999,
                    ]),
                ],
            ])
            ->add('site_image', TextType::class, [
                'required' => false,
                'constraints' => [
                    new Assert\Length([
                        'max' => $this->eccubeConfig['eccube_stext_len'],
                    ]),
                    new Assert\Url(),
                ],
            ])
            ->add('OpeningHours', CollectionType::class, [
                'entry_type' => OpeningHoursType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'prototype' => true,
                'by_reference' => false,
                'required' => false,
            ])
            // 送料設定
            ->add('delivery_free_amount', PriceType::class, [
                'required' => false,
            ])

            ->add('delivery_free_quantity', IntegerType::class, [
                'required' => false,
                'constraints' => [
                    new Assert\Regex([
                        'pattern' => "/^\d+$/u",
                        'message' => 'form_error.numeric_only',
                    ]),
                ],
            ])
            ->add('option_product_delivery_fee', ToggleSwitchType::class)
            // 会員設定
            ->add('option_customer_activate', ToggleSwitchType::class)
            // マイページに注文状況を表示する
            ->add('option_mypage_order_status_display', ToggleSwitchType::class)
            // 自動ログイン
            ->add('option_remember_me', ToggleSwitchType::class)
            // 会員の重要操作時にメールを通知する
            ->add('option_mail_notifier', ToggleSwitchType::class)
            // ゲスト購入設定
            ->add('option_guest_purchase', ToggleSwitchType::class)
            // お気に入り商品設定
            ->add('option_favorite_product', ToggleSwitchType::class)
            // 在庫切れ商品を非表示にする
            ->add('option_nostock_hidden', ToggleSwitchType::class)
            // CSV出力時に数式評価され得る先頭文字を無害化する
            ->add('option_sanitize_csv_formulas', ToggleSwitchType::class)
            // 適格請求書発行事業者登録番号
            ->add('invoice_registration_number', TextType::class, [
                'required' => false,
                'constraints' => [
                    new Assert\Length([
                        'max' => $this->eccubeConfig['eccube_stext_len'],
                    ]),
                ],
            ])
            // 個別税率設定
            ->add('option_product_tax_rule', ToggleSwitchType::class)
            // ポイント設定
            ->add('option_point', ToggleSwitchType::class)
            // クッキーポリシー同意機能
            ->add('option_cookie_consent', ToggleSwitchType::class)
            ->add('basic_point_rate', NumberType::class, [
                'required' => false,
                'constraints' => [
                    new Assert\Regex([
                        'pattern' => "/^\d+$/u",
                        'message' => 'form_error.numeric_only',
                    ]),
                    new Assert\Range([
                        'min' => 0,
                        'max' => 100,
                    ]),
                ],
            ])
            ->add('point_conversion_rate', NumberType::class, [
                'required' => false,
                'constraints' => [
                    new Assert\Regex([
                        'pattern' => "/^\d+$/u",
                        'message' => 'form_error.numeric_only',
                    ]),
                    new Assert\Range([
                        'min' => 1,
                        'max' => 100,
                    ]),
                ],
            ])
            ->add('ga_id', TextType::class, [
                'required' => false,
                'constraints' => [
                    new Assert\Length([
                        'max' => $this->eccubeConfig['eccube_stext_len'],
                    ]),
                ],
            ])
            // エージェントコマース checkout の有効化フラグ (discovery / catalog は常時公開、checkout のみ制御)
            ->add('acp_checkout_enabled', ToggleSwitchType::class)
            ->add('ucp_checkout_enabled', ToggleSwitchType::class)
            // MCP サーバ機能の有効化フラグ (既定 OFF。 OFF の間は ^/admin/mcp が 404)
            ->add('mcp_enabled', ToggleSwitchType::class)
        ;

        $builder->add(
            $builder
                ->create('company_kana', TextType::class, [
                    'required' => false,
                    'constraints' => [
                        new Assert\Regex([
                            'pattern' => '/^[ァ-ヶｦ-ﾟー]+$/u',
                        ]),
                        new Assert\Length([
                            'max' => $this->eccubeConfig['eccube_stext_len'],
                        ]),
                    ],
                ])
                ->addEventSubscriber(new ConvertKanaListener('CV'))
        );

        $builder->add(
            $builder
                ->create('shop_kana', TextType::class, [
                    'required' => false,
                    'constraints' => [
                        new Assert\Length([
                            'max' => $this->eccubeConfig['eccube_stext_len'],
                        ]),
                        new Assert\Regex([
                            'pattern' => '/^[ァ-ヶｦ-ﾟー]+$/u',
                        ]),
                    ],
                ])
                ->addEventSubscriber(new ConvertKanaListener('CV'))
        );
    }

    /**
     * {@inheritdoc}
     */
    #[\Override]
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => BaseInfo::class,
            'constraints' => [
                new Assert\Callback($this->validateOpeningHoursOverlap(...)),
            ],
        ]);
    }

    /**
     * sameAs（改行区切りの複数URL）の各行が有効な URL か検証する.
     *
     * 単一URLの site_image と同じ Assert\Url で1行ずつ検証し、
     * 1行でも不正なら項目全体にエラーを付ける（空行は無視する）.
     */
    public function validateSameAsUrls(?string $sameAs, ExecutionContextInterface $context): void
    {
        if ($sameAs === null || $sameAs === '') {
            return;
        }

        $validator = $context->getValidator();
        $lines = preg_split('/\R/u', $sameAs) ?: [];
        foreach ($lines as $line) {
            $url = trim($line);
            if ($url === '') {
                continue;
            }
            if ($validator->validate($url, new Assert\Url())->count() > 0) {
                $context->buildViolation('admin.setting.shop.same_as.error.invalid_url')
                    ->addViolation();

                return;
            }
        }
    }

    /**
     * 同一曜日を含む営業時間の時間帯が重複していないか検証する.
     */
    public function validateOpeningHoursOverlap(?BaseInfo $BaseInfo, ExecutionContextInterface $context): void
    {
        if (!$BaseInfo instanceof BaseInfo) {
            return;
        }

        $list = array_values($BaseInfo->getOpeningHours()->toArray());
        $count = count($list);
        for ($i = 0; $i < $count; ++$i) {
            for ($j = $i + 1; $j < $count; ++$j) {
                $a = $list[$i];
                $b = $list[$j];

                $daysA = $a->getDayOfWeek() ?? [];
                $daysB = $b->getDayOfWeek() ?? [];
                if (array_intersect($daysA, $daysB) === []) {
                    continue;
                }

                $opensA = $a->getOpens();
                $closesA = $a->getCloses();
                $opensB = $b->getOpens();
                $closesB = $b->getCloses();
                // 時刻が欠けている行は単体バリデーションに委ねる
                if ($opensA === null || $closesA === null || $opensB === null || $closesB === null) {
                    continue;
                }

                // 時間帯が交差する場合はエラー（max(開店) < min(閉店)）
                // 描画済みのリーフ（closes）にエラーを付け、該当行に表示されるようにする
                if (max($opensA, $opensB) < min($closesA, $closesB)) {
                    $context->buildViolation('admin.setting.shop.opening_hours.error.overlap')
                        ->atPath('OpeningHours['.$j.'].closes')
                        ->addViolation();
                }
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    #[\Override]
    public function getBlockPrefix(): string
    {
        return 'shop_master';
    }
}
