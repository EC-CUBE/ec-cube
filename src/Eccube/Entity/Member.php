<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2015 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */


namespace Eccube\Entity;

use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Mapping\ClassMetadata;

/**
 * Member
 */
class Member extends \Eccube\Entity\AbstractEntity implements UserInterface
{
    public static function loadValidatorMetadata(ClassMetadata $metadata)
    {
        $metadata->addConstraint(new UniqueEntity(array(
            'fields'  => 'login_id',
            'message' => '既に利用されているログインIDです'
        )));
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getName();
    }

    /**
     * {@inheritdoc}
     */
    public function getRoles()
    {
        return array('ROLE_ADMIN');
    }

    /**
     * {@inheritdoc}
     */
    public function getUsername()
    {
        return $this->login_id;
    }

    /**
     * {@inheritdoc}
     */
    public function eraseCredentials()
    {
    }

    /**
     * @var integer
     */
    protected $id;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $department;

    /**
     * @var string
     */
    protected $login_id;

    /**
     * @var string
     */
    protected $password;

    /**
     * @var string
     */
    protected $salt;

    /**
     * @var \Eccube\Entity\Master\Authority
     */
    protected $Authority;

    /**
     * @var integer
     */
    protected $rank;

    /**
     * @var \Eccube\Entity\Master\Work
     */
    protected $Work;

    /**
     * @var integer
     */
    protected $del_flg;

    /**
     * @var \Eccube\Entity\Member
     */
    protected $Creator;

    /**
     * @var \DateTime
     */
    protected $create_date;

    /**
     * @var \DateTime
     */
    protected $update_date;

    /**
     * @var \DateTime
     */
    protected $login_date;

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
     * Set name
     *
     * @param  string $name
     * @return Member
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

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
     * Set department
     *
     * @param  string $department
     * @return Member
     */
    public function setDepartment($department)
    {
        $this->department = $department;

        return $this;
    }

    /**
     * Get department
     *
     * @return string
     */
    public function getDepartment()
    {
        return $this->department;
    }

    /**
     * Set login_id
     *
     * @param  string $loginId
     * @return Member
     */
    public function setLoginId($loginId)
    {
        $this->login_id = $loginId;

        return $this;
    }

    /**
     * Get login_id
     *
     * @return string
     */
    public function getLoginId()
    {
        return $this->login_id;
    }

    /**
     * Set password
     *
     * @param  string $password
     * @return Member
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get password
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set salt
     *
     * @param  string $salt
     * @return Member
     */
    public function setSalt($salt)
    {
        $this->salt = $salt;

        return $this;
    }

    /**
     * Get salt
     *
     * @return string
     */
    public function getSalt()
    {
        return $this->salt;
    }

    /**
     * Set authority
     *
     * @param \Eccube\Entity\Master\Authority $authority
     * @return Member
     */
    public function setAuthority(\Eccube\Entity\Master\Authority $Authority = null)
    {
        $this->Authority = $Authority;

        return $this;
    }

    /**
     * Get authority
     *
     * @return \Eccube\Entity\Master\Authority
     */
    public function getAuthority()
    {
        return $this->Authority;
    }

    /**
     * Set rank
     *
     * @param  integer $rank
     * @return Member
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
     * Set work
     *
     * @param  \Eccube\Entity\Master\Work $Work
     * @return Member
     */
    public function setWork(\Eccube\Entity\Master\Work $Work)
    {
        $this->Work = $Work;

        return $this;
    }

    /**
     * Get work
     *
     * @return \Eccube\Entity\Master\Work
     */
    public function getWork()
    {
        return $this->Work;
    }

    /**
     * Set del_flg
     *
     * @param  integer $delFlg
     * @return Member
     */
    public function setDelFlg($delFlg)
    {
        $this->del_flg = $delFlg;

        return $this;
    }

    /**
     * Get del_flg
     *
     * @return boolean
     */
    public function getDelFlg()
    {
        return $this->del_flg;
    }

    /**
     * Set Creator
     *
     * @param  \Eccube\Entity\Member $Creator
     * @return \Eccube\Entity\Member
     */
    public function setCreator(\Eccube\Entity\Member $Creator)
    {
        $this->Creator = $Creator;

        return $this;
    }

    /**
     * Get Creator
     *
     * @return \Eccube\Entity\Member
     */
    public function getCreator()
    {
        return $this->Creator;
    }

    /**
     * Set create_date
     *
     * @param  \DateTime $createDate
     * @return Member
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
     * @param  \DateTime $updateDate
     * @return Member
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
     * Set login_date
     *
     * @param  \DateTime $loginDate
     * @return Member
     */
    public function setLoginDate($loginDate)
    {
        $this->login_date = $loginDate;

        return $this;
    }

    /**
     * Get login_date
     *
     * @return \DateTime
     */
    public function getLoginDate()
    {
        return $this->login_date;
    }
}
