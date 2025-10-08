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
use Eccube\Common\EccubeConfig;
use Eccube\Repository\CartRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[\Symfony\Component\Console\Attribute\AsCommand(name: 'eccube:delete-carts', description: 'Delete Carts from the database')]
class DeleteCartsCommand extends Command
{
    /**
     * @var EccubeConfig
     */
    protected $eccubeConfig;

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

    public function __construct(EccubeConfig $eccubeConfig, EntityManagerInterface $entityManager, CartRepository $cartRepository)
    {
        parent::__construct();

        $this->eccubeConfig = $eccubeConfig;
        $this->entityManager = $entityManager;
        $this->cartRepository = $cartRepository;
    }

    /**
     * @return void
     */
    #[\Override]
    protected function configure(): void
    {
        $this
            ->addArgument('date', InputArgument::REQUIRED, 'Deletes carts before the specified date');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return void
     *
     * @throws \Exception
     */
    #[\Override]
    protected function interact(InputInterface $input, OutputInterface $output): void
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

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return void
     *
     * @throws \Exception
     */
    #[\Override]
    protected function initialize(InputInterface $input, OutputInterface $output): void
    {
        $this->io = new SymfonyStyle($input, $output);
        $this->locale = $this->eccubeConfig->get('locale');
        $this->timezone = new \DateTimeZone($this->eccubeConfig->get('timezone'));
        $this->formatter = $this->createIntlFormatter();
    }

    #[\Override]
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $dateStr = $input->getArgument('date');
        $timestamp = $this->formatter->parse($dateStr);
        $dateTime = new \DateTime("@$timestamp", $this->timezone);

        $this->deleteCarts($dateTime);

        $this->io->success('Delete carts successful.');

        return 0;
    }

    /**
     * @param \DateTime $dateTime
     *
     * @return void
     */
    protected function deleteCarts(\DateTime $dateTime): void
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

            $this->io->comment("Deleted {$deleteRows} carts.");
        } catch (\Exception) {
            $this->io->error('Failed delete carts. Rollbacked.');
            $this->entityManager->rollback();
        }
    }

    /**
     * @return \IntlDateFormatter|null
     */
    protected function createIntlFormatter(): ?\IntlDateFormatter
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
