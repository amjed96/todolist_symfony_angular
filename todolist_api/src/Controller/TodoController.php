<?php

namespace App\Controller;

use App\Entity\Todo;
use App\Repository\TodoRepository;
use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

use DateTime;

class TodoController extends AbstractController
{
    #[Route('/api/todos', name: 'app_todo', methods: ['GET'])]
    public function getAllTodos(TodoRepository $todoRepositry, SerializerInterface $serializer): JsonResponse
    {
        $todoList = $todoRepositry->findAll();

        $jsonTodoList = $serializer->serialize($todoList, 'json', ['groups' => 'getTodos']);

        return new JsonResponse($jsonTodoList, Response::HTTP_OK, [], true);
    }

    #[Route('/api/todo/{id}', name: 'app_todo_id', methods: ['GET'])]
    public function getTodoById(int $id, TodoRepository $todoRepositry, SerializerInterface $serializer) 
    {
        $todo = $todoRepositry->find($id);
        if ($todo != null) 
        {
            $jsonTodo = $serializer->serialize($todo, 'json', ['groups' => 'getTodos']);
            return new JsonResponse($jsonTodo, Response::HTTP_OK, [], true);
        }
        return new JsonResponse(null , Response::HTTP_NOT_FOUND);
    }

    #[Route('/api/todo/delete/{id}', name: 'delete_todo', methods: ['DELETE'])]
    public function deleteTodo(Todo $todo, EntityManagerInterface $em): JsonResponse
    {
        $em->remove($todo);
        $em->flush();
        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

    #[Route('/api/todo/add', name: 'create_todo', methods: ['POST'])]
    public function createTodo(Request $request, SerializerInterface $serializer, CategoryRepository $categoryRepository, EntityManagerInterface $em): JsonResponse
    {
        $todo = $serializer->deserialize($request->getContent(), Todo::class, 'json');
        
        $todo->setDateCreation(new DateTime('now'));
        $todo->setEtat(false);

        $content = $request->toArray();
        $id_category = $content['category'] ?? -1;

        $todo->setCategory($categoryRepository->find($id_category));
        
        $em->persist($todo);
        $em->flush();
        
        $jsonTodo = $serializer->serialize($todo, 'json', ['groups' => 'getTodos']);

        return new JsonResponse($jsonTodo, Response::HTTP_CREATED, [], true);
    }

    #[Route('/api/todo/update/{id}', name: 'update_todo', methods: ['PUT'])]
    public function updateTodo(Todo $currentTodo, Request $request, SerializerInterface $serializer, CategoryRepository $categoryRepository, EntityManagerInterface $em): JsonResponse
    {
        $updatedTodo = $serializer->deserialize(
            $request->getContent(),
            Todo::class,
            'json',
            [AbstractNormalizer::OBJECT_TO_POPULATE => $currentTodo]
        );
        $content = $request->toArray();
        $id_category = $content['category'] ?? -1;
        $updatedTodo->setCategory($categoryRepository->find($id_category));

        $em->persist($updatedTodo);
        $em->flush();

        return new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT);
    }

    #[Route('/api/todo/done/{id}', name: 'done_todo', methods: ['PUT'])]
    public function doneTodo(Todo $currentTodo, Request $request, SerializerInterface $serializer, EntityManagerInterface $em): JsonResponse
    {
        /*$currentTodo = $serializer->deserialize(
            $request->getContent(),
            Todo::class,
            'json',
            // [AbstractNormalizer::OBJECT_TO_POPULATE => $currentTodo]
        );*/
        $currentTodo->setEtat(true);

        $em->persist($currentTodo);
        $em->flush();

        return new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT);
    }

    #[Route('/api/todo/undone/{id}', name: 'undone_todo', methods: ['PUT'])]
    public function undoneTodo(Todo $currentTodo, Request $request, SerializerInterface $serializer, EntityManagerInterface $em): JsonResponse
    {
        /*$currentTodo = $serializer->deserialize(
            $request->getContent(),
            Todo::class,
            'json',
            // [AbstractNormalizer::OBJECT_TO_POPULATE => $currentTodo]
        );*/
        $currentTodo->setEtat(false);

        $em->persist($currentTodo);
        $em->flush();

        return new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT);
    }
}
