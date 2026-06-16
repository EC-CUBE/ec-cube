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

namespace Eccube\Service;

use Doctrine\ORM\EntityManagerInterface;
use Eccube\Common\EccubeConfig;
use Eccube\Entity\Master\RefundRequestStatus;
use Eccube\Entity\RefundRequest;
use Eccube\Entity\RefundRequestFile;
use Eccube\Event\EccubeEvents;
use Eccube\Event\RefundRequestEvent;
use Eccube\Repository\Master\RefundRequestStatusRepository;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * 返品申請の業務ロジック.
 *
 * 申請の作成（エビデンスファイル保存を含む）・ステータス遷移を担う。
 * 受注処理（在庫・採番・ポイント）には関与しない独立サービス。
 * メール送信は MailService に委譲する。
 *
 * エビデンスファイルは「確認画面遷移中の一時保存」と「申請確定時の本保存」を分けて扱う。
 * <input type="file"> は HTML 仕様上 value を再描画できず確認画面を跨いで再 POST できないため、
 * 入力 → 確認の段階で一時領域（var/refund_request/tmp/{sessionId}/）に move し、
 * セッションに参照キー（ファイル名と元 MIME/サイズ）を保持して complete 時に本領域へ rename する。
 */
class RefundRequestService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly RefundRequestStatusRepository $refundRequestStatusRepository,
        private readonly RefundRequestStateMachine $refundRequestStateMachine,
        private readonly MailService $mailService,
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly EccubeConfig $eccubeConfig,
    ) {
    }

    /**
     * アップロードされたエビデンスファイルを一時領域に保存する.
     *
     * 戻り値はセッションで保持する参照情報。
     *
     * @return array{key: string, client_name: string, mime_type: string, size: int}
     */
    public function saveTempFile(UploadedFile $uploadedFile, string $sessionId): array
    {
        $dir = $this->getTempDir($sessionId);
        if (!is_dir($dir) && !@mkdir($dir, 0755, true) && !is_dir($dir)) {
            throw new \RuntimeException(sprintf('Failed to create temp dir: %s', $dir));
        }

        $extension = $uploadedFile->guessExtension() ?: $uploadedFile->getClientOriginalExtension();
        $key = bin2hex(random_bytes(16)).'.'.$extension;
        $clientName = (string) $uploadedFile->getClientOriginalName();
        $mimeType = (string) $uploadedFile->getMimeType();
        $size = (int) $uploadedFile->getSize();

        $uploadedFile->move($dir, $key);

        return [
            'key' => $key,
            'client_name' => $clientName,
            'mime_type' => $mimeType,
            'size' => $size,
        ];
    }

    /**
     * 一時領域のファイルの実パスを返す（所有セッションのもののみ）.
     */
    public function getTempFilePath(string $sessionId, string $key): ?string
    {
        $dir = $this->getTempDir($sessionId);
        $path = $dir.'/'.$key;
        $real = realpath($path);
        $realDir = realpath($dir);
        if ($real === false || $realDir === false || !str_starts_with($real, $realDir.DIRECTORY_SEPARATOR)) {
            return null;
        }

        return $real;
    }

    /**
     * 一時領域のファイルを削除する.
     */
    public function removeTempFile(string $sessionId, string $key): void
    {
        $real = $this->getTempFilePath($sessionId, $key);
        if ($real !== null) {
            @unlink($real);
        }
    }

    /**
     * 返品申請を確定する.
     *
     * セッションに保持していた一時ファイルを本領域へ移動して RefundRequest にひも付け、
     * ステータスを「新規申請」に設定して永続化したのち、管理者へ通知メールを送信する。
     *
     * @param list<array{key: string, client_name?: string, mime_type: string, size: int}> $tempFiles
     */
    public function createRefundRequest(RefundRequest $RefundRequest, array $tempFiles, string $sessionId): RefundRequest
    {
        $NewStatus = $this->refundRequestStatusRepository->find(RefundRequestStatus::NEW);
        $RefundRequest->setRefundRequestStatus($NewStatus);

        $finalDir = $this->eccubeConfig['eccube_save_refund_request_file_dir'];
        if (!is_dir($finalDir) && !@mkdir($finalDir, 0755, true) && !is_dir($finalDir)) {
            throw new \RuntimeException(sprintf('Failed to create save dir: %s', $finalDir));
        }

        $sortNo = 1;
        foreach ($tempFiles as $info) {
            $tempPath = $this->getTempFilePath($sessionId, $info['key']);
            if ($tempPath === null) {
                continue;
            }
            $finalPath = $finalDir.'/'.$info['key'];
            if (!@rename($tempPath, $finalPath)) {
                throw new \RuntimeException(sprintf('Failed to move temp file: %s', $info['key']));
            }
            $File = new RefundRequestFile();
            $File->setFileName($info['key'])
                ->setMimeType($info['mime_type'])
                ->setFileSize($info['size'])
                ->setSortNo($sortNo++);
            $RefundRequest->addRefundRequestFile($File);
        }

        $this->entityManager->persist($RefundRequest);
        $this->entityManager->flush();

        $this->cleanupTempDir($sessionId);

        $this->mailService->sendRefundRequestNotifyMail($RefundRequest);

        return $RefundRequest;
    }

    /**
     * ステータスを遷移させる.
     *
     * @throws \InvalidArgumentException 不正な遷移の場合
     */
    public function changeStatus(RefundRequest $RefundRequest, string $transition): void
    {
        $PreviousStatus = $RefundRequest->getRefundRequestStatus();

        $this->refundRequestStateMachine->applyTransition($RefundRequest, $transition);
        $this->entityManager->flush();

        $event = new RefundRequestEvent($RefundRequest, $PreviousStatus, $RefundRequest->getRefundRequestStatus());
        $this->eventDispatcher->dispatch($event, EccubeEvents::REFUND_REQUEST_STATUS_CHANGE);
    }

    /**
     * 指定した遷移が実行可能かどうかを判定する.
     */
    public function canApplyTransition(RefundRequest $RefundRequest, string $transition): bool
    {
        return $this->refundRequestStateMachine->can($RefundRequest, $transition);
    }

    /**
     * 現在のステータスから実行可能な遷移を取得する.
     *
     * @return array<string, RefundRequestStatus> [遷移名 => 遷移先ステータス]
     */
    public function getAvailableTransitions(RefundRequest $RefundRequest): array
    {
        return $this->refundRequestStateMachine->getAvailableTransitions($RefundRequest);
    }

    private function getTempDir(string $sessionId): string
    {
        $safeId = preg_replace('/[^A-Za-z0-9_-]/', '', $sessionId) ?: 'anon';

        return rtrim((string) $this->eccubeConfig['eccube_temp_refund_request_file_dir'], '/').'/'.$safeId;
    }

    private function cleanupTempDir(string $sessionId): void
    {
        $dir = $this->getTempDir($sessionId);
        if (!is_dir($dir)) {
            return;
        }
        foreach (glob($dir.'/*') ?: [] as $file) {
            @unlink($file);
        }
        @rmdir($dir);
    }
}
