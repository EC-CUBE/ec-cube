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

namespace Eccube\Annotation;

use Doctrine\Common\Annotations\Annotation\Target;

/**
 * @Annotation
 * @Target("PROPERTY")
 */
#[\Attribute(\Attribute::TARGET_PROPERTY)]
final class FormAppend
{
    /**
     * @var bool
     */
    public $auto_render;

    /**
     * @var string
     */
    public $form_theme;

    /**
     * @var string
     */
    public $type;

    /**
     * @var array
     */
    public $options;

    /**
     * @var string
     */
    public $style_class;

    public function __construct(
        $auto_render = null,
        ?string $form_theme = null,
        ?string $type = null,
        ?array $options = null,
        ?string $style_class = null,
    ) {
        // Support both annotation array and attribute named arguments
        if (is_array($auto_render)) {
            // Called from annotation reader with array of values
            $values = $auto_render;
            $this->auto_render = $values['auto_render'] ?? null;
            $this->form_theme = $values['form_theme'] ?? null;
            $this->type = $values['type'] ?? null;
            $this->options = $values['options'] ?? null;
            $this->style_class = $values['style_class'] ?? null;
        } else {
            // Called from PHP attribute with named arguments
            $this->auto_render = $auto_render;
            $this->form_theme = $form_theme;
            $this->type = $type;
            $this->options = $options;
            $this->style_class = $style_class;
        }
    }
}
