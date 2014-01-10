<?php
namespace MFB\FeedbackBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class RatingMaxBoundsValidator extends ConstraintValidator
{
    private $maxBounds;

    public function __construct($bounds)
    {
        $this->maxBounds = $bounds['max'];
    }

    public function validate($value, Constraint $constraint)
    {
        if ($value > $this->maxBounds) {
            $this->context->addViolation($constraint->message);
        }
    }
}