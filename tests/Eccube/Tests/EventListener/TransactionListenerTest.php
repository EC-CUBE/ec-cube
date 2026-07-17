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

namespace Eccube\Tests\EventListener;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\TransactionIsolationLevel;
use Doctrine\ORM\EntityManagerInterface;
use Eccube\EventListener\TransactionListener;
use Eccube\Tests\EccubeTestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\TerminateEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * TransactionListener のテスト.
 *
 * services_test.yaml でこの Listener は無効化されているため, コンテナからは取得せず,
 * モックを注入して手動でインスタンス化する.
 */
final class TransactionListenerTest extends EccubeTestCase
{
    /**
     * onKernelRequest では 接続確立 -> setAutoCommit(false) -> setTransactionIsolation
     * -> beginTransaction の順に実行される.
     *
     * DBAL 4 では autoCommit=false にしてから接続すると connect() 内で暗黙の
     * トランザクションが開始され, 後続の beginTransaction がネストしてしまう.
     * その結果 onKernelTerminate の commit でトランザクションが確定されず,
     * 書き込みが永続化されない. この順序を守ることが本テストの主目的.
     */
    public function testOnKernelRequestBeginsTransactionInCorrectOrder(): void
    {
        /** @var list<string> $calls 実行順序の記録先 */
        $calls = [];

        $Connection = $this->createConnectionMock();
        $Connection->expects($this->once())
            ->method('isConnected')
            ->willReturn(false);
        $Connection->expects($this->once())
            ->method('getNativeConnection')
            ->willReturnCallback(function () use (&$calls) {
                $calls[] = 'connect';

                return $this->createMock(\PDO::class);
            });
        $Connection->expects($this->once())
            ->method('setAutoCommit')
            ->with(false)
            ->willReturnCallback(function () use (&$calls): void {
                $calls[] = 'setAutoCommit';
            });
        $Connection->expects($this->once())
            ->method('setTransactionIsolation')
            ->with(TransactionIsolationLevel::READ_COMMITTED)
            ->willReturnCallback(function () use (&$calls): void {
                $calls[] = 'setTransactionIsolation';
            });

        $entityManager = $this->createEntityManagerMock($Connection);
        $entityManager->expects($this->once())
            ->method('beginTransaction')
            ->willReturnCallback(function () use (&$calls): void {
                $calls[] = 'beginTransaction';
            });

        $listener = new TransactionListener($entityManager, true);
        $listener->onKernelRequest($this->createRequestEvent());

        self::assertSame(
            ['connect', 'setAutoCommit', 'setTransactionIsolation', 'beginTransaction'],
            $calls
        );
    }

    /**
     * 接続済みの場合は明示的な接続確立を行わない.
     */
    public function testOnKernelRequestSkipsConnectWhenAlreadyConnected(): void
    {
        $Connection = $this->createConnectionMock();
        $Connection->expects($this->once())
            ->method('isConnected')
            ->willReturn(true);
        $Connection->expects($this->never())
            ->method('getNativeConnection');
        $Connection->expects($this->once())
            ->method('setAutoCommit')
            ->with(false);
        $Connection->expects($this->once())
            ->method('setTransactionIsolation')
            ->with(TransactionIsolationLevel::READ_COMMITTED);

        $entityManager = $this->createEntityManagerMock($Connection);
        $entityManager->expects($this->once())->method('beginTransaction');

        $listener = new TransactionListener($entityManager, true);
        $listener->onKernelRequest($this->createRequestEvent());
    }

    /**
     * サブリクエストでは何もしない.
     */
    public function testOnKernelRequestDoesNothingOnSubRequest(): void
    {
        $entityManager = $this->createEntityManagerMock();
        $entityManager->expects($this->never())->method('getConnection');
        $entityManager->expects($this->never())->method('beginTransaction');

        $listener = new TransactionListener($entityManager, true);
        $listener->onKernelRequest($this->createRequestEvent(HttpKernelInterface::SUB_REQUEST));
    }

