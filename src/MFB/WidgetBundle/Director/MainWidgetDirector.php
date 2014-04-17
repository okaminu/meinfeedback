<?php
namespace MFB\WidgetBundle\Director;

use MFB\WidgetBundle\Builder\BuilderInterface;
use MFB\WidgetBundle\Builder\Elements\ImageBaseElement;
use MFB\WidgetBundle\Builder\Elements\ImageCommentElement;
use MFB\WidgetBundle\Builder\Elements\ImageRepeatTextElement;
use MFB\WidgetBundle\Builder\Elements\ImageTextElement;
use MFB\WidgetBundle\Entity\Color;

class MainWidgetDirector implements WidgetDirectorInterface
{
    protected $builder;

    public function __construct(BuilderInterface $builder)
    {
        $this->builder = $builder;
    }

    public function build(
        $lastFeedbacks,
        $feedbackCount,
        $feedbackRatingAverage,
        Color $textColor,
        Color $backgroundColor
    ) {
        $baseImage = new ImageBaseElement($this->builder->getResources());
        $baseImage->setBackgroundColor($backgroundColor);
        $this->builder->addElement($baseImage);

        $repText = new ImageRepeatTextElement($this->builder->getResources());
        $repText->setPositionX(10)
            ->setLastLine(214)
            ->addText($feedbackCount . " Bewertungen")
            ->setFontColorCode($textColor);

        if ($feedbackRatingAverage != 0) {
            $repText->addText($feedbackRatingAverage ." Average");
        }
        $this->builder->addElement($repText);

        /** @var Feedback $lastFeedback */
        $lastFeedback = reset($lastFeedbacks);
        $date = '';
        if (!empty($lastFeedback)) {
            $date =  $lastFeedback->getFeedback()->getCreatedAt()->format('d.m.Y');
        }

        $text = new ImageTextElement($this->builder->getResources());
        $text->setText($date)
            ->setPositionX(120)
            ->setPositionY(214)
            ->setFontColorCode($textColor);
        $this->builder->addElement($text);


        $text = new ImageTextElement($this->builder->getResources());
        $text->setText('Jetzt Feedback geben')
            ->setPositionX(9)
            ->setPositionY(273)
            ->setFontSize(10)
            ->setFontColorCode($textColor);
        $this->builder->addElement($text);

        $comment = new ImageCommentElement($this->builder->getResources());
        $comment->setText($lastFeedbacks)
            ->setBoxWidth(170)
            ->setBoxHeight(180)
            ->setPositionX(10)
            ->setPositionY(25)
            ->setFontColorCode(new Color('E6E6E6'));
        $this->builder->addElement($comment);

        return $this->builder->createImage();
    }
}
