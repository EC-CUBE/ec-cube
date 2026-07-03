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

namespace Eccube\Entity\Master;

use Doctrine\ORM\Mapping as ORM;
use Eccube\Repository\Master\AgentProtocolRepository;

if (!class_exists(AgentProtocol::class, false)) {
    /**
     * AgentProtocol.
     *
     * エージェントコマースのプロトコル種別マスタ (ACP / UCP)。
     * `CheckoutSession` と `Order.agent_protocol` の双方から参照されるため、
     * 文字列での二重管理を避けてマスタ化している。
     * `name` には正準識別子 (`acp`/`ucp`) を保持する (ロケール非依存・ja/en 共通)。
     */
    #[ORM\Table(name: 'mtb_agent_protocol')]
    #[ORM\InheritanceType('SINGLE_TABLE')]
    #[ORM\DiscriminatorColumn(name: 'discriminator_type', type: 'string', length: 255)]
    #[ORM\HasLifecycleCallbacks]
    #[ORM\Entity(repositoryClass: AgentProtocolRepository::class)]
    #[ORM\Cache(usage: 'NONSTRICT_READ_WRITE')]
    class AgentProtocol extends AbstractMasterEntity
    {
        /** Agentic Commerce Protocol (OpenAI + Stripe). */
        public const ACP = 1;

        /** Universal Commerce Protocol (ucp.dev / Google). */
        public const UCP = 2;
    }
}
