<?php
namespace MFB\FeedbackBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
* @Annotation
*/
class RatingMaxBounds extends Constraint
{
    public function validatedBy()
    {
        return 'mfb_feedback_validator_rating_max_bounds';
    }

    public $message = 'The rating is too big';
}