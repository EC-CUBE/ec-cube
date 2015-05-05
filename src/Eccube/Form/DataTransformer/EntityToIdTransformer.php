<?php

namespace Eccube\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Doctrine\Common\Persistence\ObjectManager;

class EntityToIdTransformer implements DataTransformerInterface {

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
     * @param stging $className
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
        if (!$id) {
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