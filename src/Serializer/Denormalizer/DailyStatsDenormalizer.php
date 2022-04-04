<?php

namespace App\Serializer\Denormalizer;

use App\Entity\DailyStats;
use Symfony\Component\Serializer\Normalizer\ContextAwareDenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareTrait;

class DailyStatsDenormalizer implements ContextAwareDenormalizerInterface, DenormalizerAwareInterface
{
    use DenormalizerAwareTrait;

    private const ALREADY_CALLED = 'DAILY_STATS_DENORMALIZER_ALREADY_CALLED';

    public function supportsDenormalization(mixed $data, string $type, string $format = null, array $context = []): bool
    {
        // avoid recursion: only call once per object
        if (isset($context[self::ALREADY_CALLED])) {
            return false;
        }

        return DailyStats::class === $type;
    }

    public function denormalize(mixed $data, string $type, string $format = null, array $context = [])
    {
        $context[self::ALREADY_CALLED] = true;

        $dailyStats = $this->denormalizer->denormalize($data, $type, $format, $context);

        if (!$dailyStats->getDate()) {
            $dailyStats->setDate(new \DateTimeImmutable('now'));
        }

        return $dailyStats;
    }
}