<?php

namespace App\Controller;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

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

    #[Route('/api/category/delete/{id}', name: 'delete_category', methods: ['DELETE'])]
    public function deleteCategory(Category $category, EntityManagerInterface $em): JsonResponse
    {
        $em->remove($category);
        $em->flush();
        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

    #[Route('/api/category/add', name: 'create_category', methods: ['POST'])]
    public function createCategory(Request $request, SerializerInterface $serializer, EntityManagerInterface $em): JsonResponse
    {
        $category = $serializer->deserialize($request->getContent(), Category::class, 'json');

        $em->persist($category);
        $em->flush();

        $jsonCategory = $serializer->serialize($category, 'json', ['groups' => 'getCategories']);

        return new JsonResponse($jsonCategory, Response::HTTP_CREATED, [], true);
    }

    #[Route('/api/category/update/{id}', name: 'update_category', methods: ['PUT'])]
    public function updateCategory(Category $currentCategory, Request $request, SerializerInterface $serializer, EntityManagerInterface $em): JsonResponse
    {
        $updatedCategory = $serializer->deserialize(
            $request->getContent(),
            Category::class,
            'json',
            [AbstractNormalizer::OBJECT_TO_POPULATE => $currentCategory]
        );
        
        $em->persist($updatedCategory);
        $em->flush();

        return new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT);
    }
}
