<?php

namespace App\Controller;

use App\Repository\TodoRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\Response;

class TodoController extends AbstractController
{
    #[Route('/api/todos', name: 'app_todo', methods: ['GET'])]
    public function getAllTodos(TodoRepository $todoRepositry, SerializerInterface $serializer): JsonResponse
    {
        $todoList = $todoRepositry->findAll();

        $jsonTodoList = $serializer->serialize($todoList, 'json');

        return new JsonResponse($jsonTodoList, Response::HTTP_OK, [], true);
    }

    #[Route('/api/todo/{id}', name: 'app_todo_id', methods: ['GET'])]
    public function getTodoById(int $id, TodoRepository $todoRepositry, SerializerInterface $serializer) {
        $todo = $todoRepositry->find($id);
        if ($todo != null) {
            $jsonTodo = $serializer->serialize($todo, 'json');
            return new JsonResponse($jsonTodo, Response::HTTP_OK, [], true);
        }
        return new JsonResponse(null , Response::HTTP_NOT_FOUND);
    }
}
