<?php

declare(strict_types=1);

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

namespace Eccube\Service\AgentCommerce\Security;

/**
 * 登録されている鍵の用途 (KeyPurposeInterface) を purpose で引けるようにする.
 *
 * タグ付きサービスを集めるため, プラグインや app/Customize が KeyPurposeInterface を
 * 実装すると eccube:keystore:* の対象へ自動的に加わる.
 */
final class KeyPurposeRegistry
{
    /**
     * @var array<string, KeyPurposeInterface>|null
     */
    private ?array $indexed = null;

    /**
     * @param iterable<KeyPurposeInterface> $purposes
     */
    public function __construct(private readonly iterable $purposes)
    {
    }

    /**
     * 登録されている用途を purpose => 定義 で返す. purpose の昇順.
     *
     * @return array<string, KeyPurposeInterface>
     */
    public function all(): array
    {
        if ($this->indexed !== null) {
            return $this->indexed;
        }

        $indexed = [];
        foreach ($this->purposes as $purpose) {
            $key = $purpose->getPurpose();

            // 同じ purpose を 2 つの実装が宣言すると, どちらが使われるかは登録順で決まってしまう.
            // 署名鍵を別の実装に差し替えられることになるため, 起動時に落とす.
            if (isset($indexed[$key])) {
                throw new \LogicException(sprintf('鍵の用途 "%s" が %s と %s の双方で宣言されています. purpose は一意にしてください.', $key, $indexed[$key]::class, $purpose::class));
            }

            $indexed[$key] = $purpose;
        }

        ksort($indexed);

        return $this->indexed = $indexed;
    }

    public function has(string $purpose): bool
    {
        return isset($this->all()[$purpose]);
    }

    /**
     * @throws \InvalidArgumentException 未登録の purpose を指定した場合
     */
    public function get(string $purpose): KeyPurposeInterface
    {
        $all = $this->all();

        if (!isset($all[$purpose])) {
            throw new \InvalidArgumentException(sprintf('鍵の用途 "%s" は登録されていません. 指定できるのは %s です.', $purpose, $all === [] ? '(なし)' : implode(' / ', array_keys($all))));
        }

        return $all[$purpose];
    }
}
