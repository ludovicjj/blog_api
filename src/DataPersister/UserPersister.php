<?php

namespace App\DataPersister;

use ApiPlatform\Core\DataPersister\DataPersisterInterface;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserPersister implements DataPersisterInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserPasswordHasherInterface $hasher
    )
    {
    }

    public function supports($data): bool
    {
        return $data instanceof User;
    }

    public function persist($data)
    {
        if ($data->getPlainPassword()) {
            $data->setPassword($this->hasher->hashPassword($data, $data->getPlainPassword()));
        }
        $data->eraseCredentials();

        $this->entityManager->persist($data);
        $this->entityManager->flush();

        return $data;
    }

    public function remove($data)
    {
        // TODO: Implement remove() method.
    }
}