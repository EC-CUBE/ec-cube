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

if (!class_exists(RateLimiter::class)) {
    /**
     * RateLimiter.
     *
     * スロットリング(レートリミッター)のデータストレージ用テーブル.
     *
     * 実体は Symfony\Component\Cache\Adapter\DoctrineDbalAdapter が読み書きするキャッシュテーブルで,
     * カラム名は services.yaml で定義した DoctrineDbalAdapter の options
     * (db_id_col / db_data_col / db_lifetime_col / db_time_col) と厳密に一致させている.
     * これによりテーブルを ORM のメタデータ管理下に置き, プラグイン削除やスキーマ再構築で
     * 削除されないようにするとともに, symfony/cache・DBAL のバージョンアップで既定カラム名が
     * 変わってもサイレントに壊れないようにする.
     *
     * NOTE: dtb_* テーブルは通常 integer の id を主キーに持つが, 本テーブルは adapter が
     * キャッシュキー(文字列)を主キーとして SELECT/DELETE するため, 文字列カラムを主キーにしている.
     */
    #[ORM\Table(name: 'dtb_rate_limiter')]
    #[ORM\Entity]
    class RateLimiter extends AbstractEntity
    {
        #[ORM\Id]
        #[ORM\Column(name: 'cache_key', type: Types::STRING, length: 255)]
        private string $cacheKey;

        /**
         * @var resource|string|null
         */
        #[ORM\Column(name: 'cache_data', type: Types::BLOB)]
        private $cacheData;

        #[ORM\Column(name: 'cache_lifetime', type: Types::INTEGER, nullable: true, options: ['unsigned' => true])]
        private ?int $cacheLifetime = null;

        #[ORM\Column(name: 'cache_time', type: Types::INTEGER, options: ['unsigned' => true])]
        private int $cacheTime;

        /**
         * Get cacheKey.
         */
        public function getCacheKey(): string
        {
            return $this->cacheKey;
        }

        /**
         * Set cacheKey.
         */
        public function setCacheKey(string $cacheKey): RateLimiter
        {
            $this->cacheKey = $cacheKey;

            return $this;
        }

        /**
         * Get cacheData.
         *
         * @return resource|string|null
         */
        public function getCacheData(): mixed
        {
            return $this->cacheData;
        }

        /**
         * Set cacheData.
         *
         * @param resource|string|null $cacheData
         */
        public function setCacheData(mixed $cacheData): RateLimiter
        {
            $this->cacheData = $cacheData;

            return $this;
        }

        /**
         * Get cacheLifetime.
         */
        public function getCacheLifetime(): ?int
        {
            return $this->cacheLifetime;
        }

        /**
         * Set cacheLifetime.
         */
        public function setCacheLifetime(?int $cacheLifetime = null): RateLimiter
        {
            $this->cacheLifetime = $cacheLifetime;

            return $this;
        }

        /**
         * Get cacheTime.
         */
        public function getCacheTime(): int
        {
            return $this->cacheTime;
        }

        /**
         * Set cacheTime.
         */
        public function setCacheTime(int $cacheTime): RateLimiter
        {
            $this->cacheTime = $cacheTime;

            return $this;
        }
    }
}
