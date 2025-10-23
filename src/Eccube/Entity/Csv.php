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

use Doctrine\ORM\Mapping as ORM;
use Eccube\Entity\Master\CsvType;
use Eccube\Repository\CsvRepository;

if (!class_exists(Csv::class)) {
    /**
     * Csv
     */
    #[ORM\Table(name: 'dtb_csv')]
    #[ORM\InheritanceType('SINGLE_TABLE')]
    #[ORM\DiscriminatorColumn(name: 'discriminator_type', type: 'string', length: 255)]
    #[ORM\HasLifecycleCallbacks]
    #[ORM\Entity(repositoryClass: CsvRepository::class)]
    class Csv extends AbstractEntity
    {
        /**
         * @var int
         */
        #[ORM\Column(name: 'id', type: 'integer', options: ['unsigned' => true])]
        #[ORM\Id]
        #[ORM\GeneratedValue(strategy: 'IDENTITY')]
        /**  @phpstan-ignore-next-line Doctrine ORMによって自動生成されるため、setterは不要 */
        private $id;

        /**
         * @var string
         */
        #[ORM\Column(name: 'entity_name', type: 'string', length: 255)]
        private $entity_name;

        /**
         * @var string
         */
        #[ORM\Column(name: 'field_name', type: 'string', length: 255)]
        private $field_name;

        /**
         * @var string|null
         */
        #[ORM\Column(name: 'reference_field_name', type: 'string', length: 255, nullable: true)]
        private $reference_field_name;

        /**
         * @var string
         */
        #[ORM\Column(name: 'disp_name', type: 'string', length: 255)]
        private $disp_name;

        /**
         * @var int
         */
        #[ORM\Column(name: 'sort_no', type: 'smallint', options: ['unsigned' => true])]
        private $sort_no;

        /**
         * @var bool
         */
        #[ORM\Column(name: 'enabled', type: 'boolean', options: ['default' => true])]
        private $enabled = true;

        /**
         * @var \DateTime
         */
        #[ORM\Column(name: 'create_date', type: 'datetimetz')]
        private $create_date;

        /**
         * @var \DateTime
         */
        #[ORM\Column(name: 'update_date', type: 'datetimetz')]
        private $update_date;

        /**
         * @var CsvType|null
         */
        #[ORM\ManyToOne(targetEntity: CsvType::class)]
        #[ORM\JoinColumn(name: 'csv_type_id', referencedColumnName: 'id')]
        private $CsvType;

        /**
         * @var Member|null
         */
        #[ORM\ManyToOne(targetEntity: Member::class)]
        #[ORM\JoinColumn(name: 'creator_id', referencedColumnName: 'id')]
        private $Creator;

        /**
         * Get id.
         *
         * @return int
         */
        public function getId(): ?int
        {
            return $this->id;
        }

        /**
         * Set entityName.
         */
        public function setEntityName(string $entityName): Csv
        {
            $this->entity_name = $entityName;

            return $this;
        }

        /**
         * Get entityName.
         */
        public function getEntityName(): string
        {
            return $this->entity_name;
        }

        /**
         * Set fieldName.
         */
        public function setFieldName(string $fieldName): Csv
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
         * Set referenceFieldName.
         */
        public function setReferenceFieldName(?string $referenceFieldName = null): Csv
        {
            $this->reference_field_name = $referenceFieldName;

            return $this;
        }

        /**
         * Get referenceFieldName.
         */
        public function getReferenceFieldName(): ?string
        {
            return $this->reference_field_name;
        }

        /**
         * Set dispName.
         */
        public function setDispName(string $dispName): Csv
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
         * Set sortNo.
         */
        public function setSortNo(int $sortNo): Csv
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
         * Set enabled.
         */
        public function setEnabled(bool $enabled): Csv
        {
            $this->enabled = $enabled;

            return $this;
        }

        /**
         * Get enabled.
         */
        public function isEnabled(): bool
        {
            return $this->enabled;
        }

        /**
         * Set createDate.
         */
        public function setCreateDate(\DateTime $createDate): Csv
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
        public function setUpdateDate(\DateTime $updateDate): Csv
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

        /**
         * Set csvType.
         */
        public function setCsvType(?CsvType $csvType = null): Csv
        {
            $this->CsvType = $csvType;

            return $this;
        }

        /**
         * Get csvType.
         */
        public function getCsvType(): ?CsvType
        {
            return $this->CsvType;
        }

        /**
         * Set creator.
         */
        public function setCreator(?Member $creator = null): Csv
        {
            $this->Creator = $creator;

            return $this;
        }

        /**
         * Get creator.
         */
        public function getCreator(): ?Member
        {
            return $this->Creator;
        }
    }
}
