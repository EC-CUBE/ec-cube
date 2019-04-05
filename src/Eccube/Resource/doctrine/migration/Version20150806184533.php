<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) EC-CUBE CO.,LTD. All Rights Reserved.
 *
 * http://www.ec-cube.co.jp/
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150806184533 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {

        // this up() migration is auto-generated, please modify it to your needs
        $t = $schema->getTable('dtb_product_class');
        $c = $t->getColumn('stock_unlimited');

        if($c->getType()->getName() != 'smallint'){
            $this->addSql('ALTER TABLE dtb_product_class ADD stock_unlimited_tmp int ;');
            $this->addSql('UPDATE dtb_product_class SET stock_unlimited_tmp = 1 where stock_unlimited =  true ');
            $this->addSql('UPDATE dtb_product_class SET stock_unlimited_tmp = 0 where stock_unlimited <> true ');
            $t->dropColumn('stock_unlimited');
        } 
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
    }
}
