<?php
namespace MFB\WidgetBundle\Director;

use Foxrate\BaseWidgetBundle\Builder\BuilderInterface;
use MFB\WidgetBundle\Builder\Elements\ImageBaseElement;
use MFB\WidgetBundle\Builder\Elements\ImageCommentElement;
use MFB\WidgetBundle\Builder\Elements\ImageRepeatTextElement;
use MFB\WidgetBundle\Builder\Elements\ImageTextElement;
use MFB\WidgetBundle\Entity\Color;
use MFB\WidgetBundle\Service\Resources;

class MainWidgetDirector implements WidgetDirectorInterface
{
    protected $builder;
    protected $resources;

    public function __construct(BuilderInterface $builder, Resources $resources)
    {
        $this->builder = $builder;
        $this->resources = $resources;
    }

    public function build(
        $lastFeedbacks,
        $feedbackCount,
        $feedbackRatingAverage,
        Color $textColor,
        Color $backgroundColor
    ) {
        $baseImage = new ImageBaseElement($this->resources->getResources());
        $baseImage->setBackgroundColor($backgroundColor);
        $this->builder->addElement($baseImage);

        $repText = new ImageRepeatTextElement($this->resources->getResources());
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

        $text = new ImageTextElement($this->resources->getResources());
        $text->setText($date)
            ->setPositionX(120)
            ->setPositionY(214)
            ->setFontColorCode($textColor);
        $this->builder->addElement($text);


        $text = new ImageTextElement($this->resources->getResources());
        $text->setText('Jetzt Feedback geben')
            ->setPositionX(9)
            ->setPositionY(273)
            ->setFontSize(10)
            ->setFontColorCode($textColor);
        $this->builder->addElement($text);

        $comment = new ImageCommentElement($this->resources->getResources());
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
