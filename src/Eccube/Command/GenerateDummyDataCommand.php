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

use Doctrine\ORM\EntityManager;
use Eccube\Entity\Master\OrderStatus;
use Eccube\Repository\DeliveryRepository;
use Eccube\Repository\ProductRepository;
use Eccube\Tests\Fixture\Generator;
use Faker\Factory as Faker;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateDummyDataCommand extends Command
{
    protected static $defaultName = 'eccube:fixtures:generate';

    /**
     * @var Generator
     */
    protected $generator;

    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @var DeliveryRepository
     */
    protected $deliveryRepository;

    /**
     * @var ProductRepository
     */
    protected $productRepository;

    public function __construct(Generator $generator = null, EntityManager $entityManager = null, DeliveryRepository $deliveryRepository = null, ProductRepository $productRepository = null)
    {
        parent::__construct();
        $this->generator = $generator;
        $this->entityManager = $entityManager;
        $this->deliveryRepository = $deliveryRepository;
        $this->productRepository = $productRepository;
    }

    protected function configure()
    {
        $this
            ->setDescription('Dummy data generator')
            ->addOption('with-locale', null, InputOption::VALUE_REQUIRED, 'Set to the locale.', 'ja_JP')
            ->addOption('without-image', null, InputOption::VALUE_NONE, 'Do not generate images.')
            ->addOption('with-image', null, InputOption::VALUE_REQUIRED, 'Generate image type of abstract|animals|business|cats|city|food|nightlife|fashion|people|nature|sports|technics|transport', 'cats')
            ->addOption('products', null, InputOption::VALUE_REQUIRED, 'Number of Products.', 100)
            ->addOption('orders', null, InputOption::VALUE_REQUIRED, 'Number of Orders.', 10)
            ->addOption('customers', null, InputOption::VALUE_REQUIRED, 'Number of Customers.', 100)
            ->setHelp(<<<EOF
The <info>%command.name%</info> command generate of dummy data.

  <info>php %command.full_name%</info>

Generate of dummy data with images.

  <info>php %command.full_name% --without-image</info>

Generate of dummy data without images, use for options to faster.
;
EOF
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $locale = $input->getOption('with-locale');
        $imageType = $input->getOption('with-image');
        $notImage = $input->getOption('without-image');
        $numberOfProducts = $input->getOption('products');
        $numberOfOrder = $input->getOption('orders');
        $numberOfCustomer = $input->getOption('customers');

        $Customers = [];
        $Products = [];

        $faker = Faker::create($locale);
        for ($i = 0; $i < $numberOfCustomer; $i++) {
            $email = microtime(true).'.'.$faker->safeEmail;
            $Customer = $this->generator->createCustomer($email);
            $Customer->setBirth($faker->dateTimeBetween('-110 years', '- 5 years'));
            switch ($output->getVerbosity()) {
                case OutputInterface::VERBOSITY_QUIET:
                    break;
                case OutputInterface::VERBOSITY_NORMAL:
                    $output->write('C');
                    break;
                case OutputInterface::VERBOSITY_VERBOSE:
                case OutputInterface::VERBOSITY_VERY_VERBOSE:
                case OutputInterface::VERBOSITY_DEBUG:
                    $output->writeln('Customer: id='.$Customer->getId().' '.$Customer->getEmail());
                    break;
            }
            if ($output->getVerbosity() >= OutputInterface::VERBOSITY_NORMAL && ($i % 100) === 0 && $i > 0) {
                $output->writeln(' ...'.$i);
            }
            $Customers[] = $Customer;
        }
        for ($i = 0; $i < $numberOfProducts; $i++) {
            // @see https://github.com/fzaninotto/Faker/issues/1125#issuecomment-268676186
            gc_collect_cycles();

            $Product = $this->generator->createProduct(null, 3, $notImage ? null : $imageType);
            switch ($output->getVerbosity()) {
                case OutputInterface::VERBOSITY_QUIET:
                    break;
                case OutputInterface::VERBOSITY_NORMAL:
                    $output->write('P');
                    break;
                case OutputInterface::VERBOSITY_VERBOSE:
                case OutputInterface::VERBOSITY_VERY_VERBOSE:
                case OutputInterface::VERBOSITY_DEBUG:
                    $output->writeln('Product: id='.$Product->getId().' '.$Product->getName());
                    break;
            }
            if ($output->getVerbosity() >= OutputInterface::VERBOSITY_NORMAL && ($i % 100) === 0 && $i > 0) {
                $output->writeln(' ...'.$i);
            }
            $Products[] = $Product;
        }
        $Deliveries = $this->deliveryRepository->findAll();
        $j = 0;
        $randomOrderStatus = [
            OrderStatus::NEW,
            OrderStatus::CANCEL,
            OrderStatus::IN_PROGRESS,
            OrderStatus::DELIVERED,
            OrderStatus::PAID,
            OrderStatus::PENDING,
            OrderStatus::PROCESSING,
            OrderStatus::RETURNED,
        ];
        foreach ($Customers as $Customer) {
            $Delivery = $Deliveries[$faker->numberBetween(0, count($Deliveries) - 1)];
            if (count($Products) > 0) {
                $Product = $Products[$faker->numberBetween(0, count($Products) - 1)];
            } else {
                $orderBy = ['ASC', 'DESC'];
                $orderByKey = $faker->numberBetween(0, 1);
                $Product = $this->productRepository->findOneBy([], ['id' => $orderBy[$orderByKey]]);
            }
            $charge = $faker->randomNumber(4);
            $discount = $faker->randomNumber(4);
            for ($i = 0; $i < $numberOfOrder; $i++) {
                // @see https://github.com/fzaninotto/Faker/issues/1125#issuecomment-268676186
                gc_collect_cycles();

                $Order = $this->generator->createOrder($Customer, $Product->getProductClasses()->toArray(), $Delivery, $charge, $discount);
                $Status = $this->entityManager->find(OrderStatus::class, $faker->randomElement($randomOrderStatus));
                $Order->setOrderStatus($Status);
                $Order->setOrderDate($faker->dateTimeThisYear());
                switch ($output->getVerbosity()) {
                    case OutputInterface::VERBOSITY_QUIET:
                        break;
                    case OutputInterface::VERBOSITY_NORMAL:
                        $output->write('O');
                        break;
                    case OutputInterface::VERBOSITY_VERBOSE:
                    case OutputInterface::VERBOSITY_VERY_VERBOSE:
                    case OutputInterface::VERBOSITY_DEBUG:
                        $output->writeln('Order: id='.$Order->getId());
                        break;
                }
                $this->entityManager->flush($Order);
                $j++;
                if ($output->getVerbosity() >= OutputInterface::VERBOSITY_NORMAL && ($j % 100) === 0 && $j > 0) {
                    $output->writeln(' ...'.$j);
                }
            }
        }
        $output->writeln('');
        $output->writeln(sprintf('%s <info>success</info>', 'eccube:fixtures:generate'));
    }
}
