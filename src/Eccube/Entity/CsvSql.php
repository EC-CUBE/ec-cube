<?php

namespace Eccube\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CsvSql
 */
class CsvSql extends \Eccube\Entity\AbstractEntity
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $sql_name;

    /**
     * @var string
     */
    private $csv_sql;

    /**
     * @var \DateTime
     */
    private $create_date;

    /**
     * @var \DateTime
     */
    private $update_date;


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set sql_name
     *
     * @param string $sqlName
     * @return CsvSql
     */
    public function setSqlName($sqlName)
    {
        $this->sql_name = $sqlName;

        return $this;
    }

    /**
     * Get sql_name
     *
     * @return string 
     */
    public function getSqlName()
    {
        return $this->sql_name;
    }

    /**
     * Set csv_sql
     *
     * @param string $csvSql
     * @return CsvSql
     */
    public function setCsvSql($csvSql)
    {
        $this->csv_sql = $csvSql;

        return $this;
    }

    /**
     * Get csv_sql
     *
     * @return string 
     */
    public function getCsvSql()
    {
        return $this->csv_sql;
    }

    /**
     * Set create_date
     *
     * @param \DateTime $createDate
     * @return CsvSql
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
     * Set update_date
     *
     * @param \DateTime $updateDate
     * @return CsvSql
     */
    public function setUpdateDate($updateDate)
    {
        $this->update_date = $updateDate;

        return $this;
    }

    /**
     * Get update_date
     *
     * @return \DateTime 
     */
    public function getUpdateDate()
    {
        return $this->update_date;
    }
}
