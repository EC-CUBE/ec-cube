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
use Eccube\Repository\RefundRequestFileRepository;

if (!class_exists(RefundRequestFile::class)) {
    /**
     * RefundRequestFile
     *
     * 返品申請のエビデンスファイル（画像・動画）.
     */
    #[ORM\Table(name: 'dtb_refund_request_file')]
    #[ORM\InheritanceType('SINGLE_TABLE')]
    #[ORM\DiscriminatorColumn(name: 'discriminator_type', type: 'string', length: 255)]
    #[ORM\HasLifecycleCallbacks]
    #[ORM\Entity(repositoryClass: RefundRequestFileRepository::class)]
    class RefundRequestFile extends AbstractEntity
    {
        #[ORM\Column(name: 'id', type: Types::INTEGER, options: ['unsigned' => true])]
        #[ORM\Id]
        #[ORM\GeneratedValue(strategy: 'IDENTITY')]
        private ?int $id = null;

        #[ORM\Column(name: 'file_name', type: Types::STRING, length: 255)]
        private ?string $file_name = null;

        #[ORM\Column(name: 'mime_type', type: Types::STRING, length: 100)]
        private ?string $mime_type = null;

        #[ORM\Column(name: 'file_size', type: Types::INTEGER, options: ['unsigned' => true])]
        private ?int $file_size = null;

        #[ORM\Column(name: 'sort_no', type: Types::SMALLINT, options: ['unsigned' => true, 'default' => 0])]
        private ?int $sort_no = 0;

        /**
         * @var \DateTime
         */
        #[ORM\Column(name: 'create_date', type: Types::DATETIMETZ_MUTABLE)]
        private $create_date;

        #[ORM\ManyToOne(targetEntity: RefundRequest::class, inversedBy: 'RefundRequestFiles')]
        #[ORM\JoinColumn(name: 'refund_request_id', referencedColumnName: 'id')]
        private ?RefundRequest $RefundRequest = null;

        public function getId(): ?int
        {
            return $this->id;
        }

        public function setFileName(?string $fileName): self
        {
            $this->file_name = $fileName;

            return $this;
        }

        public function getFileName(): ?string
        {
            return $this->file_name;
        }

        public function setMimeType(?string $mimeType): self
        {
            $this->mime_type = $mimeType;

            return $this;
        }

        public function getMimeType(): ?string
        {
            return $this->mime_type;
        }

        public function setFileSize(?int $fileSize): self
        {
            $this->file_size = $fileSize;

            return $this;
        }

        public function getFileSize(): ?int
        {
            return $this->file_size;
        }

        public function setSortNo(?int $sortNo): self
        {
            $this->sort_no = $sortNo;

            return $this;
        }

        public function getSortNo(): ?int
        {
            return $this->sort_no;
        }

        public function setCreateDate(\DateTime $createDate): self
        {
            $this->create_date = $createDate;

            return $this;
        }

        public function getCreateDate(): ?\DateTime
        {
            return $this->create_date;
        }

        public function setRefundRequest(?RefundRequest $refundRequest = null): self
        {
            $this->RefundRequest = $refundRequest;

            return $this;
        }

        public function getRefundRequest(): ?RefundRequest
        {
            return $this->RefundRequest;
        }

        /**
         * ファイルが画像かどうか.
         */
        public function isImage(): bool
        {
            return str_starts_with((string) $this->mime_type, 'image/');
        }

        /**
         * ファイルが動画かどうか.
         */
        public function isVideo(): bool
        {
            return str_starts_with((string) $this->mime_type, 'video/');
        }
    }
}
