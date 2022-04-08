<?php

namespace App\Serializer\Denormalizer;

use App\Dto\CheeseListingInput;
use App\Entity\CheeseListing;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\ContextAwareDenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareTrait;

class CheeseListingInputDenormalizer implements ContextAwareDenormalizerInterface, DenormalizerAwareInterface
{
    use DenormalizerAwareTrait;

    private const ALREADY_CALLED = 'CHEESE_LISTING_INPUT_DENORMALIZER_ALREADY_CALLED';

    public function supportsDenormalization(mixed $data, string $type, string $format = null, array $context = []): bool
    {
        // avoid recursion: only call once per object
        if (isset($context[self::ALREADY_CALLED])) {
            return false;
        }

        return $type === CheeseListingInput::class;
    }


    public function denormalize(mixed $data, string $type, string $format = null, array $context = [])
    {
        $context[self::ALREADY_CALLED] = true;
        $context[AbstractNormalizer::OBJECT_TO_POPULATE] = $this->createDto($context);

        return $this->denormalizer->denormalize($data, $type, $format, $context);
    }

    private function createDto(array $context): CheeseListingInput
    {
        $entity = $context['object_to_populate'] ?? null;

        if ($entity && !$entity instanceof CheeseListing) {
            throw new \Exception(sprintf('Unexpected resource class "%s"', get_class($entity)));
        }

        return CheeseListingInput::createFromEntity($entity);
    }
}