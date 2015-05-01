<?php

namespace Eccube\Entity;

/**
 * IndexList
 */
class IndexList extends \Eccube\Entity\AbstractEntity
{
    /**
     * @var string
     */
    private $table_name;

    /**
     * @var string
     */
    private $column_name;

    /**
     * @var integer
     */
    private $recommend_flg;

    /**
     * @var string
     */
    private $recommend_comment;

    /**
     * Set table_name
     *
     * @param  string    $tableName
     * @return IndexList
     */
    public function setTableName($tableName)
    {
        $this->table_name = $tableName;

        return $this;
    }

    /**
     * Get table_name
     *
     * @return string
     */
    public function getTableName()
    {
        return $this->table_name;
    }

    /**
     * Set column_name
     *
     * @param  string    $columnName
     * @return IndexList
     */
    public function setColumnName($columnName)
    {
        $this->column_name = $columnName;

        return $this;
    }

    /**
     * Get column_name
     *
     * @return string
     */
    public function getColumnName()
    {
        return $this->column_name;
    }

    /**
     * Set recommend_flg
     *
     * @param  integer   $recommendFlg
     * @return IndexList
     */
    public function setRecommendFlg($recommendFlg)
    {
        $this->recommend_flg = $recommendFlg;

        return $this;
    }

    /**
     * Get recommend_flg
     *
     * @return integer
     */
    public function getRecommendFlg()
    {
        return $this->recommend_flg;
    }

    /**
     * Set recommend_comment
     *
     * @param  string    $recommendComment
     * @return IndexList
     */
    public function setRecommendComment($recommendComment)
    {
        $this->recommend_comment = $recommendComment;

        return $this;
    }

    /**
     * Get recommend_comment
     *
     * @return string
     */
    public function getRecommendComment()
    {
        return $this->recommend_comment;
    }
}
