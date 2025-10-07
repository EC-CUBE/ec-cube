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

namespace Eccube\Form\DataTransformer;

use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

/**
 * @template T of object
 *
 * @implements DataTransformerInterface<T|null, string|int|null>
 */
class EntityToIdTransformer implements DataTransformerInterface
{
    /**
     * @var ObjectManager
     */
    private $om;

    /**
     * @var class-string<T>
     */
    private $className;

    /**
     * @param ObjectManager $om
     * @param class-string<T> $className
     */
    public function __construct(ObjectManager $om, $className)
    {
        $this->om = $om;
        $this->className = $className;
    }

    /**
     * @param T|null $entity
     *
     * @return string|int|null
     */
    #[\Override]
    public function transform($entity): string|int|null
    {
        if (null === $entity) {
            return '';
        }

        return $entity->getId();
    }

    /**
     * @param string|int|null $id
     *
     * @return T|null
     */
    #[\Override]
    public function reverseTransform($id): ?object
    {
        if ('' === $id || null === $id) {
            return null;
        }
        /** @var class-string<T> $classname */
        $classname = $this->className;
        $entity = $this->om
            ->getRepository($classname)
            ->find($id)
        ;

        if (null === $entity) {
            throw new TransformationFailedException();
        }

        return $entity;
    }
}
