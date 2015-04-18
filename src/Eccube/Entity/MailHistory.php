<?php

namespace Eccube\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * MailHistory
 */
class MailHistory extends \Eccube\Entity\AbstractEntity
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var integer
     */
    private $order_id;

    /**
     * @var \DateTime
     */
    private $send_date;

    /**
     * @var integer
     */
    private $template_id;

    /**
     * @var string
     */
    private $subject;

    /**
     * @var string
     */
    private $mail_body;

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
     * Set order_id
     *
     * @param integer $orderId
     * @return MailHistory
     */
    public function setOrderId($orderId)
    {
        $this->order_id = $orderId;

        return $this;
    }

    /**
     * Get order_id
     *
     * @return integer 
     */
    public function getOrderId()
    {
        return $this->order_id;
    }

    /**
     * Set send_date
     *
     * @param \DateTime $sendDate
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
     * Set template_id
     *
     * @param integer $templateId
     * @return MailHistory
     */
    public function setTemplateId($templateId)
    {
        $this->template_id = $templateId;

        return $this;
    }

    /**
     * Get template_id
     *
     * @return integer 
     */
    public function getTemplateId()
    {
        return $this->template_id;
    }

    /**
     * Set subject
     *
     * @param string $subject
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
     * @param string $mailBody
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
     * Set Creator
     *
     * @param \Eccube\Entity\Member $creator
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
