<?php

namespace Plugin\E2E\Utility;

class ReviewData
{
    public string $reviewer_name;
    public string $reviewer_url;
    public string $title;
    public string $comment;

    /**
     *
     * @param $reviewer_name
     * @param $reviewer_url
     * @param $title
     * @param $comment
     */
    public function __construct($reviewer_name, $reviewer_url, $title, $comment)
    {
        $this->reviewer_name = $reviewer_name;
        $this->reviewer_url = $reviewer_url;
        $this->title = $title;
        $this->comment = $comment;
    }
}
