<?php
namespace MFB\FeedbackBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
* @Annotation
*/
class RatingMinBounds extends Constraint
{
    public function validatedBy()
    {
        return 'mfb_feedback_validator_rating_min_bounds';
    }

    public $message = 'Not all star ratings were selected';
}