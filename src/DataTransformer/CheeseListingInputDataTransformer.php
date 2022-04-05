<?php

namespace App\DataTransformer;

use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use App\Dto\CheeseListingInput;
use App\Entity\CheeseListing;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

class CheeseListingInputDataTransformer implements DataTransformerInterface
{
    /**
     * Hydrate CheeseListing with data from CheeseListingInput (DTO)
     *
     * First check if Context have underlying CheeseListing object into "object_to_populate",
     * If no CheeseListing into context, create a new CheeseListing (example: POST request)
     * Else get CheeseListing from context (example: PUT request)
     * Then change CheeseListing's properties value using CheeseListingInput data
     *
     * @param CheeseListingInput $object
     */
    public function transform($object, string $to, array $context = []): CheeseListing
    {
        if (isset($context[AbstractNormalizer::OBJECT_TO_POPULATE])) {
            $cheeseListing = $context[AbstractNormalizer::OBJECT_TO_POPULATE];
        } else {
            $cheeseListing = new CheeseListing();
        }

        $cheeseListing
            ->setTitle($object->title)
            ->setPrice($object->price)
            ->setDescription($object->description)
            ->setOwner($object->owner)
            ->setIsPublished($object->isPublished)
        ;

        return $cheeseListing;
    }

    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        if ($data instanceof CheeseListing) {
            return false;
        }

        return $to === CheeseListing::class && ($context['input']['class'] ?? null) === CheeseListingInput::class;
    }

    /**
     * To use this method class must implement DataTransformerInitializerInterface
     * Actually it's not used. I hydrate DTO with denormalizer
     * (see: Serializer/Denormalizer/CheeseListingInputDenormalizer)
     *
     *
     * Fetching the Entity from the Context
     * Hydrate CheeseListingInput with value from CheeseListing.
     * return empty or filled DTO to transform method
     *
     * @param string $inputClass
     * @param array $context
     * @return CheeseListingInput
     */
    public function initialize(string $inputClass, array $context = [])
    {
        $dto = new CheeseListingInput();

        if(isset($context[AbstractNormalizer::OBJECT_TO_POPULATE])) {

            /** @var CheeseListing $cheeseListing */
            $cheeseListing = $context[AbstractNormalizer::OBJECT_TO_POPULATE];

            $dto->title = $cheeseListing->getTitle();
            $dto->description = $cheeseListing->getDescription();
            $dto->price = $cheeseListing->getPrice();
            $dto->isPublished = $cheeseListing->getIsPublished();
            $dto->owner = $cheeseListing->getOwner();
        }

        return $dto;
    }
}