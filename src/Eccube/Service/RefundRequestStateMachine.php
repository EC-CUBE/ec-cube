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

use Eccube\Entity\Master\RefundRequestStatus;
use Eccube\Entity\RefundRequest;
use Eccube\Repository\Master\RefundRequestStatusRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Workflow\Event\Event;
use Symfony\Component\Workflow\WorkflowInterface;

/**
 * 返品申請ステータスのステートマシン.
 *
 * Symfony Workflow（refund_request）をラップし、遷移可否判定・遷移実行・
 * 遷移可能な選択肢の取得を提供する。受注処理（在庫・採番・ポイント）には関与しない。
 */
class RefundRequestStateMachine implements EventSubscriberInterface
{
    public function __construct(
        private readonly WorkflowInterface $_refundRequestStateMachine,
        private readonly RefundRequestStatusRepository $refundRequestStatusRepository,
    ) {
    }

    /**
     * 指定した遷移を実行する.
     *
     * @throws \InvalidArgumentException 遷移できない場合
     */
    public function applyTransition(RefundRequest $RefundRequest, string $transition): void
    {
        $context = $this->newContext($RefundRequest);
        if (!$this->_refundRequestStateMachine->can($context, $transition)) {
            throw new \InvalidArgumentException(sprintf('Cannot apply transition "%s".', $transition));
        }
        $this->_refundRequestStateMachine->apply($context, $transition);
    }

    /**
     * 指定した遷移を実行できるかどうかを判定する.
     */
    public function can(RefundRequest $RefundRequest, string $transition): bool
    {
        if (!$RefundRequest->getRefundRequestStatus()) {
            return false;
        }

        return $this->_refundRequestStateMachine->can($this->newContext($RefundRequest), $transition);
    }

    /**
     * 現在のステータスから実行可能な遷移を取得する.
     *
     * @return array<string, RefundRequestStatus> [遷移名 => 遷移先ステータス]
     */
    public function getAvailableTransitions(RefundRequest $RefundRequest): array
    {
        if (!$RefundRequest->getRefundRequestStatus()) {
            return [];
        }

        $result = [];
        foreach ($this->_refundRequestStateMachine->getEnabledTransitions($this->newContext($RefundRequest)) as $transition) {
            $toId = (int) $transition->getTos()[0];
            $Status = $this->refundRequestStatusRepository->find($toId);
            if ($Status instanceof RefundRequestStatus) {
                $result[$transition->getName()] = $Status;
            }
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    #[\Override]
    public static function getSubscribedEvents(): array
    {
        return [
            'workflow.refund_request.completed' => ['onCompleted'],
        ];
    }

    /**
     * 申請ステータスを再設定する.
     * StateMachine による遷移完了時には marking（id）が変更されるだけなので、
     * RefundRequestStatus エンティティを設定し直す.
     */
    public function onCompleted(Event $event): void
    {
        /** @var RefundRequestStateMachineContext $context */
        $context = $event->getSubject();
        $RefundRequest = $context->getRefundRequest();
        $CompletedStatus = $this->refundRequestStatusRepository->find((int) $context->getStatus());
        $RefundRequest->setRefundRequestStatus($CompletedStatus);
    }

    private function newContext(RefundRequest $RefundRequest): RefundRequestStateMachineContext
    {
        $status = $RefundRequest->getRefundRequestStatus();
        $statusId = $status ? (string) $status->getId() : '';

        return new RefundRequestStateMachineContext($statusId, $RefundRequest);
    }
}

class RefundRequestStateMachineContext
{
    public function __construct(private string $status, private readonly RefundRequest $RefundRequest)
    {
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): void
    {
        $this->status = $status;
    }

    public function getRefundRequest(): RefundRequest
    {
        return $this->RefundRequest;
    }

    /**
     * Alias of getStatus()
     */
    public function getMarking(): string
    {
        return $this->getStatus();
    }

    /**
     * Alias of setStatus()
     */
    public function setMarking(string $status): void
    {
        $this->setStatus($status);
    }
}
