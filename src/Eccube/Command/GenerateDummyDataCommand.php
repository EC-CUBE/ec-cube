<?php

namespace Eccube\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Faker\Factory as Faker;

class GenerateDummyDataCommand extends \Knp\Command\Command
{

    protected $app;

    protected function configure()
    {
        $this
            ->setName('dummydata:generate')
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

        $this->app = $this->getSilexApplication();
        $this->app->register(new \Eccube\Tests\ServiceProvider\FixtureServiceProvider());
        $Customers = [];
        $Products = [];

        $faker = Faker::create($locale);
        for ($i = 0; $i < $numberOfCustomer; $i++) {
            $email = microtime(true).'.'.$faker->safeEmail;
            $Customer = $this->app['eccube.fixture.generator.locale']($locale)->createCustomer($email);
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
            $Product = $this->app['eccube.fixture.generator.locale']($locale)->createProduct(null, 3, $notImage ? null : $imageType);
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
        $Deliveries = $this->app['eccube.repository.delivery']->findAll();
        $j = 0;
        foreach ($Customers as $Customer) {
            $Delivery = $Deliveries[$faker->numberBetween(0, count($Deliveries) - 1)];
            $Product = $Products[$faker->numberBetween(0, $numberOfProducts - 1)];
            $charge = $faker->randomNumber(4);
            $discount = $faker->randomNumber(4);
            for ($i = 0; $i < $numberOfOrder; $i++) {
                $Order = $this->app['eccube.fixture.generator.locale']($locale)->createOrder($Customer, $Product->getProductClasses()->toArray(), $Delivery, $charge, $discount);
                $Status = $this->app['eccube.repository.order_status']->find($faker->numberBetween(1, 8));
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
                $this->app['orm.em']->flush($Order);
                $j++;
                if ($output->getVerbosity() >= OutputInterface::VERBOSITY_NORMAL && ($j % 100) === 0 && $j > 0) {
                    $output->writeln(' ...'.$j);
                }
            }
        }
        $output->writeln('');
        $output->writeln(sprintf("%s <info>success</info>", 'dummydata:generate'));
    }
}
