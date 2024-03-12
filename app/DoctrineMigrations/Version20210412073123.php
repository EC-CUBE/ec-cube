<?php

declare(strict_types=1);

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

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Eccube\Entity\Master\LoginHistoryStatus;
use Eccube\Util\StringUtil;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210412073123 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        if ($schema->hasTable('plg_admin_record_config')) {
            $denyHostsPlugin = $this->connection->fetchOne('select admin_deny_hosts FROM plg_admin_record_config') ?: '';
            $denyHostsPlugin = array_filter(\explode("\n", StringUtil::convertLineFeed($denyHostsPlugin)), function ($str) {
                return StringUtil::isNotBlank($str);
            });

            $denyHosts = array_merge(env('ECCUBE_ADMIN_DENY_HOSTS', []), $denyHostsPlugin);
            $denyHosts = array_values(array_unique($denyHosts));

            $denyHosts = \json_encode($denyHosts);

            $envFile = __DIR__.'/../../.env';
            $env = file_get_contents($envFile);

            $env = StringUtil::replaceOrAddEnv($env, [
                'ECCUBE_ADMIN_DENY_HOSTS' => "'${denyHosts}'",
            ]);

            file_put_contents($envFile, $env);
        }

        if ($schema->hasTable('plg_admin_record')) {
            $stmt = $this->connection->createQueryBuilder()
                ->select('*')
                ->from('plg_admin_record')
                ->orderBy('id', 'ASC')
                ->execute();

            while ($row = $stmt->fetch()) {
                $this->addSql(
                    "INSERT INTO dtb_login_history (user_name, client_ip, create_date, update_date, login_history_status_id, member_id, discriminator_type) VALUES (?, ?, ?, ?, ?, ?, 'loginhistory')",
                    [
                        $row['user_name'],
                        $row['client_ip'],
                        $row['create_date'],
                        $row['update_date'],
                        $row['success_flg'] ? LoginHistoryStatus::SUCCESS : LoginHistoryStatus::FAILURE,
                        $row['member_id'],
                    ]
                );
            }
        }
    }

    public function down(Schema $schema): void
    {
        if ($schema->hasTable('dtb_login_history')) {
            $this->addSql('DELETE FROM dtb_login_history');
        }
    }
}
