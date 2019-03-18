<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eccube\Form\DataTransformer;

use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class EntityToIdTransformer implements DataTransformerInterface
{
    /**
     * @var ObjectManager
     */
    private $om;

    /**
     * @var string
     */
    private $className;

    /**
     * @param ObjectManager $om
     * @param string $className
     */
    public function __construct(ObjectManager $om, $className)
    {
        $this->om = $om;
        $this->className = $className;
    }

    public function transform($entity)
    {
        if (null === $entity) {
            return '';
        }

        return $entity->getId();
    }

    public function reverseTransform($id)
    {
        if ('' === $id || null === $id) {
            return null;
        }

        $entity = $this->om
            ->getRepository($this->className)
            ->find($id)
        ;

        if (null === $entity) {
            throw new TransformationFailedException();
        }

        return $entity;
    }
}
