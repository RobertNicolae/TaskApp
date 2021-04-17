<?php

namespace App\Controller;

use App\Entity\Task;
use App\Entity\User;
use App\Form\TaskType;
use App\Repository\TaskRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class TaskController
 * @package App\Controller
 * @Route("/task", name="task.")
 */
class TaskController extends AbstractController
{
    /**
     * @Route("/index", name="index")
     * @param UserRepository $userRepository
     * @param TaskRepository $taskRepository
     * @return Response
     */
    public function index(UserRepository $userRepository, TaskRepository $taskRepository): Response
    {
        $user = $userRepository->findOneBy([
            "id" => 1
        ]);
        $tasksFromDB = $taskRepository->findBy([
            "user" => $user
        ]);

        $tasks = [];

        foreach ($tasksFromDB as $task) {
            $tasks[] = [
                "name" => $task->getName(),
                "status" => $task->getStatus(),
                "id" => $task->getId(),
                "username" => $task->getUser()->getEmail()
            ];
        }
        return $this->json([
            "tasks" => $tasks
        ]);
    }

    /**
     * @Route("/create", name="create")
     * @param Request $request
     */
    public function createTask(Request $request, UserRepository $userRepository)
    {
        $body = $request->getContent();
        $data = json_decode($body, true);

        $task = new Task();
        $task->setStatus(0);
        $user = $userRepository->findOneBy([
            "id" => 1
        ]);
        $task->setUser($user);
        $form = $this->createForm(TaskType::class, $task);

        $form->submit($data);
        if ($form->isSubmitted()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($task);
            $em->flush();

            http_response_code(200);
            return $this->json([
                "message" => "Success"
            ]);
        }
        http_response_code(400);
        return $this->json([
            "message" => "Application error"
        ]);
    }

    /**
     * @Route("/delete/{id}", name="delete")
     * @param Task $task
     */
    public function deleteTask(Task $task)
    {
        if ($task) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($task);
            $em->flush();

            http_response_code(200);
            return $this->json([
                "message" => "Success"
            ]);
        }
        http_response_code(400);
        return $this->json([
            "message" => "Task invalid"
        ]);
    }

    /**
     * @Route("/update/{id}", name="update")
     * @param Task $task
     * @param Request $request
     */
    public function updateTask(Task $task, Request $request)
    {
        $body = $request->getContent();
        $data = json_decode($body, true);


        $form = $this->createForm(TaskType::class, $task);
        $form->submit($data);

        if ($form->isSubmitted()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($task);
            $em->flush();


            http_response_code(200);
            return $this->json([
                "message" => "Success"
            ]);
        }

        http_response_code(400);
        return $this->json([
            "message" => "Application Error"
        ]);
    }

    /**
     * @Route("/mark/{id}", name="mark")
     * @param Request $request
     * @param TaskRepository $taskRepository
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function markTask(Request $request, TaskRepository $taskRepository)
    {
        $id = $request->get("id");
        $task = $taskRepository->findOneBy([
            "id" => $id
        ]);

        if ($task->getStatus() === 0) {
            $task->setStatus(1);
            $em = $this->getDoctrine()->getManager();
            $em->persist($task);
            $em->flush();
            return $this->json([
                "message" => "Success"
            ]);
        } else if ($task->getStatus() === 1) {
            $task->setStatus(0);
            $em = $this->getDoctrine()->getManager();
            $em->persist($task);
            $em->flush();
            return $this->json([
                "message" => "Success"
            ]);
        }
        return $this->json([
            "message" => "Error"
        ]);


    }
}
