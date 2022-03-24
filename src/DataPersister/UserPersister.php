<?php

namespace App\DataPersister;

use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use ApiPlatform\Core\DataPersister\DataPersisterInterface;
use App\Entity\User;
use Psr\Log\LoggerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserPersister implements ContextAwareDataPersisterInterface
{
    public function __construct(
        private DataPersisterInterface $decoratedDataPersister,
        private UserPasswordHasherInterface $hasher,
        private LoggerInterface $appLogger
    )
    {
    }

    public function supports($data, array $context = []): bool
    {
        return $data instanceof User;
    }

    public function persist($data, array $context = [])
    {
        if (($context['item_operation_name'] ?? null) === 'put') {
            $this->appLogger->info(sprintf('User %s is being updated', $data->getEmail()));
        }
        if (!$data->getId()) {
            $this->appLogger->info(sprintf('User %s just registered! Eureka!', $data->getEmail()));
        }
        if ($data->getPlainPassword()) {
            $data->setPassword($this->hasher->hashPassword($data, $data->getPlainPassword()));
        }
        $data->eraseCredentials();
        // it's now handle by subscriber
        //$data->setIsMe($this->security->getUser() === $data);
        $this->decoratedDataPersister->persist($data);

        return $data;
    }

    public function remove($data, array $context = [])
    {
        $this->decoratedDataPersister->remove($data);
    }
}