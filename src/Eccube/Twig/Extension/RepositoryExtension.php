<?php

namespace Eccube\Twig\Extension;


use Doctrine\ORM\EntityManagerInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class RepositoryExtension extends AbstractExtension
{
    /**
     * @var EntityManagerInterface
     */
    protected $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('repository', function ($entity) {
                $repository = $this->em->getRepository($entity);

                return $repository;
            }, ['pre_escape' => 'html', 'is_safe' => ['html']]),
        ];
    }
}