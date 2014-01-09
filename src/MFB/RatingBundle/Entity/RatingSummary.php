<?php
namespace MFB\RatingBundle\Entity;


class RatingSummary
{
    private $name;

    private $rating;

    public function __construct($name, $rating)
    {
        $this->name = $name;
        $this->rating = $rating;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $rating
     */
    public function setRating($rating)
    {
        $this->rating = $rating;
    }

    /**
     * @return mixed
     */
    public function getRating()
    {
        return $this->rating;
    }
}