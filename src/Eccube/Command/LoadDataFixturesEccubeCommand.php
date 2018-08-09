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

use Doctrine\Bundle\DoctrineBundle\Command\DoctrineCommand;
use Eccube\Common\EccubeConfig;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class LoadDataFixturesEccubeCommand extends DoctrineCommand
{
    protected static $defaultName = 'eccube:fixtures:load';

    protected function configure()
    {
        $this
            ->setDescription('Load data fixtures to your database.')
            ->setHelp(<<<EOF
The <info>%command.name%</info> command loads data fixtures from EC-CUBE.

  <info>php %command.full_name%</info>
EOF
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $doctrine = $this->getContainer()->get('doctrine');
        $em = $doctrine->getManager();
        $loader = new \Eccube\Doctrine\Common\CsvDataFixtures\Loader();
        $loader->loadFromDirectory(__DIR__.'/../Resource/doctrine/import_csv');
        $executer = new \Eccube\Doctrine\Common\CsvDataFixtures\Executor\DbalExecutor($em);
        $fixtures = $loader->getFixtures();
        $executer->execute($fixtures);

        $login_id = env('ECCUBE_ADMIN_USER', 'admin');
        $login_password = env('ECCUBE_ADMIN_PASS', 'password');

        $eccubeConfig = $this->getContainer()->get(EccubeConfig::class);
        $encoder = new \Eccube\Security\Core\Encoder\PasswordEncoder($eccubeConfig);

        $salt = \Eccube\Util\StringUtil::random(32);
        $password = $encoder->encodePassword($login_password, $salt);

        $conn = $em->getConnection();
        $member_id = ('postgresql' === $conn->getDatabasePlatform()->getName())
            ? $conn->fetchColumn("select nextval('dtb_member_id_seq')")
            : null;

        $conn->insert('dtb_member', [
            'id' => $member_id,
            'login_id' => $login_id,
            'password' => $password,
            'salt' => $salt,
            'work_id' => 1,
            'authority_id' => 0,
            'creator_id' => 1,
            'sort_no' => 1,
            'update_date' => new \DateTime(),
            'create_date' => new \DateTime(),
            'name' => '管理者',
            'department' => 'EC-CUBE SHOP',
            'discriminator_type' => 'member',
        ], [
            'update_date' => \Doctrine\DBAL\Types\Type::DATETIME,
            'create_date' => \Doctrine\DBAL\Types\Type::DATETIME,
        ]);

        $shop_name = env('ECCUBE_SHOP_NAME', 'EC-CUBE SHOP');
        $admin_mail = env('ECCUBE_ADMIN_MAIL', 'admin@example.com');

        $id = ('postgresql' === $conn->getDatabasePlatform()->getName())
            ? $conn->fetchColumn("select nextval('dtb_base_info_id_seq')")
            : null;

        $conn->insert('dtb_base_info', [
            'id' => $id,
            'shop_name' => $shop_name,
            'email01' => $admin_mail,
            'email02' => $admin_mail,
            'email03' => $admin_mail,
            'email04' => $admin_mail,
            'update_date' => new \DateTime(),
            'discriminator_type' => 'baseinfo',
        ], [
            'update_date' => \Doctrine\DBAL\Types\Type::DATETIME,
        ]);
        $output->writeln(sprintf('  <comment>></comment> <info>%s</info>', 'Finished Successful!'));
    }
}
