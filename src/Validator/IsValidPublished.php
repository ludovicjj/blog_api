<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;
use Doctrine\Common\Annotations\Annotation\Target;

/**
 * @Annotation
 * @Target({"CLASS"})
 */
class IsValidPublished extends Constraint
{
    public $message = "Cannot publish, description is too short";
    public $unpublishMessage = "Only Admin can unpublish";

    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}