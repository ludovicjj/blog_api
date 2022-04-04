<?php

namespace App\DataTransformer;

use ApiPlatform\Core\DataTransformer\DataTransformerInitializerInterface;
use App\Dto\CheeseListingInput;
use App\Entity\CheeseListing;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

class CheeseListingInputDataTransformer implements DataTransformerInitializerInterface
{
    /**
     * Hydrate CheeseListing with data from CheeseListingInput (DTO)
     * If Context have no entity, create new CheeseListing (create)
     * Else get updated Entity from context (PUT)
     * Then change CheeseListing's properties value with CheeseListingInput data
     *
     * @param CheeseListingInput $object
     */
    public function transform($object, string $to, array $context = []): CheeseListing
    {
        if (isset($context[AbstractNormalizer::OBJECT_TO_POPULATE])) {
            /** @var CheeseListing $cheeseListing */
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