    /**
     * 無効化されている場合は onKernelRequest で何もしない.
     */
    public function testOnKernelRequestDoesNothingWhenDisabled(): void
    {
        $entityManager = $this->createEntityManagerMock();
        $entityManager->expects($this->never())->method('getConnection');
        $entityManager->expects($this->never())->method('beginTransaction');

        $listener = new TransactionListener($entityManager, false);
        $listener->onKernelRequest($this->createRequestEvent());
    }

    /**
     * disable() 後は onKernelRequest で何もしない.
     */
    public function testDisable(): void
    {
        $entityManager = $this->createEntityManagerMock();
        $entityManager->expects($this->never())->method('getConnection');
        $entityManager->expects($this->never())->method('beginTransaction');

        $listener = new TransactionListener($entityManager, true);
        $listener->disable();
        $listener->onKernelRequest($this->createRequestEvent());
    }

    /**
     * 無効化されている場合は onKernelException で何もしない.
     */
    public function testOnKernelExceptionDoesNothingWhenDisabled(): void
    {
        $entityManager = $this->createEntityManagerMock();
        $entityManager->expects($this->never())->method('getConnection');
        $entityManager->expects($this->never())->method('rollback');

        $listener = new TransactionListener($entityManager, false);
        $listener->onKernelException($this->createExceptionEvent());
    }

    /**
     * トランザクション中かつ rollbackOnly の場合はロールバックする.
     */
    public function testOnKernelExceptionRollbackWhenRollbackOnly(): void
    {
        $Connection = $this->createConnectionMock();
        $Connection->method('getNativeConnection')
            ->willReturn($this->createNativeConnectionMock(true));
        $Connection->expects($this->once())
            ->method('isRollbackOnly')
            ->willReturn(true);

        $entityManager = $this->createEntityManagerMock($Connection);
        $entityManager->expects($this->once())->method('rollback');

        $listener = new TransactionListener($entityManager, true);
        $listener->onKernelException($this->createExceptionEvent());
    }

    /**
     * トランザクション中でも rollbackOnly でなければロールバックしない.
     */
    public function testOnKernelExceptionDoesNotRollbackWhenNotRollbackOnly(): void
    {
        $Connection = $this->createConnectionMock();
        $Connection->method('getNativeConnection')
            ->willReturn($this->createNativeConnectionMock(true));
        $Connection->expects($this->once())
            ->method('isRollbackOnly')
            ->willReturn(false);

        $entityManager = $this->createEntityManagerMock($Connection);
        $entityManager->expects($this->never())->method('rollback');

        $listener = new TransactionListener($entityManager, true);
        $listener->onKernelException($this->createExceptionEvent());
    }

    /**
     * トランザクションが無い場合は何もしない.
     */
    public function testOnKernelExceptionDoesNothingWithoutTransaction(): void
    {
        $Connection = $this->createConnectionMock();
        $Connection->method('getNativeConnection')
            ->willReturn($this->createNativeConnectionMock(false));
        $Connection->expects($this->never())->method('isRollbackOnly');

        $entityManager = $this->createEntityManagerMock($Connection);
        $entityManager->expects($this->never())->method('rollback');

        $listener = new TransactionListener($entityManager, true);
        $listener->onKernelException($this->createExceptionEvent());
    }

    /**
     * 無効化されている場合は onKernelTerminate で何もしない.
     */
    public function testOnKernelTerminateDoesNothingWhenDisabled(): void
    {
        $entityManager = $this->createEntityManagerMock();
        $entityManager->expects($this->never())->method('getConnection');
        $entityManager->expects($this->never())->method('commit');
        $entityManager->expects($this->never())->method('rollback');

        $listener = new TransactionListener($entityManager, false);
        $listener->onKernelTerminate($this->createTerminateEvent());
    }

