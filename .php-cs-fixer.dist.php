<?php

if (php_sapi_name() !== 'cli') {
    throw new \LogicException();
}

$header = <<<EOL
This file is part of EC-CUBE

Copyright(c) EC-CUBE CO.,LTD. All Rights Reserved.

http://www.ec-cube.co.jp/

For the full copyright and license information, please view the LICENSE
file that was distributed with this source code.
EOL;

$rules = [
    '@Symfony' => true,
    'array_syntax' => ['syntax' => 'short'],
    'phpdoc_align' => false,
    'phpdoc_summary' => false,
    'phpdoc_annotation_without_dot' => false,
    'no_superfluous_phpdoc_tags' => false,
    'increment_style' => false,
    'yoda_style' => false,
    'header_comment' => ['header' => $header],
    'phpdoc_add_missing_param_annotation' => true,
    'phpdoc_param_order' => true,
    'phpdoc_to_comment' => false, // /** @var */ を変換してしまうため
    'global_namespace_import' => [
        'import_classes' => false,
        'import_constants' => false,
        'import_functions' => false,
    ],
];

$finder = \PhpCsFixer\Finder::create()
    ->in(__DIR__.'/src')
    ->in(__DIR__.'/tests')
    ->in(__DIR__.'/app')
    ->in(__DIR__.'/codeception')
    ->name('*.php')
;
$config = new \PhpCsFixer\Config();
return $config
    ->setRules($rules)
    ->setFinder($finder)
    ;
