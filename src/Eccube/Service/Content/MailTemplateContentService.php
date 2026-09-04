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

namespace Eccube\Service\Content;

use Doctrine\ORM\EntityManagerInterface;
use Eccube\Common\EccubeConfig;
use Eccube\Entity\MailTemplate;
use Eccube\Exception\ContentValidationException;
use Eccube\Form\Type\Admin\MailType;
use Eccube\Repository\MailTemplateRepository;
use Eccube\Util\StringUtil;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Form\FormFactoryInterface;
use Twig\Environment;
use Twig\Error\LoaderError;

/**
 * メールテンプレート (dtb_mail_template と app/template/{theme}/Mail 配下の twig) を対で扱う.
 *
 * 管理画面 (MailController) と CLI (eccube:mail-template:*) の双方から使用する.
 * dtb_mail_template.file_name は "Mail/xxx.twig" 形式で保持する.
 */
class MailTemplateContentService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly MailTemplateRepository $mailTemplateRepository,
        private readonly FormFactoryInterface $formFactory,
        private readonly Environment $twig,
        private readonly Filesystem $filesystem,
        private readonly EccubeConfig $eccubeConfig,
    ) {
    }

    public function getTemplateDir(): string
    {
        return (string) $this->eccubeConfig->get('eccube_theme_front_dir');
    }

    public function getFilePath(MailTemplate $Mail): string
    {
        return $this->getTemplateDir().'/'.$Mail->getFileName();
    }

    public function getHtmlFilePath(MailTemplate $Mail): string
    {
        return $this->getTemplateDir().'/'.$this->getHtmlFileName((string) $Mail->getFileName());
    }

    /**
     * HTML 用テンプレート名を取得する.
     */
    public function getHtmlFileName(string $fileName): string
    {
        $targetTemplate = pathinfo($fileName);
        $suffix = '.html';

        return $targetTemplate['dirname'].DIRECTORY_SEPARATOR.$targetTemplate['filename'].$suffix.'.'.$targetTemplate['extension'];
    }

    /**
     * テンプレートディレクトリ配下のパスかどうかを検証する.
     */
    public function isInsideTemplateDir(string $path): bool
    {
        $templatePath = realpath($this->getTemplateDir());
        $path = realpath($path);

        return false !== $path && false !== $templatePath && str_starts_with($path, $templatePath);
    }

    /**
     * "xxx" / "Mail/xxx.twig" のいずれの表記でもテンプレートを取得する.
     */
    public function findByFileName(string $fileName): ?MailTemplate
    {
        return $this->mailTemplateRepository->findOneBy(['file_name' => $this->normalizeFileName($fileName)]);
    }

    public function normalizeFileName(string $fileName): string
    {
        return str_contains($fileName, '/') ? $fileName : 'Mail/'.$fileName.'.twig';
    }

    public function readTemplate(MailTemplate $Mail): string
    {
        return (string) $this->read($this->getFilePath($Mail), (string) $Mail->getFileName());
    }

    /**
     * HTML パートの本文を取得する. HTML パートが無い場合は null を返す.
     */
    public function readHtmlTemplate(MailTemplate $Mail): ?string
    {
        return $this->read($this->getHtmlFilePath($Mail), $this->getHtmlFileName((string) $Mail->getFileName()));
    }

    /**
     * ファイル名または ID を鍵にメールテンプレートを登録・更新する (upsert).
     *
     * @param array{file_name?: string, id?: int, name?: string, subject?: string, body?: string, html_body?: string, remove_html?: bool} $payload
     *
     * @throws ContentValidationException
     */
    public function apply(array $payload, bool $dryRun = false): ContentResult
    {
        $Mail = $this->resolveTarget($payload);
        $isNew = null === $Mail;
        if (null === $Mail) {
            $Mail = new MailTemplate();
            // 管理画面から登録したテンプレートと同様に削除可能にする
            $Mail->setDeletable(true);
        }

        // 新規登録時は比較対象が無い (未設定のゲッタは null を返すため呼び出さない)
        $before = $isNew ? [] : $this->snapshot($Mail);
        $beforeBody = $isNew ? '' : StringUtil::convertLineFeed($this->readTemplate($Mail));
        $beforeHtmlBody = $isNew ? null : $this->readHtmlTemplate($Mail);

        $removeHtml = (bool) ($payload['remove_html'] ?? false);
        $htmlBody = $removeHtml ? null : ($payload['html_body'] ?? $beforeHtmlBody);

        $data = [
            'tpl_data' => $payload['body'] ?? $beforeBody,
            // 空の HTML 本文は「HTML パートなし」として扱う (管理画面と同じ)
            'html_tpl_data' => $htmlBody,
        ];
        foreach (['name' => 'name', 'subject' => 'mail_subject'] as $key => $field) {
            if (array_key_exists($key, $payload)) {
                $data[$field] = (string) $payload[$key];
            }
        }
        if ($isNew && isset($payload['file_name'])) {
            // 新規登録時のみファイル名を指定できる (MailType が Mail/xxx.twig へ変換する)
            $data['file_name'] = $this->toBaseName($payload['file_name']);
        }

        $form = $this->formFactory->create(MailType::class, $Mail, ['csrf_protection' => false]);
        $form->submit($data, false);

        if (!$form->isValid()) {
            throw ContentValidationException::fromForm($form);
        }

        $body = StringUtil::convertLineFeed((string) $form->get('tpl_data')->getData());
        $newHtmlBody = $form->get('html_tpl_data')->getData();
        $newHtmlBody = null === $newHtmlBody ? null : StringUtil::convertLineFeed((string) $newHtmlBody);

        $fieldChanges = self::diffFields($before, $this->snapshot($Mail));
        $fileChanges = [];
        if ($beforeBody !== $body) {
            $fileChanges[$this->getFilePath($Mail)] = [$beforeBody, $body];
        }
        if ($beforeHtmlBody !== $newHtmlBody) {
            $fileChanges[$this->getHtmlFilePath($Mail)] = [(string) $beforeHtmlBody, (string) $newHtmlBody];
        }

        if ($dryRun) {
            if (!$isNew) {
                $this->entityManager->refresh($Mail);
            }

            return new ContentResult(
                $isNew ? ContentStatus::Created : ([] === $fieldChanges && [] === $fileChanges ? ContentStatus::Unchanged : ContentStatus::Updated),
                $Mail->getId(),
                (string) $Mail->getFileName(),
                [],
                [],
                $fieldChanges,
                $fileChanges
            );
        }

        if (!$isNew && [] === $fieldChanges && [] === $fileChanges) {
            return new ContentResult(ContentStatus::Unchanged, $Mail->getId(), (string) $Mail->getFileName());
        }

        return $this->save($Mail, $body, $newHtmlBody)->withChanges($fieldChanges, $fileChanges);
    }

    /**
     * メールテンプレートを永続化し, テンプレートファイルを書き出す.
     *
     * $htmlBody に null を渡すと HTML パートのファイルを削除する (管理画面と同じ挙動).
     */
    public function save(MailTemplate $Mail, string $body, ?string $htmlBody): ContentResult
    {
        $isNew = null === $Mail->getId();

        $this->entityManager->persist($Mail);
        $this->entityManager->flush();

        $filePath = $this->getFilePath($Mail);
        $this->filesystem->dumpFile($filePath, StringUtil::convertLineFeed($body));

        $writtenPaths = [$filePath];
        $removedPaths = [];
        $htmlFilePath = $this->getHtmlFilePath($Mail);

        if (null !== $htmlBody) {
            $this->filesystem->dumpFile($htmlFilePath, StringUtil::convertLineFeed($htmlBody));
            $writtenPaths[] = $htmlFilePath;
        } elseif ($this->isInsideTemplateDir($htmlFilePath) && is_file($htmlFilePath)) {
            $this->filesystem->remove($htmlFilePath);
            $removedPaths[] = $htmlFilePath;
        }

        return new ContentResult(
            $isNew ? ContentStatus::Created : ContentStatus::Updated,
            $Mail->getId(),
            (string) $Mail->getFileName(),
            $writtenPaths,
            $removedPaths
        );
    }

    /**
     * メールテンプレートとテンプレートファイルを削除する.
     */
    public function remove(MailTemplate $Mail): ContentResult
    {
        if (!$Mail->isDeletable()) {
            throw new \LogicException(sprintf('MailTemplate "%s" is not removable.', (string) $Mail->getFileName()));
        }

        $id = $Mail->getId();
        $fileName = (string) $Mail->getFileName();
        $filePath = $this->getFilePath($Mail);
        $htmlFilePath = $this->getHtmlFilePath($Mail);

        $this->entityManager->remove($Mail);
        $this->entityManager->flush();

        $removedPaths = [];
        foreach ([$filePath, $htmlFilePath] as $path) {
            if ($this->isInsideTemplateDir($path) && is_file($path)) {
                $this->filesystem->remove($path);
                $removedPaths[] = $path;
            }
        }

        return new ContentResult(ContentStatus::Removed, $id, $fileName, [], $removedPaths);
    }

    /**
     * @param array{file_name?: string, id?: int} $payload
     */
    private function resolveTarget(array $payload): ?MailTemplate
    {
        if (isset($payload['id'])) {
            return $this->mailTemplateRepository->find($payload['id']);
        }

        return isset($payload['file_name']) ? $this->findByFileName($payload['file_name']) : null;
    }

    /**
     * 書き込み先のファイルがあればそれを読み, 無い場合は twig のローダ経由で
     * コアのテンプレートへフォールバックする.
     *
     * twig のローダはテンプレートの探索結果をプロセス内でキャッシュするため,
     * 直前に書き出したファイルを読み落とさないようファイルを優先する.
     */
    private function read(string $filePath, string $twigName): ?string
    {
        if (is_file($filePath)) {
            return (string) file_get_contents($filePath);
        }

        try {
            return $this->twig->getLoader()->getSourceContext($twigName)->getCode();
        } catch (LoaderError) {
            return null;
        }
    }

    /**
     * "Mail/xxx.twig" 形式で渡された場合に基底名 (xxx) を返す.
     */
    private function toBaseName(string $fileName): string
    {
        return str_contains($fileName, '/') ? pathinfo($fileName, PATHINFO_FILENAME) : $fileName;
    }

    /**
     * @return array<string, string>
     */
    private function snapshot(MailTemplate $Mail): array
    {
        return [
            'name' => (string) $Mail->getName(),
            'file_name' => (string) $Mail->getFileName(),
            'mail_subject' => (string) $Mail->getMailSubject(),
        ];
    }

    /**
     * @param array<string, string> $before
     * @param array<string, string> $after
     *
     * @return array<string, array{0: string, 1: string}>
     */
    private static function diffFields(array $before, array $after): array
    {
        $changes = [];
        foreach ($after as $field => $value) {
            $previous = $before[$field] ?? '';
            if ($previous !== $value) {
                $changes[$field] = [$previous, $value];
            }
        }

        return $changes;
    }
}
