<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Routing\Annotation\Route;

class CategoryController extends AbstractController
{
    #[Route('/api/category', name: 'app_category', methods: ['GET'])]
    public function getAllCategories(CategoryRepository $categoryRepository, SerializerInterface $serializer): JsonResponse
    {
        $categories = $categoryRepository->findAll();

        $jsonCategories = $serializer->serialize($categories, 'json', ['groups' => 'getCategories']);

        return new JsonResponse($jsonCategories, Response::HTTP_OK, [], true);
    }

    #[Route('/api/category/{id}', name: 'app_category_id', methods: ['GET'])]
    public function getCategoryById(int $id,CategoryRepository $categoryRepository, SerializerInterface $serializer): JsonResponse
    {
        $category = $categoryRepository->find($id);
        if ($category != null) 
        {
            $jsonCategory = $serializer->serialize($category, 'json', ['groups' => 'getCategories']);
            return new JsonResponse($jsonCategory, Response::HTTP_OK, [], true);
        }
        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }
}
