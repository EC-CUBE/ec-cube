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

namespace Eccube\Form\Validator;

use Eccube\Common\EccubeConfig;
use Eccube\Util\PasswordNormalizer;
use Psr\Log\LoggerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/**
 * PasswordBlocklist 制約のバリデータ.
 *
 * 入力値を NFKC 正規化し, 大文字小文字を無視してブロックリストと照合する.
 * 一致した場合は違反を登録する.
 */
class PasswordBlocklistValidator extends ConstraintValidator
{
    /**
     * @var array<string>|null 正規化済みブロックリスト(初回読込時にキャッシュ)
     */
    private ?array $blocklist = null;

    public function __construct(private readonly EccubeConfig $eccubeConfig, private readonly LoggerInterface $logger)
    {
    }

    /**
     * {@inheritdoc}
     *
     * @param mixed $value
     */
    #[\Override]
    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof PasswordBlocklist) {
            throw new UnexpectedTypeException($constraint, PasswordBlocklist::class);
        }

        if (null === $value || '' === $value) {
            return;
        }

        if (!is_scalar($value) && !(\is_object($value) && method_exists($value, '__toString'))) {
            throw new UnexpectedTypeException($value, 'string');
        }

        $normalized = $this->normalizeForComparison((string) $value);

        if (in_array($normalized, $this->getBlocklist(), true)) {
            $this->context->buildViolation($constraint->message)
                ->addViolation();
        }
    }

    /**
     * 比較用に NFKC 正規化し小文字化する.
     */
    private function normalizeForComparison(string $value): string
    {
        return mb_strtolower((string) PasswordNormalizer::normalize($value));
    }

    /**
     * ブロックリストを読み込む(初回のみファイルアクセスし以降はキャッシュ).
     *
     * @return array<string>
     */
    private function getBlocklist(): array
    {
        if ($this->blocklist !== null) {
            return $this->blocklist;
        }

        $this->blocklist = [];

        $file = $this->eccubeConfig['eccube_password_blocklist'] ?? null;
        if (!$file || !is_file($file) || !is_readable($file)) {
            // 設定ミス等でブロックリストが読めない場合, 無言で無効化(fail-open)せず警告を残す.
            // 漏洩チェック(NotCompromisedPassword)が主防御のため登録自体は継続する.
            $this->logger->warning('パスワードブロックリストを読み込めませんでした。', ['file' => $file]);

            return $this->blocklist;
        }

        $lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        if ($lines === false) {
            $this->logger->warning('パスワードブロックリストの読み込みに失敗しました。', ['file' => $file]);

            return $this->blocklist;
        }

        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '' || str_starts_with($line, '#')) {
                continue;
            }
            $this->blocklist[] = $this->normalizeForComparison($line);
        }

        return $this->blocklist;
    }
}
