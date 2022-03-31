<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;
use Doctrine\Common\Annotations\Annotation\Target;

/**
 * @Annotation
 * @Target({"CLASS"})
 */
class IsUniqueStats extends Constraint
{
    public $message = 'This daily stats already exist, come back tomorrow';

    /**
     * @return string
     */
    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}