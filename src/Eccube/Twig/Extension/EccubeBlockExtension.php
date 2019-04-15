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

namespace Eccube\Twig\Extension;

use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class EccubeBlockExtension extends AbstractExtension
{
    protected $twig;

    protected $blockTemplates;

    public function __construct(Environment $twig, array $blockTemplates)
    {
        $this->twig = $twig;
        $this->blockTemplates = $blockTemplates;
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('eccube_block_*', function ($context, $name, array $parameters = []) {
                if (!empty($parameters)) {
                    $context = array_merge($context, $parameters);
                }
                $files = $this->blockTemplates;
                foreach ($files as $file) {
                    $template = $this->twig->loadTemplate($file);
                    if ($template->hasBlock($name, $context)) {
                        return $template->renderBlock($name, $context);
                    }
                }
                @trigger_error($name.' block is not found', E_USER_WARNING);
            }, ['needs_context' => true, 'pre_escape' => 'html', 'is_safe' => ['html']]),
        ];
    }
}
