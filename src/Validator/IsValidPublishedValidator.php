<?php

namespace App\Validator;

use App\Entity\CheeseListing;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class IsValidPublishedValidator extends ConstraintValidator
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private Security $security
    )
    {
    }

    public function validate(mixed $value, Constraint $constraint)
    {
        /* @var $constraint IsValidPublished */

        if (!$value instanceof CheeseListing) {
            throw new \LogicException('Only CheeseListing is supported');
        }

        $previousData = $this->entityManager->getUnitOfWork()->getOriginalEntityData($value);
        $previousIsPublished = $previousData['isPublished'] ?? false;

        // isPublished don't change
        if ($previousIsPublished === $value->getIsPublished()) {
            return;
        }

        // trying publish
        if ($value->getIsPublished()) {
            if (strlen($value->getDescription()) < 100 && !$this->security->isGranted('ROLE_ADMIN')) {
                $this->context->buildViolation($constraint->message)->atPath('description')->addViolation();
            }
            return;
        }

        if (!$this->security->isGranted('ROLE_ADMIN')) {
            //throw new AccessDeniedException($constraint->unpublishMessage);
            $this->context->buildViolation($constraint->unpublishMessage)->atPath('description')->addViolation();
        }
    }
}