<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class SecurityController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login', methods: ['POST'])]
    public function login()
    {
        if (!$this->isGranted("IS_AUTHENTICATED_FULLY")) {
            return $this->json([
                'error' => 'Invalid login request: check that the Content-Type header is "application/json".'
            ], 400);
        }
        $user = $this->getUser();
        return $this->json([
            'email' => $user->getUserIdentifier()
        ]);
    }
}