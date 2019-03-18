#!/usr/bin/env php
<?php

/**
 * 日本語テンプレート生成スクリプト
 *
 * メッセージIDを通常の日本語に置換します.
 * {{ 'message_id'|trans }}は{{ 'メッセージID'|trans }}のように置換されます.
 *
 * symfonyのライブラリを使用しているため, composer install後に実行してください.
 *
 * composer install
 * bin/template_jp.php
 *
 */
require_once __DIR__.'/../vendor/autoload.php';

use Symfony\Component\Finder\Finder;
use Symfony\Component\Yaml\Yaml;

define('PROJECT_DIR', realpath(__DIR__.'/../'));
define('LOCALE_DIR', realpath(PROJECT_DIR.'/src/Eccube/Resource/locale'));
define('TEMPLATE_DIR', realpath(PROJECT_DIR.'/src/Eccube/Resource/template'));

$messagesFile = LOCALE_DIR.'/messages.ja.yaml';
$messages = Yaml::parse(file_get_contents($messagesFile));

$directories = [
    TEMPLATE_DIR.'/default',
    TEMPLATE_DIR.'/install',
    TEMPLATE_DIR.'/toolbar',
];

$files = Finder::create()
    ->in($directories)
    ->name('*.twig')
    ->files();

$templates = [];
/** @var SplFileInfo $file */
foreach ($files as $file) {
    $template = new \stdClass();
    $template->realpath = $file->getRealPath();
    $template->content = file_get_contents($file->getRealPath());
    $templates[] = $template;
}

foreach ($templates as $template) {
    $replaced = $template->content;
    foreach ($messages as $messageId => $messageText) {
        $pattern = "/('|\")".$messageId."('|\")/";   // 'message_id' or "message_id"を置換
        $replacement = "'".$messageText."'";
        $result = \preg_replace($pattern, $replacement, $replaced);
        if (null === $result) {
            echoLn('preg_replace is failed.');
            echoLn('  file: '.$template->realpath);
            echoLn('  message id: '.$messageId);
            echoLn('  message text: '.$messageText);

            eixt(1);
        }
        $replaced = $result;
    }

    if ($replaced === $template->content) {
        // ヒットなし
        echoLn('SKIP: '.$template->realpath);
        continue;
    }

    // 上書き
    echoLn('FIX: '.$template->realpath);
    file_put_contents($template->realpath, $replaced);
}

function echoLn($message)
{
    echo $message.PHP_EOL;
    @ob_flush();
}

