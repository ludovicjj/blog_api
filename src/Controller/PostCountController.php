<?php

namespace App\Controller;

use App\Repository\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class PostCountController extends AbstractController
{
    public function __construct(private PostRepository $postRepository)
    {}

    public function __invoke(Request $request): JsonResponse
    {
        $queryParameters = $request->query->all();

        $publishedQuery = $this->getQueryParameter('published', $queryParameters, '');
        $fieldsQuery = $this->getQueryParameter('fields', $queryParameters, '');
        $criteria = [];

        if ($publishedQuery !== '') {
            $criteria = ['isPublished' => $publishedQuery === '1'];
        }
        return new JsonResponse(['posts' => $this->postRepository->count($criteria)]);
    }

    private function getQueryParameter(string $key, array $queryParameters, mixed $default)
    {
        return array_key_exists($key, $queryParameters) ? $queryParameters[$key] : $default;
    }
}