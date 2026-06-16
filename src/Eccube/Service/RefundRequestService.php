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
     * 返品申請を作成する.
     *
     * ステータスを「新規申請」に設定し、エビデンスファイルを保存して永続化したのち、
     * 管理者へ通知メールを送信する。
     *
     * @param UploadedFile[] $uploadedFiles アップロードされたエビデンスファイル
     */
    public function createRefundRequest(RefundRequest $RefundRequest, array $uploadedFiles = []): RefundRequest
    {
        $NewStatus = $this->refundRequestStatusRepository->find(RefundRequestStatus::NEW);
        $RefundRequest->setRefundRequestStatus($NewStatus);

        $sortNo = 1;
        foreach ($uploadedFiles as $uploadedFile) {
            $RefundRequest->addRefundRequestFile($this->saveFile($uploadedFile, $sortNo++));
        }

        $this->entityManager->persist($RefundRequest);
        $this->entityManager->flush();

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

    /**
     * エビデンスファイルを非公開ディレクトリ（var/配下）へ保存する.
     *
     * 保存名は推測困難なランダム名にする（実体は公開せず、配信はコントローラ経由に限定）。
     */
    private function saveFile(UploadedFile $uploadedFile, int $sortNo): RefundRequestFile
    {
        $dir = $this->eccubeConfig['eccube_save_refund_request_file_dir'];
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $mimeType = $uploadedFile->getMimeType();
        $fileSize = $uploadedFile->getSize();
        $extension = $uploadedFile->guessExtension() ?: $uploadedFile->getClientOriginalExtension();
        $fileName = bin2hex(random_bytes(16)).'.'.$extension;

        $uploadedFile->move($dir, $fileName);

        $RefundRequestFile = new RefundRequestFile();
        $RefundRequestFile->setFileName($fileName)
            ->setMimeType($mimeType)
            ->setFileSize($fileSize)
            ->setSortNo($sortNo);

        return $RefundRequestFile;
    }
}
