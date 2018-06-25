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

namespace Plugin\SamplePayment\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Config
 *
 * TODO プラグインのテーブルで必要なアノテーションを精査
 *
 * @ORM\Table(name="plg_sample_payment_config")
 * @ORM\Entity(repositoryClass="Plugin\SamplePayment\Repository\ConfigRepository")
 */
class Config
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", options={"unsigned":true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="api_url", type="string", length=1024, nullable=true)
     */
    private $api_url;

    /**
     * @var string
     *
     * @ORM\Column(name="api_id", type="string", length=255, nullable=true)
     */
    private $api_id;

    /**
     * @var string
     *
     * @ORM\Column(name="api_password", type="string", length=255, nullable=true)
     */
    private $api_password;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getApiUrl()
    {
        return $this->api_url;
    }

    /**
     * @param string $api_url
     *
     * @return $this;
     */
    public function setApiUrl($api_url)
    {
        $this->api_url = $api_url;

        return $this;
    }

    /**
     * @return string
     */
    public function getApiId()
    {
        return $this->api_id;
    }

    /**
     * @param string $api_id
     *
     * @return $this;
     */
    public function setApiId($api_id)
    {
        $this->api_id = $api_id;

        return $this;
    }

    /**
     * @return string
     */
    public function getApiPassword()
    {
        return $this->api_password;
    }

    /**
     * @param string $api_password
     *
     * @return $this
     */
    public function setApiPassword($api_password)
    {
        $this->api_password = $api_password;

        return $this;
    }
}
