<?php

namespace App\DataTransformer;

use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use ApiPlatform\Core\Validator\ValidatorInterface;
use App\Dto\CheeseListingInput;
use App\Entity\CheeseListing;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

class CheeseListingInputDataTransformer implements DataTransformerInterface
{
    public function __construct(private ValidatorInterface $validator)
    {
    }

    /**
     * Transform CheeseListingInput to CheeseListing
     *
     * @param CheeseListingInput $object
     */
    public function transform($object, string $to, array $context = []): CheeseListing
    {
        $this->validator->validate($object);
        $cheeseListing = $context[AbstractNormalizer::OBJECT_TO_POPULATE] ?? null;

        return $object->createOrUpdateEntity($cheeseListing);
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
     * required API_PLATFORM -v 2.6
     * Not used
     * (see: Serializer/Denormalizer/CheeseListingInputDenormalizer)
     *
     *
     * Fetching the Entity from the Context
     * Hydrate CheeseListingInput with value from CheeseListing.
     * return empty or filled DTO used by transform method
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