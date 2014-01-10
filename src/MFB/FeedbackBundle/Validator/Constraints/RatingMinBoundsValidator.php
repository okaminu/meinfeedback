<?php
namespace MFB\FeedbackBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class RatingMinBoundsValidator extends ConstraintValidator
{
    private $minBounds;

    public function __construct($bounds)
    {
        $this->minBounds = $bounds['min'];
    }


    public function validate($value, Constraint $constraint)
    {
        if ($value < $this->minBounds) {
            $this->context->addViolation($constraint->message);
        }
    }
}