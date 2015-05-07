<?php

namespace Eccube\Entity;

/**
 * MailHistory
 */
class MailHistory extends \Eccube\Entity\AbstractEntity
{
    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getSubject();
    }

    /**
     * @var integer
     */
    private $id;

    /**
     * @var \DateTime
     */
    private $send_date;

    /**
     * @var string
     */
    private $subject;

    /**
     * @var string
     */
    private $mail_body;

    /**
     * @var \Eccube\Entity\Order
     */
    private $Order;

    /**
     * @var \Eccube\Entity\Master\MailTemplate
     */
    private $MailTemplate;

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
     * Set send_date
     *
     * @param  \DateTime   $sendDate
     * @return MailHistory
     */
    public function setSendDate($sendDate)
    {
        $this->send_date = $sendDate;

        return $this;
    }

    /**
     * Get send_date
     *
     * @return \DateTime
     */
    public function getSendDate()
    {
        return $this->send_date;
    }

    /**
     * Set subject
     *
     * @param  string      $subject
     * @return MailHistory
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;

        return $this;
    }

    /**
     * Get subject
     *
     * @return string
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * Set mail_body
     *
     * @param  string      $mailBody
     * @return MailHistory
     */
    public function setMailBody($mailBody)
    {
        $this->mail_body = $mailBody;

        return $this;
    }

    /**
     * Get mail_body
     *
     * @return string
     */
    public function getMailBody()
    {
        return $this->mail_body;
    }

    /**
     * Set Order
     *
     * @param  \Eccube\Entity\Order $order
     * @return MailHistory
     */
    public function setOrder(\Eccube\Entity\Order $order)
    {
        $this->Order = $order;

        return $this;
    }

    /**
     * Get Order
     *
     * @return \Eccube\Entity\Order
     */
    public function getOrder()
    {
        return $this->Order;
    }

    /**
     * Set MailTemplate
     *
     * @param  \Eccube\Entity\Master\MailTemplate $mailTemplate
     * @return MailHistory
     */
    public function setMailTemplate(\Eccube\Entity\Master\MailTemplate $mailTemplate = null)
    {
        $this->MailTemplate = $mailTemplate;

        return $this;
    }

    /**
     * Get MailTemplate
     *
     * @return \Eccube\Entity\Master\MailTemplate
     */
    public function getMailTemplate()
    {
        return $this->MailTemplate;
    }

    /**
     * Set Creator
     *
     * @param  \Eccube\Entity\Member $creator
     * @return MailHistory
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
        return $this->Creator;
    }
}
