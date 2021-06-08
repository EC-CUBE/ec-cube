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

use Eccube\Entity\Block;
use Eccube\Repository\BlockRepository;
use Symfony\Bridge\Twig\Extension\HttpKernelRuntime;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class EccubeDynamicBlockExtension extends AbstractExtension
{
    /** @var BlockRepository */
    protected $blockRepository;

    public function __construct(BlockRepository $blockRepository)
    {
        $this->blockRepository = $blockRepository;
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('eccube_dynamic_block', [$this, 'eccube_dynamic_block'], [
                'needs_environment' => true,
                'needs_context' => true,
                'pre_escape' => 'html',
                'is_safe' => [
                    'html',
                ],
            ]),
        ];
    }

    public function eccube_dynamic_block(Environment $env, $context, $fileName, $parameters = [])
    {
        $Block = $this->blockRepository->findOneBy([
            'file_name' => $fileName,
        ]);
        if (!($Block instanceof Block)) {
            @trigger_error($fileName . ' block is not found', E_USER_WARNING);
            return;
        }
        if ($Block->isUseController()) {
            $blockPath = sprintf('block_%s', $fileName);
            $runtime = $env->getRuntime(HttpKernelRuntime::class);
            assert($runtime instanceof HttpKernelRuntime);
            $path = $env->getFunction('path')->getCallable();
            return $runtime->renderFragment(
                $path($blockPath, $parameters)
            );
        } else {
            $template = sprintf('Block/%s.twig', $fileName);
            return ($env->getFunction('include_dispatch')->getCallable())($context, $template);
        }
    }
}
