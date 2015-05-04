<?php

namespace Eccube\Entity;

/**
 * Bkup
 */
class Bkup extends \Eccube\Entity\AbstractEntity
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $memo;

    /**
     * @var \DateTime
     */
    private $create_date;

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set memo
     *
     * @param  string $memo
     * @return Bkup
     */
    public function setMemo($memo)
    {
        $this->memo = $memo;

        return $this;
    }

    /**
     * Get memo
     *
     * @return string
     */
    public function getMemo()
    {
        return $this->memo;
    }

    /**
     * Set create_date
     *
     * @param  \DateTime $createDate
     * @return Bkup
     */
    public function setCreateDate($createDate)
    {
        $this->create_date = $createDate;

        return $this;
    }

    /**
     * Get create_date
     *
     * @return \DateTime
     */
    public function getCreateDate()
    {
        return $this->create_date;
    }

    /**
     * Set name
     *
     * @param  string $name
     * @return Bkup
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }
}
