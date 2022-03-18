<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class HomeController extends AbstractController
{
    #[Route(path: '/')]
    public function homepage(SerializerInterface $serializer): Response
    {
        return $this->render('front/homepage.html.twig',
            [
                'user' => $serializer->serialize($this->getUser(), 'jsonld')
            ]
        );
    }
}