<?php

namespace Eccube\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Kiyaku
 */
class Kiyaku extends AbstractEntity
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $kiyaku_title;

    /**
     * @var string
     */
    private $kiyaku_text;

    /**
     * @var integer
     */
    private $rank;

    /**
     * @var integer
     */
    private $creator_id;

    /**
     * @var \DateTime
     */
    private $create_date;

    /**
     * @var \DateTime
     */
    private $update_date;

    /**
     * @var integer
     */
    private $del_flg;

    /**
     * Set id
     *
     * @param integer $kiyaku_id
     * @return Kiyaku
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get kiyaku_id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set kiyaku_title
     *
     * @param string $kiyakuTitle
     * @return Kiyaku
     */
    public function setKiyakuTitle($kiyakuTitle)
    {
        $this->kiyaku_title = $kiyakuTitle;

        return $this;
    }

    /**
     * Get kiyaku_title
     *
     * @return string
     */
    public function getKiyakuTitle()
    {
        return $this->kiyaku_title;
    }

    /**
     * Set kiyaku_text
     *
     * @param string $kiyakuText
     * @return Kiyaku
     */
    public function setKiyakuText($kiyakuText)
    {
        $this->kiyaku_text = $kiyakuText;

        return $this;
    }

    /**
     * Get kiyaku_text
     *
     * @return string
     */
    public function getKiyakuText()
    {
        return $this->kiyaku_text;
    }

    /**
     * Set rank
     *
     * @param string $rank
     * @return Kiyaku
     */
    public function setRank($rank)
    {
        $this->rank = $rank;

        return $this;
    }

    /**
     * Get rank
     *
     * @return integer
     */
    public function getRank()
    {
        return $this->rank;
    }

    /**
     * Set creator_id
     *
     * @param string $creatorId
     * @return Kiyaku
     */
    public function setCreatorId($creatorId)
    {
        $this->creator_id = $creatorId;

        return $this;
    }

    /**
     * Get creator_id
     *
     * @return integer
     */
    public function getCreatorId()
    {
        return $this->creator_id;
    }

    /**
     * Set create_date
     *
     * @param integer $createDate
     * @return Kiyaku
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
     * @param string $updateDate
     * @return Kiyaku
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

    /**
     * Set del_flg
     *
     * @param string $delFlg
     * @return Kiyaku
     */
    public function setDelFlg($delFlg)
    {
        $this->del_flg = $delFlg;

        return $this;
    }

    /**
     * Set top_tpl
     *
     * @param string $topTpl
     * @return BaseInfo
     */
    public function setTopTpl($topTpl)
    {
        $this->top_tpl = $topTpl;

        return $this;
    }

    /**
     * Get top_tpl
     *
     * @return string
     */
    public function getTopTpl()
    {
        return $this->top_tpl;
    }

    /**
     * Set product_tpl
     *
     * @param string $productTpl
     * @return BaseInfo
     */
    public function setProductTpl($productTpl)
    {
        $this->product_tpl = $productTpl;

        return $this;
    }

    /**
     * Get product_tpl
     *
     * @return string
     */
    public function getProductTpl()
    {
        return $this->product_tpl;
    }

    /**
     * Set detail_tpl
     *
     * @param string $detailTpl
     * @return BaseInfo
     */
    public function setDetailTpl($detailTpl)
    {
        $this->detail_tpl = $detailTpl;

        return $this;
    }

    /**
     * Get detail_tpl
     *
     * @return string
     */
    public function getDetailTpl()
    {
        return $this->detail_tpl;
    }

    /**
     * Set mypage_tpl
     *
     * @param string $mypageTpl
     * @return BaseInfo
     */
    public function setMypageTpl($mypageTpl)
    {
        $this->mypage_tpl = $mypageTpl;

        return $this;
    }

    /**
     * Get mypage_tpl
     *
     * @return string
     */
    public function getMypageTpl()
    {
        return $this->mypage_tpl;
    }

    /**
     * Set good_traded
     *
     * @param string $goodTraded
     * @return BaseInfo
     */
    public function setGoodTraded($goodTraded)
    {
        $this->good_traded = $goodTraded;

        return $this;
    }

    /**
     * Get good_traded
     *
     * @return string
     */
    public function getGoodTraded()
    {
        return $this->good_traded;
    }

    /**
     * Set message
     *
     * @param string $message
     * @return BaseInfo
     */
    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * Get message
     *
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Set regular_holiday_ids
     *
     * @param string $regularHolidayIds
     * @return BaseInfo
     */
    public function setRegularHolidayIds($regularHolidayIds)
    {
        $this->regular_holiday_ids = $regularHolidayIds;

        return $this;
    }

    /**
     * Get regular_holiday_ids
     *
     * @return string
     */
    public function getRegularHolidayIds()
    {
        return $this->regular_holiday_ids;
    }

    /**
     * Set latitude
     *
     * @param string $latitude
     * @return BaseInfo
     */
    public function setLatitude($latitude)
    {
        $this->latitude = $latitude;

        return $this;
    }

    /**
     * Get latitude
     *
     * @return string
     */
    public function getLatitude()
    {
        return $this->latitude;
    }

    /**
     * Set longitude
     *
     * @param string $longitude
     * @return BaseInfo
     */
    public function setLongitude($longitude)
    {
        $this->longitude = $longitude;

        return $this;
    }

    /**
     * Get longitude
     *
     * @return string
     */
    public function getLongitude()
    {
        return $this->longitude;
    }

    /**
     * Set downloadable_days
     *
     * @param string $downloadableDays
     * @return BaseInfo
     */
    public function setDownloadableDays($downloadableDays)
    {
        $this->downloadable_days = $downloadableDays;

        return $this;
    }

    /**
     * Get downloadable_days
     *
     * @return string
     */
    public function getDownloadableDays()
    {
        return $this->downloadable_days;
    }

    /**
     * Set downloadable_days_unlimited
     *
     * @param string $downloadableDaysUnlimited
     * @return BaseInfo
     */
    public function setDownloadableDaysUnlimited($downloadableDaysUnlimited)
    {
        $this->downloadable_days_unlimited = $downloadableDaysUnlimited;

        return $this;
    }

    /**
     * Get downloadable_days_unlimited
     *
     * @return string
     */
    public function getDownloadableDaysUnlimited()
    {
        return $this->downloadable_days_unlimited;
    }
}
