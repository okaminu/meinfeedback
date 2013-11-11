<?php


namespace MFB\WidgetBundle\Director;

use MFB\WidgetBundle\Builder\BuilderInterface;

class MainWidgetDirector implements WidgetDirectorInterface {

    protected $builder;

    public function __construct(BuilderInterface $builder)
    {
        $this->builder = $builder;
    }

    public function build($lastFeedbacks, $feedbackCount, $feedbackRatingCount, $feedbackRatingAverage)
    {
        $this->builder->getFilledLayout();

        $this->builder->getElement('base');

        $this->builder->getElement('repeatText')
            ->setPositionX(10)
            ->setLastLine(222)
            ->addText($feedbackCount . " Bewertungen")
            ->addText($feedbackCount . " Ratings")
            ->addText($feedbackRatingAverage ." Average");

        /** @var Feedback $lastFeedback */
        $lastFeedback = reset($lastFeedbacks);
        $this->builder->getElement('text')
            ->setText($lastFeedback->getCreatedAt()->format('d.m.Y'))
            ->setPositionX(120)
            ->setPositionY(222);

        $this->builder->getElement('comment')
            ->setText($lastFeedbacks);

        return $this->builder->createImage();
    }
} 