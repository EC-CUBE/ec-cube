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
use Eccube\Entity\Block;
use Eccube\Entity\Master\DeviceType;
use Eccube\Exception\ContentValidationException;
use Eccube\Form\Type\Admin\BlockType;
use Eccube\Repository\BlockRepository;
use Eccube\Repository\Master\DeviceTypeRepository;
use Eccube\Util\StringUtil;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Form\FormFactoryInterface;
use Twig\Environment;
use Twig\Error\LoaderError;

/**
 * ブロック (dtb_block と app/template/{theme}/Block 配下の twig) を対で扱う.
 *
 * 管理画面 (BlockController) と CLI (eccube:block:*) の双方から使用する.
 */
class BlockContentService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly BlockRepository $blockRepository,
        private readonly DeviceTypeRepository $deviceTypeRepository,
        private readonly FormFactoryInterface $formFactory,
        private readonly Environment $twig,
        private readonly Filesystem $filesystem,
        private readonly EccubeConfig $eccubeConfig,
    ) {
    }

    public function getTemplateDir(): string
    {
        return $this->eccubeConfig->get('eccube_theme_front_dir').'/Block';
    }

    public function getFilePath(Block $Block): string
    {
        return $this->getTemplateDir().'/'.$Block->getFileName().'.twig';
    }

    public function findByFileName(string $fileName, DeviceType $DeviceType): ?Block
    {
        return $this->blockRepository->findOneBy([
            'file_name' => $fileName,
            'DeviceType' => $DeviceType,
        ]);
    }

    public function getDeviceType(int $id = DeviceType::DEVICE_TYPE_PC): DeviceType
    {
        $DeviceType = $this->deviceTypeRepository->find($id);
        if (null === $DeviceType) {
            throw new \InvalidArgumentException(sprintf('DeviceType "%d" is not found.', $id));
        }

        return $DeviceType;
    }

    /**
     * テンプレートの本文を取得する.
     *
     * 書き込み先のファイルがあればそれを読み, 無い場合は twig のローダ経由で
     * コアのテンプレートへフォールバックする (PageContentService::readTemplate と同じ理由).
     */
    public function readTemplate(Block $Block): string
    {
        $filePath = $this->getFilePath($Block);
        if (is_file($filePath)) {
            return (string) file_get_contents($filePath);
        }

        try {
            return $this->twig->getLoader()
                ->getSourceContext('Block/'.$Block->getFileName().'.twig')
                ->getCode();
        } catch (LoaderError) {
            return '';
        }
    }

    /**
     * ファイル名を鍵にブロックを登録・更新する (upsert).
     *
     * @param array{file_name: string, name?: string, body?: string, device_type?: int} $payload
     *
     * @throws ContentValidationException  入力値が不正な場合
     * @throws \InvalidArgumentException   デバイス種別が存在しない場合
     */
    public function apply(array $payload, bool $dryRun = false): ContentResult
    {
        $fileName = $payload['file_name'];
        $DeviceType = $this->getDeviceType($payload['device_type'] ?? DeviceType::DEVICE_TYPE_PC);

        $Block = $this->findByFileName($fileName, $DeviceType);
        $isNew = null === $Block;
        if (null === $Block) {
            $Block = $this->blockRepository->newBlock($DeviceType);
        }

        // 新規登録時は比較対象が無い (未設定のゲッタは null を返すため呼び出さない)
        $before = $isNew ? [] : $this->snapshot($Block);
        $beforeBody = $isNew ? '' : StringUtil::convertLineFeed($this->readTemplate($Block));

        $data = [
            'file_name' => $fileName,
            'block_html' => $payload['body'] ?? $beforeBody,
            'DeviceType' => (string) $DeviceType->getId(),
        ];
        if (array_key_exists('name', $payload)) {
            $data['name'] = (string) $payload['name'];
        }
        if (!$isNew) {
            $data['id'] = (string) $Block->getId();
        }

        $form = $this->formFactory->create(BlockType::class, $Block, ['csrf_protection' => false]);
        $form->submit($data, false);

        if (!$form->isValid()) {
            throw ContentValidationException::fromForm($form);
        }

        $body = StringUtil::convertLineFeed((string) $form->get('block_html')->getData());
        $fieldChanges = self::diffFields($before, $this->snapshot($Block));
        $fileChanges = $beforeBody === $body ? [] : [$this->getFilePath($Block) => [$beforeBody, $body]];

        if ($dryRun) {
            if (!$isNew) {
                $this->entityManager->refresh($Block);
            }

            return new ContentResult(
                $isNew ? ContentStatus::Created : ([] === $fieldChanges && [] === $fileChanges ? ContentStatus::Unchanged : ContentStatus::Updated),
                $Block->getId(),
                $fileName,
                [],
                [],
                $fieldChanges,
                $fileChanges
            );
        }

        if (!$isNew && [] === $fieldChanges && [] === $fileChanges) {
            return new ContentResult(ContentStatus::Unchanged, $Block->getId(), $fileName);
        }

        return $this->save($Block, $body, null)->withChanges($fieldChanges, $fileChanges);
    }

    /**
     * ブロックを永続化し, テンプレートファイルを書き出す.
     */
    public function save(Block $Block, string $body, ?string $previousFileName): ContentResult
    {
        $isNew = null === $Block->getId();

        $this->entityManager->persist($Block);
        $this->entityManager->flush();

        $dir = $this->getTemplateDir();
        $filePath = $dir.'/'.$Block->getFileName().'.twig';
        $this->filesystem->dumpFile($filePath, StringUtil::convertLineFeed($body));

        $removedPaths = [];
        // 更新でファイル名を変更した場合, 以前のファイルを削除する
        if (null !== $previousFileName && $Block->getFileName() !== $previousFileName) {
            $oldFilePath = $dir.'/'.$previousFileName.'.twig';
            if ($this->filesystem->exists($oldFilePath)) {
                $this->filesystem->remove($oldFilePath);
                $removedPaths[] = $oldFilePath;
            }
        }

        return new ContentResult(
            $isNew ? ContentStatus::Created : ContentStatus::Updated,
            $Block->getId(),
            $Block->getFileName(),
            [$filePath],
            $removedPaths
        );
    }

    /**
     * ブロックとテンプレートファイルを削除する.
     *
     * ユーザーが作成したブロック (deletable) のみ削除できる.
     */
    public function remove(Block $Block): ContentResult
    {
        if (!$Block->isDeletable()) {
            throw new \LogicException(sprintf('Block "%s" is not removable.', $Block->getFileName()));
        }

        $id = $Block->getId();
        $fileName = $Block->getFileName();
        $filePath = $this->getFilePath($Block);

        $removedPaths = [];
        if ($this->filesystem->exists($filePath)) {
            $this->filesystem->remove($filePath);
            $removedPaths[] = $filePath;
        }

        $this->entityManager->remove($Block);
        $this->entityManager->flush();

        return new ContentResult(ContentStatus::Removed, $id, $fileName, [], $removedPaths);
    }

    /**
     * @return array<string, string>
     */
    private function snapshot(Block $Block): array
    {
        return [
            'name' => $Block->getName(),
            'file_name' => $Block->getFileName(),
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
