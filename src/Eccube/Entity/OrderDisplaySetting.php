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

namespace Eccube\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Eccube\Repository\OrderDisplaySettingRepository;

if (!class_exists(OrderDisplaySetting::class)) {
    /**
     * OrderDisplaySetting
     *
     * 受注一覧画面の表示項目（列）の表示/非表示・並び順を保持する店舗共通設定.
     */
    #[ORM\Table(name: 'dtb_order_display_setting')]
    #[ORM\Entity(repositoryClass: OrderDisplaySettingRepository::class)]
    class OrderDisplaySetting extends AbstractEntity
    {
        /**
         * 受注一覧の表示項目の既定値（field_name => 既定の表示名）. 配列順が既定の sort_no.
         *
         * コード上の正本。未登録時のフォールバック(OrderController)と既存環境向けの
         * 初期データ投入(マイグレーション)はこの定数を参照し, 定義の二重管理を避ける。
         * 新規インストールはロケール別の import_csv(dtb_order_display_setting.csv)から投入される。
         *
         * @var array<string, string>
         */
        public const DEFAULT_DISPLAY_FIELDS = [
            'order_info' => 'ID・注文者',
            'payment_method' => '支払方法',
            'order_status' => '対応状況',
            'payment_info' => '購入金額',
            'message' => 'お問合せ',
            'shipping_status' => '出荷状況',
            'tracking_number' => 'お問い合わせ番号',
            'delivery_address' => 'お届け先',
        ];

        #[ORM\Column(name: 'id', type: Types::INTEGER, options: ['unsigned' => true])]
        #[ORM\Id]
        #[ORM\GeneratedValue(strategy: 'IDENTITY')]
        private ?int $id = null;

        #[ORM\Column(name: 'field_name', type: Types::STRING, length: 255)]
        private string $field_name;

        #[ORM\Column(name: 'disp_name', type: Types::STRING, length: 255)]
        private string $disp_name;

        #[ORM\Column(name: 'enabled', type: Types::BOOLEAN, options: ['default' => true])]
        private bool $enabled = true;

        #[ORM\Column(name: 'sort_no', type: Types::INTEGER, options: ['default' => 0])]
        private int $sort_no = 0;

        #[ORM\Column(name: 'create_date', type: Types::DATETIMETZ_MUTABLE)]
        private ?\DateTime $create_date = null;

        #[ORM\Column(name: 'update_date', type: Types::DATETIMETZ_MUTABLE)]
        private ?\DateTime $update_date = null;

        /**
         * Get id.
         */
        public function getId(): ?int
        {
            return $this->id;
        }

        /**
         * Set fieldName.
         */
        public function setFieldName(string $fieldName): OrderDisplaySetting
        {
            $this->field_name = $fieldName;

            return $this;
        }

        /**
         * Get fieldName.
         */
        public function getFieldName(): string
        {
            return $this->field_name;
        }

        /**
         * Set dispName.
         */
        public function setDispName(string $dispName): OrderDisplaySetting
        {
            $this->disp_name = $dispName;

            return $this;
        }

        /**
         * Get dispName.
         */
        public function getDispName(): string
        {
            return $this->disp_name;
        }

        /**
         * Set enabled.
         */
        public function setEnabled(bool $enabled): OrderDisplaySetting
        {
            $this->enabled = $enabled;

            return $this;
        }

        /**
         * Get enabled.
         */
        public function getEnabled(): bool
        {
            return $this->enabled;
        }

        /**
         * Set sortNo.
         */
        public function setSortNo(int $sortNo): OrderDisplaySetting
        {
            $this->sort_no = $sortNo;

            return $this;
        }

        /**
         * Get sortNo.
         */
        public function getSortNo(): int
        {
            return $this->sort_no;
        }

        /**
         * Set createDate.
         */
        public function setCreateDate(\DateTime $createDate): OrderDisplaySetting
        {
            $this->create_date = $createDate;

            return $this;
        }

        /**
         * Get createDate.
         */
        public function getCreateDate(): ?\DateTime
        {
            return $this->create_date;
        }

        /**
         * Set updateDate.
         */
        public function setUpdateDate(\DateTime $updateDate): OrderDisplaySetting
        {
            $this->update_date = $updateDate;

            return $this;
        }

        /**
         * Get updateDate.
         */
        public function getUpdateDate(): ?\DateTime
        {
            return $this->update_date;
        }
    }
}
