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

namespace Eccube\EventListener;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\TransactionIsolationLevel;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\TerminateEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * トランザクション制御のためのListener
 */
class TransactionListener implements EventSubscriberInterface
{
    /**
     * @var EntityManagerInterface
     */
    protected $em;

    /**
     * @var bool
     */
    protected $isEnabled = true;

    /**
     * TransactionListener constructor.
     *
     * @param EntityManager $em
     * @param bool $isEnabled
     */
    public function __construct(EntityManagerInterface $em, $isEnabled = true)
    {
        $this->em = $em;
        $this->isEnabled = $isEnabled;
    }

    /**
     * Disable transaction listener.
     */
    public function disable()
    {
        $this->isEnabled = false;
    }

    /**
     * Kernel request listener callback.
     *
     * @param RequestEvent $event
     */
    public function onKernelRequest(RequestEvent $event)
    {
        if (!$this->isEnabled) {
            log_debug('Transaction Listener is disabled.');

            return;
        }

        if (!$event->isMainRequest()) {
            return;
        }

        /** @var Connection $Connection */
        $Connection = $this->em->getConnection();
        if (!$Connection->isConnected()) {
            $Connection->connect();
        }
        $Connection->setAutoCommit(false);
        $Connection->setTransactionIsolation(TransactionIsolationLevel::READ_COMMITTED);
        $this->em->beginTransaction();
        log_debug('Begin Transaction.');
    }

    /**
     * Kernel exception listener callback.
     *
     * @param ExceptionEvent $event
     */
    public function onKernelException(ExceptionEvent $event)
    {
        if (!$this->isEnabled) {
            log_debug('Transaction Listener is disabled.');

            return;
        }

        if (!$event->isMainRequest()) {
            return;
        }

        if ($this->em->getConnection()->getNativeConnection()->inTransaction()) {
            if ($this->em->getConnection()->isRollbackOnly()) {
                $this->em->rollback();
            }
            log_debug('Rollback executed.');
        } else {
            log_debug('Transaction is not active. Rollback skipped.');
        }
    }

    /**
     *  Kernel terminate listener callback.
     *
     * @param PostResponseEvent $event
     */
    public function onKernelTerminate(TerminateEvent $event)
    {
        if (!$this->isEnabled) {
            log_debug('Transaction Listener is disabled.');

            return;
        }
        if ($this->em->getConnection()->getNativeConnection()->inTransaction()) {
            if ($this->em->getConnection()->isRollbackOnly()) {
                $this->em->rollback();
                log_debug('Rollback executed.');
            } else {
                $this->em->commit();
                log_debug('Commit executed.');
            }
        } else {
            log_debug('Transaction is not active. Rollback skipped.');
        }
    }

    /**
     * Return the events to subscribe to.
     *
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => 'onKernelRequest',
            KernelEvents::EXCEPTION => 'onKernelException',
            KernelEvents::TERMINATE => 'onKernelTerminate',
        ];
    }
}
