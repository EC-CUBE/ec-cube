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
use Eccube\Entity\OpeningHours;
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
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * Class ShopMasterType
 */
class ShopMasterType extends AbstractType
{
    /**
     * 日をまたぐ営業時間の重複判定で使う、曜日（schema.org DayOfWeek）の翌日.
     * PublicHolidays は翌日の曜日が決まらないため含めない.
     */
    private const NEXT_DAY_OF_WEEK = [
        'Monday' => 'Tuesday',
        'Tuesday' => 'Wednesday',
        'Wednesday' => 'Thursday',
        'Thursday' => 'Friday',
        'Friday' => 'Saturday',
        'Saturday' => 'Sunday',
        'Sunday' => 'Monday',
    ];

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
                    new Assert\Length(max: $this->eccubeConfig['eccube_stext_len']),
                ],
            ])
            ->add('shop_name', TextType::class, [
                'required' => true,
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length(max: $this->eccubeConfig['eccube_stext_len']),
                ],
            ])
            ->add('shop_name_eng', TextType::class, [
                'required' => false,
                'constraints' => [
                    new Assert\Length(max: $this->eccubeConfig['eccube_mtext_len']),
                    new Assert\Regex(pattern: '/^[[:graph:][:space:]]+$/i'),
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
                    new Assert\Length(max: $this->eccubeConfig['eccube_stext_len']),
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
                    new Assert\Length(max: $this->eccubeConfig['eccube_ltext_len']),
                ],
            ])
            ->add('message', TextareaType::class, [
                'required' => false,
                'constraints' => [
                    new Assert\Length(max: $this->eccubeConfig['eccube_ltext_len']),
                ],
            ])
            // 構造化データ（JSON-LD / schema.org）
            ->add('same_as', TextareaType::class, [
                'required' => false,
                'constraints' => [
                    new Assert\Length(max: $this->eccubeConfig['eccube_ltext_len']),
                    new Assert\Callback($this->validateSameAsUrls(...)),
                ],
            ])
            ->add('founding_date', DateType::class, [
                'required' => false,
                'input' => 'datetime',
                'widget' => 'single_text',
                'constraints' => [
                    new Assert\LessThanOrEqual(value: 'today', message: 'admin.setting.shop.founding_date.error.not_future'),
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
                    new Assert\Range(min: 1900, max: 9999),
                ],
            ])
            ->add('site_image', TextType::class, [
                'required' => false,
                'constraints' => [
                    new Assert\Length(max: $this->eccubeConfig['eccube_stext_len']),
                    new Assert\Url(),
                ],
            ])
            // opening_hours_rendered は営業時間の欄が描画されたことを示すセンチネル。
            // shop_master.twig が営業時間の欄と一緒に出力するため、テンプレートを上書きして
            // 欄を描画していない場合だけ送信データから欠落する。
            // 「上書きで未描画」と「画面で全行削除」はどちらも OpeningHours のキーが送られてこないため、
            // このセンチネルが無いと両者を区別できない（PRE_SUBMIT の removeUnrenderedOpeningHours() を参照）。
            ->add('opening_hours_rendered', HiddenType::class, [
                'mapped' => false,
                'data' => '1',
            ])
            ->add('OpeningHours', CollectionType::class, [
                'entry_type' => OpeningHoursType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'prototype' => true,
                'by_reference' => false,
                'required' => false,
                // 既存の行の値をすべて消した場合も行ごと削除する。`true` だと子フォームの
                // データが OpeningHours のインスタンスのため isEmpty() が false になり、削除されない
                'delete_empty' => static fn (?OpeningHours $OpeningHours = null): bool => $OpeningHours === null
                    || (!$OpeningHours->getDayOfWeek() && $OpeningHours->getOpens() === null && $OpeningHours->getCloses() === null),
            ])
            // 送料設定
            ->add('delivery_free_amount', PriceType::class, [
                'required' => false,
            ])

            ->add('delivery_free_quantity', IntegerType::class, [
                'required' => false,
                'constraints' => [
                    new Assert\Regex(pattern: "/^\d+$/u", message: 'form_error.numeric_only'),
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
                    new Assert\Length(max: $this->eccubeConfig['eccube_stext_len']),
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
                    new Assert\Regex(pattern: "/^\d+$/u", message: 'form_error.numeric_only'),
                    new Assert\Range(min: 0, max: 100),
                ],
            ])
            ->add('point_conversion_rate', NumberType::class, [
                'required' => false,
                'constraints' => [
                    new Assert\Regex(pattern: "/^\d+$/u", message: 'form_error.numeric_only'),
                    new Assert\Range(min: 1, max: 100),
                ],
            ])
            ->add('ga_id', TextType::class, [
                'required' => false,
                'constraints' => [
                    new Assert\Length(max: $this->eccubeConfig['eccube_stext_len']),
                ],
            ])
            // エージェントコマース checkout の有効化フラグ (discovery / catalog は常時公開、checkout のみ制御)
            ->add('acp_checkout_enabled', ToggleSwitchType::class)
            ->add('ucp_checkout_enabled', ToggleSwitchType::class)
            // MCP サーバ機能の有効化フラグ (既定 OFF。 OFF の間は ^/admin/mcp が 404)
            ->add('mcp_enabled', ToggleSwitchType::class)
            // 納品書PDFの店舗情報出力項目トグル（#6197）。並び順は納品書の描画順にそろえる
            ->add('order_pdf_visible_shop_name', ToggleSwitchType::class)
            ->add('order_pdf_visible_shop_name_eng', ToggleSwitchType::class)
            ->add('order_pdf_visible_address', ToggleSwitchType::class)
            ->add('order_pdf_visible_company_name', ToggleSwitchType::class)
            ->add('order_pdf_visible_phone_number', ToggleSwitchType::class)
            ->add('order_pdf_visible_business_hour', ToggleSwitchType::class)
            ->add('order_pdf_visible_email', ToggleSwitchType::class)
            ->add('order_pdf_visible_invoice_number', ToggleSwitchType::class)
        ;

        $builder->add(
            $builder
                ->create('company_kana', TextType::class, [
                    'required' => false,
                    'constraints' => [
                        new Assert\Regex(pattern: '/^[ァ-ヶｦ-ﾟー]+$/u'),
                        new Assert\Length(max: $this->eccubeConfig['eccube_stext_len']),
                    ],
                ])
                ->addEventSubscriber(new ConvertKanaListener('CV'))
        );

        $builder->add(
            $builder
                ->create('shop_kana', TextType::class, [
                    'required' => false,
                    'constraints' => [
                        new Assert\Length(max: $this->eccubeConfig['eccube_stext_len']),
                        new Assert\Regex(pattern: '/^[ァ-ヶｦ-ﾟー]+$/u'),
                    ],
                ])
                ->addEventSubscriber(new ConvertKanaListener('CV'))
        );

        $builder->addEventListener(FormEvents::PRE_SUBMIT, $this->removeUnrenderedOpeningHours(...));
        $builder->addEventListener(FormEvents::PRE_SUBMIT, $this->reindexOpeningHours(...));
        // 営業時間の重複はフォームの子キーに紐づけたいので、クラス制約の Callback ではなく
        // POST_SUBMIT で検証する（詳細は validateOpeningHoursOverlap() のコメント参照）。
        $builder->addEventListener(FormEvents::POST_SUBMIT, $this->validateOpeningHoursOverlap(...));
        $builder->addEventListener(FormEvents::POST_SUBMIT, $this->sortOpeningHours(...));
    }

    /**
     * {@inheritdoc}
     */
    #[\Override]
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => BaseInfo::class,
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
     * 営業時間の欄が描画されていない（テンプレートの上書き等）ときは、営業時間に一切触れない.
     *
     * CollectionType は送信キーが無いと空のコレクションとして扱い、allow_delete と
     * BaseInfo::$OpeningHours の orphanRemoval により既存の営業時間を警告なしで全件削除してしまうため、
     * フィールドごと取り除いて DataMapper が $OpeningHours に触れないようにする.
     */
    private function removeUnrenderedOpeningHours(FormEvent $event): void
    {
        $data = $event->getData();
        if (!is_array($data) || empty($data['opening_hours_rendered'])) {
            $event->getForm()->remove('OpeningHours');
        }
    }

    /**
     * 送信された営業時間の行のキーを 0 から詰め直す.
     *
     * 画面で中間の行を削除すると送信キーは歯抜け（0, 2 等）になるが、BaseInfo::$OpeningHours は
     * adder で 0 から詰めて追加されるため、OpeningHours::validate() の違反のパス
     * （OpeningHours[<コレクションの添字>].closes 等）が別の行や実在しない子を指し、
     * エラーが該当の行に表示されない（ルートのフォームに付き、画面に何も出ずに保存されない）。
     * 子フォームのキーとコレクションの添字をそろえるため、送信データの段階で詰め直す.
     */
    private function reindexOpeningHours(FormEvent $event): void
    {
        $data = $event->getData();
        if (!is_array($data) || !isset($data['OpeningHours']) || !is_array($data['OpeningHours'])) {
            return;
        }

        $data['OpeningHours'] = array_values($data['OpeningHours']);
        $event->setData($data);
    }

    /**
     * 営業時間の表示順（sort_no）を画面の行の順に 0 から振り直す.
     *
     * 送信キーではなくフォームの子の並び順を使う（キーは reindexOpeningHours() で詰め直している）.
     */
    private function sortOpeningHours(FormEvent $event): void
    {
        $form = $event->getForm();
        if (!$form->has('OpeningHours')) {
            return;
        }

        $sortNo = 0;
        foreach ($form->get('OpeningHours') as $child) {
            $OpeningHours = $child->getData();
            if ($OpeningHours instanceof OpeningHours) {
                $OpeningHours->setSortNo($sortNo++);
            }
        }
    }

    /**
     * 営業時間の時間帯が重複していないかを、日をまたぐ行は翌日の曜日の行とも比べて検証する.
     *
     * 閉店が開店より前の行（深夜営業）は、曜日 d の [開店, 24:00) と翌日の [00:00, 閉店) に分けて比較する。
     * 日曜の翌日は月曜とする。祝日（PublicHolidays）は翌日の曜日が決まらないため、翌日側は比較しない。
     *
     * エンティティのコレクションではなく**フォームの子**を走査し、エラーを子フォームへ直接付ける
     * （送信キーは reindexOpeningHours() で 0 から詰め直しており、子のキーと画面の行の順が一致する）。
     */
    private function validateOpeningHoursOverlap(FormEvent $event): void
    {
        $form = $event->getForm();
        if (!$form->has('OpeningHours')) {
            return;
        }

        /** @var FormInterface[] $children */
        $children = iterator_to_array($form->get('OpeningHours'));
        $names = array_keys($children);
        $segments = [];
        foreach ($names as $name) {
            $data = $children[$name]->getData();
            $segments[$name] = $data instanceof OpeningHours ? $this->toDailySegments($data) : [];
        }

        $count = count($names);
        for ($i = 0; $i < $count; ++$i) {
            for ($j = $i + 1; $j < $count; ++$j) {
                if ($this->segmentsOverlap($segments[$names[$i]], $segments[$names[$j]])) {
                    // 描画済みのリーフ（closes）にエラーを付け、該当行に表示されるようにする
                    $children[$names[$j]]->get('closes')
                        ->addError(new FormError(trans('admin.setting.shop.opening_hours.error.overlap')));
                }
            }
        }
    }

    /**
     * 営業時間 1 行を、曜日ごとの [開始分, 終了分) の区間に分解する.
     *
     * 時刻が欠けている行・開店と閉店が同じ行は単体バリデーションに委ね、区間を返さない.
     *
     * @return list<array{0: string, 1: int, 2: int}>
     */
    private function toDailySegments(OpeningHours $OpeningHours): array
    {
        $opens = $OpeningHours->getOpens();
        $closes = $OpeningHours->getCloses();
        if ($opens === null || $closes === null) {
            return [];
        }

        $opensMin = (int) $opens->format('G') * 60 + (int) $opens->format('i');
        $closesMin = (int) $closes->format('G') * 60 + (int) $closes->format('i');
        if ($opensMin === $closesMin) {
            return [];
        }

        $segments = [];
        foreach ($OpeningHours->getDayOfWeek() ?? [] as $day) {
            if ($opensMin < $closesMin) {
                $segments[] = [$day, $opensMin, $closesMin];
                continue;
            }

            // 日をまたぐ営業: 当日の [開店, 24:00) と翌日の [00:00, 閉店)
            $segments[] = [$day, $opensMin, 24 * 60];
            $nextDay = self::NEXT_DAY_OF_WEEK[$day] ?? null;
            if ($nextDay !== null) {
                $segments[] = [$nextDay, 0, $closesMin];
            }
        }

        return $segments;
    }

    /**
     * 2 つの区間リストに、同じ曜日で交差する区間があるか.
     *
     * @param list<array{0: string, 1: int, 2: int}> $a
     * @param list<array{0: string, 1: int, 2: int}> $b
     */
    private function segmentsOverlap(array $a, array $b): bool
    {
        foreach ($a as [$dayA, $startA, $endA]) {
            foreach ($b as [$dayB, $startB, $endB]) {
                if ($dayA === $dayB && max($startA, $startB) < min($endA, $endB)) {
                    return true;
                }
            }
        }

        return false;
    }
}
