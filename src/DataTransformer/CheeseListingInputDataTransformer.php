<?php

namespace App\DataTransformer;

use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use App\Dto\CheeseListingInput;
use App\Entity\CheeseListing;

class CheeseListingInputDataTransformer implements DataTransformerInterface
{
    /**
     * @param CheeseListingInput $object
     */
    public function transform($object, string $to, array $context = []): CheeseListing
    {
        $cheeseListing = new CheeseListing();
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
}