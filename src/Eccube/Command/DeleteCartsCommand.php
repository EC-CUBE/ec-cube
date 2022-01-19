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

namespace Eccube\Command;

use Doctrine\ORM\EntityManagerInterface;
use Eccube\Repository\CartRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ContainerInterface;

class DeleteCartsCommand extends Command
{
    protected static $defaultName = 'eccube:delete-carts';

    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var SymfonyStyle
     */
    protected $io;

    /**
     * @var string
     */
    protected $locale;

    /**
     * @var \DateTimeZone
     */
    protected $timezone;

    /**
     * @var \IntlDateFormatter
     */
    protected $formatter;

    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;
    /**
     * @var CartRepository
     */
    private $cartRepository;

    public function __construct(ContainerInterface $container, EntityManagerInterface $entityManager, CartRepository $cartRepository)
    {
        parent::__construct();

        $this->container = $container;
        $this->entityManager = $entityManager;
        $this->cartRepository = $cartRepository;
    }

    protected function configure()
    {
        $this
            ->setDescription('Delete Carts from the database')
            ->addArgument('date', InputArgument::REQUIRED, 'Deletes carts before the specified date');
    }

    protected function interact(InputInterface $input, OutputInterface $output)
    {
        if (null !== $input->getArgument('date')) {
            return;
        }

        $pattern = $this->formatter->getPattern();
        $this->io->title('Delete Cart Command Interactive Wizard');
        $this->io->text([
            'If you prefer to not use this interactive wizard, provide the',
            'arguments required by this command as follows:',
            '',
            ' $ php bin/console eccube:delete-cart <'.$pattern.'>',
            '',
            'Now we\'ll ask you for the value of all the missing command arguments.',
            '',
        ]);

        $now = new \DateTime('now', $this->timezone);

        $dateStr = $this->formatter->format($now->getTimestamp());
        $dateStr = $this->io->ask('date', $dateStr);

        $input->setArgument('date', $dateStr);
    }

    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $this->io = new SymfonyStyle($input, $output);
        $this->locale = $this->container->getParameter('locale');
        $this->timezone = new \DateTimeZone($this->container->getParameter('timezone'));
        $this->formatter = $this->createIntlFormatter();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $dateStr = $input->getArgument('date');
        $timestamp = $this->formatter->parse($dateStr);
        $dateTime = new \DateTime("@$timestamp", $this->timezone);

        $this->deleteCarts($dateTime);

        $this->io->success('Delete carts successful.');

        return 0;
    }

    protected function deleteCarts(\DateTime $dateTime)
    {
        try {
            $this->entityManager->beginTransaction();

            $qb = $this->cartRepository->createQueryBuilder('c')
                ->delete()
                ->where('c.update_date <= :date')
                ->setParameter('date', $dateTime);

            $deleteRows = $qb->getQuery()->getResult();

            $this->entityManager->flush();
            $this->entityManager->commit();

            $this->io->comment("Deleted ${deleteRows} carts.");
        } catch (\Exception $e) {
            $this->io->error('Failed delete carts. Rollbacked.');
            $this->entityManager->rollback();
        }

        return;
    }

    protected function createIntlFormatter()
    {
        return \IntlDateFormatter::create(
            $this->locale,
            \IntlDateFormatter::MEDIUM,
            \IntlDateFormatter::NONE,
            $this->timezone,
            \IntlDateFormatter::GREGORIAN
        );
    }
}
