<?php

namespace App\DataPersister;

use ApiPlatform\Core\DataPersister\DataPersisterInterface;
use App\Entity\User;
use Psr\Log\LoggerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserPersister implements DataPersisterInterface
{
    public function __construct(
        private DataPersisterInterface $decoratedDataPersister,
        private UserPasswordHasherInterface $hasher,
        private LoggerInterface $appLogger
    )
    {
    }

    public function supports($data): bool
    {
        return $data instanceof User;
    }

    public function persist($data)
    {
        if (!$data->getId()) {
            $this->appLogger->info(sprintf('User %s just registered! Eureka!', $data->getEmail()));
        }
        if ($data->getPlainPassword()) {
            $data->setPassword($this->hasher->hashPassword($data, $data->getPlainPassword()));
        }
        $data->eraseCredentials();

        $this->decoratedDataPersister->persist($data);

        return $data;
    }

    public function remove($data)
    {
        $this->decoratedDataPersister->remove($data);
    }
}