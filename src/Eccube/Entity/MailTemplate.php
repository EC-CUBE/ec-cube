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

namespace Eccube\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * MailTemplate
 *
 * @ORM\Table(name="dtb_mail_template")
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="discriminator_type", type="string", length=255)
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Entity(repositoryClass="Eccube\Repository\MailTemplateRepository")
 */
class MailTemplate extends \Eccube\Entity\AbstractEntity
{
    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getName() ? $this->getName() : '';
    }

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", options={"unsigned":true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string|null
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=true)
     */
    private $name;

    /**
     * @var string|null
     *
     * @ORM\Column(name="file_name", type="string", length=255, nullable=true)
     */
    private $file_name;

    /**
     * @var string|null
     *
     * @ORM\Column(name="mail_subject", type="string", length=255, nullable=true)
     */
    private $mail_subject;

    /**
     * @var string|null
     *
     * @ORM\Column(name="mail_header", type="string", length=4000, nullable=true)
     */
    private $mail_header;

    /**
     * @var string|null
     *
     * @ORM\Column(name="mail_footer", type="string", length=4000, nullable=true)
     */
    private $mail_footer;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="create_date", type="datetimetz")
     */
    private $create_date;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="update_date", type="datetimetz")
     */
    private $update_date;

    /**
     * @var \Eccube\Entity\Member
     *
     * @ORM\ManyToOne(targetEntity="Eccube\Entity\Member")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="creator_id", referencedColumnName="id")
     * })
     */
    private $Creator;

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name.
     *
     * @param string|null $name
     *
     * @return MailTemplate
     */
    public function setName($name = null)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name.
     *
     * @return string|null
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set fileName.
     *
     * @param string|null $fileName
     *
     * @return MailTemplate
     */
    public function setFileName($fileName = null)
    {
        $this->file_name = $fileName;

        return $this;
    }

    /**
     * Get fileName.
     *
     * @return string|null
     */
    public function getFileName()
    {
        return $this->file_name;
    }

    /**
     * Set mailSubject.
     *
     * @param string|null $mailSubject
     *
     * @return MailTemplate
     */
    public function setMailSubject($mailSubject = null)
    {
        $this->mail_subject = $mailSubject;

        return $this;
    }

    /**
     * Get mailSubject.
     *
     * @return string|null
     */
    public function getMailSubject()
    {
        return $this->mail_subject;
    }

    /**
     * Set mailHeader.
     *
     * @param string|null $mailHeader
     *
     * @return MailTemplate
     */
    public function setMailHeader($mailHeader = null)
    {
        $this->mail_header = $mailHeader;

        return $this;
    }

    /**
     * Get mailHeader.
     *
     * @return string|null
     */
    public function getMailHeader()
    {
        return $this->mail_header;
    }

    /**
     * Set mailFooter.
     *
     * @param string|null $mailFooter
     *
     * @return MailTemplate
     */
    public function setMailFooter($mailFooter = null)
    {
        $this->mail_footer = $mailFooter;

        return $this;
    }

    /**
     * Get mailFooter.
     *
     * @return string|null
     */
    public function getMailFooter()
    {
        return $this->mail_footer;
    }

    /**
     * Set createDate.
     *
     * @param \DateTime $createDate
     *
     * @return MailTemplate
     */
    public function setCreateDate($createDate)
    {
        $this->create_date = $createDate;

        return $this;
    }

    /**
     * Get createDate.
     *
     * @return \DateTime
     */
    public function getCreateDate()
    {
        return $this->create_date;
    }

    /**
     * Set updateDate.
     *
     * @param \DateTime $updateDate
     *
     * @return MailTemplate
     */
    public function setUpdateDate($updateDate)
    {
        $this->update_date = $updateDate;

        return $this;
    }

    /**
     * Get updateDate.
     *
     * @return \DateTime
     */
    public function getUpdateDate()
    {
        return $this->update_date;
    }

    /**
     * Set creator.
     *
     * @param \Eccube\Entity\Member|null $creator
     *
     * @return MailTemplate
     */
    public function setCreator(\Eccube\Entity\Member $creator = null)
    {
        $this->Creator = $creator;

        return $this;
    }

    /**
     * Get creator.
     *
     * @return \Eccube\Entity\Member|null
     */
    public function getCreator()
    {
        return $this->Creator;
    }
}
