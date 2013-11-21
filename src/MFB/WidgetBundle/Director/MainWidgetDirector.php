<?php


namespace MFB\WidgetBundle\Director;

use MFB\WidgetBundle\Builder\BuilderInterface;
use MFB\WidgetBundle\Builder\Elements\ImageBaseElement;
use MFB\WidgetBundle\Builder\Elements\ImageRepeatTextElement;
use MFB\WidgetBundle\Builder\Elements\ImageTextElement;
use MFB\WidgetBundle\Builder\Elements\ImageCommentElement;

class MainWidgetDirector implements WidgetDirectorInterface
{
    protected $builder;

    public function __construct(BuilderInterface $builder)
    {
        $this->builder = $builder;
    }

    public function build($lastFeedbacks, $feedbackCount, $feedbackRatingCount, $feedbackRatingAverage)
    {
        $this->builder->addElement(new ImageBaseElement($this->builder->getResources()));

        $repText = new ImageRepeatTextElement($this->builder->getResources());
        $repText->setPositionX(10)
            ->setLastLine(214)
            ->addText($feedbackCount . " Bewertungen")
            ->setFontColorCode(108, 108, 108);

        if ($feedbackRatingCount != 0) {
            $repText->addText($feedbackRatingCount . " Ratings");
            $repText->addText($feedbackRatingAverage ." Average");
        }
        $this->builder->addElement($repText);

        /** @var Feedback $lastFeedback */
        $lastFeedback = reset($lastFeedbacks);
        $date = '';
        if (!empty($lastFeedback)) {
            $date =  $lastFeedback->getCreatedAt()->format('d.m.Y');
        }

        $text = new ImageTextElement($this->builder->getResources());
        $text->setText($date)
             ->setPositionX(120)
             ->setPositionY(214)
             ->setFontColorCode(108, 108, 108);
        $this->builder->addElement($text);


        $text = new ImageTextElement($this->builder->getResources());
        $text->setText('Jetzt Feedback geben')
            ->setPositionX(9)
            ->setPositionY(273)
            ->setFontSize(10)
            ->setFontColorCode(108, 108, 108);
        $this->builder->addElement($text);

        $comment = new ImageCommentElement($this->builder->getResources());
        $comment->setText($lastFeedbacks)
            ->setBoxWidth(170)
            ->setBoxHeight(180)
            ->setPositionX(10)
            ->setPositionY(25)
            ->setFontColorCode(230, 230, 230);
        $this->builder->addElement($comment);

        return $this->builder->createImage();
    }
}
