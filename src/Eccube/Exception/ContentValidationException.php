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

namespace Eccube\Exception;

use Symfony\Component\Form\FormInterface;

/**
 * コンテンツ操作の入力値が不正なときに送出する.
 *
 * 検証は管理画面と同じ FormType を submit して行うため, エラーの内容も管理画面と一致する.
 */
class ContentValidationException extends \RuntimeException
{
    /**
     * @param list<string> $errors フィールド名付きのエラーメッセージ
     */
    public function __construct(private readonly array $errors, string $message = 'Validation failed.')
    {
        parent::__construct($message);
    }

    /**
     * @return list<string>
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * @param FormInterface<mixed> $form
     */
    public static function fromForm(FormInterface $form): self
    {
        $errors = [];
        foreach ($form->getErrors(true) as $error) {
            $origin = $error->getOrigin();
            $name = $origin instanceof FormInterface ? $origin->getName() : '';
            $errors[] = '' === $name || $form->getName() === $name
                ? $error->getMessage()
                : sprintf('%s: %s', $name, $error->getMessage());
        }

        return new self($errors);
    }
}
