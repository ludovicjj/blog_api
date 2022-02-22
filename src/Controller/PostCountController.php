<?php

namespace App\Controller;

use App\Repository\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PostCountController extends AbstractController
{
    public function __construct(private PostRepository $postRepository)
    {
    }

    public function __invoke(): array
    {
        return ['posts' => $this->postRepository->count([])];

    }
}