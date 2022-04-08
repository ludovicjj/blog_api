<?php

namespace App\DataPersister;

use ApiPlatform\Core\DataPersister\DataPersisterInterface;
use App\Entity\CheeseListing;
use App\Entity\CheeseNotification;
use Doctrine\ORM\EntityManagerInterface;

class CheeseListingPersister implements DataPersisterInterface
{

    public function __construct(
        private DataPersisterInterface $decoratedDataPersister,
        private EntityManagerInterface $entityManager
    )
    {
    }

    public function supports($data): bool
    {
        return $data instanceof CheeseListing;
    }

    /**
     * @param CheeseListing $data
     */
    public function persist($data): void
    {
        $originalData = $this->entityManager->getUnitOfWork()->getOriginalEntityData($data);
        $wasPublished = $originalData['isPublished'] ?? false;

        if ($data->getIsPublished() && !$wasPublished) {
            $notification = new CheeseNotification($data, 'A new cheese listing is publish');
            $this->entityManager->persist($notification);
            // let DataPersister flush himself
        }
        $this->decoratedDataPersister->persist($data);
    }

    public function remove($data)
    {
        $this->decoratedDataPersister->remove($data);
    }
}