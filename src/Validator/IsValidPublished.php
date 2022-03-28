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
    public string $message = "Cannot publish, description is too short";
    public string $unpublishMessage = "Only Admin can unpublish";

    /**
     * @return string
     */
    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}