<?php

namespace Plugin\HogePlugin\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="reviews")
 */
class Review
{
    /**
     * @ORM\Id
     * @ORM\Column(type="string", length=50)
     */
    private $reviewId;

    /** 
     * @ORM\Column(type="string", length=50)
     */
    private $productId;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $userId;

    /**
     * @ORM\Column(type="integer")
     */
    private $rating;

    /**
     * @ORM\Column(type="string", length=300, nullable=true)
     */
    private $comment;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    public function getReviewId() { return $this->reviewId; }
    public function setReviewId($reviewId) { $this->reviewId = $reviewId; }

    public function getProductId() { return $this->productId; }
    public function setProductId($productId) { $this->productId = $productId; }

    public function getUserId() { return $this->userId; }
    public function setUserId($userId) { $this->userId = $userId; }

    public function getRating() { return $this->rating; }
    public function setRating($rating) { $this->rating = $rating; }

    public function getComment() { return $this->comment; }
    public function setComment($comment) { $this->comment = $comment; }

    public function getCreatedAt() { return $this->createdAt; }
    public function setCreatedAt($createdAt) { $this->createdAt = $createdAt; }
}