<?php

namespace App\Controller;

use App\Entity\Post;

class PostPublishController
{
    public function __invoke(Post $post): Post
    {
        $post->setIsPublished(true);
        return $post;
    }
}