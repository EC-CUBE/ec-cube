<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150722170707 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {

        if($this->connection->getDatabasePlatform()->getName() == "mysql"){
            // this up() migration is auto-generated, please modify it to your needs
            $this->addSql("alter table  dtb_base_info                 collate utf8_general_ci ;");
            $this->addSql("alter table  dtb_block                     collate utf8_general_ci ;");
            $this->addSql("alter table  dtb_block_position            collate utf8_general_ci ;");
            $this->addSql("alter table  dtb_category                  collate utf8_general_ci ;");
            $this->addSql("alter table  dtb_category_count            collate utf8_general_ci ;");
            $this->addSql("alter table  dtb_category_total_count      collate utf8_general_ci ;");
            $this->addSql("alter table  dtb_class_category            collate utf8_general_ci ;");
            $this->addSql("alter table  dtb_class_name                collate utf8_general_ci ;");
            $this->addSql("alter table  dtb_csv                       collate utf8_general_ci ;");
            $this->addSql("alter table  dtb_customer                  collate utf8_general_ci ;");
            $this->addSql("alter table  dtb_customer_address          collate utf8_general_ci ;");
            $this->addSql("alter table  dtb_customer_favorite_product collate utf8_general_ci ;");
            $this->addSql("alter table  dtb_delivery                  collate utf8_general_ci ;");
            $this->addSql("alter table  dtb_delivery_date             collate utf8_general_ci ;");
            $this->addSql("alter table  dtb_delivery_fee              collate utf8_general_ci ;");
            $this->addSql("alter table  dtb_delivery_time             collate utf8_general_ci ;");
            $this->addSql("alter table  dtb_help                      collate utf8_general_ci ;");
            $this->addSql("alter table  dtb_mail_history              collate utf8_general_ci ;");
            $this->addSql("alter table  dtb_mail_template             collate utf8_general_ci ;");
            $this->addSql("alter table  dtb_member                    collate utf8_general_ci ;");
            $this->addSql("alter table  dtb_news                      collate utf8_general_ci ;");
            $this->addSql("alter table  dtb_order                     collate utf8_general_ci ;");
            $this->addSql("alter table  dtb_order_detail              collate utf8_general_ci ;");
            $this->addSql("alter table  dtb_page_layout               collate utf8_general_ci ;");
            $this->addSql("alter table  dtb_payment                   collate utf8_general_ci ;");
            $this->addSql("alter table  dtb_payment_option            collate utf8_general_ci ;");
            $this->addSql("alter table  dtb_plugin                    collate utf8_general_ci ;");
            $this->addSql("alter table  dtb_plugin_event_handler      collate utf8_general_ci ;");
            $this->addSql("alter table  dtb_product                   collate utf8_general_ci ;");
            $this->addSql("alter table  dtb_product_category          collate utf8_general_ci ;");
            $this->addSql("alter table  dtb_product_class             collate utf8_general_ci ;");
            $this->addSql("alter table  dtb_product_image             collate utf8_general_ci ;");
            $this->addSql("alter table  dtb_product_stock             collate utf8_general_ci ;");
            $this->addSql("alter table  dtb_product_tag               collate utf8_general_ci ;");

            if ($schema->hasTable('dtb_send_customer')) {
                $this->addSql("alter table  dtb_send_customer             collate utf8_general_ci ;");
            }
            if ($schema->hasTable('dtb_send_history')) {
                $this->addSql("alter table  dtb_send_history              collate utf8_general_ci ;");
            }

            $this->addSql("alter table  dtb_shipment_item             collate utf8_general_ci ;");
            $this->addSql("alter table  dtb_shipping                  collate utf8_general_ci ;");
            $this->addSql("alter table  dtb_tax_rule                  collate utf8_general_ci ;");
            $this->addSql("alter table  dtb_template                  collate utf8_general_ci ;");
            $this->addSql("alter table  mtb_authority                 collate utf8_general_ci ;");
            $this->addSql("alter table  mtb_country                   collate utf8_general_ci ;");
            $this->addSql("alter table  mtb_csv_type                  collate utf8_general_ci ;");
            $this->addSql("alter table  mtb_customer_order_status     collate utf8_general_ci ;");
            $this->addSql("alter table  mtb_customer_status           collate utf8_general_ci ;");
            $this->addSql("alter table  mtb_db                        collate utf8_general_ci ;");
            $this->addSql("alter table  mtb_device_type               collate utf8_general_ci ;");
            $this->addSql("alter table  mtb_disp                      collate utf8_general_ci ;");
            $this->addSql("alter table  mtb_job                       collate utf8_general_ci ;");
            $this->addSql("alter table  mtb_order_status              collate utf8_general_ci ;");
            $this->addSql("alter table  mtb_order_status_color        collate utf8_general_ci ;");
            $this->addSql("alter table  mtb_page_max                  collate utf8_general_ci ;");
            $this->addSql("alter table  mtb_pref                      collate utf8_general_ci ;");
            $this->addSql("alter table  mtb_product_list_max          collate utf8_general_ci ;");
            $this->addSql("alter table  mtb_product_list_order_by     collate utf8_general_ci ;");
            $this->addSql("alter table  mtb_product_type              collate utf8_general_ci ;");
            $this->addSql("alter table  mtb_sex                       collate utf8_general_ci ;");
            $this->addSql("alter table  mtb_tag                       collate utf8_general_ci ;");
            $this->addSql("alter table  mtb_taxrule                   collate utf8_general_ci ;");
            $this->addSql("alter table  mtb_work                      collate utf8_general_ci ;");
            $this->addSql("alter table  mtb_zip                       collate utf8_general_ci ;");

            $this->addSql("alter table dtb_base_info             modify company_name            longtext                COLLATE utf8_general_ci ;");
            $this->addSql("alter table dtb_base_info             modify company_kana            longtext                COLLATE utf8_general_ci ;");
            $this->addSql("alter table dtb_base_info             modify zip01                   longtext                COLLATE utf8_general_ci ;");
            $this->addSql("alter table dtb_base_info             modify zip02                   longtext                COLLATE utf8_general_ci ;");
            $this->addSql("alter table dtb_base_info             modify zipcode                 longtext                COLLATE utf8_general_ci ;");
            $this->addSql("alter table dtb_base_info             modify addr01                  longtext                COLLATE utf8_general_ci ;");
            $this->addSql("alter table dtb_base_info             modify addr02                  longtext                COLLATE utf8_general_ci ;");
            $this->addSql("alter table dtb_base_info             modify tel01                   longtext                COLLATE utf8_general_ci ;");
            $this->addSql("alter table dtb_base_info             modify tel02                   longtext                COLLATE utf8_general_ci ;");
            $this->addSql("alter table dtb_base_info             modify tel03                   longtext                COLLATE utf8_general_ci ;");
            $this->addSql("alter table dtb_base_info             modify fax01                   longtext                COLLATE utf8_general_ci ;");
            $this->addSql("alter table dtb_base_info             modify fax02                   longtext                COLLATE utf8_general_ci ;");
            $this->addSql("alter table dtb_base_info             modify fax03                   longtext                COLLATE utf8_general_ci ;");
            $this->addSql("alter table dtb_base_info             modify business_hour           longtext                COLLATE utf8_general_ci ;");
            $this->addSql("alter table dtb_base_info             modify email01                 longtext                COLLATE utf8_general_ci ;");
            $this->addSql("alter table dtb_base_info             modify email02                 longtext                COLLATE utf8_general_ci ;");
            $this->addSql("alter table dtb_base_info             modify email03                 longtext                COLLATE utf8_general_ci ;");
            $this->addSql("alter table dtb_base_info             modify email04                 longtext                COLLATE utf8_general_ci ;");
            $this->addSql("alter table dtb_base_info             modify shop_name               longtext                COLLATE utf8_general_ci ;");
            $this->addSql("alter table dtb_base_info             modify shop_kana               longtext                COLLATE utf8_general_ci ;");
            $this->addSql("alter table dtb_base_info             modify shop_name_eng           longtext                COLLATE utf8_general_ci ;");
            $this->addSql("alter table dtb_base_info             modify good_traded             longtext                COLLATE utf8_general_ci ;");
            $this->addSql("alter table dtb_base_info             modify message                 longtext                COLLATE utf8_general_ci ;");
            $this->addSql("alter table dtb_base_info             modify latitude                longtext                COLLATE utf8_general_ci ;");
            $this->addSql("alter table dtb_base_info             modify longitude               longtext                COLLATE utf8_general_ci ;");
            $this->addSql("alter table dtb_block                 modify block_name              longtext                COLLATE utf8_general_ci ;");
            $this->addSql("alter table dtb_block                 modify file_name               varchar(200)  not null           COLLATE utf8_general_ci ;");
            $this->addSql("alter table dtb_category              modify category_name           longtext                COLLATE utf8_general_ci ;");
            $this->addSql("alter table dtb_class_category        modify name                    longtext                COLLATE utf8_general_ci ;");
            $this->addSql("alter table dtb_class_name            modify name                    longtext                COLLATE utf8_general_ci ;");
            $this->addSql("alter table dtb_csv                   modify entity_name             longtext      not null           COLLATE utf8_general_ci ;");
            $this->addSql("alter table dtb_csv                   modify field_name              longtext      not null           COLLATE utf8_general_ci ;");
            $this->addSql("alter table dtb_csv                   modify reference_field_name    longtext                COLLATE utf8_general_ci ;");
            $this->addSql("alter table dtb_csv                   modify disp_name               varchar(255)  not null           COLLATE utf8_general_ci ;");
            $this->addSql("alter table dtb_customer              modify name01                  longtext                COLLATE utf8_general_ci ;");
            $this->addSql("alter table dtb_customer              modify name02                  longtext      not null           COLLATE utf8_general_ci ;");
            $this->addSql("alter table dtb_customer              modify kana01                  longtext                COLLATE utf8_general_ci ;");
            $this->addSql("alter table dtb_customer              modify kana02                  longtext                COLLATE utf8_general_ci ;");
            $this->addSql("alter table dtb_customer              modify company_name            longtext                COLLATE utf8_general_ci ;");
            $this->addSql("alter table dtb_customer              modify zip01                   longtext                COLLATE utf8_general_ci ;");
            $this->addSql("alter table dtb_customer              modify zip02                   longtext                COLLATE utf8_general_ci ;");
            $this->addSql("alter table dtb_customer              modify zipcode                 longtext                COLLATE utf8_general_ci ;");
            $this->addSql("alter table dtb_customer              modify addr01                  longtext                COLLATE utf8_general_ci ;");
            $this->addSql("alter table dtb_customer              modify addr02                  longtext                COLLATE utf8_general_ci ;");
            $this->addSql("alter table dtb_customer              modify email                   longtext      not null           COLLATE utf8_general_ci ;");
            $this->addSql("alter table dtb_customer              modify tel01                   longtext                COLLATE utf8_general_ci ;");
            $this->addSql("alter table dtb_customer              modify tel02                   longtext                COLLATE utf8_general_ci ;");
            $this->addSql("alter table dtb_customer              modify tel03                   longtext                COLLATE utf8_general_ci ;");
            $this->addSql("alter table dtb_customer              modify fax01                   longtext                COLLATE utf8_general_ci ;");
            $this->addSql("alter table dtb_customer              modify fax02                   longtext                COLLATE utf8_general_ci ;");
            $this->addSql("alter table dtb_customer              modify fax03                   longtext                COLLATE utf8_general_ci ;");
            $this->addSql("alter table dtb_customer              modify password                longtext                COLLATE utf8_general_ci ;");
            $this->addSql("alter table dtb_customer              modify salt                    longtext                COLLATE utf8_general_ci ;");
            $this->addSql("alter table dtb_customer              modify secret_key              varchar(200)  not null           COLLATE utf8_general_ci ;");
            $this->addSql("alter table dtb_customer              modify note                    longtext                COLLATE utf8_general_ci ;");
            $this->addSql("alter table dtb_customer              modify reset_key               longtext                COLLATE utf8_general_ci ;");
            $this->addSql("alter table dtb_customer_address      modify name01                  longtext                COLLATE utf8_general_ci ;");
            $this->addSql("alter table dtb_customer_address      modify name02                  longtext                COLLATE utf8_general_ci ;");
            $this->addSql("alter table dtb_customer_address      modify kana01                  longtext                COLLATE utf8_general_ci ;");
            $this->addSql("alter table dtb_customer_address      modify kana02                  longtext                COLLATE utf8_general_ci ;");
            $this->addSql("alter table dtb_customer_address      modify company_name            longtext                COLLATE utf8_general_ci ;");
            $this->addSql("alter table dtb_customer_address      modify zip01                   longtext                COLLATE utf8_general_ci ;");
            $this->addSql("alter table dtb_customer_address      modify zip02                   longtext                COLLATE utf8_general_ci ;");
            $this->addSql("alter table dtb_customer_address      modify zipcode                 longtext                COLLATE utf8_general_ci ;");
            $this->addSql("alter table dtb_customer_address      modify addr01                  longtext                COLLATE utf8_general_ci ;");
            $this->addSql("alter table dtb_customer_address      modify addr02                  longtext                COLLATE utf8_general_ci ;");
            $this->addSql("alter table dtb_customer_address      modify tel01                   longtext                COLLATE utf8_general_ci ;");
            $this->addSql("alter table dtb_customer_address      modify tel02                   longtext                COLLATE utf8_general_ci ;");
            $this->addSql("alter table dtb_customer_address      modify tel03                   longtext                COLLATE utf8_general_ci ;");
            $this->addSql("alter table dtb_customer_address      modify fax01                   longtext                COLLATE utf8_general_ci ;");
            $this->addSql("alter table dtb_customer_address      modify fax02                   longtext                COLLATE utf8_general_ci ;");
            $this->addSql("alter table dtb_customer_address      modify fax03                   longtext                COLLATE utf8_general_ci ;");
            $this->addSql("alter table dtb_delivery              modify name                    longtext                COLLATE utf8_general_ci ;");
            $this->addSql("alter table dtb_delivery              modify service_name            longtext                COLLATE utf8_general_ci ;");
            $this->addSql("alter table dtb_delivery              modify description             longtext                COLLATE utf8_general_ci ;");
            $this->addSql("alter table dtb_delivery              modify confirm_url             longtext                COLLATE utf8_general_ci ;");
            $this->addSql("alter table dtb_delivery_date         modify name                    longtext                COLLATE utf8_general_ci ;");
            $this->addSql("alter table dtb_delivery_time         modify delivery_time           longtext      not null           COLLATE utf8_general_ci ;");
            $this->addSql("alter table dtb_help                  modify customer_agreement      longtext                COLLATE utf8_general_ci ;");
            $this->addSql("alter table dtb_help                  modify law_company             longtext                COLLATE utf8_general_ci ;");
            $this->addSql("alter table dtb_help                  modify law_manager             longtext                COLLATE utf8_general_ci ;");
            $this->addSql("alter table dtb_help                  modify law_zip01               longtext                COLLATE utf8_general_ci ;");
            $this->addSql("alter table dtb_help                  modify law_zip02               longtext                COLLATE utf8_general_ci ;");
            $this->addSql("alter table dtb_help                  modify law_zipcode             longtext                COLLATE utf8_general_ci ;");
            $this->addSql("alter table dtb_help                  modify law_addr01              longtext                COLLATE utf8_general_ci ;");
            $this->addSql("alter table dtb_help                  modify law_addr02              longtext                COLLATE utf8_general_ci ;");
            $this->addSql("alter table dtb_help                  modify law_tel01               longtext                COLLATE utf8_general_ci ;");
            $this->addSql("alter table dtb_help                  modify law_tel02               longtext                COLLATE utf8_general_ci ;");
            $this->addSql("alter table dtb_help                  modify law_tel03               longtext                COLLATE utf8_general_ci ;");
            $this->addSql("alter table dtb_help                  modify law_fax01               longtext                COLLATE utf8_general_ci ;");
            $this->addSql("alter table dtb_help                  modify law_fax02               longtext                COLLATE utf8_general_ci ;");
            $this->addSql("alter table dtb_help                  modify law_fax03               longtext                COLLATE utf8_general_ci ;");
            $this->addSql("alter table dtb_help                  modify law_email               longtext                COLLATE utf8_general_ci ;");
            $this->addSql("alter table dtb_help                  modify law_url                 longtext                COLLATE utf8_general_ci ;");
            $this->addSql("alter table dtb_help                  modify law_term01              longtext                COLLATE utf8_general_ci ;");
            $this->addSql("alter table dtb_help                  modify law_term02              longtext                COLLATE utf8_general_ci ;");
            $this->addSql("alter table dtb_help                  modify law_term03              longtext                COLLATE utf8_general_ci ;");
            $this->addSql("alter table dtb_help                  modify law_term04              longtext                COLLATE utf8_general_ci ;");
            $this->addSql("alter table dtb_help                  modify law_term05              longtext                COLLATE utf8_general_ci ;");
            $this->addSql("alter table dtb_help                  modify law_term06              longtext                COLLATE utf8_general_ci ;");
            $this->addSql("alter table dtb_help                  modify law_term07              longtext                COLLATE utf8_general_ci ;");
            $this->addSql("alter table dtb_help                  modify law_term08              longtext                COLLATE utf8_general_ci ;");
            $this->addSql("alter table dtb_help                  modify law_term09              longtext                COLLATE utf8_general_ci ;");
            $this->addSql("alter table dtb_help                  modify law_term10              longtext                COLLATE utf8_general_ci ;");
            $this->addSql("alter table dtb_mail_history          modify subject                 longtext                COLLATE utf8_general_ci ;");
            $this->addSql("alter table dtb_mail_history          modify mail_body               longtext                COLLATE utf8_general_ci ;");
            $this->addSql("alter table dtb_mail_template         modify name                    longtext                COLLATE utf8_general_ci ;");
            $this->addSql("alter table dtb_mail_template         modify file_name               longtext                COLLATE utf8_general_ci ;");
            $this->addSql("alter table dtb_mail_template         modify subject                 longtext                COLLATE utf8_general_ci ;");
            $this->addSql("alter table dtb_mail_template         modify header                  longtext                COLLATE utf8_general_ci ;");
            $this->addSql("alter table dtb_mail_template         modify footer                  longtext                COLLATE utf8_general_ci ;");
            $this->addSql("alter table dtb_member                modify name                    longtext                COLLATE utf8_general_ci ;");
            $this->addSql("alter table dtb_member                modify department              longtext                COLLATE utf8_general_ci ;");
            $this->addSql("alter table dtb_member                modify login_id                longtext      not null           COLLATE utf8_general_ci ;");
            $this->addSql("alter table dtb_member                modify password                longtext      not null           COLLATE utf8_general_ci ;");
            $this->addSql("alter table dtb_member                modify salt                    longtext      not null           COLLATE utf8_general_ci ;");
            $this->addSql("alter table dtb_news                  modify news_title              longtext      not null           COLLATE utf8_general_ci ;");
            $this->addSql("alter table dtb_news                  modify news_comment            longtext                COLLATE utf8_general_ci ;");
            $this->addSql("alter table dtb_news                  modify news_url                longtext                COLLATE utf8_general_ci ;");
            $this->addSql("alter table dtb_order                 modify pre_order_id            longtext                COLLATE utf8_general_ci ;");
            $this->addSql("alter table dtb_order                 modify message                 longtext                COLLATE utf8_general_ci ;");
            $this->addSql("alter table dtb_order                 modify order_name01            longtext                COLLATE utf8_general_ci ;");
            $this->addSql("alter table dtb_order                 modify order_name02            longtext                COLLATE utf8_general_ci ;");
            $this->addSql("alter table dtb_order                 modify order_kana01            longtext                COLLATE utf8_general_ci ;");
            $this->addSql("alter table dtb_order                 modify order_kana02            longtext                COLLATE utf8_general_ci ;");
            $this->addSql("alter table dtb_order                 modify order_company_name      longtext                COLLATE utf8_general_ci ;");
            $this->addSql("alter table dtb_order                 modify order_email             longtext                COLLATE utf8_general_ci ;");
            $this->addSql("alter table dtb_order                 modify order_tel01             longtext                COLLATE utf8_general_ci ;");
            $this->addSql("alter table dtb_order                 modify order_tel02             longtext                COLLATE utf8_general_ci ;");
            $this->addSql("alter table dtb_order                 modify order_tel03             longtext                COLLATE utf8_general_ci ;");
            $this->addSql("alter table dtb_order                 modify order_fax01             longtext                COLLATE utf8_general_ci ;");
            $this->addSql("alter table dtb_order                 modify order_fax02             longtext                COLLATE utf8_general_ci ;");
            $this->addSql("alter table dtb_order                 modify order_fax03             longtext                COLLATE utf8_general_ci ;");
            $this->addSql("alter table dtb_order                 modify order_zip01             longtext                COLLATE utf8_general_ci ;");
            $this->addSql("alter table dtb_order                 modify order_zip02             longtext                COLLATE utf8_general_ci ;");
            $this->addSql("alter table dtb_order                 modify order_zipcode           longtext                COLLATE utf8_general_ci ;");
            $this->addSql("alter table dtb_order                 modify order_addr01            longtext                COLLATE utf8_general_ci ;");
            $this->addSql("alter table dtb_order                 modify order_addr02            longtext                COLLATE utf8_general_ci ;");
            $this->addSql("alter table dtb_order                 modify payment_method          longtext                COLLATE utf8_general_ci ;");
            $this->addSql("alter table dtb_order                 modify note                    longtext                COLLATE utf8_general_ci ;");
            $this->addSql("alter table dtb_order_detail          modify product_name            longtext      not null           COLLATE utf8_general_ci ;");
            $this->addSql("alter table dtb_order_detail          modify product_code            longtext                COLLATE utf8_general_ci ;");
            $this->addSql("alter table dtb_order_detail          modify class_name1             longtext                COLLATE utf8_general_ci ;");
            $this->addSql("alter table dtb_order_detail          modify class_name2             longtext                COLLATE utf8_general_ci ;");
            $this->addSql("alter table dtb_order_detail          modify class_category_name1    longtext                COLLATE utf8_general_ci ;");
            $this->addSql("alter table dtb_order_detail          modify class_category_name2    longtext                COLLATE utf8_general_ci ;");
            $this->addSql("alter table dtb_page_layout           modify page_name               longtext                COLLATE utf8_general_ci ;");
            $this->addSql("alter table dtb_page_layout           modify url                     longtext      not null           COLLATE utf8_general_ci ;");
            $this->addSql("alter table dtb_page_layout           modify file_name               longtext                COLLATE utf8_general_ci ;");
            $this->addSql("alter table dtb_page_layout           modify author                  longtext                COLLATE utf8_general_ci ;");
            $this->addSql("alter table dtb_page_layout           modify description             longtext                COLLATE utf8_general_ci ;");
            $this->addSql("alter table dtb_page_layout           modify keyword                 longtext                COLLATE utf8_general_ci ;");
            $this->addSql("alter table dtb_page_layout           modify update_url              longtext                COLLATE utf8_general_ci ;");
            $this->addSql("alter table dtb_page_layout           modify meta_robots             longtext                COLLATE utf8_general_ci ;");
            $this->addSql("alter table dtb_payment               modify payment_method          longtext                COLLATE utf8_general_ci ;");
            $this->addSql("alter table dtb_payment               modify payment_image           longtext                COLLATE utf8_general_ci ;");
            $this->addSql("alter table dtb_plugin                modify name                    longtext      not null           COLLATE utf8_general_ci ;");
            $this->addSql("alter table dtb_plugin                modify code                    longtext      not null           COLLATE utf8_general_ci ;");
            $this->addSql("alter table dtb_plugin                modify class_name              longtext      not null           COLLATE utf8_general_ci ;");
            $this->addSql("alter table dtb_plugin                modify version                 varchar(255)  not null           COLLATE utf8_general_ci ;");
            $this->addSql("alter table dtb_plugin                modify source                  longtext      not null           COLLATE utf8_general_ci ;");
            $this->addSql("alter table dtb_plugin_event_handler  modify event                   varchar(255)  not null           COLLATE utf8_general_ci ;");
            $this->addSql("alter table dtb_plugin_event_handler  modify handler                 varchar(255)  not null           COLLATE utf8_general_ci ;");
            $this->addSql("alter table dtb_plugin_event_handler  modify handler_type            varchar(255)  not null           COLLATE utf8_general_ci ;");
            $this->addSql("alter table dtb_product               modify name                    text          not null           COLLATE utf8_general_ci ;");
            $this->addSql("alter table dtb_product               modify note                    text                    COLLATE utf8_general_ci ;");
            $this->addSql("alter table dtb_product               modify description_list        text                    COLLATE utf8_general_ci ;");
            $this->addSql("alter table dtb_product               modify description_detail      text                    COLLATE utf8_general_ci ;");
            $this->addSql("alter table dtb_product               modify search_word             text                    COLLATE utf8_general_ci ;");
            $this->addSql("alter table dtb_product               modify free_area               text                    COLLATE utf8_general_ci ;");
            $this->addSql("alter table dtb_product_class         modify product_code            longtext                COLLATE utf8_general_ci ;");
            $this->addSql("alter table dtb_product_image         modify file_name               longtext      not null           COLLATE utf8_general_ci ;");

            if ($schema->hasTable('dtb_send_customer')) {
                $this->addSql("alter table dtb_send_customer         modify email                   longtext                COLLATE utf8_general_ci ;");
                $this->addSql("alter table dtb_send_customer         modify name                    longtext                COLLATE utf8_general_ci ;");
            }
            if ($schema->hasTable('dtb_send_history')) {
                $this->addSql("alter table dtb_send_history          modify subject                 longtext                COLLATE utf8_general_ci ;");
                $this->addSql("alter table dtb_send_history          modify body                    longtext                COLLATE utf8_general_ci ;");
                $this->addSql("alter table dtb_send_history          modify search_data             longtext                COLLATE utf8_general_ci ;");
            }
            $this->addSql("alter table dtb_shipment_item         modify product_name            longtext      not null           COLLATE utf8_general_ci ;");
            $this->addSql("alter table dtb_shipment_item         modify product_code            longtext                COLLATE utf8_general_ci ;");
            $this->addSql("alter table dtb_shipment_item         modify class_name1             longtext                COLLATE utf8_general_ci ;");
            $this->addSql("alter table dtb_shipment_item         modify class_name2             longtext                COLLATE utf8_general_ci ;");
            $this->addSql("alter table dtb_shipment_item         modify class_category_name1    longtext                COLLATE utf8_general_ci ;");
            $this->addSql("alter table dtb_shipment_item         modify class_category_name2    longtext                COLLATE utf8_general_ci ;");
            $this->addSql("alter table dtb_shipping              modify shipping_name01         longtext                COLLATE utf8_general_ci ;");
            $this->addSql("alter table dtb_shipping              modify shipping_name02         longtext                COLLATE utf8_general_ci ;");
            $this->addSql("alter table dtb_shipping              modify shipping_kana01         longtext                COLLATE utf8_general_ci ;");
            $this->addSql("alter table dtb_shipping              modify shipping_kana02         longtext                COLLATE utf8_general_ci ;");
            $this->addSql("alter table dtb_shipping              modify shipping_company_name   longtext                COLLATE utf8_general_ci ;");
            $this->addSql("alter table dtb_shipping              modify shipping_tel01          longtext                COLLATE utf8_general_ci ;");
            $this->addSql("alter table dtb_shipping              modify shipping_tel02          longtext                COLLATE utf8_general_ci ;");
            $this->addSql("alter table dtb_shipping              modify shipping_tel03          longtext                COLLATE utf8_general_ci ;");
            $this->addSql("alter table dtb_shipping              modify shipping_fax01          longtext                COLLATE utf8_general_ci ;");
            $this->addSql("alter table dtb_shipping              modify shipping_fax02          longtext                COLLATE utf8_general_ci ;");
            $this->addSql("alter table dtb_shipping              modify shipping_fax03          longtext                COLLATE utf8_general_ci ;");
            $this->addSql("alter table dtb_shipping              modify shipping_zip01          longtext                COLLATE utf8_general_ci ;");
            $this->addSql("alter table dtb_shipping              modify shipping_zip02          longtext                COLLATE utf8_general_ci ;");
            $this->addSql("alter table dtb_shipping              modify shipping_zipcode        longtext                COLLATE utf8_general_ci ;");
            $this->addSql("alter table dtb_shipping              modify shipping_addr01         longtext                COLLATE utf8_general_ci ;");
            $this->addSql("alter table dtb_shipping              modify shipping_addr02         longtext                COLLATE utf8_general_ci ;");
            $this->addSql("alter table dtb_shipping              modify shipping_delivery_name  longtext                COLLATE utf8_general_ci ;");
            $this->addSql("alter table dtb_shipping              modify shipping_delivery_time  longtext                COLLATE utf8_general_ci ;");
            $this->addSql("alter table dtb_template              modify template_code           longtext      not null           COLLATE utf8_general_ci ;");
            $this->addSql("alter table dtb_template              modify template_name           longtext      not null           COLLATE utf8_general_ci ;");
            $this->addSql("alter table mtb_authority             modify name                    longtext                COLLATE utf8_general_ci ;");
            $this->addSql("alter table mtb_country               modify name                    longtext                COLLATE utf8_general_ci ;");
            $this->addSql("alter table mtb_csv_type              modify name                    longtext                COLLATE utf8_general_ci ;");
            $this->addSql("alter table mtb_customer_order_status modify name                    longtext                COLLATE utf8_general_ci ;");
            $this->addSql("alter table mtb_customer_status       modify name                    longtext                COLLATE utf8_general_ci ;");
            $this->addSql("alter table mtb_db                    modify name                    longtext                COLLATE utf8_general_ci ;");
            $this->addSql("alter table mtb_device_type           modify name                    longtext                COLLATE utf8_general_ci ;");
            $this->addSql("alter table mtb_disp                  modify name                    longtext                COLLATE utf8_general_ci ;");
            $this->addSql("alter table mtb_job                   modify name                    longtext                COLLATE utf8_general_ci ;");
            $this->addSql("alter table mtb_order_status          modify name                    longtext                COLLATE utf8_general_ci ;");
            $this->addSql("alter table mtb_order_status_color    modify name                    longtext                COLLATE utf8_general_ci ;");
            $this->addSql("alter table mtb_page_max              modify name                    longtext                COLLATE utf8_general_ci ;");
            $this->addSql("alter table mtb_pref                  modify name                    longtext                COLLATE utf8_general_ci ;");
            $this->addSql("alter table mtb_product_list_max      modify name                    longtext                COLLATE utf8_general_ci ;");
            $this->addSql("alter table mtb_product_list_order_by modify name                    longtext                COLLATE utf8_general_ci ;");
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

