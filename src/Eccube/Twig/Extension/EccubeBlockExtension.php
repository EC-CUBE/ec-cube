<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
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
            new TwigFunction('eccube_block_*', function () {
                $sources = $this->blockTemplates;
                $arg_list = func_get_args();
                $block_name = array_shift($arg_list);
                foreach ($sources as $source) {
                    $template = $this->twig->loadTemplate($source);
                    if (!isset($arg_list[0])) {
                        $arg_list[0] = [];
                    }
                    if ($template->hasBlock($block_name, $arg_list[0])) {
                        echo $result = $template->renderBlock($block_name, $arg_list[0]);

                        return;
                    }
                }
                trigger_error($block_name.' block is not found', E_USER_WARNING);
            }, ['pre_escape' => 'html', 'is_safe' => ['html']]),
        ];
    }
}
