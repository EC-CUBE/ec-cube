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

/**
 * @Annotation
 * @Target({"PROPERTY", "METHOD", "ANNOTATION"})
 */
#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class Email extends \Symfony\Component\Validator\Constraints\Email
{
    /**
     * EC-CUBE独自のバリデーションモード（緩い検証）.
     * Symfony 7.x で VALIDATION_MODE_LOOSE が削除されたため自前で定義.
     */
    public const VALIDATION_MODE_LOOSE = 'loose';

    public function __construct(
        ?array $options = null,
        ?string $message = null,
        ?string $mode = null,
        ?callable $normalizer = null,
        ?array $groups = null,
        mixed $payload = null,
    ) {
        // LOOSEモードの場合、親コンストラクタにはmodeをnullで渡し、後で設定する
        $isLoose = false;
        if ($mode === self::VALIDATION_MODE_LOOSE) {
            $isLoose = true;
            $mode = null;
        }
        if (is_array($options) && isset($options['mode']) && $options['mode'] === self::VALIDATION_MODE_LOOSE) {
            $isLoose = true;
            unset($options['mode']);
        }

        parent::__construct($options, $message, $mode, $normalizer, $groups, $payload);

        if ($isLoose) {
            $this->mode = self::VALIDATION_MODE_LOOSE;
        }
    }
}
