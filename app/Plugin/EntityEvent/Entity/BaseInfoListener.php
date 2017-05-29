<?php

namespace Plugin\EntityEvent\Entity;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Eccube\Annotation\PreUpdate;
use Eccube\Entity\Event\EntityEventListener;

/**
 * @PreUpdate("Eccube\Entity\BaseInfo")
 */
class BaseInfoListener implements EntityEventListener
{
    public function execute(LifecycleEventArgs $eventArgs)
    {
        /** @var PreUpdateEventArgs $eventArgs */
        $new = $eventArgs->getNewValue('company_name');
        $old = $eventArgs->getOldValue('company_name');

        error_log($new);
        error_log($old);
    }
}
