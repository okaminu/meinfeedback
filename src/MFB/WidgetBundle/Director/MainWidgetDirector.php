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
            ->setLastLine(214)
            ->addText($feedbackCount . " Bewertungen")
            ->setFontColorCode(108, 108, 108)
        ;

        if ($feedbackRatingCount != 0) {
            $this->builder->getElement('repeatText')
                ->addText($feedbackRatingCount . " Ratings")
                ->addText($feedbackRatingAverage ." Average");
        }

        /** @var Feedback $lastFeedback */
        $lastFeedback = reset($lastFeedbacks);
        $this->builder->getElement('text')
            ->setText($lastFeedback->getCreatedAt()->format('d.m.Y'))
            ->setPositionX(120)
            ->setPositionY(214)
            ->setFontColorCode(108, 108, 108);

        $this->builder->cloneElement('text')
            ->setText('Jetzt Feedback geben')
            ->setPositionX(9)
            ->setPositionY(273)
            ->setFontSize(10)
            ->setFontColorCode(108, 108, 108);

        $this->builder->getElement('comment')
            ->setText($lastFeedbacks)
            ->setBoxWidth(170)
            ->setBoxHeight(180)
            ->setPositionX(10)
            ->setPositionY(25)
            ->setFontColorCode(230, 230, 230);

        return $this->builder->createImage();
    }
} 