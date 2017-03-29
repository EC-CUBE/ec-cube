<?php

namespace Eccube\Entity;

use Doctrine\ORM\Mapping as ORM;
use Eccube\Util\EntityUtil;

/**
 * AuthorityRole
 */
class AuthorityRole extends \Eccube\Entity\AbstractEntity
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $deny_url;

    /**
     * @var \DateTime
     */
    private $create_date;

    /**
     * @var \DateTime
     */
    private $update_date;

    /**
     * @var \Eccube\Entity\Master\Authority
     */
    private $Authority;

    /**
     * @var \Eccube\Entity\Member
     */
    private $Creator;


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
     * Set deny_url
     *
     * @param string $denyUrl
     * @return AuthorityRole
     */
    public function setDenyUrl($denyUrl)
    {
        $this->deny_url = $denyUrl;

        return $this;
    }

    /**
     * Get deny_url
     *
     * @return string
     */
    public function getDenyUrl()
    {
        return $this->deny_url;
    }

    /**
     * Set create_date
     *
     * @param \DateTime $createDate
     * @return AuthorityRole
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
     * @return AuthorityRole
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
     * Set Authority
     *
     * @param \Eccube\Entity\Master\Authority $authority
     * @return AuthorityRole
     */
    public function setAuthority(\Eccube\Entity\Master\Authority $authority = null)
    {
        $this->Authority = $authority;

        return $this;
    }

    /**
     * Get Authority
     *
     * @return \Eccube\Entity\Master\Authority
     */
    public function getAuthority()
    {
        return $this->Authority;
    }

    /**
     * Set Creator
     *
     * @param \Eccube\Entity\Member $creator
     * @return AuthorityRole
     */
    public function setCreator(\Eccube\Entity\Member $creator)
    {
        $this->Creator = $creator;

        return $this;
    }

    /**
     * Get Creator
     *
     * @return \Eccube\Entity\Member
     */
    public function getCreator()
    {
        if (EntityUtil::isEmpty($this->Creator)) {
            return null;
        }
        return $this->Creator;
    }
}
