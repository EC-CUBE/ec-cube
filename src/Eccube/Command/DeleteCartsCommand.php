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

namespace Eccube\Command;

use Doctrine\ORM\EntityManagerInterface;
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

    public function __construct(ContainerInterface $container, EntityManagerInterface $entityManager)
    {
        parent::__construct();

        $this->container = $container;
        $this->entityManager = $entityManager;
    }

    protected function configure()
    {
        $this
            ->setDescription('Delete Carts from the database')
            ->addArgument('date', InputArgument::REQUIRED, '指定した日付以前のカート情報を削除します.');
    }

    protected function interact(InputInterface $input, OutputInterface $output)
    {
        if (null !== $input->getArgument('date')) {
            return;
        }

        $this->io->title('Delete Cart Command Interactive Wizard');
        $this->io->text([
            'If you prefer to not use this interactive wizard, provide the',
            'arguments required by this command as follows:',
            '',
            ' $ php bin/console eccube:delete-cart yyyy/mm/dd',
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
    }

    protected function deleteCarts(\DateTime $dateTime)
    {
        $batchSize = 20;
        $i = 0;

        try {
            $this->entityManager->beginTransaction();
            $q = $this->entityManager->createQuery('SELECT c FROM Eccube\Entity\Cart c WHERE c.update_date <= :date');
            $q->setParameter('date', $dateTime);

            $iterableResult = $q->iterate();
            while (($row = $iterableResult->next()) !== false) {
                $this->entityManager->remove($row[0]);
                if (($i % $batchSize) === 0) {
                    $this->flush(); // Executes all deletions.
                    $this->clear(); // Detaches all objects from Doctrine!
                }
                ++$i;
            }
            $this->entityManager->flush();
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
