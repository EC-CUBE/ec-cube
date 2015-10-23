<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20151023102323 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs

        // pege_idはauto increment項目なので指定しない
        $this->addSql("INSERT INTO dtb_page_layout (device_type_id, page_name, url, file_name, edit_flg, author, description, keyword, update_url, create_date, update_date, meta_robots) VALUES (10, '商品購入/お届け先の追加', 'shopping_shipping_edit', 'Shopping/shipping_edit', 2, NULL, NULL, NULL, NULL, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, 'noindex');");
        $this->addSql("INSERT INTO dtb_page_layout (device_type_id, page_name, url, file_name, edit_flg, author, description, keyword, update_url, create_date, update_date, meta_robots) VALUES (10, '商品購入/お届け先の複数指定(お届け先の追加)', 'shopping_shipping_multiple_edit', 'Shopping/shipping_multiple_edit', 2, NULL, NULL, NULL, NULL, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, 'noindex');");
        $this->addSql("INSERT INTO dtb_page_layout (device_type_id, page_name, url, file_name, edit_flg, author, description, keyword, update_url, create_date, update_date, meta_robots) VALUES (10, '商品購入/購入エラー', 'shopping_error', 'Shopping/shopping_error', 2, NULL, NULL, NULL, NULL, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, 'noindex');");
        $this->addSql("INSERT INTO dtb_page_layout (device_type_id, page_name, url, file_name, edit_flg, author, description, keyword, update_url, create_date, update_date, meta_robots) VALUES (10, 'ご利用ガイド', 'help_guide', 'Help/guide', 2, NULL, NULL, NULL, NULL, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, NULL);");
        $this->addSql("INSERT INTO dtb_page_layout (device_type_id, page_name, url, file_name, edit_flg, author, description, keyword, update_url, create_date, update_date, meta_robots) VALUES (10, 'パスワード再発行(入力ページ)', 'forgot', 'Forgot/index', 2, NULL, NULL, NULL, NULL, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, NULL);");
        $this->addSql("INSERT INTO dtb_page_layout (device_type_id, page_name, url, file_name, edit_flg, author, description, keyword, update_url, create_date, update_date, meta_robots) VALUES (10, 'パスワード再発行(完了ページ)', 'forgot_complete', 'Forgot/complete', 2, NULL, NULL, NULL, NULL, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, NULL);");
        $this->addSql("INSERT INTO dtb_page_layout (device_type_id, page_name, url, file_name, edit_flg, author, description, keyword, update_url, create_date, update_date, meta_robots) VALUES (10, 'パスワード変更(完了ページ)', 'forgot_reset', 'Forgot/reset', 2, NULL, NULL, NULL, NULL, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, NULL);");

        $this->addSql("UPDATE dtb_page_layout  SET url = 'help_agreement', file_name = 'Help/agreement' WHERE file_name = 'Entry/kiyaku';");
        $this->addSql("UPDATE dtb_page_layout  SET url = 'shopping_shipping_multiple', file_name = 'Shopping/shipping_multiple' WHERE file_name = 'Shopping/multiple';");
        $this->addSql("UPDATE dtb_page_layout  SET page_name = '商品購入' WHERE page_name = '商品購入/ログイン';");
        $this->addSql("UPDATE dtb_page_layout  SET page_name = 'MYページ/お届け先一覧' WHERE page_name = 'MYページ/お届け先変更';");
        $this->addSql("UPDATE dtb_page_layout  SET file_name = 'Mypage/delivery_edit' WHERE page_name = 'MYページ/お届け先追加';");

        $this->addSql("DELETE from dtb_page_layout  WHERE file_name = 'Shopping/payment';");
        $this->addSql("DELETE from dtb_page_layout  WHERE file_name = 'Shopping/confirm';");
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
    }
}
