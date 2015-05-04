<?php

namespace Eccube\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Doctrine\Common\Persistence\ObjectManager;

class ClassCategoryTransformer implements DataTransformerInterface {
    /**
     * @var ObjectManager
     */
    private $om;

    /**
     * @param ObjectManager $om
     */
    public function __construct(ObjectManager $om)
    {
        $this->om = $om;
    }

    public function transform($ClassCategory)
    {
        if (null === $ClassCategory) {
            return "";
        }

        return (string) $ClassCategory->getId();
    }

    public function reverseTransform($id)
    {
        if (!$id) {
            return null;
        }

        $ClassCategory = $this->om
            ->getRepository('\Eccube\Entity\ClassCategory')
            ->find($id)
        ;

        if (null === $ClassCategory) {
            throw new TransformationFailedException(sprintf(
                'class category "%s" does not exist!',
                $id
            ));
        }

        return $ClassCategory;
    }
}