    /**
     * rollbackOnly でなければコミットする.
     */
    public function testOnKernelTerminateCommits(): void
    {
        $Connection = $this->createConnectionMock();
        $Connection->method('getNativeConnection')
            ->willReturn($this->createNativeConnectionMock(true));
        $Connection->expects($this->once())
            ->method('isRollbackOnly')
            ->willReturn(false);

        $entityManager = $this->createEntityManagerMock($Connection);
        $entityManager->expects($this->once())->method('commit');
        $entityManager->expects($this->never())->method('rollback');

        $listener = new TransactionListener($entityManager, true);
        $listener->onKernelTerminate($this->createTerminateEvent());
    }

    /**
     * rollbackOnly の場合はロールバックする.
     */
    public function testOnKernelTerminateRollbackWhenRollbackOnly(): void
    {
        $Connection = $this->createConnectionMock();
        $Connection->method('getNativeConnection')
            ->willReturn($this->createNativeConnectionMock(true));
        $Connection->expects($this->once())
            ->method('isRollbackOnly')
            ->willReturn(true);

        $entityManager = $this->createEntityManagerMock($Connection);
        $entityManager->expects($this->once())->method('rollback');
        $entityManager->expects($this->never())->method('commit');

        $listener = new TransactionListener($entityManager, true);
        $listener->onKernelTerminate($this->createTerminateEvent());
    }

    /**
     * トランザクションが無い場合は commit も rollback もしない.
     */
    public function testOnKernelTerminateDoesNothingWithoutTransaction(): void
    {
        $Connection = $this->createConnectionMock();
        $Connection->method('getNativeConnection')
            ->willReturn($this->createNativeConnectionMock(false));
        $Connection->expects($this->never())->method('isRollbackOnly');

        $entityManager = $this->createEntityManagerMock($Connection);
        $entityManager->expects($this->never())->method('commit');
        $entityManager->expects($this->never())->method('rollback');

        $listener = new TransactionListener($entityManager, true);
        $listener->onKernelTerminate($this->createTerminateEvent());
    }

    public function testGetSubscribedEvents(): void
    {
        self::assertSame([
            KernelEvents::REQUEST => 'onKernelRequest',
            KernelEvents::EXCEPTION => 'onKernelException',
            KernelEvents::TERMINATE => 'onKernelTerminate',
        ], TransactionListener::getSubscribedEvents());
    }

    /**
     * @return Connection&MockObject
     */
    private function createConnectionMock(): MockObject
    {
        return $this->createMock(Connection::class);
    }

    /**
     * @return \PDO&MockObject
     */
    private function createNativeConnectionMock(bool $inTransaction): MockObject
    {
        $nativeConnection = $this->createMock(\PDO::class);
        $nativeConnection->method('inTransaction')->willReturn($inTransaction);

        return $nativeConnection;
    }

    /**
     * @return EntityManagerInterface&MockObject
     */
    private function createEntityManagerMock(?MockObject $Connection = null): MockObject
    {
        $entityManager = $this->createMock(EntityManagerInterface::class);
        if ($Connection !== null) {
            $entityManager->method('getConnection')->willReturn($Connection);
        }

        return $entityManager;
    }

    private function createRequestEvent(int $requestType = HttpKernelInterface::MAIN_REQUEST): RequestEvent
    {
        return new RequestEvent($this->getKernel(), Request::create('/'), $requestType);
    }

    private function createExceptionEvent(): ExceptionEvent
    {
        return new ExceptionEvent(
            $this->getKernel(),
            Request::create('/'),
            HttpKernelInterface::MAIN_REQUEST,
            new \RuntimeException('test')
        );
    }

    private function createTerminateEvent(): TerminateEvent
    {
        return new TerminateEvent(
            $this->getKernel(),
            Request::create('/'),
            new Response()
        );
    }

    private function getKernel(): HttpKernelInterface
    {
        return static::getContainer()->get('kernel');
    }
}
