<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2015 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */

namespace Eccube\EventListener;

use Doctrine\ORM\EntityManager;
use Eccube\Log\Logger;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Event\PostResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * トランザクション制御のためのListener
 *
 * @package Eccube\EventListener
 */
class TransactionListener implements EventSubscriberInterface
{
    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @var Logger
     */
    protected $logger;

    /**
     * @var bool
     */
    protected $isEnabled = true;

    /**
     * TransactionListener constructor.
     *
     * @param EntityManager $em
     * @param Logger $logger
     * @param bool $isEnabled
     */
    public function __construct(EntityManager $em, Logger $logger, $isEnabled = true)
    {
        $this->em = $em;
        $this->logger = $logger;
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
     * @param GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        if (!$this->isEnabled) {
            $this->logger->debug('Transaction Listener is disabled.');

            return;
        }

        if (!$event->isMasterRequest()) {
            return;
        }

        $this->em->getConnection()->setAutoCommit(false);
        $this->em->beginTransaction();
        $this->logger->debug('Begin Transaction.');
    }

    /**
     * Kernel exception listener callback.
     *
     * @param GetResponseForExceptionEvent $event
     */
    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        if (!$this->isEnabled) {
            $this->logger->debug('Transaction Listener is disabled.');

            return;
        }

        if (!$event->isMasterRequest()) {
            return;
        }

        if ($this->em->getConnection()->isTransactionActive()) {
            $this->em->rollback();
            $this->logger->debug('Rollback executed.');
        } else {
            $this->logger->debug('Transaction is not active. Rollback skipped.');
        }
    }

    /**
     *  Kernel terminate listener callback.
     *
     * @param PostResponseEvent $event
     */
    public function onKernelTerminate(PostResponseEvent $event)
    {
        if (!$this->isEnabled) {
            $this->logger->debug('Transaction Listener is disabled.');

            return;
        }
        if ($this->em->getConnection()->isTransactionActive()) {
            if ($this->em->getConnection()->isRollbackOnly()) {
                $this->em->rollback();
                $this->logger->debug('Rollback executed.');
            } else {
                $this->em->commit();
                $this->logger->debug('Commit executed.');
            }
        } else {
            $this->logger->debug('Transaction is not active. Rollback skipped.');
        }
    }

    /**
     * Return the events to subscribe to.
     *
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return array(
            KernelEvents::REQUEST => 'onKernelRequest',
            KernelEvents::EXCEPTION => 'onKernelException',
            KernelEvents::TERMINATE => 'onKernelTerminate',
        );
    }
}
