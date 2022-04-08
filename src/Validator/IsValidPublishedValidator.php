<?php

namespace App\Validator;

use App\Dto\CheeseListingInput;
use App\Entity\CheeseListing;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class IsValidPublishedValidator extends ConstraintValidator
{
    public function __construct(
        private Security $security,
        private RequestStack $requestStack
    )
    {
    }

    public function validate(mixed $value, Constraint $constraint)
    {
        /* @var $constraint IsValidPublished */

        if (!$value instanceof CheeseListingInput) {
            throw new \LogicException('Only CheeseListingInput is supported');
        }
        // constraint is now on CheeseListingInput (DTO) and DTO is not a doctrine entity
        // then we cannot use entity manager to get original entity data (cheeseListing)
        //$previousData = $this->entityManager->getUnitOfWork()->getOriginalEntityData($value);
        //$previousIsPublished = $previousData['isPublished'] ?? false;

        $request = $this->requestStack->getCurrentRequest();

        if (!$request) {
            return;
        }
        /** @var CheeseListing|null $previousData */
        $previousData = $request->attributes->get('data');

        $previousIsPublished = ($previousData) ? $previousData->getIsPublished() : false;

        // isPublished don't change
        if ($previousIsPublished === $value->isPublished) {
            return;
        }

        // trying publish
        if ($value->isPublished) {
            if (strlen($value->description) < 100 && !$this->security->isGranted('ROLE_ADMIN')) {
                $this->context->buildViolation($constraint->message)->atPath('description')->addViolation();
            }
            return;
        }

        // trying unpublish
        if (!$this->security->isGranted('ROLE_ADMIN')) {
            //throw new AccessDeniedException($constraint->unpublishMessage);
            $this->context->buildViolation($constraint->unpublishMessage)->atPath('description')->addViolation();
        }
    }
}