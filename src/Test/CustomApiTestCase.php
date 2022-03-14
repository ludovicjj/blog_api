<?php

namespace App\Test;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\Client;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class CustomApiTestCase extends ApiTestCase
{
    protected function createUser(string $email, string $password, array $roles = []): User
    {
        $container = static::getContainer();

        /** @var UserPasswordHasherInterface $encoder */
        $encoder = $container->get('test.security.password_hash');

        $user = new User();
        $user->setEmail($email)
             ->setPassword($encoder->hashPassword($user, $password))
             ->setUsername(substr($email, 0, strpos($email, '@')))
             ->setRoles($roles);

        $em = $container->get('doctrine')->getManager();
        $em->persist($user);
        $em->flush();

        return $user;
    }

    protected function login(Client $client, string $email, string $password)
    {
        $client->request('POST', '/login', [
            'headers' => ['Content-Type' => 'application/json'],
            'json' => [
                'email' => $email,
                'password' => $password
            ],
        ]);

        $this->assertResponseStatusCodeSame(200);
    }

    protected function createUserAndLogin(Client $client, string $email, string $password): User
    {
        $user = $this->createUser($email, $password);
        $this->login($client, $email, $password);
        return $user;
    }

    protected function getEntityManager() : EntityManagerInterface
    {
        $container = static::getContainer();
        return $container->get('doctrine')->getManager();
    }
}