<?php

namespace App\Validator;

use App\Entity\DailyStats;
use App\Repository\DailyStatsRepository;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class IsUniqueStatsValidator extends ConstraintValidator
{
    public function __construct(
        private RequestStack $requestStack,
        private DailyStatsRepository $dailyStatsRepository
    )
    {
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        /* @var IsUniqueStats $constraint */

        if (!$value instanceof DailyStats) {
            throw new \LogicException('Only DailyStats is supported');
        }
        $request = $this->requestStack->getCurrentRequest();

        if (!$request) {
            return;
        }

        $originalData = $request->attributes->get('previous_data');
        $method = $request->getMethod();

        // try to create a new daily stats
        if (!$originalData && $method === 'POST') {
            $dailyStats = $this->dailyStatsRepository->find($value->getDateString());

            if ($dailyStats) {
                $this->context->buildViolation($constraint->message)
                    ->atPath('totalVisitors')
                    ->addViolation();
            }
        }
    }
}