<?php

namespace App\Controller;

use App\Entity\Post;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
class PostPublishController
{
    public function __invoke(Post $data): Post
    {
        $data->setIsPublished(true);
        return $data;
    }
}