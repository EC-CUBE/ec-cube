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

namespace Eccube\Service\AgentCommerce\CheckoutSession;

use Eccube\Common\EccubeConfig;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * エージェントが送った購入者/配送先住所を、店頭のゲスト購入フォームと同じ基準で検証する.
 *
 * 店頭の購入フローはフォーム (NonMemberType 等) で入力を検証してから PurchaseFlow を通すが、
 * エージェント経路は中立 DTO から直接受注を組み立てる。PurchaseFlow は住所の必須・形式を検証しないため、
 * 本クラスを通さないと都道府県を解決できない住所 (送料が計算されず 0 円になる) や、DB の列長を
 * 超える値のまま受注を確定できてしまう。
 *
 * 検証の基準 (必須項目・最大長・数字のみ) は店頭のフォームに合わせる。フリガナは海外のエージェントが
 * 持たないため必須にしない。違反はビジネス系エラー (HTTP 200 + messages[]) として返し、
 * エージェントは update で住所を直して再試行できる。
 */
class AgentCheckoutAddressValidator
{
    public function __construct(
        private readonly ValidatorInterface $validator,
        private readonly EccubeConfig $eccubeConfig,
    ) {
    }

    /**
     * 郵便番号・電話番号の表記ゆれ (全角数字・ハイフン・空白) を店頭のフォームと同様に吸収する.
     */
    public function normalize(AgentCheckoutAddress $address): AgentCheckoutAddress
    {
        return new AgentCheckoutAddress(
            name01: $address->name01,
            name02: $address->name02,
            kana01: $address->kana01,
            kana02: $address->kana02,
            companyName: $address->companyName,
            postalCode: $this->digitsOnly($address->postalCode),
            prefId: $address->prefId,
            addr01: $address->addr01,
            addr02: $address->addr02,
            email: $address->email !== null ? trim($address->email) : null,
            phoneNumber: $this->digitsOnly($address->phoneNumber),
        );
    }

    /**
     * 住所を検証し、違反をエラーメッセージの配列で返す (違反が無ければ空配列).
     *
     * @return list<AgentCheckoutMessage>
     */
    public function validate(AgentCheckoutAddress $address): array
    {
        $nameLength = (int) $this->eccubeConfig['eccube_name_len'];
        $fields = [
            'family name' => [$address->name01, [new Assert\NotBlank(), new Assert\Length(max: $nameLength)]],
            'given name' => [$address->name02, [new Assert\NotBlank(), new Assert\Length(max: $nameLength)]],
            'family name (kana)' => [$address->kana01, [new Assert\Length(max: $nameLength)]],
            'given name (kana)' => [$address->kana02, [new Assert\Length(max: $nameLength)]],
            'company name' => [$address->companyName, [new Assert\Length(max: (int) $this->eccubeConfig['eccube_stext_len'])]],
            'postal code' => [$address->postalCode, [
                new Assert\NotBlank(),
                new Assert\Length(max: (int) $this->eccubeConfig['eccube_postal_code']),
                new Assert\Regex(pattern: '/\A\d+\z/'),
            ]],
            'city' => [$address->addr01, [new Assert\NotBlank(), new Assert\Length(max: (int) $this->eccubeConfig['eccube_address1_len'])]],
            'street address' => [$address->addr02, [new Assert\NotBlank(), new Assert\Length(max: (int) $this->eccubeConfig['eccube_address2_len'])]],
            'email' => [$address->email, [
                new Assert\NotBlank(),
                new Assert\Email(),
                new Assert\Length(max: (int) $this->eccubeConfig['eccube_email_len']),
            ]],
            'phone number' => [$address->phoneNumber, [
                new Assert\NotBlank(),
                new Assert\Length(max: (int) $this->eccubeConfig['eccube_tel_len_max']),
                new Assert\Regex(pattern: '/\A\d+\z/'),
            ]],
        ];

        $messages = [];
        if ($address->prefId === null) {
            // 都道府県が無い (または都道府県名として解決できない) と送料を計算できない。
            $messages[] = new AgentCheckoutMessage(
                AgentCheckoutMessageLevel::ERROR,
                'The shipping address must include a valid Japanese prefecture name (e.g. "東京都") as the region.',
            );
        }

        foreach ($fields as $label => [$value, $constraints]) {
            if ($this->hasViolation($value, $constraints)) {
                $messages[] = new AgentCheckoutMessage(
                    AgentCheckoutMessageLevel::ERROR,
                    sprintf('The %s of the buyer or shipping address is missing or invalid.', $label),
                );
            }
        }

        return $messages;
    }

    /**
     * @param list<Constraint> $constraints
     */
    private function hasViolation(?string $value, array $constraints): bool
    {
        return $this->validator->validate($value, $constraints)->count() > 0;
    }

    private function digitsOnly(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        // 全角数字を半角へ寄せ、ハイフン類と空白を取り除く (店頭の PostalType / PhoneNumberType と同等)。
        $converted = mb_convert_kana($value, 'na', 'UTF-8');
        $stripped = preg_replace('/[\s\-‐‑‒–—―−ー]/u', '', $converted);

        return $stripped === null || $stripped === '' ? null : $stripped;
    }
}
