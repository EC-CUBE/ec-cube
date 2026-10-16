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
use Eccube\Util\IdUtil;
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
     * @param class-string<T> $className
     */
    public function __construct(private readonly ObjectManager $om, private $className)
    {
    }

    /**
     * @param T|null $entity
     */
    #[\Override]
    public function transform(mixed $entity): string|int|null
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
    public function reverseTransform(mixed $id): ?object
    {
        if ('' === $id || null === $id) {
            return null;
        }
        /** @var class-string<T> $classname */
        $classname = $this->className;

        // 整数型の ID に数値でない値や範囲外の値を渡すと、DB によっては例外になるため問い合わせる前に弾く
        $metadata = $this->om->getClassMetadata($classname);
        $identifiers = $metadata->getIdentifierFieldNames();
        if (1 === count($identifiers)) {
            $max = IdUtil::maxForType($metadata->getTypeOfField($identifiers[0]));
            if (null !== $max && null === IdUtil::toId($id, $max)) {
                throw new TransformationFailedException();
            }
        }

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
